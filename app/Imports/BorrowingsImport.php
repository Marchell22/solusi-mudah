<?php
// app/Imports/BorrowingsImport.php
namespace App\Imports;

use App\Models\Borrowing;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BorrowingsImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    use Importable;
    
    // Map kebutuhan field
    protected $fieldMap = [
        'book_title' => 'book_title',
        'book_author' => 'book_author',
        'book_category' => 'book_category_name',
        'borrower_name' => 'borrower_name',
        'borrower_email' => 'borrower_email',
        'borrowed_at' => 'borrowed_at',
        'due_date' => 'due_date',
        'returned_at' => 'returned_at',
        'is_returned' => 'is_returned',
    ];
    
    // Dynamic field map
    protected $customFieldMap;
    
    public function __construct(array $fieldMap = [])
    {
        $this->customFieldMap = $fieldMap;
    }
    
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // Merge default field map with custom field map
        $fieldMap = !empty($this->customFieldMap) ? $this->customFieldMap : $this->fieldMap;
        
        foreach ($rows as $row) {
            // Prepare data for insertion
            $data = [];
            $bookData = [];
            
            foreach ($fieldMap as $excelColumn => $dbField) {
                // Skip if column doesn't exist
                if (!isset($row[$excelColumn])) {
                    continue;
                }
                
                // Handle special columns
                if ($dbField === 'book_title' || $dbField === 'book_author' || $dbField === 'book_category_name') {
                    // Store for finding book later
                    $bookData[$dbField] = $row[$excelColumn];
                    // Also store in borrowing record
                    $data[$dbField] = $row[$excelColumn];
                } elseif ($dbField === 'borrowed_at' || $dbField === 'due_date' || $dbField === 'returned_at') {
                    // Handle date values
                    if (!empty($row[$excelColumn])) {
                        if ($row[$excelColumn] instanceof \DateTime) {
                            $data[$dbField] = $row[$excelColumn]->format('Y-m-d');
                        } else {
                            $data[$dbField] = date('Y-m-d', strtotime($row[$excelColumn]));
                        }
                    }
                } elseif ($dbField === 'is_returned') {
                    // Handle boolean values
                    $value = $row[$excelColumn];
                    if (is_string($value)) {
                        $data[$dbField] = in_array(strtolower($value), ['yes', 'true', '1', 'y', 'returned']);
                    } else {
                        $data[$dbField] = (bool) $value;
                    }
                } else {
                    // Default handling
                    $data[$dbField] = $row[$excelColumn];
                }
            }
            
            // Find or create the book if we have book details
            if (!empty($bookData['book_title']) && !empty($bookData['book_author'])) {
                // Look for existing book by title and author
                $book = Book::where('title', $bookData['book_title'])
                           ->where('author', $bookData['book_author'])
                           ->first();
                
                if (!$book) {
                    // Create category if needed
                    $category = null;
                    if (!empty($bookData['book_category_name'])) {
                        $category = Category::firstOrCreate(
                            ['name' => $bookData['book_category_name']],
                            ['description' => 'Imported from Excel', 'is_active' => true]
                        );
                    }
                    
                    // Create book
                    $book = Book::create([
                        'title' => $bookData['book_title'],
                        'author' => $bookData['book_author'],
                        'category_id' => $category ? $category->id : null,
                        'stock' => 1,
                        'is_available' => true,
                    ]);
                }
                
                // Set book_id for borrowing
                $data['book_id'] = $book->id;
            }
            
            // Create the borrowing if we have required fields
            if (isset($data['book_id']) && isset($data['borrower_name']) && isset($data['borrower_email'])
                && isset($data['borrowed_at']) && isset($data['due_date'])) {
                
                // Create borrowing record
                Borrowing::create($data);
                
                // Update book stock if borrowing is active
                if (!($data['is_returned'] ?? false)) {
                    $book = Book::find($data['book_id']);
                    $book->stock = max(0, $book->stock - 1);
                    if ($book->stock <= 0) {
                        $book->is_available = false;
                    }
                    $book->save();
                }
            }
        }
    }
    
    public function rules(): array
    {
        return [
            '*.book_title' => 'required|string|max:255',
            '*.book_author' => 'required|string|max:255',
            '*.borrower_name' => 'required|string|max:255',
            '*.borrower_email' => 'required|email|max:255',
            '*.borrowed_at' => 'required|date',
            '*.due_date' => 'required|date',
            '*.returned_at' => 'nullable|date',
            '*.is_returned' => 'nullable',
        ];
    }
    
    public function chunkSize(): int
    {
        return 100; // Import 100 records at once
    }
}