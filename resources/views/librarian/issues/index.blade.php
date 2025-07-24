@extends('layouts.app') {{-- Change this to your main layout file --}}

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            
            {{-- Page Title and Action Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Issued Book Records</h1>
                <a href="{{ route('librarian.issue.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Issue a New Book
                </a>
            </div>

            {{-- Success and Error Messages for the Return action --}}
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Book Title</th>
                                <th>Student</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Return Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop through the issued book records --}}
                            @forelse ($issuedBooks as $issue)
                                {{-- Add a class for overdue books to highlight them --}}
                                <tr class="{{ ($issue->status == 'issued' && \Carbon\Carbon::parse($issue->due_date)->isPast()) ? 'table-danger' : '' }}">
                                    <td>{{ $issue->book->title ?? 'Book Not Found' }}</td>
                                    <td>{{ $issue->student->first_name ?? 'Student' }} {{ $issue->student->last_name ?? 'Not Found' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($issue->due_date)->format('d M, Y') }}</td>
                                    <td>
                                        @if ($issue->status == 'returned')
                                            <span class="badge bg-secondary">Returned</span>
                                        @elseif (\Carbon\Carbon::parse($issue->due_date)->isPast())
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-primary">Issued</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Only show return date if it exists --}}
                                        {{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{-- The "Return" button should only appear if the book is still issued --}}
                                        @if ($issue->status == 'issued')
                                            <form action="{{ route('librarian.issue.return', $issue->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this book as returned?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-info">Return Book</button>
                                            </form>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                {{-- Message shown when no books have been issued --}}
                                <tr>
                                    <td colspan="7" class="text-center">No issued book records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination Links --}}
                    <div class="mt-3">
                        {{ $issuedBooks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection