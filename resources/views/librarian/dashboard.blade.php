@extends('layouts.app') {{-- Change this to your main layout file --}}

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-4">Librarian Dashboard</h1>

    {{-- Display success messages --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Dashboard Stats Cards --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Books</h5>
                            <p class="h2">{{ $totalBooks }}</p>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Issued Books</h5>
                            <p class="h2">{{ $issuedBooks }}</p>
                        </div>
                        <i class="fas fa-hand-holding-heart fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Students</h5>
                            <p class="h2">{{ $totalStudents }}</p>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Links --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Quick Actions
                </div>
                <div class="card-body">
                    <a href="{{ route('librarian.books.index') }}" class="btn btn-lg btn-outline-primary m-2">Manage Books</a>
                    <a href="{{ route('librarian.issue.create') }}" class="btn btn-lg btn-outline-success m-2">Issue New Book</a>
                    <a href="{{ route('librarian.issues.index') }}" class="btn btn-lg btn-outline-info m-2">View Issued Books</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection