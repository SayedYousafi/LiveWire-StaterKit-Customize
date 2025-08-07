<?php
namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\WorkProfile;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeaveBalance extends Component
{
    public $user;
    public $balances = [];

    public function render()
    {
        $this->dispatch('leave-requested');
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

        $this->balances = $balances;

        return view('livewire.leave-balance')->with(['balances' => $this->balances]);
    }
}
