<?php
namespace App\Livewire;

use App\Models\ItemQuality;
use App\Services\ItemDetail;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ItemDetails extends Component
{
    public $itemId;

    protected ItemDetail $ItemDetail;

    public function boot(ItemDetail $ItemDetail)
    {
        $this->ItemDetail = $ItemDetail;
    }

    public function render()
    {
        $qualities = ItemQuality::where('item_id', $this->itemId)->get();
        $item      = $this->ItemDetail->getItemDetial($this->itemId);

        $attachments = DB::table('attachments')
            ->join('attachment_item', 'attachments.id', '=', 'attachment_item.attachment_id')
            ->where('attachment_item.item_id', $this->itemId)
            ->get(['attachments.filename', 'attachments.path']);

        return view('livewire.item-details', [
            'itemDetail'  => $item,
            'attachments' => $attachments,
            'qualities'   => $qualities,
        ]);
    }
    public function download($filePath)
    {
        return response()->download(storage_path("app/public/{$filePath}"));
    }
}
