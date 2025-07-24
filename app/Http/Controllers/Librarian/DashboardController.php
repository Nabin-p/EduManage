<?php

// app/Http/Controllers/Librarian/DashboardController.php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Example stats you might want to show on the dashboard
        $totalBooks = Book::count();
        $totalStudents = User::where('role', 'student')->count();
        $issuedBooks = BookIssue::where('status', 'issued')->count();
        
        return view('librarian.dashboard', compact('totalBooks', 'totalStudents', 'issuedBooks'));
    }
}