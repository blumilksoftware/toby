<?php

declare(strict_types=1);

namespace Toby\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Toby\Helpers\YearPeriodRetriever;
use Toby\Models\YearPeriod;

class SelectYearPeriodController extends Controller
{
    public function __invoke(Request $request, YearPeriod $yearPeriod): RedirectResponse
    {
        $request->session()->put(YearPeriodRetriever::SESSION_KEY, $yearPeriod->id);

        return redirect()
            ->back()
            ->with("success", __("Selected year period has been changed"));
    }
}
