<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\UserResource;

class MonthlyUsage extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize("listMonthlyUsage");

        $currentUser = $request->user();

        $users = User::query()
            ->where("id", "!=", $currentUser->id)
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->get();

        $users->prepend($currentUser);

        return inertia("MonthlyUsage", [
            "users" => UserResource::collection($users),
            "can" => [
                "listMonthlyUsage" => $request->user()->can("listMonthlyUsage"),
            ],
        ]);
    }
}
