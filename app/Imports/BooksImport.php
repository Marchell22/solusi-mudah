<?php
// app/Imports/BooksImport.php
namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class BooksImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading, WithCustomValueBinder
{
    use Importable;
    
    // Map kebutuhan field
    protected $fieldMap = [
        'title' => 'title',
        'author' => 'author',
        'description' => 'description',
        'category' => 'category_id',
        'stock' => 'stock',
        'is_available' => 'is_available',
        'published_date' => 'published_at',
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
            
            foreach ($fieldMap as $excelColumn => $dbField) {
                // Skip if column doesn't exist
                if (!isset($row[$excelColumn])) {
                    continue;
                }
                
                // Handle special columns
                if ($dbField === 'category_id') {
                    // Find or create category by name
                    $category = Category::firstOrCreate(
                        ['name' => $row[$excelColumn]],
                        ['description' => 'Imported from Excel', 'is_active' => true]
                    );
                    $data[$dbField] = $category->id;
                } elseif ($dbField === 'is_available') {
                    // Handle boolean values
                    $value = $row[$excelColumn];
                    if (is_string($value)) {
                        $data[$dbField] = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
                    } else {
                        $data[$dbField] = (bool) $value;
                    }
                } elseif ($dbField === 'published_at') {
                    // Handle date values
                    if (!empty($row[$excelColumn])) {
                        if ($row[$excelColumn] instanceof \DateTime) {
                            $data[$dbField] = $row[$excelColumn]->format('Y-m-d');
                        } else {
                            $data[$dbField] = date('Y-m-d', strtotime($row[$excelColumn]));
                        }
                    }
                } else {
                    // Default handling
                    $data[$dbField] = $row[$excelColumn];
                }
            }
            
            // Create the book if we have required fields
            if (isset($data['title']) && isset($data['author'])) {
                Book::create($data);
            }
        }
    }
    
    public function rules(): array
    {
        return [
            '*.title' => 'required|string|max:255',
            '*.author' => 'required|string|max:255',
            '*.category' => 'required|string|max:255',
            '*.stock' => 'nullable|integer|min:0',
            '*.is_available' => 'nullable',
            '*.published_date' => 'nullable|date',
        ];
    }
    
    public function chunkSize(): int
    {
        return 100; // Import 100 records at once
    }
}