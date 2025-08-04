<?php

namespace App\Livewire;

use App\Models\ean;
use App\Models\Item;
use App\Models\Parents;
use App\Models\Supplier;
use App\Models\Supplier_item;
use App\Models\Taric;
use App\Models\VarVal;

use App\Models\Warehouse;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Item - Insert new item')]
class AddItem extends Component
{
    use WithFileUploads;
    public $search = '';

    public $showresult = true;

    public $records;

    public $parentDetails;

    public $itemID;

    public $item_name;

    public $item_name_de;

    public $item_name_en;

    public $item_name_cn;

    public $de_v1;

    public $value_de_2;

    public $value_de_3;

    public $en_v1;

    public $value_en_2;

    public $value_en_3;

    public $is_qty_dividable = 'N';

    public $is_stock_item = 'N';

    public $is_pu_item;

    public $is_meter_item;

    public $is_SnSI = 'Y';

    public $remark;

    public $isbn = 0;

    public $height;

    public $length;

    public $weight;

    public $width;

    public $foq;

    public $fsq;

    public $pix_path_eBay;

    public $pix_path;

    public $de_no;

    public $supp_cat;

    public $taric_id;

    public $many_components = 1;

    public $effort_rating = 3;

    public $rmb_special_price;

    public $RMB_Price;

    public $supplier_id;

    public $is_po;

    public $url;

    public $photo;

    public $is_npr = 'Y';

    private $dummyPhoto = 'DummyPicture.jpg';

    public function searchResult()
    {
        $this->records = ! empty($this->search)
            ? Parents::where('de_no', 'like', "%$this->search%")->orderBy('de_no')->limit(50)->get()
            : collect();

        $this->showresult = $this->records->isNotEmpty();
    }

    public function fetchParentDetail($id)
    {
        $record = Parents::with(['items.values'])->find($id);
        // dd($record);
        if (! $record) {
            session()->flash('error', 'No parent record found.');

            return;
        }
        $this->de_no = $record->de_no;
        $this->parentDetails = $record;

        $item = $record->items->first();
        // dd($item);
        $this->itemID = $item->id ?? null;
        $this->item_name = $this->item_name_en = $record->name_en;
        $this->item_name_de = $record->name_de;
        $this->item_name_cn = $record->name_cn;
        $this->supplier_id = $record->supplier_id;

        if ($item) {
            $this->taric_id = $item->taric_id;
            $this->many_components = $item->many_components;
            $this->effort_rating = $item->effort_rating;
            $this->rmb_special_price = $item->rmb_special_price;
            $this->is_qty_dividable = $item->is_qty_dividable;
            $this->is_stock_item = $item->is_stock_item;
            $this->is_pu_item = $item->is_pu_item;
            $this->is_meter_item = $item->is_meter_item;
            $this->isbn = $item->ISBN;
            $this->remark = $item->remark;
        }

        if ($id == 262) {
            $this->supplier_id = 1;
            $this->url = 'N/A';
            $this->is_SnSI = 'N';
            $this->taric_id = 1;
        } else {
            $supplier = Supplier_item::where('item_id', $this->itemID)->first();
            $this->supplier_id = $supplier->supplier_id ?? 1;
            $this->url = $supplier->url ?? 'N/A';
            $this->RMB_Price = $supplier->price_rmb;
        }

        $this->showresult = false;
        $this->search = '';
    }

