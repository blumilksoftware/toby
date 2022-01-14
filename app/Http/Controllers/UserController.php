<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Inertia\Response;
use Toby\Http\Resources\UserResource;
use Toby\Models\User;

class UserController extends Controller
{
    public function index(): Response
    {
        return inertia("Users/Index", [
            "users" => UserResource::collection(User::all()),
        ]);
    }
}
