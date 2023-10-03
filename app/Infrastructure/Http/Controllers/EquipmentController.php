<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
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
    public function index(Request $request): Response
    {
        $this->authorize("manageEquipment");

        $searchQuery = $request->query("search");

        $equipmentItems = EquipmentItem::query()
            ->search($searchQuery)
            ->labels($request->query("labels"))
            ->paginate()
            ->withQueryString();

        return inertia("Equipment/Index", [
            "equipmentItems" => EquipmentItemResource::collection($equipmentItems),
            "labels" => EquipmentLabel::query()->pluck("name"),
            "filters" => [
                "search" => $searchQuery,
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
}
