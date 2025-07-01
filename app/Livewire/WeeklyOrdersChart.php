<?php

namespace App\Livewire;

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;

class WeeklyOrdersChart extends Component
{
    public $weeklyData = [];

    public function mount()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $weeks = [];
        $current = $startOfMonth->copy();

        while ($current->lte($endOfMonth)) {
            $start = $current->copy();
            $end = $current->copy()->endOfWeek();

            // Limit to end of month
            if ($end->gt($endOfMonth)) {
                $end = $endOfMonth->copy();
            }

            $count = Order::whereRaw(
                "STR_TO_DATE(date_created, '%d.%m.%Y') BETWEEN ? AND ?",
                [$start->toDateString(), $end->toDateString()]
            )->count();

            $weeks[] = [
                'label' => $start->format('M j').' - '.$end->format('M j'),
                'count' => $count,
            ];

            $current->addWeek();
        }

        $this->weeklyData = $weeks;
    }

    public function render()
    {
        return view('livewire.weekly-orders-chart');
    }
}
