<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Console\Commands\Database;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupPostgresDatabase extends Command
{
    protected $signature = "toby:backup-db";
    protected $description = "Backup application Postgres database.";

    public function handle(Application $application, Repository $configRepository, FilesystemManager $filesystemManager): void
    {
        $this->purgeBackupDirectory($filesystemManager);

        $connectionString = $this->buildConnectionString($configRepository);
        $backupFilePath = $this->buildBackupFilePath($application, $configRepository);

        $this->createDump($connectionString, $backupFilePath);
    }

    protected function purgeBackupDirectory(FilesystemManager $filesystemManager): void
    {
        $backupStorage = $filesystemManager->disk("database_backup");
        $allFiles = $backupStorage->allFiles();
        $backupStorage->delete($allFiles);
    }

    protected function buildConnectionString(Repository $configRepository): string
    {
        $username = $configRepository->get("database.connections.pgsql.username");
        $password = $configRepository->get("database.connections.pgsql.password");
        $databaseName = $configRepository->get("database.connections.pgsql.database");
        $databaseHost = $configRepository->get("database.connections.pgsql.host");
        $databasePort = $configRepository->get("database.connections.pgsql.port");

        return "postgresql://$username:$password@$databaseHost:$databasePort/$databaseName";
    }

    protected function buildBackupFilePath(Application $application, Repository $configRepository): string
    {
        $databaseName = $configRepository->get("database.connections.pgsql.database");
        $environmentName = $application->environment();
        $dateString = Carbon::now()->toDateString();

        $backupFilename = "$environmentName-$databaseName-dump-$dateString.dump";

        $diskPath = $configRepository->get("filesystems.disks.database_backup.root");

        return $diskPath . "/" . $backupFilename;
    }

    protected function createDump(string $connectionString, string $backupFilePath): void
    {
        $process = new Process([
            "pg_dump",
            "--format",
            "custom",
            "--no-owner",
            "--dbname",
            $connectionString,
            "--file",
            $backupFilePath,
        ]);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            $this->error($exception->getMessage());

            throw $exception;
        }

        $this->info("Created backup file successfully.");
    }
}
