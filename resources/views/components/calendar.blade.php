<x-app-layout>

@section('content')
<div class="container">
    <h1 class="text-lg font-bold mb-4">Appointments Calendar</h1>
    <div id="calendar"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // Month view
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/appointments/calendar', // Fetch events from the route
            eventClick: function(info) {
                alert(`Appointment: ${info.event.title}`);
            },
        });

        calendar.render();
    });
</script>
</x-app-layout>
