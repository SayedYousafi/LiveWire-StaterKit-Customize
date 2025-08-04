<?php
namespace App\Livewire\Settings;

use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\WorkProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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
        $user     = Auth::user();
        $entitled = $user->workProfile->entitlement ?? 0;

        $now      = Carbon::now();
        $joined   = Carbon::parse($user->join_date); // string → Carbon
        $annStart = $joined->copy()->year($now->year);
        if ($now->lt($annStart)) {
            $annStart->subYear();
        }
        $annEnd = $annStart->copy()->addYear()->subDay();

        $used = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'Expired'])
            ->whereBetween('dateFrom', [$annStart, $annEnd])
            ->sum('noOfDays');

        return max($entitled - $used, 0);
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

        // Only check leave balance if the request is for this year
        if ($requestYear === $currentYear) {
            if ($this->daysDifference > $this->leaveBalance()) {
                $this->addError('noOfDays', 'Insufficient leave balance.');
                return;
            }
        }

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
