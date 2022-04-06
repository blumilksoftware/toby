<?php

declare(strict_types=1);

namespace Toby\Domain;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Toby\Eloquent\Models\User;

class TimesheetExport implements WithMultipleSheets
{
    protected Collection $users;
    protected Collection $types;
    protected Carbon $month;

    public function sheets(): array
    {
        return $this->users
            ->map(fn(User $user) => new TimesheetPerUserSheet($user, $this->month, $this->types))
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

    public function forVacationTypes(Collection $types): static
    {
        $this->types = $types;

        return $this;
    }
}
