<?php
namespace App\Livewire;

use App\Models\Confirm;
use App\Models\Item;
use App\Models\Order_status as Status;
use App\Models\Supplier_item;
use App\Models\VarVal;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Controls')]
class Controls extends Component
{
    protected Client $client;

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function xRate(): array
    {
        return Cache::remember('xrate', now()->addHours(1), function () {
            $response = $this->client->get('https://api.exchangerate-api.com/v4/latest/EUR');
            $rates    = json_decode($response->getBody()->getContents(), true)['rates'];

            return $this->filterRates($rates);
        });
    }

    protected function filterRates(array $rates): array
    {
        return [
            'EUR' => $rates['EUR'] ?? 1,
            'USD' => $rates['USD'] ?? null,
            'RMB' => $rates['CNY'] ?? null,
        ];
    }

    public function render()
    {
        // $test = Supplier_item::all();
        // dd($test);
        return view('livewire.controls', [

            //'confirmed' => $this->getConfirmedCount(),
            'specialRmb_noValue' => $this->getCountSpecialRMB(),
            'specialEUR_novalue' => $this->getCountSpecialEUR(),
            'zeroRmb'            => $this->getZeroRmbCount(),
            'count_supp'         => $this->getMissingSuppliersCount(),
            'specialDim_novalue' => $this->getCountSpecialDim(),
            'countNpr'           => $this->getNprCount(),
            'rates'              => $this->xRate(),
            'unusedImages'       => $this->getImageDiffs()[0],
            'naClass'            => $this->getCountNaclass(),
            'nullPixs'           => $this->getNullPictures(),
            'parents'            => $this->getTaricMismatch(),
            'count_null_cargo'   => $this->getCountNullCargo(),
            'nullTaric'          => $this->getNullTaricIds(),
            'countNoEngVarValue' => $this->getMissingEnglishValuesCount(),
            'nullCat'            => $this->getNullCategoriesCount(),
            'parents_results'    => $this->getDuplicatePhotos(),
            'purchaseProblem'    => $this->getStatusCount('P_Problem'),
            'checkProblem'       => $this->getStatusCount('C_Problem'),
            'isPoNo'            => $this->getIsPoNo(),
            'isPoNull'            => $this->isPoNull(),
        ]);
    }

    protected function getTaricMismatch()
    {
        return Cache::remember('taric_mismatch', 600, function () {
            $query1 = DB::table('parents as p')
                ->leftJoin('items as t', 'p.id', '=', 't.parent_id')
                ->selectRaw("
                    p.id as parent_id, t.id as item_id, t.ean, t.item_name,
                    p.taric_id as parent_taric_id, p.name_en AS parent_name,
                    t.taric_id as items_taric_id,
                    CASE
                        WHEN t.taric_id IS NULL THEN 'Missing in items'
                        WHEN p.taric_id <> t.taric_id THEN 'Mismatched'
                    END AS status
                ")
                ->where('p.is_active', 'Y')
                ->whereNotNull('p.taric_id')
                ->where(fn($q) => $q->whereRaw('p.taric_id <> t.taric_id')->orWhereNull('t.taric_id'));

            $query2 = DB::table('items as t')
                ->leftJoin('parents as p', 'p.id', '=', 't.parent_id')
                ->selectRaw("
                    t.parent_id as parent_id, t.id as item_id, t.ean, t.item_name,
                    p.taric_id as parent_taric_id, NULL AS parent_name,
                    t.taric_id as items_taric_id,
                    'Missing in parents' AS status
                ")
                ->whereNull('p.id');

            return $query1->union($query2)->limit(100)->get(); // LIMIT added for memory
        });
    }

    protected function getNullPictures()
    {
        // return Cache::remember('null_pictures', 600, function () {
        $noPics = Item::select('id', 'photo')
            ->where('isActive', 'Y')
            ->where(function ($query) {
                $query->whereNull('photo')
                    ->orWhereIn('photo', ['DummyPicture.jpg', '']);
            })
            ->get();

        // });
        return $noPics;
    }

    protected function getCountSpecialRMB()
    {
        return Cache::remember('specialRmb_count', 600, function () {
            return Item::join('order_statuses as os', 'os.ItemID_DE', '=', 'items.ItemID_DE')
                ->where('items.is_rmb_special', 'Y')
                ->whereNull('os.rmb_special_price')
                ->count();
        });
    }

    protected function getCountSpecialEUR()
    {
        return Cache::remember('specialEUR_count', 600, function () {
            return Item::join('order_statuses as os', 'os.ItemID_DE', '=', 'items.ItemID_DE')

                ->where('items.is_eur_special', 'Y')
                ->whereNull('os.eur_special_price')
                ->count();
        });
    }

    protected function getCountSpecialDim()
    {
        //return Cache::remember('specialDim_count', 600, function () {

        $count = Item::where('items.is_dimension_special', 'Y')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('dimensions')
                    ->whereColumn('dimensions.item_id', 'items.id')->distinct();
            })
            ->count();

        return $count;
        //});
    }

