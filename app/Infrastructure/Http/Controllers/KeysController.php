<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Toby\Domain\Notifications\KeyHasBeenGivenNotification;
use Toby\Domain\Notifications\KeyHasBeenTakenNotification;
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
        $this->authorize("manage", Key::class);

        $key = Key::query()->create();

        return redirect()
            ->back()
            ->with("success", __("Key no :number has been created.", [
                "number" => $key->id,
            ]));
    }

    public function take(Key $key, Request $request): RedirectResponse
    {
        $previousUser = $key->user;

        $key->user()->associate($request->user());

        $key->save();

        if ($previousUser) {
            $key->notify(new KeyHasBeenTakenNotification($request->user(), $previousUser));
        }

        return redirect()
            ->back()
            ->with("success", __("Key no :number has been taken from :user.", [
                "number" => $key->id,
                "user" => $previousUser?->profile->full_name,
            ]));
    }

    /**
     * @throws AuthorizationException
     */
    public function give(Key $key, GiveKeyRequest $request): RedirectResponse
    {
        $this->authorize("give", $key);

        $recipient = $request->recipient();

        $key->user()->associate($recipient);

        $key->save();

        $key->notify(new KeyHasBeenGivenNotification($request->user(), $recipient));

        return redirect()
            ->back()
            ->with("success", __("Key no :number has been given to :user.", [
                "number" => $key->id,
                "user" => $recipient->profile->full_name,
            ]));
    }

    public function destroy(Key $key): RedirectResponse
    {
        $this->authorize("manage", Key::class);

        $key->delete();

        return redirect()
            ->back()
            ->with("success", __("Key no :number has been deleted.", [
                "number" => $key->id,
            ]));
    }
}
