@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h2 mb-4">Mark Attendance via Face Recognition</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Live Camera Feed</div>
                <div class="card-body text-center">
                    <video id="video" width="480" height="360" autoplay playsinline></video>
                    <canvas id="canvas" width="480" height="360" style="display: none;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <button id="capture-btn" class="btn btn-primary btn-lg">Capture & Mark Attendance</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Status & Results</div>
                <div id="status-log" class="card-body" style="height: 480px; overflow-y: auto;">
                    <p class="text-muted">Waiting to start...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('capture-btn');
    const statusLog = document.getElementById('status-log');
    const context = canvas.getContext('2d');
    const attendanceUrl = "{{ route('attendance.mark') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function logMessage(message, type = 'info') {
        const color = type === 'success' ? 'text-success' : (type === 'error' ? 'text-danger' : 'text-muted');
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('p');
        logEntry.className = color;
        logEntry.innerHTML = `<strong>[${timestamp}]</strong> ${message}`;
        statusLog.insertBefore(logEntry, statusLog.firstChild);
    }

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                logMessage('Camera started successfully.');
            }).catch(error => {
                logMessage('Error accessing camera: ' + error.message, 'error');
                captureBtn.disabled = true;
            });
    }

    captureBtn.addEventListener('click', function() {
        context.drawImage(video, 0, 0, 480, 360);
        const imageData = canvas.toDataURL('image/jpeg');

        logMessage('Image captured. Sending to server...');
        captureBtn.disabled = true;
        captureBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Recognizing...';

        fetch(attendanceUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ image: imageData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                logMessage(`SUCCESS: ${data.message} (${data.student_name})`, 'success');
            } else {
                logMessage(`ERROR: ${data.message}`, 'error');
                if(data.details) console.error('Details:', data.details);
            }
        })
        .catch(error => {
            logMessage('A network error occurred. See console for details.', 'error');
            console.error('Fetch Error:', error);
        })
        .finally(() => {
            captureBtn.disabled = false;
            captureBtn.textContent = 'Capture & Mark Attendance';
        });
    });
});
</script>
@endpush