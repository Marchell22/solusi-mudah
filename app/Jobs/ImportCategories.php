<?php
namespace App\Jobs;

use App\Imports\CategoriesImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ImportCompleted;

class ImportCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fieldMap;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, array $fieldMap = [], $userId = null)
    {
        $this->filePath = $filePath;
        $this->fieldMap = $fieldMap;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Run import
        Excel::import(
            new CategoriesImport($this->fieldMap),
            $this->filePath,
            'public'
        );

        // Send notification if user ID is provided
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                Notification::send($user, new ImportCompleted('Categories'));
            }
        }
    }
}