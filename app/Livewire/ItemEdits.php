<?php
namespace App\Livewire;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Supplier_item;
use App\Models\Taric;
use App\Models\VarVal;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemEdits extends Component
{

    // protected ItemDetail $ItemDetail;

    // public function boot(ItemDetail $ItemDetail)
    // {
    //     $this->ItemDetail = $ItemDetail;
    // }

    use WithFileUploads;
    //warehouse columns
    public $item_name_de, $item_name_en, $is_no_auto_order, $is_active, $isActiv, $msq, $buffer, $is_stock_item;
    //item columns
    public $item_name, $item_name_cn, $remark, $weight, $width, $height, $length, $foq, $fsq, $is_qty_dividable, $isbn, $price_rmb,
    $many_components, $effort_rating, $rmb_special_price, $is_pu_item, $is_meter_item, $stock_qty;
    public $id, $tariff_code, $taric_id, $is_po, $url, $model, $moq, $oi, $note_cn, $lead_time, $is_new, $is_npr;
    public $name, $province, $full_address, $name_cn, $contact_person, $company_name, $extra_note, $min_order_value, $is_fully_prepared, $is_tax_included
    , $is_freight_included, $city, $mobile, $street, $phone, $website, $email, $ItemID_DE, $item_no_de, $is_SnSI;
    public $value_en, $photo, $value_de, $value_en_2, $value_en_3, $value_de_2, $value_de_3, $pix_path, $pix_path_eBay, $file_name;

    public ?int $cat_id = null;
    public $successMessage;
    public $image, $var_values, $par_no, $npr_remark, $supp_cat, $is_rmb_special, $is_eur_special, $test_special, $isActive;

    public function mount()
    {
        
        $itemDetail     = Item::with('warehouse', 'categories', 'supplierItem', 'tarics')
        ->where('id',$this->id)->first();

        $this->cat_id   = $itemDetail->cat_id;
        $this->isActive = $itemDetail->isActive;
        $this->is_rmb_special = $itemDetail->is_rmb_special;
        $this->is_eur_special = $itemDetail->is_eur_special;
        $this->is_new         = $itemDetail->is_new;
        $this->is_SnSI        = $itemDetail->warehouse->is_SnSI;
        $this->is_po          = $itemDetail->supplierItem->is_po;
        $this->taric_id    = $itemDetail->taric_id;
        $this->is_npr         = $itemDetail->is_npr;
    }

    public function editPixPath($id)
    {
        date_default_timezone_set('Europe/Berlin');
        Item::where('id', $this->id)->update([
            'pix_path'      => $this->pix_path,
            'pix_path_eBay' => $this->pix_path_eBay,
            'is_npr'     => 'Y',
            'npr_remark' => $this->npr_remark,
        ]);

        $this->successMessage = 'Item pix paths updated successfully !!!';
    }
    
    public function editNames($id)
    {
        date_default_timezone_set('Europe/Berlin');
        //dd($id, $this->cat_id);
        switch ($this->cat_id) {
            case 1:
                $cat = 'STD';
                break;
            case 2:
                $cat = 'GBL';
                break;
            case 3:
                $cat = 'GTR';
                break;
            case 4:
                $cat = 'PRO';
                break;
            case 5:
                $cat = 'ERS';
                break;
            default:
                $cat = 'STD';
        }
        Item::where('id', $id)->update([
            'item_name'    => $this->item_name,
            'remark'       => $this->remark,
            'supp_cat'     => $cat,
            'item_name_cn' => $this->item_name_cn,
            'isActive'     => $this->isActive,
            'cat_id'       => $this->cat_id,
            'model'        => $this->model,
        ]);

        $this->successMessage = 'Items value saved successfully.';
    }
    public function editDimentions($id)
    {
        //dd($id);
        Item::where('id', $this->id)->update([
            //'is_default'=> $this->is_default,
            'weight'           => $this->weight,
            'width'            => $this->width,
            'height'           => $this->height,
            'length'           => $this->length,
            'foq'              => $this->foq,
            'fsq'              => $this->fsq,

            'taric_id'         => $this->taric_id,
            'is_qty_dividable' => $this->is_qty_dividable,
            'isbn'             => $this->isbn,
            'many_components'  => $this->many_components,
            'effort_rating'    => $this->effort_rating,

            'is_pu_item'       => $this->is_pu_item,
            'is_meter_item'    => $this->is_meter_item,
            'is_new'           => $this->is_new,
            'is_npr'           => $this->is_npr,

        ]);
        $this->successMessage = 'Item Dimentions Updated Successfully !!!';
    }
    public function editWareHouse($ean)
    {

        Warehouse::where('ean', $ean)->update([
            // 'ItemID_DE'=> $this->ItemID_DE,
            'item_no_de'       => $this->item_no_de,
            'item_name_de'     => $this->item_name_de,
            'item_name_en'     => $this->item_name_en,
            'is_no_auto_order' => $this->is_no_auto_order,
            'is_active'        => $this->is_active,
            //dd($this->is_active),

            'msq'              => $this->msq,
            'buffer'           => $this->buffer,
            'is_stock_item'    => $this->is_stock_item,
            'is_SnSI'          => $this->is_SnSI,
        ]);
        $this->successMessage = 'Warehouse Items Updated Successfully !!!';
    }

    public function editValues($id)
    {

        VarVal::where('item_id', $this->id)->update([
            'value_en'   => $this->value_en,
            'value_de'   => $this->value_de,

            'value_en_2' => $this->value_en_2,
            'value_en_3' => $this->value_en_3,

            'value_de_2' => $this->value_de_2,
            'value_de_3' => $this->value_de_3,
        ]);
        $this->successMessage = 'Varation values updated successfully !!!';

    }
    public function editSuppItem($supp_id)
    {
        Supplier_item::where('id', $supp_id)->update([
            //'is_default'=> $this->is_default,
            'moq'       => $this->moq,
            'price_rmb' => $this->price_rmb,
            'url'       => $this->url,
            'note_cn'   => $this->note_cn,
            'is_po'     => $this->is_po,
            'oi'        => $this->oi,
            'lead_time' => $this->lead_time,
        ]);

        $this->successMessage = 'Default Supplier item Updated Successfully !!!';
    }

    public function editSupplier($supp_id)
    {
        //dd($supp_id);
        Supplier::where('id', $supp_id)->update([
            'name'                => $this->name, 'name_cn'                      => $this->name_cn, 'company_name' => $this->company_name,
            'extra_note'          => $this->extra_note, 'min_order_value'        => $this->min_order_value,
            'is_fully_prepared'   => $this->is_fully_prepared, 'is_tax_included' => $this->is_tax_included,
            'is_freight_included' => $this->is_freight_included, 'province'      => $this->province,
            'city'                => $this->city, 'street'                       => $this->street, 'full_address'  => $this->full_address,
            'contact_person'      => $this->contact_person, 'phone'              => $this->phone, 'mobile'         => $this->mobile,
            'email'               => $this->email, 'website'                     => $this->website,
        ]);
        $this->successMessage = 'Supplier Updated Successfully !!!';

    }
    public function setPrice($id)
    {
        Item::where('id', $this->id)->update([
            'is_rmb_special' => $this->is_rmb_special,
            'is_eur_special' => $this->is_eur_special,
        ]);

        $this->successMessage = 'Special price set successfully !!!';
    }

    public function getValues($par_id)
    {
        date_default_timezone_set('Europe/Berlin');
        //dd($par_id,$this->var_values);
        $myItem = Item::where('id', $this->id)->first();

        //dd( $myItem);
        
        $itemIds = DB::table('variation_values')
            ->join('items', 'variation_values.item_id', '=', 'items.id')
            ->join('parents', 'items.parent_id', '=', 'parents.id')
            ->where('parents.id', '=', $par_id)
            ->where('variation_values.value_de', '=', "{$this->var_values}")
            ->orWhere('variation_values.value_de_2', '=', "{$this->var_values}")
            ->orWhere('variation_values.value_de_3', '=', "{$this->var_values}")
            ->pluck('items.id')
            ->toArray();

        
        $shop_image = $myItem->photo;

        $eBay_image = substr($shop_image, 0, -5) . 'e.jpg';
        date_default_timezone_set('Europe/Berlin');
        if (! empty($itemIds)) {
            Item::whereIn('id', $itemIds)->update([

                'photo'         => $myItem->photo,
                'pix_path'      => $shop_image,
                'pix_path_eBay' => $eBay_image,
                'is_npr'        => 'N',
            ]);
        }
        $this->successMessage = 'Pictures applied successfully !!!';
    }
    public function applyPicParent($id)
    {
        //dd($id . ' Hi') ;
        date_default_timezone_set('Europe/Berlin');

        $myItem = Item::where('id', $this->id)->first();

        //dd( $myItem);
        //DB::enableQueryLog();
        $itemIds = DB::table('variation_values')
            ->join('items', 'variation_values.item_id', '=', 'items.id')
            ->join('parents', 'items.parent_id', '=', 'parents.id')
            ->where('parents.id', '=', $id)

            ->pluck('items.id')
            ->toArray();

        $shop_image = $myItem->photo;

        $eBay_image = substr($shop_image, 0, -5) . 'e.jpg';

        if (! empty($itemIds)) {
            Item::whereIn('id', $itemIds)->update([

                'photo'         => $myItem->photo,
                'pix_path'      => $shop_image,
                'pix_path_eBay' => $eBay_image,
                'is_npr'        => 'N',
            ]);
        }
        $this->successMessage = 'Pictures applied on Parent successfully !!!';

    }

    public function render()
    {
        // Check if $this->ean is set and not empty
        $eanProvided = ! empty($this->ean);

        // Build the query
        $query = DB::table('items')
            ->join('parents', 'parents.id', '=', 'items.parent_id')
            ->join('variation_values', 'items.id', '=', 'variation_values.item_id')
            ->join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_items.supplier_id')
            ->join('warehouse_items', 'warehouse_items.item_id', '=', 'items.id')
            ->join('tarics', 'items.taric_id', '=', 'tarics.id')
            ->join('categories', 'categories.id', '=', 'items.cat_id')

            ->select(
                'parents.*',
                'parents.name_de as de_name',
                'parents.name_de as en_name',
                'parents.id as par_id',
                'items.*',
                'variation_values.*',
                'suppliers.*',
                'warehouse_items.*',
                'supplier_items.*',
                'tarics.*',
                'categories.*',
                'supplier_items.id as supp_id',
                'items.id as item_id'
            );

        // check if ean comes from pictures
        if (preg_match('/^ean(\d+)-1\.jpg$/', $this->id, $matches)) {
            $ean = $matches[1]; // Extract the EAN from the parameter
                                //dd('hi', $ean);
            $query->where('items.ean', '=', $ean);
        } else {
            $query->where('items.id', '=', $this->id);
        }

        // Execute the query
        $itemDetail = $query->first();

        //dd($itemDetail);
        $this->item_name_de = $itemDetail->name_de;
        $this->item_name_en = $itemDetail->item_name_en;
        $this->item_name    = $itemDetail->item_name;
        $this->item_name_cn = $itemDetail->item_name_cn;

        $this->value_en = $itemDetail->value_en;
        $this->value_de = $itemDetail->value_de;

        $this->value_en_2 = $itemDetail->value_en_2;
        $this->value_en_3 = $itemDetail->value_en_3;

        $this->value_de_2 = $itemDetail->value_de_2;
        $this->value_de_3 = $itemDetail->value_de_3;

       
        // $this->taric_id    = $itemDetail->taric_id;

        $this->remark           = $itemDetail->remark;
        $this->is_no_auto_order = $itemDetail->is_no_auto_order;
        $this->is_active        = $itemDetail->is_active;

        $this->is_stock_item    = $itemDetail->is_stock_item;
        $this->msq              = $itemDetail->msq;
        $this->buffer           = $itemDetail->buffer;
        $this->weight           = $itemDetail->weight;
        $this->width            = $itemDetail->width;
        $this->height           = $itemDetail->height;
        $this->length           = $itemDetail->length;
        $this->foq              = $itemDetail->FOQ;
        $this->fsq              = $itemDetail->FSQ;
        $this->is_qty_dividable = $itemDetail->is_qty_dividable;
        $this->isbn             = $itemDetail->ISBN;
        $this->price_rmb        = $itemDetail->price_rmb;
        $this->many_components  = $itemDetail->many_components;
        $this->effort_rating    = $itemDetail->effort_rating;
        //$this->rmb_special_price = $itemDetail->rmb_special_price;
        $this->is_pu_item     = $itemDetail->is_pu_item;
        $this->is_meter_item  = $itemDetail->is_meter_item;
        // $this->is_po          = $itemDetail->is_po;
        $this->url            = $itemDetail->url;
        $this->oi             = $itemDetail->oi;
        $this->moq            = $itemDetail->moq;
        $this->name           = $itemDetail->name;
        $this->province       = $itemDetail->province;
        $this->full_address   = $itemDetail->full_address;
        $this->contact_person = $itemDetail->contact_person;
        // $this->is_SnSI        = $itemDetail->is_SnSI;
        $this->ItemID_DE      = $itemDetail->ItemID_DE;
        $this->item_no_de     = $itemDetail->item_no_de;
        $this->pix_path_eBay  = $itemDetail->pix_path_eBay;
        $this->pix_path       = $itemDetail->pix_path;
        $this->photo          = $itemDetail->photo;
        // $this->is_npr         = $itemDetail->is_npr;
        $this->npr_remark     = $itemDetail->npr_remark;
        $this->supp_cat       = $itemDetail->supp_cat;
        // $this->is_new         = $itemDetail->is_new;
        // $this->is_rmb_special = $itemDetail->is_rmb_special;
        // $this->is_eur_special = $itemDetail->is_eur_special;
        //$this->isActive       = $itemDetail->isActive;
        // $this->cat_id = $itemDetail->cat_id;
        $this->stock_qty = $itemDetail->stock_qty;
        $this->model     = $itemDetail->model;

        $tarics     = Taric::all();
        $categories = Category::pluck('name', 'id');
        //dd($cats);

        //$item = $this->ItemDetail->getItemDetial($this->itemId);
        //dd($item);
        return view('livewire.item-edits', compact('itemDetail', 'tarics', 'categories'));
    }

}
