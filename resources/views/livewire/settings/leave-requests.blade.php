<section class="w-full">
    @include('partials.settings-heading')
    @include('partials.editLeaveRequest')

    <x-settings.layout :heading="__('Leave request')" :subheading="__('Fill out the required fields')"
        :user="Auth::user()->name">

        <form wire:submit="leaveRequest" class="my-6 w-full space-y-6">
            <flux:input wire:model.live="dateFrom" :label="__('Date from:')" type="date" autofocus
                autocomplete="Date from" class="!w-75" />
            <flux:input wire:model.live="dateTo" :label="__('Date to:')" type="date" autocomplete="Date to"
                class="!w-75" />

            @if (!is_null($this->daysDifference))

            @php
            $noOfDays = $this->daysDifference;
            $requestYear = \Carbon\Carbon::parse($this->dateFrom)->year;
            $currentYear = now()->year;
            $leaveBalance = $this->leaveBalance();

            $remainingPrevious = $leaveBalance['Previous']['remaining'] ?? 0;
            $remainingCurrent = $leaveBalance['Current']['remaining'] ?? 0;
            $totalRemaining = $remainingPrevious + $remainingCurrent;
            @endphp

            <div class="flex items-center gap-2">
                <span>No. of days requested: {{ $noOfDays }}</span>
            </div>

            {{-- Insufficient balance warning --}}
            @if ($requestYear == $currentYear && $noOfDays > $totalRemaining)
            <flux:callout icon="x-circle" variant="danger" heading="Insufficient leave balance" class="mt-2">
                You only have <strong>{{ $totalRemaining }}</strong> day(s) remaining.
            </flux:callout>
            @endif

            @endif

            <flux:textarea wire:model='reason' label='Reason:' rows='2' />
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button icon='plus-circle' variant="primary" type="submit" class="w-full">{{ __('Send request')
                        }}
                    </flux:button>
                </div>
            </div>
        </form>
        <x-action-message class="me-3" on="leave-requested">
            <flux:callout variant='success'
                heading="{{ __('Leave request successfully registered, wait for approval from admin') }}" />
        </x-action-message>

    </x-settings.layout>
    <div>
        <div class="text-lg mb-3 text-center">Leave history of {{ Auth::user()->name }}</div>
        <table class="table-default">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Normal Days</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Requested</th>
                    <th>Reject remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaves as $leave )
                <tr>
                    @php
                    $f = strtotime($leave->dateFrom);
                    $t = strtotime( $leave->dateTo)
                    @endphp
                    <td>{{ $leave->id }}</td>

                    <td>{{ $leave->dateFrom }}</td>
                    <td>{{ $leave->dateTo }}</td>
                    {{-- <td>{{ round(($t-$f) / (60 * 60 * 24)) + 1}}</td> --}}
                    <td>{{ $leave->noOfDays }}</td>
                    <td>
                        @php
                        $statusClasses = [
                        'pending' => 'bg-orange-100 text-orange-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'approved' => 'bg-green-100 text-green-800',
                        ];
                        @endphp
                        <span class="px-3 py-1 rounded-full font-semibold text-sm 
        {{ $statusClasses[$leave->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </td>
                    <td>{{ $leave->reason }}</td>
                    <td>{{ $leave->created_at }}</td>
                    <td>{{ $leave->remarks }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>