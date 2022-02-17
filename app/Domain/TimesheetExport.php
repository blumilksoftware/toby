<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TimesheetExport implements WithMultipleSheets
{
    protected Collection $users;
    protected Carbon $month;

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->users as $user) {
            $sheets[] = new TimesheetPerUserSheet($user, $this->month);
        }

        return $sheets;
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
