<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Toby\Eloquent\Helpers\YearPeriodRetriever;
use Toby\Eloquent\Models\YearPeriod;

class SelectYearPeriodController extends Controller
{
    public function __invoke(Request $request, YearPeriod $yearPeriod): RedirectResponse
    {
        $request->session()->put(YearPeriodRetriever::SESSION_KEY, $yearPeriod->id);
        Cache::forget('selected_year_period');

        return redirect()
            ->back()
            ->with("info", __("Selected year period changed."));
    }
}
