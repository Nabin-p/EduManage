@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="display-6 mb-3"><i class="bi bi-person-check-fill"></i> Face Recognition Attendance</h1>
        <h4>{{ $school_class->class_name }} @if($school_section) - {{ $school_section->section_name }} @endif</h4>
        <p>Date: {{ now()->format('Y-m-d') }}</p>
        @include('session-messages')

        <div class="row mt-4">
            <!-- Live Video Stream -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Live Recognition Stream</span>
                        <span id="ws-status" class="badge bg-secondary">Connecting...</span>
                    </div>
                    <div class="card-body text-center bg-dark rounded-bottom">
                        <img id="video-stream" src="{{ $pythonApiUrl }}/video-feed" width="100%" height="auto"
                            class="rounded" alt="Live video feed is offline." />
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                        <span>Student List</span>
                        <button id="submit-attendance" class="btn btn-success btn-sm">Submit Attendance</button>
                    </div>
                    <div class="card-body">
                        <form id="attendance-form">
                            @csrf
                            <input type="hidden" name="class_id" value="{{ $class_id }}">
                            <input type="hidden" name="section_id" value="{{ $section_id }}">
                            <input type="hidden" name="course_id" value="{{ $course_id }}">

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($student_list as $enrollment)
                                            {{-- The ID now correctly uses the student's ID from the relationship --}}
                                            <tr id="row-student-{{ $enrollment->student_id }}">
                                                <td>
                                                    {{-- The name now correctly uses the relationship --}}
                                                    {{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}
                                                    <small class="text-muted d-block">ID: {{ $enrollment->student_id }}</small>
                                                </td>
                                                <td class="text-center align-middle">
                                                    {{-- The badge and input IDs are now correct --}}
                                                    <span id="status-badge-{{ $enrollment->student_id }}"
                                                        class="badge bg-danger">Absent</span>

                                                    {{-- The name of this input is now correct --}}
                                                    <input type="hidden" name="status[{{ $enrollment->student_id }}]"
                                                        id="status-input-{{ $enrollment->student_id }}" value="0">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">No students found in this section.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
document.addEventListener('DOMContentLoaded', function() {
    // --- Element Selectors ---
    const wsStatus = document.getElementById('ws-status');
    const videoStream = document.getElementById('video-stream');
    
    // --- API and WebSocket URLs ---
    const websocketUrl = "{{ str_replace(['http://', 'https://'], ['ws://', 'wss://'], $pythonApiUrl) }}/ws/recognized-faces";
    
    console.log("DEBUG: Script loaded. Starting WebSocket connection to:", websocketUrl);

    // --- WebSocket Connection Logic ---
    function connectWebSocket() {
        const socket = new WebSocket(websocketUrl);

        socket.onopen = function(event) {
            wsStatus.textContent = 'Connected';
            wsStatus.className = 'badge bg-success';
            console.log("DEBUG: WebSocket connection ESTABLISHED.");
        };

        socket.onmessage = function(event) {
            // --- CHECKPOINT 1: Did we receive a message? ---
            console.log("DEBUG: WebSocket message RECEIVED:", event.data);
            
            const data = JSON.parse(event.data);
            if (data.recognized_ids) {
                // --- CHECKPOINT 2: Is the message valid and contains IDs? ---
                console.log("DEBUG: Recognized IDs found:", data.recognized_ids);
                markStudentsAsPresent(data.recognized_ids);
            } else {
                console.log("DEBUG: Message received, but no 'recognized_ids' key found.");
            }
        };

        socket.onclose = function(event) {
            wsStatus.textContent = 'Disconnected';
            wsStatus.className = 'badge bg-danger';
            videoStream.style.display = 'none';
            console.warn("DEBUG: WebSocket DISCONNECTED. Attempting to reconnect in 3s...");
            setTimeout(connectWebSocket, 3000);
        };

        socket.onerror = function(error) {
            wsStatus.textContent = 'Error';
            wsStatus.className = 'badge bg-danger';
            console.error("DEBUG: WebSocket ERROR occurred:", error);
        };
    }

    // --- The Core Function to Update the UI ---
    function markStudentsAsPresent(recognizedIds) {
        recognizedIds.forEach(studentId => {
            // --- CHECKPOINT 3: Are we processing each ID? ---
            console.log(`DEBUG: Processing studentId: '${studentId}' (Type: ${typeof studentId})`);

            if (studentId && studentId !== 'unknown') {
                
                // --- CHECKPOINT 4: Are we finding the HTML elements? ---
                const statusInput = document.getElementById(`status-input-${studentId}`);
                const statusBadge = document.getElementById(`status-badge-${studentId}`);
                
                if (statusInput) {
                    console.log(`DEBUG: Found statusInput for student ${studentId}. Current value: '${statusInput.value}'`);
                    
                    if (statusInput.value === '0') {
                        // --- CHECKPOINT 5: Is the student absent and are we updating them? ---
                        console.log(`%cDEBUG: SUCCESS! Marking student ${studentId} as PRESENT.`, 'color: green; font-weight: bold;');
                        
                        statusInput.value = '1'; // Update hidden input
                        
                        if (statusBadge) {
                            statusBadge.classList.remove('bg-danger');
                            statusBadge.classList.add('bg-success');
                            statusBadge.textContent = 'Present';
                        }
                    } else {
                        console.log(`DEBUG: Student ${studentId} is already marked present. Skipping.`);
                    }
                } else {
                    // --- This is the most likely error point ---
                    console.error(`DEBUG: FAILED to find element with ID: 'status-input-${studentId}'. Check your Blade view's loop.`);
                }
            }
        });
    }



    const submitBtn = document.getElementById('submit-attendance');
const attendanceForm = document.getElementById('attendance-form');
const submitUrl = "{{ route('attendance.submit') }}"; // We will create this route

// 2. Add a click event listener to the button
submitBtn.addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default button behavior

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    // 3. Get all the data from the form
    const formData = new FormData(attendanceForm);

    // 4. Send the data to the Laravel backend
    fetch(submitUrl, {
        method: 'POST',
        body: formData,
        headers: {
            // FormData sends its own headers, but we need the CSRF token
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // 5. Handle the response from the server
        if (data.status === 'success') {
            alert('Attendance submitted successfully!');
            // Optional: Redirect the user after success
            window.location.href = "{{ route('home') }}"; // Redirect to dashboard
        } else {
            alert('Error submitting attendance: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Submission Error:', error);
        alert('A network or server error occurred during submission.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Attendance';
    });
});
    
    // Start the WebSocket connection
    connectWebSocket();
});
</script>
@endpush