<?php

namespace App\Console\Commands;

use App\Models\SPTProgres;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;

class CleanupIncompleteUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spt-progres:cleanup {--days=7 : Hapus SPT yang tidak ada progress dalam N hari} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup SPT entries yang tidak ada aktivitas upload dalam periode tertentu';

    protected $googleDrive;

    public function __construct(GoogleDriveService $googleDrive)
    {
        parent::__construct();
        $this->googleDrive = $googleDrive;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $force = $this->option('force');

        $this->info("=== SPT Progres Cleanup ===");
        $this->info("Looking for incomplete SPT entries older than {$days} days...");
        $this->newLine();

        // Find incomplete SPT older than N days
        $cutoffDate = now()->subDays($days);
        
        $incompleteCount = SPTProgres::where('is_complete', false)
            ->where('created_at', '<', $cutoffDate)
            ->count();

        if ($incompleteCount === 0) {
            $this->info('✓ No incomplete SPT entries found to cleanup.');
            return 0;
        }

        $this->line("Found {$incompleteCount} incomplete SPT entries.");

        if (!$force && !$this->confirm('Delete these entries?')) {
            $this->info('Cleanup cancelled.');
            return 0;
        }

        // Delete entries
        $deleted = 0;
        $errors = 0;

        $entries = SPTProgres::where('is_complete', false)
            ->where('created_at', '<', $cutoffDate)
            ->get();

        foreach ($entries as $entry) {
            try {
                // Try to delete from Google Drive
                if ($entry->google_drive_folder_id) {
                    try {
                        $this->googleDrive->deleteFile($entry->google_drive_folder_id);
                    } catch (\Exception $e) {
                        $this->warn("Warning: Could not delete Google Drive folder for {$entry->nomor_spt}: " . $e->getMessage());
                    }
                }

                // Delete from database
                $entry->delete();
                $deleted++;
                
                $this->components->task("Deleted: {$entry->nomor_spt}");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error deleting {$entry->nomor_spt}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("=== Cleanup Complete ===");
        $this->line("Deleted: {$deleted}");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }

        return 0;
    }
}
