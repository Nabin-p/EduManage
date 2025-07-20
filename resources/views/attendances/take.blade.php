@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row pt-2">
            <div class="col ps-4">
                <h1 class="display-6 mb-3"><i class="bi bi-person-check-fill"></i> Attendance Check</h1>
                <h4>
                    Class: {{ $school_class->class_name }}
                    @if($school_section)
                        , Section: {{ $school_section->section_name }}
                    @endif
                </h4>
                <p>Date: {{ now()->format('Y-m-d') }}</p>

                @include('session-messages')

                <div class="row mt-4">
                    <!-- Camera and Controls -->
                    <div class="col-lg-7 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-bold">Camera Scanner</div>
                            <div class="card-body text-center d-flex flex-column">
                                <p class="text-muted">Point the camera at a student or group and click "Scan & Mark".</p>
                                <div class="position-relative">
                                    {{-- The video element shows the user's local webcam feed --}}
                                    <video id="video" width="100%" height="auto" autoplay muted playsinline
                                        style="border-radius: 8px; background-color: #333;"></video>
                                    {{-- The canvas is hidden and used for taking snapshots --}}
                                    <canvas id="canvas" style="display:none;"></canvas>
                                </div>
                                <button id="scan-btn" class="btn btn-primary mt-3 btn-lg">
                                    <i class="bi bi-camera-fill"></i> Scan & Mark
                                </button>
                                <div id="scan-status" class="mt-2 fw-bold" style="min-height: 50px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Student List -->
                    <div class="col-lg-5">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-bold">Student List</div>
                            <div class="card-body">
                                <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST">
                                    @csrf
                                    {{-- Hidden fields for form submission --}}
                                    <input type="hidden" name="class_id" value="{{ $class_id }}">
                                    <input type="hidden" name="section_id" value="{{ $section_id }}">
                                    <input type="hidden" name="course_id" value="{{ $course_id }}">
                                    <input type="hidden" name="session_id" value="{{ $current_school_session_id }}">
                                    <input type="hidden" name="attendance_date" value="{{ now()->format('Y-m-d') }}">

                                    <div style="max-height: 450px; overflow-y: auto;">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            {{-- This is the new table body for resources/views/attendances/take.blade.php
                                            --}}

                                            <tbody>
                                                @forelse ($student_list as $student)
                                                    {{-- This hidden input is crucial. It ensures every student is included in
                                                    the submission. --}}
                                                    <input type="hidden" name="student_ids[]"
                                                        value="{{ $student->student_id }}">

                                                    <tr id="row-student-{{ $student->student_id }}">
                                                        <td>{{ $student->student->first_name }}
                                                            {{ $student->student->last_name }}</td>
                                                        <td class="text-center">

                                                            {{-- === THE NEW RADIO BUTTONS === --}}
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input student-status-radio"
                                                                    type="radio" name="status[{{ $student->student_id }}]"
                                                                    id="present-{{ $student->student_id }}" value="1"
                                                                    data-student-name="{{ $student->student->first_name }}">
                                                                <label class="form-check-label"
                                                                    for="present-{{ $student->student_id }}">Present</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input student-status-radio"
                                                                    type="radio" name="status[{{ $student->student_id }}]"
                                                                    id="absent-{{ $student->student_id }}" value="0" checked>
                                                                {{-- Default to Absent --}}
                                                                <label class="form-check-label"
                                                                    for="absent-{{ $student->student_id }}">Absent</label>
                                                            </div>
                                                            {{-- ============================== --}}

                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center">No students found in this class.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    @if(count($student_list) > 0 && $attendance_count < 1)
                                        <div class="mt-4 text-center">
                                            <button type="submit" class="btn btn-success btn-lg"><i
                                                    class="bi bi-check2-circle"></i> Submit Final Attendance</button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const videoElement = document.getElementById('video');
        const canvasElement = document.getElementById('canvas');
        const scanBtn = document.getElementById('scan-btn');
        const statusDiv = document.getElementById('scan-status');
        // The endpoint for single image recognition.
        const pythonApiUrl = "{{ $pythonApiUrl }}/capture-and-recognize";

        // 1. Start the camera when the page loads
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                videoElement.srcObject = stream;
            } catch (err) {
                statusDiv.innerHTML = `<div class="alert alert-danger">Error: Could not access camera. Please grant permission and refresh the page.</div>`;
                scanBtn.disabled = true;
                console.error("Camera access error:", err);
            }
        }

        // 2. Add event listener to the "Scan & Mark" button
        scanBtn.addEventListener('click', async () => {
            statusDiv.innerHTML = `<div class="alert alert-info">Scanning... Please wait.</div>`;
            scanBtn.disabled = true;

            const context = canvasElement.getContext('2d');
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            // Convert the canvas snapshot to a file (blob)
            canvasElement.toBlob(async (blob) => {
                if (!blob) {
                    statusDiv.innerHTML = `<div class="alert alert-danger">Failed to capture image.</div>`;
                    scanBtn.disabled = false;
                    return;
                }

                const formData = new FormData();
                formData.append('file', blob, 'capture.jpg');

                // 3. Send the single image to the Python API
                try {
                    const response = await fetch(pythonApiUrl, {
                        method: 'POST',
                        body: formData,
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(`API Error: ${response.status} - ${errorData.detail || 'Unknown error'}`);
                    }

                    const data = await response.json();
                    markStudentsAsPresent(data.recognized_faces);

                } catch (error) {
                    console.error('Error:', error);
                    statusDiv.innerHTML = `<div class="alert alert-danger">Recognition service is unavailable or failed. Please take attendance manually.</div>`;
                } finally {
                    scanBtn.disabled = false; // Re-enable the button
                }
            }, 'image/jpeg');
        });

        // 4. Function to update checkboxes based on API response

function markStudentsAsPresent(recognizedFaces) {
    if (!recognizedFaces || recognizedFaces.length === 0) {
        statusDiv.innerHTML = `<div class="alert alert-warning">No faces were detected in the scan.</div>`;
        return;
    }

    let markedCount = 0;
    
    recognizedFaces.forEach(face => {
        const name = face.name;
        if (name !== 'Unknown') {
            // === THE JAVASCRIPT FIX IS HERE ===
            // We now look for the "Present" radio button specifically.
            // Note: The data-student-name is now on the "Present" radio button.
            const presentRadioButton = document.querySelector(`input[data-student-name="${name}"][value="1"]`);
            
            // Check if the radio button exists and is not already checked
            if (presentRadioButton && !presentRadioButton.checked) {
                presentRadioButton.checked = true; // Mark as present
                markedCount++;
                
                // Provide visual feedback by highlighting the row
                const studentId = presentRadioButton.id.split('-')[1]; // Get ID from "present-3"
                const row = document.getElementById(`row-student-${studentId}`);
                if(row) {
                    row.style.backgroundColor = '#d1e7dd'; // A light green color
                }
            }
            // ===================================
        }
    });
    
    if (markedCount > 0) {
        statusDiv.innerHTML = `<div class="alert alert-success">Success! Marked ${markedCount} new student(s) as present.</div>`;
    } else {
        statusDiv.innerHTML = `<div class="alert alert-warning">Scan complete. All recognized students were already marked as present.</div>`;
    }
}

        // Start the camera when the page has finished loading
        document.addEventListener('DOMContentLoaded', startCamera);
    </script>
@endsection