    protected function getImageDiffs(): array
    {
        return Cache::remember('image_diffs', 600, function () {
            $images = Item::whereNotNull('photo')
                ->where('photo', '!=', '')
                ->pluck('photo')
                ->map(fn($img) => basename($img)) // Normalize to just filenames
                ->toArray();

            $folder        = public_path('storage');
            $server_images = @scandir($folder) ?: [];
            $server_images = array_diff($server_images, ['.', '..']);

            $unusedImages = array_values(array_diff($server_images, $images));

            // dd( $unusedImages);
            return [$unusedImages];
        });
    }

    protected function getConfirmedCount()
    {
        return Cache::remember('confirmed_count', 600, function () {
            return Confirm::join('supplier_items as si', 'si.id', '=', 'confirms.supp_items_id')
                ->join('items as i', 'i.id', '=', 'si.item_id')
                ->where('si.is_default', 'Y')
                ->where('i.isActive', 'Y')
                ->count();
        });
    }

    protected function getNprCount()
    {
        return Cache::remember('npr_count', 600, fn() => Item::where('is_npr', 'Y')->where('isActive', 'Y')->count());
    }

    protected function getMissingEnglishValuesCount()
    {
        return Cache::remember('missing_en_values', 600, function () {
            return VarVal::where(fn($q) => $q->whereNull('value_en')->orWhere('value_en', ''))
                ->whereHas('values', fn($q) => $q->where('isActive', 'Y'))
                ->whereHas('values.parents', fn($q) => $q->where('is_var_unilingual', 'N'))
                ->count();
        });
    }

    protected function getZeroRmbCount()
    {
        return Cache::remember('zero_rmb_count', 600, function () {
            return Supplier_item::where(fn($q) => $q->whereNull('price_rmb')->orWhere('price_rmb', '0'))
                ->whereHas('item', fn($q) => $q->where('isActive', 'Y')->where('is_rmb_special', 'N'))
                ->count();
        });
    }

    protected function getMissingSuppliersCount()
    {
        return Cache::remember('missing_supplier_count', 600, function () {
            return Supplier_item::where(fn($q) => $q->whereNull('supplier_id')->orWhere('supplier_id', '0'))
                ->whereHas('item', fn($q) => $q->where('isActive', 'Y'))
                ->count();
        });
    }

    protected function getNullTaricIds()
    {
        return Cache::remember('null_taric_ids', 600, function () {
            return Item::whereNull('taric_id')
                ->where('isActive', 'Y')
                ->pluck('id');
        });
    }

    protected function getNullCategoriesCount()
    {
        return Cache::remember('null_category_count', 600, function () {
            return Item::where('isActive', 'Y')->whereNull('cat_id')->count();
        });
    }

    protected function getStatusCount(string $status)
    {
        return Cache::remember("status_count_$status", 600, fn() => Status::where('status', $status)->count());
    }

    protected function getCountNullCargo()
    {
        return Cache::remember('count_null_cargo', 600, fn() => Status::whereNull('cargo_id')->count());
    }

    protected function getCountNaclass()
    {
        return $naClass = Item::where('isActive', 'Y')->whereRaw("ShippingClass(items.weight, items.length,items.width, items.height) = 'Na'");
    }

    protected function getDuplicatePhotos()
    {
        return Cache::remember('duplicate_photos', 600, function () {
            return DB::table('items')
                ->select('photo', DB::raw('COUNT(DISTINCT parent_no_de) AS parent_no_de_count'))
                ->whereNotNull('photo')
                ->where('photo', '!=', '')
                ->where('photo', '!=', 'DummyPicture.jpg') // Exclude DummyPicture.jpg
                ->groupBy('photo')
                ->having('parent_no_de_count', '>', 1)
                ->limit(100)
                ->get();
        });
    }

    protected function getIsPoNo()
    {
        return Cache::remember('IsPoNo', 600, function () {
            return Supplier_item::where('is_po', 'No')->whereNull('url')->count();
        });

    }

    protected function isPoNull()
    {
        return Cache::remember('IsPoNull', 600, function () {
            return Supplier_item::whereNull('is_po')->count();
        });
    }
}
