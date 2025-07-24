<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

{{-- <div id='full_calendar_events'></div> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}



<!-- The entire content of resources/views/components/events/event-calendar.blade.php -->

<!-- The div where the calendar will be rendered -->
<div id="calendar"></div>

<!-- The script to initialize the calendar -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            
            // --- Basic Appearance ---
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },

            // --- Functionality (Simplified Syntax) ---
            editable: {{ ($editable ?? 'false') == 'true' ? 'true' : 'false' }},
            selectable: {{ ($selectable ?? 'false') == 'true' ? 'true' : 'false' }}
            // NO COMMA AFTER THIS LINE
        });

        calendar.render();
    });
</script>