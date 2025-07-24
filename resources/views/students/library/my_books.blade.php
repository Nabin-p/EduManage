@extends('layouts.app') {{-- Or your main layout file --}}

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">My Full Library History</h1>
                <a href="{{ url('/home') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Book Title</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($issuedBooks as $issue)
                                <tr class="{{ ($issue->status == 'issued' && \Carbon\Carbon::parse($issue->due_date)->isPast()) ? 'table-danger' : '' }}">
                                    <td>{{ $issue->book->title ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($issue->due_date)->format('d M, Y') }}</td>
                                    <td>{{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M, Y') : 'Not Returned' }}</td>
                                    <td>
                                        @if ($issue->status == 'returned')
                                            <span class="badge bg-secondary">Returned</span>
                                        @elseif (\Carbon\Carbon::parse($issue->due_date)->isPast())
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-primary">Issued</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">You have not issued any books from the library.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $issuedBooks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection