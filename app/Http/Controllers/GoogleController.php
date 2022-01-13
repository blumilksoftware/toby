<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Socialite\SocialiteManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Toby\Models\User;

class GoogleController extends Controller
{
    public function redirect(SocialiteManager $socialiteManager): RedirectResponse
    {
        return $socialiteManager->driver("google")->redirect();
    }

    public function callback(AuthFactory $auth, SocialiteManager $socialiteManager): RedirectResponse
    {
        $socialUser = $socialiteManager->driver("google")->user();

        try {
            /** @var User $user */
            $user = User::query()
                ->where("email", $socialUser->getEmail())
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return redirect()
                ->route("login")
                ->withErrors([
                    "oauth" => __("User does not exist."),
                ]);
        }

        $auth->guard()->login($user, true);

        return redirect()->route("dashboard");
    }
}
