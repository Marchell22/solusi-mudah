<?php
// app/Http/Controllers/BorrowingController.php
namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the borrowings.
     */
    public function index(Request $request)
    {
        // Implementasi searching/filtering dan sorting
        $query = Borrowing::with('book');
        
        // Searching/filtering
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('borrower_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('borrower_email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('book_title', 'LIKE', "%{$searchTerm}%") // Pencarian pada field immutable
                  ->orWhere('book_author', 'LIKE', "%{$searchTerm}%"); // Pencarian pada field immutable
        }
        
        // Filter by status
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_returned', false);
            } elseif ($status === 'returned') {
                $query->where('is_returned', true);
            } elseif ($status === 'overdue') {
                $query->where('is_returned', false)
                      ->where('due_date', '<', now());
            }
        }
        
        // Filter by book
        if ($request->has('book')) {
            $query->where('book_id', $request->input('book'));
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'borrowed_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $borrowings = $query->paginate(10);
        $books = Book::where('is_available', true)->get(); // For the filter dropdown
        
        return view('borrowings.index', compact('borrowings', 'books'));
    }

    /**
     * Show the form for creating a new borrowing.
     */
    public function create()
    {
        $books = Book::where('is_available', true)
                     ->where('stock', '>', 0)
                     ->get();
        return view('borrowings.create', compact('books'));
    }

    /**
     * Store a newly created borrowing in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'borrower_name' => 'required|max:255',
            'borrower_email' => 'required|email|max:255',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date|after:borrowed_at',
            'notes' => 'nullable|json',
        ]);

        // Set default values
        $validated['is_returned'] = false;

        // Create borrowing record
        // Immutable data akan disimpan otomatis oleh model observer/boot method
        $borrowing = Borrowing::create($validated);

        // Update book stock
        $book = Book::find($validated['book_id']);
        $book->stock = $book->stock - 1;
        if ($book->stock <= 0) {
            $book->is_available = false;
        }
        $book->save();

        return redirect()->route('borrowings.index')
                        ->with('success', 'Borrowing created successfully.');
    }

    /**
     * Display the specified borrowing.
     */
    public function show(Borrowing $borrowing)
    {
        // Load related book
        $borrowing->load('book.category');
        
        // Jika data immutable belum ada (migrasi baru), isi dengan data yang ada saat ini
        if (!$borrowing->book_title && $borrowing->book) {
            $borrowing->book_title = $borrowing->book->title;
            $borrowing->book_author = $borrowing->book->author;
            $borrowing->book_category_name = $borrowing->book->category->name ?? 'N/A';
            $borrowing->save();
        }
        
        return view('borrowings.show', compact('borrowing'));
    }

    /**
     * Show the form for editing the specified borrowing.
     */
    public function edit(Borrowing $borrowing)
    {
        $books = Book::all();
        return view('borrowings.edit', compact('borrowing', 'books'));
    }

    /**
     * Update the specified borrowing in storage.
     */
    public function update(Request $request, Borrowing $borrowing)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'borrower_name' => 'required|max:255',
            'borrower_email' => 'required|email|max:255',
            'borrowed_at' => 'required|date',
            'due_date' => 'required|date|after:borrowed_at',
            'returned_at' => 'nullable|date',
            'is_returned' => 'boolean',
            'notes' => 'nullable|json',
        ]);

        // Handle book return logic
        $oldBookId = $borrowing->book_id;
        $newBookId = $validated['book_id'];
        $wasReturned = $borrowing->is_returned;
        $isReturned = $validated['is_returned'] ?? false;

        // Jika buku berubah, perbarui data immutable
        if ($oldBookId != $newBookId) {
            $book = Book::find($newBookId);
            if ($book) {
                $validated['book_title'] = $book->title;
                $validated['book_author'] = $book->author;
                $validated['book_category_name'] = $book->category ? $book->category->name : null;
            }
        }

        // Update borrowing record
        $borrowing->update($validated);

        // Update book stock if return status changed
        if (!$wasReturned && $isReturned) {
            // Book was returned
            $book = Book::find($oldBookId);
            $book->stock = $book->stock + 1;
            $book->is_available = true;
            $book->save();
        } elseif ($wasReturned && !$isReturned) {
            // Book was un-returned
            $book = Book::find($newBookId);
            $book->stock = $book->stock - 1;
            if ($book->stock <= 0) {
                $book->is_available = false;
            }
            $book->save();
        } elseif ($oldBookId !== $newBookId) {
            // Book was changed
            $oldBook = Book::find($oldBookId);
            $newBook = Book::find($newBookId);
            
            if (!$wasReturned) {
                // If the borrowing was active, increment old book, decrement new book
                $oldBook->stock = $oldBook->stock + 1;
                $oldBook->is_available = true;
                $oldBook->save();
                
                $newBook->stock = $newBook->stock - 1;
                if ($newBook->stock <= 0) {
                    $newBook->is_available = false;
                }
                $newBook->save();
            }
        }

        return redirect()->route('borrowings.index')
                        ->with('success', 'Borrowing updated successfully.');
    }

    /**
     * Remove the specified borrowing from storage.
     */
    public function destroy(Borrowing $borrowing)
    {
        // If the book was not returned, update stock when deleting record
        if (!$borrowing->is_returned) {
            $book = Book::find($borrowing->book_id);
            $book->stock = $book->stock + 1;
            $book->is_available = true;
            $book->save();
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')
                        ->with('success', 'Borrowing deleted successfully.');
    }
}