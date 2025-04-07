<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        // Implementasi searching/filtering dan sorting
        $query = Book::with('category');
        
        // Searching/filtering
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }
        
        // Filter by availability
        if ($request->has('availability')) {
            $isAvailable = $request->input('availability') === 'available';
            $query->where('is_available', $isAvailable);
        }
        
        // Sorting
        $sortBy = $request->input('sort_by', 'title');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $books = $query->paginate(10);
        $categories = Category::all(); // For the filter dropdown
        
        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('books.create', compact('categories'));
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|file|mimes:pdf|min:100|max:500', // PDF only, 100KB - 500KB
            'is_available' => 'boolean',
            'additional_info' => 'nullable|json',
            'published_at' => 'nullable|date',
        ]);

        // Handle file upload
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $path = $file->store('covers', 'public');
            $validated['cover_path'] = $path;
        }

        Book::create($validated);

        return redirect()->route('books.index')
                        ->with('success', 'Book created successfully.');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        // Load related data
        $book->load('category', 'borrowings');
        
        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book)
    {
        $categories = Category::where('is_active', true)->get();
        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|file|mimes:pdf|min:100|max:500', // PDF only, 100KB - 500KB
            'is_available' => 'boolean',
            'additional_info' => 'nullable|json',
            'published_at' => 'nullable|date',
        ]);

        // Handle file upload
        if ($request->hasFile('cover')) {
            // Delete old file if exists
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            
            $file = $request->file('cover');
            $path = $file->store('covers', 'public');
            $validated['cover_path'] = $path;
        }

        $book->update($validated);

        return redirect()->route('books.index')
                        ->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
                        ->with('success', 'Book deleted successfully.');
    }
}

