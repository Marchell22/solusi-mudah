<?php
// app/Exports/BorrowingsExport.php
namespace App\Exports;

use App\Models\Borrowing;
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

class BorrowingsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize, WithEvents
{
    protected $columns;

    // Constructor with dynamic columns
    public function __construct(array $columns = [])
    {
        $this->columns = $columns;
        
        // If no columns are specified, use all columns
        if (empty($this->columns)) {
            $this->columns = [
                'id', 'book_title', 'book_author', 'book_category', 
                'borrower_name', 'borrower_email', 'borrowed_at', 
                'due_date', 'returned_at', 'is_returned', 'status',
                'created_at'
            ];
        }
    }

    public function collection()
    {
        return Borrowing::with('book.category')->get();
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
                case 'book_id':
                    $headings[] = 'Book ID';
                    break;
                case 'book_title':
                    $headings[] = 'Book Title';
                    break;
                case 'book_author':
                    $headings[] = 'Book Author';
                    break;
                case 'book_category':
                    $headings[] = 'Book Category';
                    break;
                case 'borrower_name':
                    $headings[] = 'Borrower Name';
                    break;
                case 'borrower_email':
                    $headings[] = 'Borrower Email';
                    break;
                case 'borrowed_at':
                    $headings[] = 'Borrowed Date';
                    break;
                case 'due_date':
                    $headings[] = 'Due Date';
                    break;
                case 'returned_at':
                    $headings[] = 'Returned Date';
                    break;
                case 'is_returned':
                    $headings[] = 'Is Returned';
                    break;
                case 'status':
                    $headings[] = 'Status';
                    break;
                case 'created_at':
                    $headings[] = 'Created At';
                    break;
                default:
                    $headings[] = ucfirst(str_replace('_', ' ', $column));
                    break;
            }
        }
        
        return $headings;
    }

    public function map($borrowing): array
    {
        $data = [];
        
        // Map data based on selected columns
        foreach ($this->columns as $column) {
            switch ($column) {
                case 'id':
                    $data[] = $borrowing->id;
                    break;
                case 'book_id':
                    $data[] = $borrowing->book_id;
                    break;
                case 'book_title':
                    $data[] = $borrowing->book_title ?? $borrowing->book->title;
                    break;
                case 'book_author':
                    $data[] = $borrowing->book_author ?? $borrowing->book->author;
                    break;
                case 'book_category':
                    $data[] = $borrowing->book_category_name ?? ($borrowing->book->category->name ?? 'N/A');
                    break;
                case 'borrower_name':
                    $data[] = $borrowing->borrower_name;
                    break;
                case 'borrower_email':
                    $data[] = $borrowing->borrower_email;
                    break;
                case 'borrowed_at':
                    $data[] = $borrowing->borrowed_at->format('Y-m-d');
                    break;
                case 'due_date':
                    $data[] = $borrowing->due_date->format('Y-m-d');
                    break;
                case 'returned_at':
                    $data[] = $borrowing->returned_at ? $borrowing->returned_at->format('Y-m-d') : 'Not Returned';
                    break;
                case 'is_returned':
                    $data[] = $borrowing->is_returned ? 'Yes' : 'No';
                    break;
                case 'status':
                    if ($borrowing->is_returned) {
                        $data[] = 'Returned';
                    } elseif ($borrowing->due_date < now()) {
                        $data[] = 'Overdue';
                    } else {
                        $data[] = 'Active';
                    }
                    break;
                case 'created_at':
                    $data[] = $borrowing->created_at->format('Y-m-d H:i:s');
                    break;
                default:
                    $data[] = $borrowing->{$column} ?? 'N/A';
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
            if (in_array($column, ['borrowed_at', 'due_date', 'returned_at', 'created_at'])) {
                $formats[chr(65 + $index)] = NumberFormat::FORMAT_DATE_DDMMYYYY;
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