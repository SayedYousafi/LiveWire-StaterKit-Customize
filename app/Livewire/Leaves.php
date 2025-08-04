<?php
namespace App\Livewire;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\WorkProfile;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Leaves extends Component
{
    public $leaves, $rejectId, $status, $remarks;
    public $balances = [], $userId;
    public function render()
    {
        $this->leaves = LeaveRequest::with('users')->latest()->get();
        //dd($this->leaves);
        return view('livewire.leaves')->with([
            'leaves'   => $this->leaves,
            'balances' => $this->balances,
        ]);
    }

    public function approve($id)
    {
        $edited = LeaveRequest::where('id', $id)->update(
            [
                'status' => 'approved',
            ]);
        if ($edited) {
            session()->flash('success', 'Leave requeste approved successfully');
        }
    }

    public function reject($id)
    {
        $this->rejectId = $id;
        Flux::modal('leaveRejectModal')->show();
    }

    public function rejected()
    {
        $edited = LeaveRequest::where('id', $this->rejectId)->update(
            [
                'status'  => 'rejected',
                'remarks' => $this->remarks,
            ]);
        if ($edited) {
            session()->flash('success', 'Leave requeste rejected successfully');
        }
        Flux::modal('leaveRejectModal')->close();
    }

    public function showDetails($id)
    {
        $this->userId = $id;
        $profileId    = User::where('id', $this->userId)->value('work_profile_id');
        $entitlement  = WorkProfile::where('id', $profileId)->value('entitlement');

        //dd($this->userId, $profileId, $entitlement);
        $now    = Carbon::now();
        $user   = User::findOrFail($this->userId);
        $joined = Carbon::parse($user->join_date);

        $years = range($joined->year, $now->year);

        foreach ($years as $year) {
            $startOfYear = Carbon::parse("$year-01-01");
            $endOfYear   = Carbon::parse("$year-12-31");

            // Total days in the year
            $daysInYear = $startOfYear->diffInDays($endOfYear) + 1;

            // Determine active period within this year
            $activeStart = $joined->greaterThan($startOfYear) ? $joined : $startOfYear;
            $activeEnd   = $now->lessThan($endOfYear) ? $now : $endOfYear;

            if ($activeEnd->lt($activeStart)) {
                $entitled = 0;
            } else {
                $daysWorkedThisYear = $activeStart->diffInDays($activeEnd) + 1;
                //fixed entitlement.
                //$entitled = 20;
                $entitled = floor($entitlement * $daysWorkedThisYear / $daysInYear);
            }

            // Get used leave in that year
            $used = LeaveRequest::where('user_id', $this->userId)
                ->whereIn('status', ['approved','expired'])
                ->whereYear('dateFrom', $year)
                ->sum(DB::raw('noOfDays'));

            $this->balances[$year] = [
                'entitled'  => $entitled,
                'used'      => $used,
                'remaining' => max(0, $entitled - $used),
            ];
        }
        Flux::modal('leaveDetailsModal')->show();
    }

    public function close()
    {
        $this->reset();
        $this->userId = '';
        Flux::modal('leaveDetailsModal')->close();
    }

    
}
