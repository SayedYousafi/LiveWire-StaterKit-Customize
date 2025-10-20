<?php
namespace App\Livewire;

use Flux\Flux;
use App\Models\Item;
use App\Models\Confirm;
use Livewire\Component;
use App\Models\Order_item;
use App\Models\ItemQuality;
use Livewire\WithFileUploads;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ItemQualities extends Component
{
    use WithFileUploads;
    #[Reactive]
    public $itemId;
    public $parentId, $masterId, $ItemID_DE, $isSoRoute;
    public $picture, $description, $full_description;
    public $editId, $confirmId, $poorQty, $issues, $txtProblem, $orderQty, $name, $name_cn;
    public bool $enableEdit = false;
    public bool $isUpdate   = false;
    public $selectedQyality, $qlyName;

    public function render()
    {
        $qualities = ItemQuality::where('item_id', $this->itemId)->get();
    
        return view('livewire.item-qualities')->with(
            [
                'qualities'       => $qualities,
                'selectedQyality' => $this->selectedQyality,
                //'confirmed' =>  $this->confirmed,
            ]);
    }

    public function save()
    {
        // Validate the picture file
        $this->validate([
            'name'    => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store in public/storage/pictures folder
        $filePath = $this->picture->storeAs(
            'pictures',
            $this->picture->getClientOriginalName(),
            'public'
        );

        // Optionally: store in DB
        $attachmentId = DB::table('item_qualities')->insertGetId([
            'item_id'          => $this->itemId,
            'picture'          => $this->picture->getClientOriginalName(),
            'description'      => $this->description,
            'full_description' => $this->full_description,
            'name'             => $this->name,
            'name_cn'          => $this->name_cn,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Reset the file
        $this->reset('picture');

        // Show success
        session()->flash('success', 'Item quality with picture successfully added !');

        // Clear the cache
        Cache::forget('uploaded_pictures');

        return $filePath;
    }

    public function confirm($id, $mId)
    {
        //dd($id, $mId);
        $this->masterId        = $mId;
        $this->confirmId       = $id;
        $this->selectedQyality = ItemQuality::findOrFail($id);
        $this->qlyName         = $this->selectedQyality->name;
        $qty                   = Order_item::where('master_id', $mId)->select('qty')->first();
        $this->orderQty        = $qty->qty;

        Flux::modal('modalConfirm')->show();
    }

    public function createConfirm()
    {
        if (empty($this->poorQty)) {

            // if ($this->poorQty !== $this->qtyCheck) {
            //     Order_status::where('master_id', $this->masterId)->update(
            //         [
            //             'status'   => 'C_Problem',
            //             'problems' => $this->txtProblem,
            //         ]
            //     );
            // }
            Confirm::updateOrCreate(
                ['id' => $this->confirmId],
                [
                    'quality_id' => $this->confirmId,
                    'm_id'       => $this->masterId,
                    'poorQty'    => null,
                    'item_id'    => $this->itemId,
                    'confirm_by' => Auth::user()->name,
                    'remarks'    => $this->txtProblem,
                    'issues'     => null,
                ]);

        } else {
            Confirm::updateOrCreate(
                ['id' => $this->confirmId],
                [
                    'quality_id' => $this->confirmId,
                    'm_id'       => $this->masterId,
                    'poorQty'    => $this->poorQty,
                    'item_id'    => $this->itemId,
                    'confirm_by' => Auth::user()->name,
                    'remarks'    => $this->txtProblem,
                    'issues'     => 1,
                ]);

        }
        ItemQuality::where('id', $this->confirmId)->update(
            [
                'confirmed' => 1,
            ]);

        session()->flash('success', 'Confirmed successfully !!!');
        $this->txtProblem = '';
        $this->poorQty    = '';
        // Emit event to parent to trigger checkDetails
        $this->dispatch('confirmationCreated', masterId: $this->masterId);
        Flux::modal('modalConfirm')->close();
    }

    public function delete($id)
    {
        $delete = ItemQuality::findOrFail($id);
        $delete->delete();
        session()->flash('success', 'Deleted successfully !!!');
    }

    public function edit($id)
    {
        $this->editId   = $id;
        $this->isUpdate = true;
        $edit           = ItemQuality::findOrFail($id);
                                                  //$this->itemId=$edit->picture;
        $this->picture          = $edit->picture; //->getClientOriginalName();
        $this->description      = $edit->description;
        $this->name             = $edit->name;
        $this->full_description = $edit->full_description;
        Flux::modal('edit-quality')->show();
    }

    public function update()
    {
        // Validate the picture file
        //$this->validate(['picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', ]);

        // Store in public/storage/pictures folder
        // $filePath = $this->picture->storeAs(
        //     'pictures',
        //     $this->picture->getClientOriginalName(),
        //     'public'
        // );

        // Optionally: store in DB
        $updated = ItemQuality::where('id', $this->editId)->update([
            'item_id'          => $this->itemId,
            'name'             => $this->name,
            'name_cn'          => $this->name_cn,
            'picture'          => $this->picture, //->getClientOriginalName(),
            'description'      => $this->description,
            'full_description' => $this->full_description,
        ]);

        // Reset the file
        $this->reset('picture', 'name');

        // Show success
        session()->flash('success', 'Item quality with picture successfully updated !');

        // Clear the cache
        Cache::forget('uploaded_pictures');

    }

    public function applyToParent($qId, $id)
    {

        // Get all Item IDs for the parent
        $qualityIds = ItemQuality::where('item_id', $id)->pluck('id')->toArray();
        dd($qId, $id, $qualityIds);
        $itemIds = Item::where('parent_id', $this->parentId)->pluck('id')->toArray();

        if (empty($itemIds)) {
            return; // No items found, exit early
        }

        $edit = ItemQuality::findOrFail($id);
                                                  //$this->itemId=$edit->picture;
        $this->picture          = $edit->picture; //->getClientOriginalName();
        $this->description      = $edit->description;
        $this->name             = $edit->name;
        $this->name_cn          = $edit->name_cn;
        $this->full_description = $edit->full_description;

        // Create or update ItemQuality records for each item_id
        foreach ($itemIds as $id) {
            ItemQuality::updateOrCreate(
                ['item_id' => $id], // Match on item_id
                [
                    'picture'          => $this->picture,
                    'name'             => $this->name,
                    'name_cn'          => $this->name_cn,
                    'description'      => $this->description,
                    'full_description' => $this->full_description,
                ]
            );
        }
        session()->flash('success', 'Applied to parent successfully');
    }
}
