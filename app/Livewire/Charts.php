<?php

namespace App\Livewire;

use App\Models\Stockvalue;
use Carbon\Carbon;
use Livewire\Component;

class Charts extends Component
{
    public $stocks = [];

    public $totalStockValues = [];

    public function render()
    {
        // Get the date range for the last 13 months
        $startDate = now()->subMonths(12)->startOfMonth();

        // Query to get stocks data within the last 13 months, including total EUR per month
        $this->stocks = Stockvalue::selectRaw("
        DATE_FORMAT(created_at, '%Y-%m') as month,
        category,
        SUM(eur) as total_eur
    ")
            ->where('created_at', '>=', $startDate)
            ->groupBy('month', 'category')

            ->union(
                Stockvalue::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            'Total' as category,
            SUM(eur) as total_eur
        ")
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('month')
            )
            ->orderByRaw("month ASC, FIELD(category, 'Total') DESC, total_eur DESC")
            ->get()
            ->toArray();

        $this->totalStockValues = Stockvalue::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') AS month,
            SUM(EUR) AS total_EUR,
            LAG(SUM(EUR)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) AS previous_total_EUR,
            SUM(EUR) - LAG(SUM(EUR)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) AS difference,
            ( (SUM(EUR) - LAG(SUM(EUR)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')))
              / LAG(SUM(EUR)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) * 100 ) AS percentage_difference
        ")
            ->where('created_at', '>=', Carbon::now()->subMonths(13))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

        return view('livewire.charts')->with([
            'stocks' => $this->stocks,
            'totalStockValues' => $this->totalStockValues,
        ]);
    }
}
