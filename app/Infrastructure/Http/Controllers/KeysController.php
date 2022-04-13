<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Key;
use Toby\Infrastructure\Http\Resources\KeyResource;

class KeysController extends Controller
{
    public function index(Request $request, YearPeriodRetriever $yearPeriodRetriever): Response
    {
        $keys = Key::query()->get();

        return inertia("Keys/Index", [
            "keys" => KeyResource::collection($keys),
        ]);
    }
}
