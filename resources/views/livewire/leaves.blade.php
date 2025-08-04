<div>
    @include('partials.leaveModal')
    @include('partials.leaveDetailsModal')
    <div class="text-lg mb-3 text-center">Team Leave Request for Review</div>
    @if (session('success'))
    <flux:callout icon="check-circle" variant='success' heading="{{ session('success') }}" class="mb-2" />
    @endif
    
    <table class="table-default">
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee name</th>
                <th>Date From</th>
                <th>Date To</th>
                {{-- <th>All days</th> --}}
                <th>No. Days</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Requested</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leaves as $leave )
            <tr wire:key="{{ $leave->id }}">
                @php
                $f = strtotime($leave->dateFrom);
                $t = strtotime( $leave->dateTo);

                $now = \Carbon\Carbon::now();
                $dateTo = \Carbon\Carbon::parse($leave->dateTo)->endOfDay();
                $status = strtolower(trim($leave->status ?? ''));

                @endphp
                <td>{{ $leave->id }}</td>
                <td>
                    @foreach ( $leave->users as $user)
                    <div wire:key="{{ $user->id }}">
                        <a href="#" wire:click='showDetails({{ $user->id }})' class="!text-blue hover:!underline">{{
                            $user->name }}</a>
                    </div>
                    @endforeach
                </td>
                <td>{{ $leave->dateFrom }}</td>
                <td>{{ $leave->dateTo }}</td>
                {{-- <td>{{ round(($t-$f) / (60 * 60 * 24)) + 1}}</td> --}}
                <td>{{ $leave->noOfDays }}</td>
                <td>{{ $leave->reason }}</td>
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

                <td>{{ $leave->created_at }}</td>

                @if (($now->gt($dateTo) && $leave->status == 'pending') || $leave->status == 'Expired')
                <td colspan="2">
                    <span class="text-sm text-red-600 font-semibold">Expired</span>
                </td>

                @else

                <td>
                    @if ($leave->status=='approved')
                    Approved
                    @elseif ($leave->status=='rejected')
                    Rejected
                    @else
                    <flux:button icon='shield-check' size='sm' wire:click="approve({{ $leave->id }})" variant='primary'
                        wire:confirm="Are you sure?">Approve</flux:button>

                    @endif
                </td>
                <td>@if ($leave->status=='approved')
                    Approved
                    @elseif ($leave->status=='rejected')
                    {{ $leave->remarks }}
                    @else
                    <flux:button icon='minus-circle' size='sm' wire:click="reject({{ $leave->id }})"
                        wire:confirm="Are you sure?" class='!bg-red-500 !text-white hover:!bg-red-400'>Reject
                    </flux:button>
                    @endif
                </td>

                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>