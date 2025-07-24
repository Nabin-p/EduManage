@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-start">
            @include('layouts.left-menu')
            <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
                <div class="row pt-3">
                    <div class="col ps-4">
                        <!-- <h1 class="display-6 mb-3"><i class="ms-auto bi bi-grid"></i> {{ __('Dashboard') }}</h1> -->
                        <div class="row dashboard">
                            <div class="col">
                                <div class="card rounded-pill">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><i class="bi bi-person-lines-fill me-3"></i> Total
                                                    Students</div>
                                            </div>
                                            <span class="badge bg-dark rounded-pill">{{$studentCount}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card rounded-pill">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><i class="bi bi-person-lines-fill me-3"></i> Total
                                                    Teachers</div>
                                            </div>
                                            <span class="badge bg-dark rounded-pill">{{$teacherCount}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card rounded-pill">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold"><i class="bi bi-diagram-3 me-3"></i> Total Classes
                                                </div>
                                            </div>
                                            <span class="badge bg-dark rounded-pill">{{ $classCount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col">
                                <div class="card rounded-pill">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="ms-2 me-auto">
                                                <div class="fw-bold">Total Books</div>
                                            </div>
                                            <span class="badge bg-dark rounded-pill">800</span>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        @if($studentCount > 0)
                            <div class="mt-3 d-flex align-items-center">
                                <div class="col-3">
                                    <span class="ps-2 me-2">Students %</span>
                                    <span class="badge rounded-pill border" style="background-color: #0678c8;">Male</span>
                                    <span class="badge rounded-pill border" style="background-color: #49a4fe;">Female</span>
                                </div>
                                @php
                                    $maleStudentPercentage = round(($maleStudentsBySession / $studentCount), 2) * 100;
                                    $maleStudentPercentageStyle = "style='background-color: #0678c8; width: $maleStudentPercentage%'";

                                    $femaleStudentPercentage = round((($studentCount - $maleStudentsBySession) / $studentCount), 2) * 100;
                                    $femaleStudentPercentageStyle = "style='background-color: #49a4fe; width: $femaleStudentPercentage%'";
                                @endphp
                                <div class="col-9 progress">
                                    <div class="progress-bar progress-bar-striped" role="progressbar"
                                        {!!$maleStudentPercentageStyle!!} aria-valuenow="{{$maleStudentPercentage}}"
                                        aria-valuemin="0" aria-valuemax="100">{{$maleStudentPercentage}}%</div>
                                    <div class="progress-bar progress-bar-striped" role="progressbar"
                                        {!!$femaleStudentPercentageStyle!!} aria-valuenow="{{$femaleStudentPercentage}}"
                                        aria-valuemin="0" aria-valuemax="100">{{$femaleStudentPercentage}}%</div>
                                </div>
                            </div>
                        @endif
                        <div class="row align-items-md-stretch mt-4">
                            <div class="col">
                                <div class="p-3 text-white bg-dark rounded-3">
                                    <h3>Welcome to EduManage!</h3>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3 bg-white border rounded-3" style="height: 100%;">
                                    <h3>Manage school better</h3>
                                    {{-- <p class="text-end">with <i class="bi bi-lightning"></i> <a
                                            href="https://github.com/changeweb/Unifiedtransform" target="_blank"
                                            style="text-decoration: none;">Unifiedtransform</a> <i
                                            class="bi bi-lightning"></i>.</p> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-lg-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-transparent"><i class="bi bi-calendar-event me-2"></i> Events
                                    </div>
                                    <div class="card-body text-dark">
                                        @include('components.events.event-calendar', ['editable' => 'false', 'selectable' => 'false'])
                                        {{-- <div class="overflow-auto" style="height: 250px;">
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h5 class="mb-1">List group item heading</h5>
                                                        <small>3 days ago</small>
                                                    </div>
                                                    <p class="mb-1">Some placeholder content in a paragraph.</p>
                                                    <small>And some small print.</small>
                                                </a>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-transparent d-flex justify-content-between"><span><i
                                                class="bi bi-megaphone me-2"></i> Notices</span> {{ $notices->links() }}
                                    </div>
                                    <div class="card-body p-0 text-dark">
                                        <div>
                                            @isset($notices)
                                                <div class="accordion accordion-flush" id="noticeAccordion">
                                                    @foreach ($notices as $notice)
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="flush-heading{{$notice->id}}">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#flush-collapse{{$notice->id}}"
                                                                    aria-expanded={{($loop->first) ? "true" : "false"}}
                                                                    aria-controls="flush-collapse{{$notice->id}}">
                                                                    Published at: {{$notice->created_at}}
                                                                </button>
                                                            </h2>
                                                            <div id="flush-collapse{{$notice->id}}"
                                                                class="accordion-collapse collapse {{($loop->first) ? "show" : "hide"}}"
                                                                aria-labelledby="flush-heading{{$notice->id}}"
                                                                data-bs-parent="#noticeAccordion">
                                                                <div class="accordion-body overflow-auto">
                                                                    {!!Purify::clean($notice->notice)!!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                            @endisset
                                                @if(count($notices) < 1)
                                                    <div class="p-3">No notices</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- In resources/views/home.blade.php --}}

                @if(Auth::user()->role == 'student')
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Currently Issued Books</h5>
                            <a href="{{ route('student.library.my_books') }}" class="btn btn-sm btn-outline-primary">View Full
                                History</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- THIS IS THE FIX: Check if the variable exists first! --}}
                                        @if (isset($myIssuedBooks) && count($myIssuedBooks) > 0)
                                            @foreach ($myIssuedBooks as $issue)
                                                <tr
                                                    class="{{ \Carbon\Carbon::parse($issue->due_date)->isPast() ? 'table-danger' : '' }}">
                                                    <td>{{ $issue->book->title ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($issue->due_date)->format('d M, Y') }}</td>
                                                    <td>
                                                        @if (\Carbon\Carbon::parse($issue->due_date)->isPast())
                                                            <span class="badge bg-danger">Overdue</span>
                                                        @else
                                                            <span class="badge bg-primary">Issued</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            {{-- This message shows if the variable doesn't exist OR is empty --}}
                                            <tr>
                                                <td colspan="3" class="text-center">You have no books currently issued.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection