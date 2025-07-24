<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BookIssue; // Import the BookIssue model

class LibraryController extends Controller
{
    /**
     * Display a list of all books (current and returned) issued to the student.
     */
    public function myBooks()
    {
        $studentId = Auth::id();

        // Fetch all book issue records for this student, new to old
        $issuedBooks = BookIssue::with('book')
                                ->where('student_id', $studentId)
                                ->latest('issue_date')
                                ->paginate(10); // Use pagination for long histories
        
        // This controller needs a view file to show the data
        return view('students.library.my_books', compact('issuedBooks'));
    }
}