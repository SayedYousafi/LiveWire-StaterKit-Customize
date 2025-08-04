<?php
namespace App\Livewire;

use App\Models\Holiday;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

#[Title('Holiday management')]
class Holidays extends Component
{
    use WithPagination;

    public bool $enableEdit = false;

    public bool $isUpdate = false;

    public $holidayId;

    public string $search = '';

    public string $filterByCountry = '';

    public string $title = 'Public Holidays';

    public $day, $date, $name, $type, $comments, $country;

    public function render()
    {
        $holidays = Holiday::search($this->search);
        if ($this->filterByCountry) {
            $holidays->where('country', $this->filterByCountry);
        }
        $holidays = $holidays->paginate(100);

        return view('livewire.holidays', compact('holidays'));
    }

    public function updatedDate($value)
    {
        if ($value) {
            // Convert date string to day name, e.g., "Monday"
            $this->day = \Carbon\Carbon::parse($value)->format('l');
        } else {
            $this->day = null;
        }
    }
    public function save()
    {
        $validated = $this->validate([
            'country' => 'required',
            'day'     => 'required',
            'date'    => 'required',
            'name'    => 'required',
            'type'    => 'required',
        ]);
// dd($validated);
        Holiday::create([
            'country'  => $this->country,
            'day'      => $this->day,
            'date'     => $this->date,
            'name'     => $this->name,
            'type'     => $this->type,
            'comments' => $this->comments,
        ]);
        Flux::modal('holidayModal')->close();
        session()->flash('success', 'Holiday successfully registered');
    }

    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);

        $this->holidayId = $id;
        $this->isUpdate  = true;

        $this->country  = $holiday->country;
        $this->day      = $holiday->day;
        $this->date     = $holiday->date;
        $this->name     = $holiday->name;
        $this->type     = $holiday->type;
        $this->comments = $holiday->comments;

        Flux::modal('holidayModal')->show();

    }

    public function update()
    {
        $updated = Holiday::where('id', $this->holidayId)->update([
            'country'  => $this->country,
            'day'      => $this->day,
            'date'     => $this->date,
            'name'     => $this->name,
            'type'     => $this->type,
            'comments' => $this->comments,
        ]);

        if (! $updated) {
            session()->flash('error', 'Something went wrong in updating holiday');

            return;
        }

        Flux::modal('holidayModal')->close();
        session()->flash('success', 'Holiday updated successfully');
        $this->reset();
    }

    public function cancel()
    {
        $this->isUpdate = false;
        $this->reset();
        //Flux::modal('holidayModal')->close();
    }

}
