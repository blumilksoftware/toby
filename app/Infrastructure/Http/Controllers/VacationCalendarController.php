<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Inertia\Response;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Resources\UserResource;

class VacationCalendarController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->orderBy("last_name")
            ->orderBy("first_name")
            ->paginate();

        return inertia("Calendar", [
            "users" => UserResource::collection($users),
        ]);
    }
}
