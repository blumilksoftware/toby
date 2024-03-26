<?php

declare(strict_types=1);

namespace Toby\Console\Commands;

use Illuminate\Console\Command;
use Toby\Domain\Enums\VacationType;
use Toby\Models\VacationRequest;

class RenameHomeOfficeToRemoteWork extends Command
{
    protected $signature = "toby:rename-home-office-to-remote-work";
    protected $description = "Rename home office to remote work.";

    public function handle(): void
    {
        VacationRequest::query()
            ->where("type", "home_office")
            ->update(["type" => VacationType::RemoteWork]);
    }
}
