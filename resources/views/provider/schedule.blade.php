@extends('layouts.app')

@section('title', 'My Schedule')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
@endpush

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">My Schedule</h1>
    <div id="calendar"></div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: @json($events),
            eventContent: function(arg) {
                let eventDiv = document.createElement('div');

                let startTime = arg.event.start.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let status = arg.event.extendedProps.status;

                let backgroundColor;
                switch (status) {
                    case 'confirmed':
                        backgroundColor = '#FFD700';
                        break;
                    case 'started':
                        backgroundColor = '#28a745';
                        break;
                    case 'completed':
                        backgroundColor = '#007bff';
                        break;
                    case 'canceled':
                        backgroundColor = '#dc3545';
                        break;
                    default:
                        backgroundColor = '#6c757d';
                }

                // Set the background color for the event
                eventDiv.style.backgroundColor = backgroundColor;
                eventDiv.style.padding = '10px';
                eventDiv.style.borderRadius = '5px';
                eventDiv.style.color = '#000';
                eventDiv.style.whiteSpace = 'wrap';

                eventDiv.innerHTML = `
                    <strong>${arg.event.title}</strong><br>
                    <span>Status: ${status}</span><br>
                    <span>Client: ${arg.event.extendedProps.clientName}</span><br>
                    <span>Location: ${arg.event.extendedProps.location}</span><br>
                    <span>Time: ${startTime}</span><br>
                    <span>Description: ${arg.event.extendedProps.description}</span>
                `;

                return {
                    domNodes: [eventDiv]
                };
            },
            eventClick: function(info) {
                // alert('Task: ' + info.event.title);
            }
        });
        calendar.render();
    });
</script>

@endpush