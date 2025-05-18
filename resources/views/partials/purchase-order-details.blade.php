<style>
  .bg-warning-with-message::after,
  .bg-warning-with-url::after {
    content: "Information missing";
    display: inline-flex;
    color: red;
    font-size: 14px;
    padding: 4px;
  }
</style>

@php use Illuminate\Support\Str; @endphp

<section wire:key="purchase-details-{{ $itemOrder->master_id }}">
<h3 class="text-center text-xl font-semibold mb-4">Confirm item before purchase</h3>

<div class="overflow-x-auto">
  <table class="min-w-full table-auto border text-sm bg-gray-100 dark:bg-gray-800">
    <tbody>
      <tr class="bg-gray-200 dark:bg-gray-700">
        <th rowspan="2" class="px-2 py-1 text-center border">Identifiers</th>
        <th colspan="2" class="px-2 py-1 text-center border">Item_id</th>
        <th class="px-2 py-1 text-center border">ItemID_DE</th>
        <th class="px-2 py-1 text-center border">Master_id</th>
        <th class="px-2 py-1 text-center border">supplier_id</th>
        <th class="px-2 py-1 text-center border">Order_Id</th>
        <th class="px-2 py-1 text-center border">Order No.</th>
      </tr>

      <tr>
        <td colspan="2" class="px-2 py-1 text-center border">{{$itemOrder->item_id}}</td>
        <td class="px-2 py-1 text-center border">{{$itemOrder->ItemID_DE}}</td>
        <td class="px-2 py-1 text-center border">{{$itemOrder->master_id}}</td>
        <td class="px-2 py-1 font-semibold text-center border">{{$itemOrder->supplier_id}}</td>
        <td class="px-2 py-1 text-center border">{{$itemOrder->ID}}</td>
        <td class="px-2 py-1 text-center border">{{$itemOrder->order_no}}</td>
      </tr>

      <tr><th class="px-2 py-1 text-left border">EAN</th>
      <td colspan="7" class="px-2 py-1 text-left border">{{$itemOrder->ean}} </td></tr>
      <tr>
        <th class="px-2 py-1 text-left border">Name - CN</th>
      <td colspan="7" class="px-2 py-1 text-left border">{{$itemOrder->item_name}} - {{$itemOrder->item_name_cn}}</td></tr>
      <tr>
        <th class="px-2 py-1 text-left border">Sales Price EUR</th>
        <td class="px-2 py-1 text-left border">
          @php $EK_net=EK_net($itemOrder->price_rmb, $itemOrder->cat_id); @endphp
        </td>
        <td colspan="6" class="text-right border">
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-end sm:space-x-2 gap-2 sm:gap-0 px-2">
            <em><strong>NPR Remarks:</strong></em>
            <flux:input wire:model="npr_remark" rows="1" class="border rounded p-1 w-full sm:w-[450px] text-sm"></flux:input>
            <flux:button size="sm" icon='chat-bubble-oval-left-ellipsis' class=" bg-black! text-white! hover:bg-gray-600!" wire:click='setNprRemark({{$itemOrder->item_id}})'>Remark</flux:button>
          </div>
        </td>
      </tr>

      <tr>
        <td rowspan="7" colspan="6" class="text-center align-middle px-2 py-1 border">
          <a href="{{ asset('storage/'.$itemOrder->photo) }}" target="_blank">
            <img src="{{ asset('storage/'.$itemOrder->photo) }}" alt="item photo" class="mx-auto h-auto w-auto max-h-64 sm:max-h-72 max-w-full object-contain dark:invert" />
          </a>
        </td>
        <th class="px-2 py-1 text-left border">Weight</th>
        <td class="px-2 py-1 text-left border">{{$itemOrder->weight}}</td>
      </tr>
      <tr><th class="px-2 py-1 text-left border">Dimensions</th><td class="px-2 py-1 text-left border">{{$itemOrder->length}}X{{$itemOrder->width}}X{{$itemOrder->height}}</td></tr>
      <tr><th class="px-2 py-1 text-left border">Remark CN</th><td class="px-2 py-1 text-left border">{{$itemOrder->remark}}</td></tr>
      <tr><th class="px-2 py-1 text-left border">Order Qty</th><td class="px-2 py-1 text-left border">{{$itemOrder->qty}}</td></tr>
      <tr><th class="px-2 py-1 text-left border">Is PO?</th><td class="px-2 py-1 text-left border @if ($itemOrder->is_po == 'No' && ($itemOrder->url == null || $itemOrder->url == '')) bg-gray-100 dark:bg-gray-700 @endif">{{$itemOrder->is_po}}</td></tr>
      <tr><th class="px-2 py-1 text-left border">Purchase price</th><td class="px-2 py-1 text-left border @if ($itemOrder->price_rmb == null || $itemOrder->price_rmb == 0) bg-warning-with-message @endif">{{$itemOrder->price_rmb}}</td></tr>
      <tr>
        <th class="px-2 py-1 text-left border">Supplier</th>
        <td class="px-2 py-1 text-left border">
          <a href="{{$itemOrder->website}}" class="text-blue-600 underline dark:text-blue-400" target="_blank">
            {{$itemOrder->supplier_id}}<br>{{$itemOrder->name}}
          </a>
        </td>
      </tr>

      <tr>
        <td colspan="8" class="text-left border">
          <strong>Item URL:</strong>
          <a href="{{ $itemOrder->url }}" class="text-blue-600 underline dark:text-blue-400">
            {{ strlen($itemOrder->url) > 100 ? substr($itemOrder->url, 0, 100) . '...' : $itemOrder->url }}
          </a>
        </td>
      </tr>

      <tr>
        <td colspan="8" class="text-center space-x-2 py-3">
          <a href="/default/{{ $itemOrder->item_id }}" class="inline-block">
            <flux:button size="sm" icon='users' variant="primary">Suppliers</flux:button>
          </a>

          @if ($itemOrder->is_po == "No" && ($itemOrder->url == null || $itemOrder->url == ""))
            <flux:button size="sm" variant="defualt" wire:click="confirmPurchase({{$itemOrder->supp_item_id}})">Enter Missing Info</flux:button>
          @elseif ($itemOrder->price_rmb==null || $itemOrder->price_rmb==0)
            <flux:button size="sm" variant="defualt" wire:click="confirmPurchase({{$itemOrder->supp_item_id}})">Enter Missing Info</flux:button>
          @else
            <flux:button size="sm" icon='currency-dollar' class=" bg-fuchsia-700! hover:bg-fuchsia-600! text-white!" wire:click="purchase('{{$itemOrder->master_id}}', {{$itemOrder->item_id}},'{{ $itemOrder->item_name}}')" wire:confirm="Are you sure?\nhave you checked/confirmed everything?">
              Purchase
            </flux:button>
          @endif
        </td>
      </tr>
    </tbody>
  </table>
</div>
</section>
<script>
  window.addEventListener('scroll-to-top', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