    public function save($id, $ean, $item_no_de)
    {
        $this->supp_cat = $this->supp_cat ?? substr($item_no_de, 0, 3);
        if (! in_array($this->supp_cat, ['TES', 'TEW', 'TLE', 'TMS', 'TMT', 'TOP', 'TSC', 'TTI'])) {
            $this->supp_cat = 'STD';
        }

        if ($id != 262) {
            $this->validate([
                'en_v1' => 'required',
                'de_v1' => 'required',
                'item_name' => 'required',
            ]);
        }

        $price = number_format((float) str_replace(',', '.', $this->RMB_Price), 2, '.', '');
        $msq = round($this->fsq * 0.7);
        $buffer = Buffer($msq, $price, $this->is_pu_item, $this->is_meter_item);

        $this->is_qty_dividable = $this->is_qty_dividable === 1 ? 'Y' : 'N';
        $this->is_stock_item = $this->is_stock_item === 1 ? 'Y' : 'N';

        do {
            $randomNumber = mt_rand(1, 9999);
        } while (Item::where('ItemID_DE', $randomNumber)->exists());

        if (empty($this->photo)) {
            $this->is_npr = 'Y';
            $this->photo = $this->pix_path = $this->pix_path_eBay = $this->dummyPhoto;
        } else {
            $filename = $this->photo.'.jpg';
            $this->is_npr = 'N';
            $this->photo = $filename;
            $this->pix_path = $filename.'-1s.jpg';
            $this->pix_path_eBay = $filename.'-1e.jpg';
        }

        if ($this->parentDetails->is_NwV === 'Y') {
            $suffixEn = $this->en_v1.$this->value_en_2.$this->value_en_3;
            $suffixDe = $this->de_v1.$this->value_de_2.$this->value_de_3;
            $this->item_name .= $suffixEn;
            $this->item_name_en .= $suffixEn;
            $this->item_name_de .= $suffixDe;
        }

        $item = Item::create([...array_merge([
            'parent_id' => $id,
            'RMB_Price' => $price,
            'ItemID_DE' => $randomNumber,
            'parent_no_de' => $this->de_no,
            'supp_cat' => $this->supp_cat,
            'cat_id' => 1,
            'item_name_cn' => $this->item_name_cn,
            'item_name' => $this->item_name,
            'ean' => $ean,
            'isbn' => $this->isbn,
            'height' => $this->height,
            'length' => $this->length,
            'weight' => $this->weight,
            'width' => $this->width,
            'photo' => $this->photo,
            'pix_path' => $this->pix_path,
            'pix_path_eBay' => $this->pix_path_eBay,
            'is_npr' => $this->is_npr,
            'is_qty_dividable' => $this->is_qty_dividable,
            'is_pu_item' => $this->is_pu_item,
            'is_meter_item' => $this->is_meter_item,
            'foq' => $this->foq,
            'fsq' => $this->fsq,
            'remark' => $this->remark,
            'taric_id' => $this->taric_id,
            'many_components' => $this->many_components,
            'effort_rating' => $this->effort_rating,
            'synced_at' => now(),
        ])]);

        VarVal::create([
            'item_id' => $item->id,
            'value_de' => $this->de_v1,
            'value_de_2' => $this->value_de_2,
            'value_de_3' => $this->value_de_3,
            'value_en' => $this->en_v1,
            'value_en_2' => $this->value_en_2,
            'value_en_3' => $this->value_en_3,
        ]);

        Warehouse::create([
            'item_id' => $item->id,
            'ean' => $ean,
            'item_no_de' => $item_no_de,
            'ItemID_DE' => $randomNumber,
            'item_name_de' => $this->item_name_de,
            'item_name_en' => $this->item_name_en,
            'msq' => $msq,
            'buffer' => $buffer,
            'is_SnSI' => $this->is_SnSI,
            'is_stock_item' => $this->is_stock_item,
        ]);

        Supplier_item::create([
            'item_id' => $item->id,
            'supplier_id' => $this->supplier_id,
            'price_rmb' => $price,
            'is_po' => $this->is_po,
            'url' => $this->url,
        ]);

        $this->reset([
            'de_v1', 'value_de_2', 'value_de_3', 'en_v1', 'value_en_2', 'value_en_3',
            'height', 'length', 'weight', 'width', 'foq', 'fsq', 'photo',
        ]);

        $this->usedEan($ean);
        session()->flash('success', 'Item created successfully!');
    }

    public function usedEan($ean)
    {
        ean::where('ean', $ean)->update(['is_used' => 1]);
    }

    public function updateditem_name_en($value)
    {
        $this->item_name = $value;
    }

    public function render()
    {
        return view('livewire.add-item', [
            'eans' => ean::where('is_used', 0)->limit(1)->get(),
            'tariffs' => Taric::all()->reverse(),
            'suppliers' => Supplier::all()->reverse(),
        ]);
    }
}
