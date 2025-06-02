<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-2">
        <div>
            <flux:label for="customer_type">Select Customer Type: </flux:label>
            <flux:select name="customer_type_id" id="customer_type" wire:model.defer="customer.customer_type_id">
                <option value="">-- Select Customer Type --</option>
                <option value="1">GT-Warehouse</option>
                <option value="2">Shared Customer</option>
                <option value="3">Normal Customer</option>
                <option value="4">External Customer</option>
                <option value="5">Express Customers</option>
            </flux:select>
        </div>

        <flux:input name="customer_company_name" label="Company Name:" wire:model.defer="customer.customer_company_name" />
        <flux:input name="phone" label="Phone No:" wire:model.defer="customer.phone" />
        <flux:input name="tax_no" label="Tax No:" wire:model.defer="customer.tax_no" />

        <flux:input name="email" label="Company Email:" wire:model.defer="customer.email" class="md:col-span-2" />
        <flux:input name="website" label="Website:" wire:model.defer="customer.website" class="md:col-span-2" />

        <flux:input name="contact_first_name" label="Contact person name:" wire:model.defer="customer.contact_first_name" />
        <flux:input name="contact_phone" label="Contact Phone:" wire:model.defer="customer.contact_phone" />
        <flux:input name="contact_mobile" label="Contact Mobile:" wire:model.defer="customer.contact_mobile" />
        <flux:input name="contact_email" label="Contact Email:" wire:model.defer="customer.contact_email" />

        <div class="md:col-span-4 text-lg font-semibold dark:text-white">BILL TO:</div>

        <flux:input name="country" label="Country:" wire:model.defer="customer.country" />
        <flux:input name="city" label="City:" wire:model.defer="customer.city" />
        <flux:input name="postal_code" label="Postal Code:" wire:model.defer="customer.postal_code" />
        <flux:input name="address_line1" label="Full Address:" wire:model.defer="customer.address_line1" class="md:col-span-1 lg:col-span-2" />

        <div class="md:col-span-4 text-lg font-semibold dark:text-white">SHIP TO:</div>

        <flux:input name="delivery_country" label="Delivery Country:" wire:model.defer="customer.delivery_country" />
        <flux:input name="delivery_city" label="Delivery City:" wire:model.defer="customer.delivery_city" />
        <flux:input name="delivery_postal_code" label="Delivery Postal Code:" wire:model.defer="customer.delivery_postal_code" />
        <flux:input name="delivery_address_line1" label="Delivery Full Address:" wire:model.defer="customer.delivery_address_line1" class="md:col-span-1 lg:col-span-2" />

        <flux:input name="delivery_company_name" label="Delivery Company Name:" wire:model.defer="customer.delivery_company_name" />
        <flux:input name="delivery_contact_person" label="Delivery Contact Person:" wire:model.defer="customer.delivery_contact_person" />
        <flux:input name="delivery_contact_phone" label="Delivery Contact Phone:" wire:model.defer="customer.delivery_contact_phone" />

        <flux:input name="remark" label="Remarks:" wire:model.defer="customer.remark" class="md:col-span-2" />

        <div class="md:col-span-4 flex justify-between items-center pt-4">
            <flux:button type="submit" variant="primary" size="sm">Save</flux:button>
            <flux:button id="close" type="button" size="sm"
             @click="Flux.modal('customerEditModal').close()">Cancel</flux:button>
        </div>
    </div>
</form>