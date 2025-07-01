<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class DailyOrdersChart extends Component
{
    public $days = [];

    public function mount()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addWeek()) {
            $count = Order::whereRaw("
            DATE(STR_TO_DATE(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                    date_created,
                    'Mär', 'Mar'),
                    'Mai', 'May'),
                    'Okt', 'Oct'),
                    'Dez', 'Dec'),
                    'Apr', 'Apr'),
                    'Jun', 'Jun'),
                    'Jul', 'Jul'),
                    'Aug', 'Aug'),
                    'Sep', 'Sep'),
                    'Nov', 'Nov'),
                    'Jan', 'Jan'),
                    'Feb', 'Feb'),
                '%b %d %Y %l:%i%p')
            ) = ?
        ", [$date->toDateString()])->count();

            $this->days[] = [
                'label' => $date->format('M j'),
                'count' => $count,
            ];
        }

    }

    public function render()
    {

        return view('livewire.daily-orders-chart');
    }
}
