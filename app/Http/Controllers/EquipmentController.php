<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\EquipmentExport;
use Toby\Http\Requests\EquipmentRequest;
use Toby\Http\Resources\EquipmentItemResource;
use Toby\Http\Resources\SimpleUserResource;
use Toby\Models\EquipmentItem;
use Toby\Models\EquipmentLabel;
use Toby\Models\User;

class EquipmentController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): RedirectResponse|Response
    {
        $authUser = $request->user();

        if ($authUser->cannot("manageEquipment")) {
            return redirect()->route("equipment-items.indexForEmployee");
        }

        $searchQuery = $request->query("search");

        $equipmentItems = EquipmentItem::query()
            ->with(["assignee" => fn(BelongsTo $query): BelongsTo => $query->withTrashed()])
            ->search($searchQuery)
            ->when(
                $request->query("assignee") && $request->query("assignee") !== "unassigned",
                fn(Builder $query): Builder => $query->where("assignee_id", $request->query("assignee")),
            )
            ->when(
                $request->query("assignee") === "unassigned",
                fn(Builder $query): Builder => $query->where("assignee_id", null),
            )
            ->labels($request->query("labels"))
            ->orderBy("id_number")
            ->paginate()
            ->withQueryString();

        $users = User::query()
            ->withTrashed($authUser->canSeeInactiveUsers())
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Equipment/Index", [
            "equipmentItems" => EquipmentItemResource::collection($equipmentItems),
            "labels" => EquipmentLabel::query()->pluck("name"),
            "users" => SimpleUserResource::collection($users),
            "filters" => [
                "search" => $searchQuery,
                "labels" => $request->query("labels"),
                "assignee" => $request->query("assignee"),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function indexForEmployee(Request $request): RedirectResponse|Response
    {
        $searchQuery = $request->query("search");

        $equipmentItems = EquipmentItem::query()
            ->with("assignee")
            ->search($searchQuery)
            ->labels($request->query("labels"))
            ->where("assignee_id", $request->user()->id)
            ->orderBy("id_number")
            ->paginate()
            ->withQueryString();

        return inertia("Equipment/IndexForEmployee", [
            "equipmentItems" => EquipmentItemResource::collection($equipmentItems),
            "labels" => EquipmentLabel::query()->pluck("name"),
            "filters" => [
                "search" => $searchQuery,
                "labels" => $request->query("labels"),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize("manageEquipment");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Equipment/Create", [
            "users" => SimpleUserResource::collection($users),
            "labels" => EquipmentLabel::query()->pluck("name"),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(
        EquipmentRequest $request,
    ): RedirectResponse {
        $this->authorize("manageEquipment");

        EquipmentItem::query()->create($request->getData());

        return redirect()
            ->route("equipment-items.index")
            ->with("success", __("Equipment item created."));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(EquipmentItem $equipmentItem): Response
    {
        $this->authorize("manageEquipment");

        $users = User::query()
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Equipment/Edit", [
            "equipmentItem" => new EquipmentItemResource($equipmentItem),
            "users" => SimpleUserResource::collection($users),
            "labels" => EquipmentLabel::query()->pluck("name"),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(
        EquipmentRequest $request,
        EquipmentItem $equipmentItem,
    ): RedirectResponse {
        $this->authorize("manageUsers");

        $equipmentItem->update($request->getData());

        return redirect()
            ->route("equipment-items.index")
            ->with("success", __("Equipment item updated."));
    }

    public function destroy(EquipmentItem $equipmentItem): RedirectResponse
    {
        $this->authorize("manageEquipment");

        $equipmentItem->delete();

        return redirect()
            ->route("equipment-items.index")
            ->with("success", __("Equipment item deleted."));
    }

    public function downloadExcel(): BinaryFileResponse
    {
        $this->authorize("manageEquipment");

        $equipmentItems = EquipmentItem::query()->with("assignee")->get();

        $equipmentExport = new EquipmentExport($equipmentItems);

        $name = __("Equipment") . " " . Carbon::now()->translatedFormat("d F Y") . ".xlsx";

        return Excel::download($equipmentExport, $name);
    }
}
