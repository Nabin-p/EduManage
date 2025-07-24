@extends('layouts.app') {{-- Change this to your main layout file --}}

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h2">Issue a New Book</h1>
                <a href="{{ route('librarian.issues.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Issued List
                </a>
            </div>

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('librarian.issue.store') }}" method="POST">
                        @csrf

                        {{-- Book Selection (now a searchable input) --}}
                        <div class="form-group mb-3">
                            <label for="book_id">Search and Select Book (by Title or ISBN)</label>
                            {{-- We only need a single select tag now --}}
                            <select class="form-control @error('book_id') is-invalid @enderror" id="book_select" name="book_id" required></select>
                            @error('book_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Student Selection (now a searchable input) --}}
                        <div class="form-group mb-4">
                            <label for="student_id">Search and Select Student (by Name or Email)</label>
                            <select class="form-control @error('student_id') is-invalid @enderror" id="student_select" name="student_id" required></select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                           <button type="submit" class="btn btn-primary">Issue Book</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for Books
    $('#book_select').select2({
        placeholder: '-- Search for a book --',
        minimumInputLength: 2, // User must type at least 2 characters
        ajax: {
            url: '{{ route("librarian.api.books.search") }}',
            dataType: 'json',
            delay: 250, // Wait 250ms after typing before sending the request
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });

    // Initialize Select2 for Students
    $('#student_select').select2({
        placeholder: '-- Search for a student --',
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("librarian.api.students.search") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        }
    });
});
</script>
@endpush