<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Eloquent\Models\EquipmentLabel;
use Toby\Infrastructure\Http\Requests\EquipmentLabelRequest;
use Toby\Infrastructure\Http\Resources\EquipmentLabelResource;

class EquipmentLabelController extends Controller
{
    public function index(): Response
    {
        $this->authorize("manageEquipment");

        $labels = EquipmentLabel::query()
            ->orderBy("name")
            ->get();

        return inertia("EquipmentLabels", [
            "labels" => EquipmentLabelResource::collection($labels),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(EquipmentLabelRequest $request): RedirectResponse
    {
        $this->authorize("manageEquipment");

        $label = EquipmentLabel::query()->create($request->data());

        return redirect()
            ->back()
            ->with("success", __("Label :name created.", [
                "name" => $label->name,
            ]));
    }

    public function destroy(EquipmentLabel $equipmentLabel): RedirectResponse
    {
        $this->authorize("manageEquipment");

        $equipmentLabel->delete();

        return redirect()
            ->back()
            ->with("success", __("Label :name deleted.", [
                "name" => $equipmentLabel->name,
            ]));
    }
}
