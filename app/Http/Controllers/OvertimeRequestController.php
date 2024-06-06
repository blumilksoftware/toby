<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Toby\Actions\OvertimeRequest\CreateAction;
use Toby\Enums\SettlementType;
use Toby\Http\Requests\OvertimeRequestRequest;

class OvertimeRequestController extends Controller
{
    public function create(): Response
    {
        return inertia("OvertimeRequest/Create", [
            "settlementTypes" => SettlementType::casesToSelect(),
        ]);
    }

    public function store(OvertimeRequestRequest $request, CreateAction $createAction): RedirectResponse
    {
        $overtimeRequest = $createAction->execute($request->data(), $request->user());

        return redirect()
            ->route("overtime.requests.show", $overtimeRequest)
            ->with("success", __("Request created."));
    }
}
