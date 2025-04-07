<?php
namespace App\Jobs;

use App\Exports\CategoriesExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExportCompleted;

class ExportCategories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $columns;
    protected $filename;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $columns = [], string $filename = null, $userId = null)
    {
        $this->columns = $columns;
        $this->filename = $filename ?? 'categories_' . date('Y-m-d_His') . '.xlsx';
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate export file
        Excel::store(
            new CategoriesExport($this->columns),
            'exports/' . $this->filename,
            'public'
        );

        // Send notification if user ID is provided
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $url = Storage::url('exports/' . $this->filename);
                Notification::send($user, new ExportCompleted('Categories', $url));
            }
        }
    }
}