<?php
namespace App\Livewire\Settings;

use Carbon\Carbon;
use App\Models\Holiday;
use Livewire\Component;
use App\Models\WorkProfile;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeaveRequests extends Component
{
    public $dateFrom, $dateTo, $noOfDays, $reason, $status, $userId, $editId;

    public function getDaysDifferenceProperty()
    {
        if ($this->dateFrom && $this->dateTo) {
            $from = strtotime($this->dateFrom);
            $to   = strtotime($this->dateTo);

            if ($to < $from) {
                return null; // Invalid range
            }

            $userCountry = WorkProfile::where('id', Auth::user()->work_profile_id)->value('public_holiday');

            // Get holidays for this country in range
            $holidays = Holiday::where('country', $userCountry)
                ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                ->pluck('date')
                ->toArray();

            $count = 0;

            for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date)) {
                $dayOfWeek = date('N', $date); // 1 (Mon) to 7 (Sun)
                $dateStr   = date('Y-m-d', $date);

                // For China: skip only Sunday (7)
                // For others: skip Sat (6) and Sun (7)
                if ($userCountry === 'China') {
                    $isWeekend = ($dayOfWeek == 7);
                } else {
                    $isWeekend = ($dayOfWeek >= 6);
                }

                if (! $isWeekend && ! in_array($dateStr, $holidays)) {
                    $count++;
                }
            }

            return $count;
        }

        return null;
    }

public function leaveBalance()
{
     $entitlement = WorkProfile::where('id', Auth::user()->work_profile_id)->value('entitlement');
        $now    = Carbon::now();
        $joined = Carbon::parse(Auth::user()->join_date);

        $startYear   = $joined->year;
        $currentYear = $now->year;

        $previousEntitled = 0;
        $previousUsed     = 0;

        $balances = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $startOfYear = Carbon::parse("$year-01-01");
            $endOfYear   = Carbon::parse("$year-12-31");

            $daysInYear = $startOfYear->diffInDays($endOfYear) + 1;

            $activeStart = $joined->greaterThan($startOfYear) ? $joined : $startOfYear;
            $activeEnd   = $now->lessThan($endOfYear) ? $now : $endOfYear;

            if ($activeEnd->lt($activeStart)) {
                $entitled = 0;
            } else {
                $daysWorkedThisYear = $activeStart->diffInDays($activeEnd) + 1;
               $entitled           = floor($entitlement * $daysWorkedThisYear / $daysInYear);
               //$entitled =$entitlement;
            }

            $used = LeaveRequest::with('user.workProfile')
                ->where('user_id', Auth::user()->id)
                ->whereIn('status', ['Expired','approved'])
                ->whereYear('dateFrom', $year)
                ->sum(DB::raw('noOfDays'));

            if ($year < $currentYear) {
                $previousEntitled += $entitled;
                $previousUsed += $used;
            } else {
                $balances['Current'] = [
                    'entitled'  => $entitlement,
                    'used'      => $used,
                    'remaining' => max(0, $entitlement - $used),
                ];
            }
        }

        // Add the accumulated previous years’ data first
        $balances = array_merge(
            ['Previous' => [
                'entitled'  => $previousEntitled,
                'used'      => $previousUsed,
                'remaining' => max(0, $previousEntitled - $previousUsed),
            ]],
            $balances
        );
        return $balances;
}

    public function leaveRequest()
    {
        $validated = $this->validate([
            'dateFrom' => 'required|date',
            'dateTo'   => 'required|date|after_or_equal:dateFrom',
            'reason'   => 'required|string|min:5',
        ]);

        // Parse years from submitted dates
        $requestYear = Carbon::parse($this->dateFrom)->year;
        $currentYear = now()->year;

        $now          = Carbon::now();
        $this->status = ($now > $this->dateTo) ? 'Expired' : 'pending';

        LeaveRequest::create([
            'user_id'  => $this->userId,
            'dateFrom' => $this->dateFrom,
            'dateTo'   => $this->dateTo,
            'reason'   => $this->reason,
            'noOfDays' => $this->daysDifference,
            'status'   => $this->status,
        ]);

        $this->dispatch('leave-requested');
        $this->reset();
    }
    public function render()
    {
        $this->userId = Auth::user()->id;
        $leaves       = LeaveRequest::where('user_id', $this->userId)->get();
        //dd($leaves);
        return view('livewire.settings.leave-requests', compact('leaves'));
    }

}
