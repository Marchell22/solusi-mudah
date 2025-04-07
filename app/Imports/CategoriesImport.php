<?php
// app/Imports/CategoriesImport.php
namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CategoriesImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    use Importable;
    
    // Map kebutuhan field
    protected $fieldMap = [
        'name' => 'name',
        'description' => 'description',
        'is_active' => 'is_active',
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
                if ($dbField === 'is_active') {
                    // Handle boolean values
                    $value = $row[$excelColumn];
                    if (is_string($value)) {
                        $data[$dbField] = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
                    } else {
                        $data[$dbField] = (bool) $value;
                    }
                } else {
                    // Default handling
                    $data[$dbField] = $row[$excelColumn];
                }
            }
            
            // Create or update the category if we have required fields
            if (isset($data['name'])) {
                // Check if category already exists (by name)
                $category = Category::where('name', $data['name'])->first();
                
                if ($category) {
                    // Update existing category
                    $category->update($data);
                } else {
                    // Create new category
                    Category::create($data);
                }
            }
        }
    }
    
    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.description' => 'nullable|string',
            '*.is_active' => 'nullable',
        ];
    }
    
    public function chunkSize(): int
    {
        return 100; // Import 100 records at once
    }
}