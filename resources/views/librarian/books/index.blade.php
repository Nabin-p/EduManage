@extends('layouts.app') 

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            
            {{-- Page Title, Back Button, and Add Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('librarian.dashboard') }}" class="btn btn-secondary me-3">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <h1 class="h2 mb-0">Book Management</h1>
                </div>
                <a href="{{ route('librarian.books.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Book
                </a>
            </div>

            {{-- Success and Error Messages --}}
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
                                <th>#</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Category</th>
                                <th>Copies (Available/Total)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop through the books --}}
                            @forelse ($books as $book)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $book->title }}</td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->isbn }}</td>
                                    <td>
                                        @if($book->category)
                                            <span class="badge bg-info">{{ $book->category->name }}</span>
                                        @else
                                            <span class="text-muted">No category</span>
                                        @endif
                                    </td>
                                    <td>{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                                    <td>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('librarian.books.edit', $book->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        {{-- Delete Button (inside a form for security) --}}
                                        <form action="{{ route('librarian.books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                {{-- Message shown when there are no books --}}
                                <tr>
                                    <td colspan="7" class="text-center">No books found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination Links --}}
                    <div class="mt-3">
                        {{ $books->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection