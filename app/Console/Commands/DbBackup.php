<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;


class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database daily';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fileName = 'dbBackup_' . now()->format('Y-m-d') . '.sql.gz';

        $dbUser   = env('DB_USERNAME');
        $dbPass   = env('DB_PASSWORD');
        $dbHost   = env('DB_HOST');
        $dbName   = env('DB_DATABASE');

        
        $path = storage_path("app/mySqlBackup/{$fileName}");

        $command = "mysqldump --user=\"{$dbUser}\" --password=\"{$dbPass}\" --host=\"{$dbHost}\" --single-transaction --set-gtid-purged=OFF {$dbName} | gzip > \"{$path}\"";
       
        exec($command);
    }

}
