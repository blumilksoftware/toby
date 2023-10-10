<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Toby\Domain\EquipmentExport;
use Toby\Eloquent\Models\EquipmentItem;
use Toby\Eloquent\Models\EquipmentLabel;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\EquipmentRequest;
use Toby\Infrastructure\Http\Resources\EquipmentItemResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class EquipmentController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): RedirectResponse|Response
    {
        $this->authorize("manageEquipment");

        $searchQuery = $request->query("search");

        $equipmentItems = EquipmentItem::query()
            ->search($searchQuery)
            ->when(
                $request->has("assignee"),
                fn($query) => $query->where("assignee_id", $request->query("assignee")),
            )
            ->labels($request->query("labels"))
            ->orderBy("id_number")
            ->paginate()
            ->withQueryString();

        $users = User::query()
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
                "assignee" => (int)$request->query("assignee"),
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

        EquipmentItem::query()->create($request->data());

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

        $equipmentItem->update($request->data());

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

        $equipmentItems = EquipmentItem::query()->get();

        $equipmentExport = new EquipmentExport($equipmentItems);

        $name = __("Equipment") . " " . Carbon::now()->translatedFormat("d F Y") . ".xlsx";

        return Excel::download($equipmentExport, $name);
    }
}
