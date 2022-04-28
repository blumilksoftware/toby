<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\Holiday;
use Toby\Infrastructure\Http\Requests\HolidayRequest;
use Toby\Infrastructure\Http\Resources\HolidayFormDataResource;
use Toby\Infrastructure\Http\Resources\HolidayResource;

class ResumeController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia("Resumes/Index");
    }

    public function create(): Response
    {
        return inertia("Resumes/Create",[
            ]);
    }
}
