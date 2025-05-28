<div class="container mx-auto">
  <div class="grid grid-cols-1">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg">
      <div class="flex justify-between content-center">
        <div>
          <flux:button icon="backward" onclick="history.back()" class="bg-blue-800! text-white! hover:bg-blue-700!">
            Back
          </flux:button>
        </div>

        <div class="text-xl font-bold border-b border-gray-200 dark:border-gray-700 p-4">
          Item Details of:
          @if(empty($itemDetail->ItemID))
          <span class="text-red-500">No corresponding variation values in value table.</span>
          exit;
          @endif
          {{ $itemDetail->ItemID }} / {{ $itemDetail->item_name }}
        </div>
        <div>
          <flux:button href="{{ route('itemEdit',$itemDetail->ItemID) }}" wire:navigate class="float-end" variant='primary'>Edit
            Data</flux:button>
        </div>
      </div>
      <div class="p-4 space-y-6">

        <!-- Item Section -->
        <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
          <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Item
          </legend>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div><strong>EAN:</strong> {{ $itemDetail->ean }}</div>
            <div><strong>Item Name:</strong> {{ $itemDetail->item_name }}</div>
            <div><strong>Item_Name_CN:</strong> {{ $itemDetail->item_name_cn }}</div>
            <div><strong>Cat:</strong> {{ $itemDetail->cat_name }}</div>
            <div><strong>Model:</strong> {{ $itemDetail->model }}</div>
            <div><strong>Remark:</strong> {{ $itemDetail->remark }}</div>
            <div><strong>isActive?</strong> {{ $itemDetail->isActive }}</div>
          </div>
        </fieldset>

        <!-- Parent Section -->
        <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
          <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Parent
          </legend>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><strong>Parent_No_DE:</strong> {{ $itemDetail->parent_no_de }}</div>
            <div><strong>Parent_Name_DE:</strong> {{ $itemDetail->de_name }}</div>
            <div><strong>Parent_Name_EN:</strong> {{ $itemDetail->en_name }}</div>
            <div><strong>is_active:</strong> {{ $itemDetail->is_active }}</div>
          </div>
        </fieldset>
        <div class="flex justify justify-between">
          <!-- Special Item Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Is Special
              Item?</legend>
            <div class="grid grid-cols-2 gap-4">
              @php $EK_net=EK_net($itemDetail->price_rmb, $itemDetail->cat_id); @endphp
              <div><strong>EUR:</strong> @if ($itemDetail->is_eur_special == 'Y') hidden @else {{ $EK_net }} @endif
              </div>
              <div><strong>RMB:</strong> {{ $itemDetail->price_rmb }}</div>
              <div><strong>is_eur_special:</strong> {{ $itemDetail->is_eur_special }}</div>
              <div><strong>is_rmb_special:</strong> {{ $itemDetail->is_rmb_special }}</div>
            </div>
          </fieldset>

          <!-- Variations DE Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Variations
              & Values DE</legend>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <strong>Variations:</strong><br>
                {{ $itemDetail->var_de_1 }}<br>
                {{ $itemDetail->var_de_2 }}<br>
                {{ $itemDetail->var_de_3 }}
              </div>
              <div>
                <strong>Values:</strong><br>
                {{ $itemDetail->value_de }}<br>
                {{ $itemDetail->value_de_2 }}<br>
                {{ $itemDetail->value_de_3 }}
              </div>
            </div>
          </fieldset>

          <!-- Variations EN Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Variations
              & Values EN</legend>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <strong>Variations:</strong><br>
                {{ $itemDetail->var_en_1 }}<br>
                {{ $itemDetail->var_en_2 }}<br>
                {{ $itemDetail->var_en_3 }}
              </div>
              <div>
                <strong>Values:</strong><br>
                {{ $itemDetail->value_en }}<br>
                {{ $itemDetail->value_en_2 }}<br>
                {{ $itemDetail->value_en_3 }}
              </div>
            </div>
          </fieldset>
        </div>
        <div class="flex justify-between gap-2">
          <!-- Dimensions Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Dimensions
            </legend>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
              <div><strong>ISBN:</strong> {{ $itemDetail->ISBN }}</div>
              <div><strong>Weight:</strong> {{ $itemDetail->weight }}</div>
              <div><strong>Length:</strong> {{ $itemDetail->length }}</div>
              <div><strong>Width:</strong> {{ $itemDetail->width }}</div>
              <div><strong>Height:</strong> {{ $itemDetail->height }}</div>
            </div>
          </fieldset>

          <!-- Remaining sections (Others, Warehouse, Supplier, Supplier Item, etc.) can follow the same pattern -->
          <!-- Others Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Others
            </legend>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div><strong>Taric code:</strong> {{ $itemDetail->code }}</div>
              <div><strong>isQTYdiv:</strong> {{ $itemDetail->is_qty_dividable }}</div>
              <div><strong>MC:</strong> {{ $itemDetail->many_components }}</div>
              <div><strong>ER:</strong> {{ $itemDetail->effort_rating }}</div>
              <div><strong>isMeter:</strong> {{ $itemDetail->is_meter_item }}</div>
              <div><strong>isPU:</strong> {{ $itemDetail->is_pu_item }}</div>
              <div><strong>isNPR:</strong> {{ $itemDetail->is_npr }}</div>
              <div><strong>isNew:</strong> {{ $itemDetail->is_new }}</div>
            </div>
          </fieldset>
        </div>
        <!-- Warehouse Item Section -->
        <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
          <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Warehouse
            Item: {{ $itemDetail->id }}</legend>
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div><strong>ID DE:</strong> {{ $itemDetail->ItemID_DE }}</div>
            <div><strong>No DE:</strong> {{ $itemDetail->item_no_de }}</div>
            <div><strong>Name DE:</strong> {{ $itemDetail->item_name_de }}</div>
            <div><strong>Name EN:</strong> {{ $itemDetail->item_name_en }}</div>
            <div><strong>isActive:</strong> {{ $itemDetail->is_active }}</div>
            <div><strong>IsStock:</strong> {{ $itemDetail->is_stock_item }}</div>
            <div><strong>Qty:</strong> {{ $itemDetail->stock_qty }}</div>
            <div><strong>MSQ:</strong> {{ $itemDetail->msq }}</div>
            <div><strong>isNAO:</strong> {{ $itemDetail->is_no_auto_order }}</div>
            <div><strong>Buffer:</strong> {{ $itemDetail->buffer }}</div>
            <div><strong>isSnSI:</strong> {{ $itemDetail->is_SnSI }}</div>
          </div>
        </fieldset>
        <div class="flex justify-between  gap-2">
          <!-- Supplier Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Default
              Supplier: {{ $itemDetail->supplier_id }}</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div><strong>Supplier name:</strong> <a href="{{ $itemDetail->website }}" class="text-blue-600 "
                  target="_blank">{{ $itemDetail->name }}</a></div>
              <div><strong>Province:</strong> {{ $itemDetail->province }}</div>

              <div><strong>Contact person:</strong> {{ $itemDetail->contact_person }}</div>
              <div><strong>Address:</strong> {{ $itemDetail->full_address }}</div>
            </div>
          </fieldset>

          <!-- Supplier Item Section -->
          <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
            <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Supplier
              Item</legend>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div><strong>Price RMB:</strong> {{ $itemDetail->price_rmb }}</div>
              <div><strong>isPO:</strong> {{ $itemDetail->is_po }}</div>

              <div><strong>MOQ:</strong> {{ $itemDetail->moq }}</div>
              <div><strong>Interval:</strong> {{ $itemDetail->oi }}</div>
              <div><strong>Lead time:</strong> {{ $itemDetail->lead_time }}</div>
              <div><strong>Note_CN:</strong> {{ $itemDetail->note_cn }}</div>
              <div><strong>Item URL:</strong> <a href="{{ $itemDetail->url }}" class="text-blue-600 " target="_blank">{{
                  substr($itemDetail->url, 0, 80) }}</a></div>
            </div>
          </fieldset>
        </div>
        <!-- Item Picture Section -->
        <fieldset class="border border-gray-300 dark:border-gray-600 p-6">
          <legend class="text-lg font-bold px-2 -mt-4 bg-white dark:bg-gray-900 text-black dark:text-white">Item Picture
          </legend>
          @php
          $goodUrlShop = str_replace('\\storage\\', '\\', $itemDetail->pix_path);
          $goodUrleBay = str_replace('\\storage\\', '\\', $itemDetail->pix_path_eBay);
          @endphp
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><strong>Shop picture path:</strong><br>{{ $goodUrlShop }}</div>
            <div><strong>eBay pictures path:</strong><br>{{ $goodUrleBay }}</div>
            <div><strong>NPR Remarks:</strong><br>{{ $itemDetail->npr_remark }}</div>
            <div class="col-span-3 text-center content-center">
              <img src="{{ asset('storage/'.$itemDetail->photo) }}" class="max-w-full max-h-[600px] mx-auto" />
            </div>
             {{-- <div><strong>Picture name:</strong><br>{{ $itemDetail->photo }}</div> --}}
          </div>
        </fieldset>

      </div>
    </div>
  </div>
</div>