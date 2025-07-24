<?php

// app/Http/Controllers/Librarian/BookIssueController.php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookIssueController extends Controller
{
    // Show a list of all issued books
    public function index()
    {
        $issuedBooks = BookIssue::with(['book', 'student'])
                                ->latest()
                                ->paginate(15);
        
        return view('librarian.issues.index', compact('issuedBooks'));
    }

    // Show the form to issue a book
    public function create()
    {
        // Get books that have at least one copy available
        $books = Book::where('available_copies', '>', 0)->get();
        // Get all users who are students
        $students = User::where('role', 'student')->get();
        
        return view('librarian.issues.create', compact('books', 'students'));
    }

    // Store the new book issue record
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:users,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $book = Book::where('id', $request->book_id)->lockForUpdate()->first();

                if ($book->available_copies <= 0) {
                    throw new \Exception('No copies of this book are available.');
                }

                BookIssue::create([
                    'book_id' => $request->book_id,
                    'student_id' => $request->student_id,
                    'issue_date' => Carbon::now(),
                    'due_date' => Carbon::now()->addDays(14), // e.g., Due in 14 days
                    'status' => 'issued',
                ]);

                // Decrement the available copies count
                $book->decrement('available_copies');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
        
        return redirect()->route('librarian.dashboard')->with('success', 'Book issued successfully.');
    }

    // Mark a book as returned
    public function returnBook(BookIssue $bookIssue)
    {
        if ($bookIssue->status === 'returned') {
            return redirect()->back()->with('error', 'This book has already been returned.');
        }
        
        try {
            DB::transaction(function () use ($bookIssue) {
                // Find the associated book and lock it for the update
                $book = Book::where('id', $bookIssue->book_id)->lockForUpdate()->first();

                $bookIssue->update([
                    'status' => 'returned',
                    'return_date' => Carbon::now(),
                ]);

                // Increment the available copies count
                $book->increment('available_copies');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Could not process the return. Please try again.');
        }

        return redirect()->back()->with('success', 'Book marked as returned.');
    }

    public function searchBooks(Request $request)
    {
        $searchTerm = $request->input('q');

        $books = Book::where('available_copies', '>', 0)
                     ->where(function ($query) use ($searchTerm) {
                         $query->where('title', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('isbn', 'LIKE', "%{$searchTerm}%");
                     })
                     ->limit(20) // Limit results for performance
                     ->get();

        $results = $books->map(function ($book) {
            return [
                'id' => $book->id,
                'text' => "{$book->title} (ISBN: {$book->isbn})",
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Search for students via AJAX for Select2.
     */
    public function searchStudents(Request $request)
    {
        $searchTerm = $request->input('q');

        $students = User::where('role', 'student')
                        ->where(function ($query) use ($searchTerm) {
                            $query->where('first_name', 'LIKE', "%{$searchTerm}%")
                                  ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
                                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                        })
                        ->limit(20) // Limit results for performance
                        ->get();

        $results = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'text' => "{$student->first_name} {$student->last_name} ({$student->email})",
            ];
        });

        return response()->json(['results' => $results]);
    }
}
