<?php
// app/Http/Controllers/ExcelController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ExportBooks;
use App\Jobs\ImportBooks;
use App\Jobs\ExportCategories;
use App\Jobs\ImportCategories;
use App\Jobs\ExportBorrowings;
use App\Jobs\ImportBorrowings;
use App\Models\Book;
use App\Models\Category;
use App\Models\Borrowing;

class ExcelController extends Controller
{
    /**
     * Display export options form
     */
    public function showExportForm(string $type)
    {
        $fields = $this->getExportFields($type);
        
        return view('excel.export', compact('type', 'fields'));
    }
    
    /**
     * Process export request
     */
    public function export(Request $request, string $type)
    {
        // Validate request
        $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'required|string',
        ]);
        
        // Get selected columns
        $columns = $request->input('columns');
        
        // Generate filename
        $filename = $type . '_export_' . date('Y-m-d_His') . '.xlsx';
        
        // Get user ID for notification
        $userId = auth()->id();
        
        // Dispatch appropriate job based on type
        switch ($type) {
            case 'books':
                ExportBooks::dispatch($columns, $filename, $userId);
                break;
            case 'categories':
                ExportCategories::dispatch($columns, $filename, $userId);
                break;
            case 'borrowings':
                ExportBorrowings::dispatch($columns, $filename, $userId);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid export type.');
        }
        
        return redirect()->back()->with('success', 'Export job has been queued. You will be notified when it is complete.');
    }
    
    /**
     * Display import form
     */
    public function showImportForm(string $type)
    {
        $fieldMaps = $this->getImportFieldMaps($type);
        
        return view('excel.import', compact('type', 'fieldMaps'));
    }
    
    /**
     * Process import request
     */
    public function import(Request $request, string $type)
    {
        // Validate request
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'field_map' => 'required|array',
            'field_map.*' => 'nullable|string',
        ]);
        
        // Build field map (remove null/empty values)
        $fieldMap = array_filter($request->input('field_map'));
        
        // Get the uploaded file
        $file = $request->file('file');
        
        // Store file
        $path = $file->store('imports', 'public');
        $fullPath = storage_path('app/public/' . $path);
        
        // Get user ID for notification
        $userId = auth()->id();
        
        // Dispatch appropriate job based on type
        switch ($type) {
            case 'books':
                ImportBooks::dispatch($path, $fieldMap, $userId);
                break;
            case 'categories':
                ImportCategories::dispatch($path, $fieldMap, $userId);
                break;
            case 'borrowings':
                ImportBorrowings::dispatch($path, $fieldMap, $userId);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid import type.');
        }
        
        return redirect()->back()->with('success', 'Import job has been queued. You will be notified when it is complete.');
    }
    
    /**
     * Get export template
     */
    public function downloadTemplate(string $type)
    {
        // Define template path based on type
        $templatePath = 'templates/' . $type . '_template.xlsx';
        
        // Check if template exists
        if (!Storage::disk('public')->exists($templatePath)) {
            // Generate template
            $this->generateTemplate($type);
        }
        
        // Return file download
        return Storage::disk('public')->download($templatePath, $type . '_import_template.xlsx');
    }
    
    /**
     * Get exportable fields for each model type
     */
    private function getExportFields(string $type): array
    {
        switch ($type) {
            case 'books':
                return [
                    'id' => 'ID',
                    'title' => 'Title',
                    'author' => 'Author',
                    'description' => 'Description',
                    'category_id' => 'Category',
                    'stock' => 'Stock',
                    'cover_path' => 'Cover Path',
                    'is_available' => 'Is Available',
                    'published_at' => 'Published Date',
                    'created_at' => 'Created At',
                    'updated_at' => 'Updated At',
                ];
            case 'categories':
                return [
                    'id' => 'ID',
                    'name' => 'Name',
                    'description' => 'Description',
                    'is_active' => 'Is Active',
                    'books_count' => 'Books Count',
                    'created_at' => 'Created At',
                    'updated_at' => 'Updated At',
                ];
            case 'borrowings':
                return [
                    'id' => 'ID',
                    'book_title' => 'Book Title',
                    'book_author' => 'Book Author',
                    'book_category' => 'Book Category',
                    'borrower_name' => 'Borrower Name',
                    'borrower_email' => 'Borrower Email',
                    'borrowed_at' => 'Borrowed Date',
                    'due_date' => 'Due Date',
                    'returned_at' => 'Returned Date',
                    'is_returned' => 'Is Returned',
                    'status' => 'Status',
                    'created_at' => 'Created At',
                ];
            default:
                return [];
        }
    }
    
    /**
     * Get importable field maps for each model type
     */
    private function getImportFieldMaps(string $type): array
    {
        switch ($type) {
            case 'books':
                return [
                    'title' => 'Title',
                    'author' => 'Author',
                    'description' => 'Description',
                    'category' => 'Category',
                    'stock' => 'Stock',
                    'is_available' => 'Is Available',
                    'published_date' => 'Published Date',
                ];
            case 'categories':
                return [
                    'name' => 'Name',
                    'description' => 'Description',
                    'is_active' => 'Is Active',
                ];
            case 'borrowings':
                return [
                    'book_title' => 'Book Title',
                    'book_author' => 'Book Author',
                    'book_category' => 'Book Category',
                    'borrower_name' => 'Borrower Name',
                    'borrower_email' => 'Borrower Email',
                    'borrowed_at' => 'Borrowed Date',
                    'due_date' => 'Due Date',
                    'returned_at' => 'Returned Date',
                    'is_returned' => 'Is Returned',
                ];
            default:
                return [];
        }
    }
    
    /**
     * Generate import template
     */
    private function generateTemplate(string $type): void
    {
        // Define template directory
        $templateDir = storage_path('app/public/templates');
        if (!file_exists($templateDir)) {
            mkdir($templateDir, 0755, true);
        }
        
        // Get fields for template
        $fields = array_values($this->getImportFieldMaps($type));
        
        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Add headers
        foreach ($fields as $index => $field) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
            $sheet->setCellValue($column . '1', $field);
            
            // Set column width
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ];
        
        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($fields) - 1) . '1')
            ->applyFromArray($headerStyle);
        
        // Create writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($templateDir . '/' . $type . '_template.xlsx');
    }
}