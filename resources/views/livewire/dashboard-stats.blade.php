<div class="grid auto-rows-min gap-4 md:grid-cols-4">
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Orders</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ordersCount }}</p>
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Items</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $itemsCount }}</p>
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Suppliers</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $suppliersCount }}</p>
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Customers</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customersCount }}</p>
    </div>


    <div class="rounded-xl bg-amber-50 border border-neutral-200 dark:border-neutral-700 p-4 dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Birthdays</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">Birthdays</p>
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300" icon='cake'>Upcoming Birthdays</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">Upcoming Birthdays</p>
    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-red-50 dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Who is on leave?</h2>
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Members on Leave:</h2>
            <ol class="list-decimal pl-6 text-gray-800 dark:text-gray-200 text-lg">
                @foreach ($leaves as $leave)
                @foreach ($leave->users as $user)
                @php
                $today = strtotime(now());
                $endLeave = strtotime($leave->dateTo);
                $daysLeft = round(($endLeave - $today) / (60 * 60 * 24) + 1);
                @endphp
                <li class="text-sm font-medium text-gray-600 dark:text-gray-300">
                    {{ $user->name }} till {{ $leave->dateTo }},
                    ({{ $daysLeft }} more {{ Str::plural('day', $daysLeft) }})
                </li>
                @endforeach
                @endforeach
            </ol>
        </div>

    </div>
    <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-white dark:bg-neutral-800">
        <h2 class="text-sm font-medium text-gray-600 dark:text-gray-300">Upcoming leave plan</h2>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">Upcoming leave plan</p>
    </div>
</div>