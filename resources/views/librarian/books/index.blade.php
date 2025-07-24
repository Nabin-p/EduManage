@extends('layouts.app') 

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            
            {{-- Page Title and Add Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Book Management</h1>
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
                                    <td colspan="6" class="text-center">No books found.</td>
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