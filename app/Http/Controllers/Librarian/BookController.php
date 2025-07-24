<?php


namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    // Display a list of all books
    public function index()
    {
        $books = Book::latest()->paginate(10); // Get latest books, 10 per page
        return view('librarian.books.index', compact('books'));
    }

    // Show the form for creating a new book
    public function create()
    {
        return view('librarian.books.create');
    }

    // Store a newly created book in the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn', // Must be unique in the books table
            'total_copies' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        // When creating a book, available copies is the same as total copies
        $bookData = $request->all();
        $bookData['available_copies'] = $request->total_copies;

        Book::create($bookData);

        return redirect()->route('librarian.books.index')
                         ->with('success', 'Book added successfully.');
    }

    // Show the form for editing a specific book
    public function edit(Book $book)
    {
        return view('librarian.books.edit', compact('book'));
    }

    // Update the specified book in the database
    public function update(Request $request, Book $book)
{
    // 1. VALIDATE THE INCOMING DATA
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'isbn' => ['required', 'string', Rule::unique('books')->ignore($book->id)],
        'total_copies' => 'required|integer|min:0', // min:0 allows setting to zero if no books are issued
        'description' => 'nullable|string',
    ]);

    // 2. CALCULATE THE NUMBER OF ISSUED BOOKS
    // This value is constant and represents books physically outside the library.
    $issuedCopies = $book->total_copies - $book->available_copies;

    $newTotalCopies = (int) $validatedData['total_copies'];

    // 3. PREVENT IMPOSSIBLE UPDATES
    // You cannot set the total number of copies to be less than what is already checked out.
    if ($newTotalCopies < $issuedCopies) {
        return redirect()->back()
            ->withInput() // Send the user's input back to the form
            ->with('error', "Cannot set total copies to {$newTotalCopies}. There are currently {$issuedCopies} books issued.");
    }

    // 4. PERFORM THE UPDATE WITH AUTOMATIC ADJUSTMENT
    
    // Calculate the new number of available copies
    $newAvailableCopies = $newTotalCopies - $issuedCopies;

    // Manually assign the validated and calculated values to the book model
    $book->title = $validatedData['title'];
    $book->author = $validatedData['author'];
    $book->isbn = $validatedData['isbn'];
    $book->description = $validatedData['description'];
    $book->total_copies = $newTotalCopies;
    $book->available_copies = $newAvailableCopies; // Here is the automatic adjustment

    $book->save(); // Save the updated model to the database

    return redirect()->route('librarian.books.index')
                     ->with('success', 'Book updated successfully. Available copies have been adjusted.');
}

    // Remove the specified book from the database
    public function destroy(Book $book)
    {
        // Add a check to prevent deleting books that are currently issued
        if ($book->issues()->where('status', 'issued')->exists()) {
            return redirect()->route('librarian.books.index')
                             ->with('error', 'Cannot delete book. There are outstanding issues.');
        }

        $book->delete();

        return redirect()->route('librarian.books.index')
                         ->with('success', 'Book deleted successfully.');
    }
}