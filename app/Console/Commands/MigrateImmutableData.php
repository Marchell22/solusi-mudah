<?php
// Jalankan: php artisan make:command MigrateImmutableData

// app/Console/Commands/MigrateImmutableData.php
namespace App\Console\Commands;

use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Console\Command;

class MigrateImmutableData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-immutable-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing borrowing records to include immutable book data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $borrowings = Borrowing::all();
        $count = 0;
        $errors = 0;

        $this->info('Starting migration of immutable data for ' . $borrowings->count() . ' borrowing records...');
        $bar = $this->output->createProgressBar($borrowings->count());
        $bar->start();

        foreach ($borrowings as $borrowing) {
            // Jika data immutable belum diisi
            if (!$borrowing->book_title) {
                try {
                    $book = Book::find($borrowing->book_id);
                    if ($book) {
                        $borrowing->book_title = $book->title;
                        $borrowing->book_author = $book->author;
                        $borrowing->book_category_name = $book->category ? $book->category->name : null;
                        $borrowing->save();
                        $count++;
                    } else {
                        $this->error('Book not found for borrowing #' . $borrowing->id);
                        $errors++;
                    }
                } catch (\Exception $e) {
                    $this->error('Error updating borrowing #' . $borrowing->id . ': ' . $e->getMessage());
                    $errors++;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Migration completed. ' . $count . ' records updated successfully, ' . $errors . ' errors.');

        return Command::SUCCESS;
    }
}