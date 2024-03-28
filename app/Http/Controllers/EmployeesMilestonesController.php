<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Domain\EmployeesMilestonesRetriever;
use Toby\Http\Resources\EmployeeMilestoneResource;

class EmployeesMilestonesController extends Controller
{
    public function index(Request $request, EmployeesMilestonesRetriever $employeesMilestoneRetriever): Response
    {
        $searchText = $request->query("search");
        $sort = $request->query("sort");

        $users = $employeesMilestoneRetriever->getResults($searchText, $sort);

        return inertia("EmployeesMilestones", [
            "users" => EmployeeMilestoneResource::collection($users),
            "filters" => [
                "search" => $searchText,
                "sort" => $sort,
            ],
        ]);
    }
}
