<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\EmployeeMilestoneResource;

class EmployeesMilestonesController extends Controller
{
    public function index(Request $request): Response
    {
        $searchText = $request->query("search");
        $sort = $request->query("sort");

        $users = User::query()
            ->sortForEmployeesMilestones($sort)
            ->search($searchText)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->paginate()
            ->withQueryString();

        return inertia("EmployeesMilestones", [
            "users" => EmployeeMilestoneResource::collection($users),
            "filters" => [
                "search" => $searchText,
            ],
        ]);
    }
}
