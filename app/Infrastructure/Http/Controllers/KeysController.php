<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Toby\Eloquent\Models\Key;
use Toby\Eloquent\Models\User;
use Toby\Infrastructure\Http\Requests\GiveKeyRequest;
use Toby\Infrastructure\Http\Resources\KeyResource;
use Toby\Infrastructure\Http\Resources\SimpleUserResource;

class KeysController extends Controller
{
    public function index(Request $request): Response
    {
        $keys = Key::query()
            ->oldest()
            ->get();

        $users = User::query()
            ->where("id", "!=", $request->user()->id)
            ->orderByProfileField("last_name")
            ->orderByProfileField("first_name")
            ->get();

        return inertia("Keys", [
            "keys" => KeyResource::collection($keys),
            "users" => SimpleUserResource::collection($users),
            "can" => [
                "manageKeys" => $request->user()->can("manage", Key::class),
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize("manageKeys");

        $request->user()->keys()->create();

        return redirect()
            ->back()
            ->with("success", __("Key has been created."));
    }

    public function take(Key $key, Request $request): RedirectResponse
    {
        $key->user()->associate($request->user());

        $key->save();

        return redirect()
            ->back()
            ->with("success", __("Key has been taked."));
    }

    public function give(Key $key, GiveKeyRequest $request): RedirectResponse
    {
        $key->user()->associate($request->recipient());

        $key->save();

        return redirect()
            ->back()
            ->with("success", __("Key has been given."));
    }

    public function destroy(Key $key): RedirectResponse
    {
        $this->authorize("manageKeys");

        $key->delete();

        return redirect()
            ->back()
            ->with("success", __("Key has been deleted."));
    }
}
