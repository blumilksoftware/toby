<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Http\Resources\UserResource;
use Toby\Models\User;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->search($request->query("search"))
            ->paginate()
            ->withQueryString();

        return inertia("Users/Index", [
            "users" => UserResource::collection($users),
            "filters" => $request->only("search"),
        ]);
    }
}
