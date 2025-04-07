<?php
// app/Exports/CategoriesExport.php
namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CategoriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $columns;

    // Constructor with dynamic columns
    public function __construct(array $columns = [])
    {
        $this->columns = $columns;
        
        // If no columns are specified, use all columns
        if (empty($this->columns)) {
            $this->columns = [
                'id', 'name', 'description', 'is_active', 'books_count',
                'created_at', 'updated_at'
            ];
        }
    }

    public function collection()
    {
        return Category::withCount('books')->get();
    }

    public function headings(): array
    {
        $headings = [];
        
        // Build headings based on selected columns
        foreach ($this->columns as $column) {
            switch ($column) {
                case 'id':
                    $headings[] = 'ID';
                    break;
                case 'name':
                    $headings[] = 'Name';
                    break;
                case 'description':
                    $headings[] = 'Description';
                    break;
                case 'is_active':
                    $headings[] = 'Is Active';
                    break;
                case 'books_count':
                    $headings[] = 'Books Count';
                    break;
                case 'created_at':
                    $headings[] = 'Created At';
                    break;
                case 'updated_at':
                    $headings[] = 'Updated At';
                    break;
                default:
                    $headings[] = ucfirst(str_replace('_', ' ', $column));
                    break;
            }
        }
        
        return $headings;
    }

    public function map($category): array
    {
        $data = [];
        
        // Map data based on selected columns
        foreach ($this->columns as $column) {
            switch ($column) {
                case 'id':
                    $data[] = $category->id;
                    break;
                case 'name':
                    $data[] = $category->name;
                    break;
                case 'description':
                    $data[] = $category->description;
                    break;
                case 'is_active':
                    $data[] = $category->is_active ? 'Yes' : 'No';
                    break;
                case 'books_count':
                    $data[] = $category->books_count;
                    break;
                case 'created_at':
                    $data[] = $category->created_at->format('Y-m-d H:i:s');
                    break;
                case 'updated_at':
                    $data[] = $category->updated_at->format('Y-m-d H:i:s');
                    break;
                default:
                    $data[] = $category->{$column} ?? 'N/A';
                    break;
            }
        }
        
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:' . $event->sheet->getHighestColumn() . '1')
                    ->getAlignment()->setWrapText(true);
                
                // Set all columns to auto size
                foreach (range('A', $event->sheet->getHighestColumn()) as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}