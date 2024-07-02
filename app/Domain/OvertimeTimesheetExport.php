<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Toby\Models\User;

class OvertimeTimesheetExport implements WithMultipleSheets
{
    protected Collection $users;
    protected Carbon $month;

    public function sheets(): array
    {
        return $this->users
            ->map(fn(User $user): OvertimeTimesheetPerUserSheet => new OvertimeTimesheetPerUserSheet($user, $this->month))
            ->toArray();
    }

    public function forUsers(Collection $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function forMonth(Carbon $month): static
    {
        $this->month = $month;

        return $this;
    }
}
