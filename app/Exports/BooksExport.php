<?php
// app/Exports/BooksExport.php
namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BooksExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize, WithEvents
{
    protected $columns;

    // Constructor with dynamic columns
    public function __construct(array $columns = [])
    {
        $this->columns = $columns;
        
        // If no columns are specified, use all columns
        if (empty($this->columns)) {
            $this->columns = [
                'id', 'title', 'author', 'description', 'category_id', 
                'stock', 'cover_path', 'is_available', 'published_at', 
                'created_at', 'updated_at'
            ];
        }
    }

    public function collection()
    {
        return Book::with('category')->get();
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
                case 'title':
                    $headings[] = 'Title';
                    break;
                case 'author':
                    $headings[] = 'Author';
                    break;
                case 'description':
                    $headings[] = 'Description';
                    break;
                case 'category_id':
                    $headings[] = 'Category';
                    break;
                case 'stock':
                    $headings[] = 'Stock';
                    break;
                case 'cover_path':
                    $headings[] = 'Cover Path';
                    break;
                case 'is_available':
                    $headings[] = 'Is Available';
                    break;
                case 'published_at':
                    $headings[] = 'Published Date';
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

    public function map($book): array
    {
        $data = [];
        
        // Map data based on selected columns
        foreach ($this->columns as $column) {
            switch ($column) {
                case 'id':
                    $data[] = $book->id;
                    break;
                case 'title':
                    $data[] = $book->title;
                    break;
                case 'author':
                    $data[] = $book->author;
                    break;
                case 'description':
                    $data[] = $book->description;
                    break;
                case 'category_id':
                    $data[] = $book->category ? $book->category->name : 'N/A';
                    break;
                case 'stock':
                    $data[] = $book->stock;
                    break;
                case 'cover_path':
                    $data[] = $book->cover_path;
                    break;
                case 'is_available':
                    $data[] = $book->is_available ? 'Yes' : 'No';
                    break;
                case 'published_at':
                    $data[] = $book->published_at ? $book->published_at->format('Y-m-d') : 'N/A';
                    break;
                case 'created_at':
                    $data[] = $book->created_at->format('Y-m-d H:i:s');
                    break;
                case 'updated_at':
                    $data[] = $book->updated_at->format('Y-m-d H:i:s');
                    break;
                default:
                    $data[] = $book->{$column} ?? 'N/A';
                    break;
            }
        }
        
        return $data;
    }

    public function columnFormats(): array
    {
        $formats = [];
        
        // Add formats for specific columns
        foreach ($this->columns as $index => $column) {
            if ($column === 'published_at' || $column === 'created_at' || $column === 'updated_at') {
                $formats[chr(65 + $index)] = NumberFormat::FORMAT_DATE_DDMMYYYY;
            }
            
            if ($column === 'stock') {
                $formats[chr(65 + $index)] = NumberFormat::FORMAT_NUMBER;
            }
        }
        
        return $formats;
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