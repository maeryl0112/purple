<div {{ $attributes->class(['m-3']) }}>
    @if($daySchedule->isEmpty())
        <p>No appointments for today.</p>
    @else
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Time Slot</th>
                    <th class="px-4 py-2 text-left">Service</th>
                    <th class="px-4 py-2 text-left">Customer</th>
                    <th class="px-4 py-2 text-left">Employee</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daySchedule as $appointment)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
                        <td class="px-4 py-2">{{ $appointment->service->name }}</td>
                        <td class="px-4 py-2">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</td>
                        <td class="px-4 py-2">{{ $appointment->employee->first_name }} {{ $appointment->employee->last_name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
