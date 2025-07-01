<div class="container mx-auto">
    <div class="flex justify-between mt-3">
        <div>
            <flux:modal.trigger name="myModal">
                <flux:button wire:click="cancel" icon="plus-circle" class="bg-blue-800! text-white! hover:bg-blue-700!">
                    New {{ Str::before($title, 's') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <flux:text color="blue" class="text-base">{{ $title }}</flux:text>
        <div class="">

            <flux:switch wire:click="$toggle('active')" label="Active / InActive" />

        </div>

        <div>
            <flux:input class="md:w-50" wire:model.live="search" icon="magnifying-glass"
                placeholder="Search {{ $title }}" :disabled="$isUpdate && !$enableEdit" />
        </div>
    </div>

    <flux:modal name="myModal" class="!w-[50rem] max-w-none">
        <div class="space-y-6">
            <flux:heading size="lg">Team Member Details</flux:heading>
            @if ($isUpdate)
            <div class="flex items-center gap-10 mt-2.5">
                <flux:switch wire:click="$toggle('enableEdit')" label="Enable edit" />
            </div>
            @endif
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-2">
                    <flux:select wire:model="status" placeholder="Active / Inactive"
                        :disabled="$isUpdate && !$enableEdit">
                        <flux:select.option>Active</flux:flux:select.option>
                        <flux:select.option>InActive</flux:flux:select.option>
                    </flux:select>
                </div>
                {{-- <div class="grid grid-cols-4 gap-4"> --}}
                    <div class="col-span-2">
                        <flux:input wire:model="contact_number" label="Emergency contact number"
                            placeholder="Emergency contact number" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="first_name" label="First Name" placeholder="First Name" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="last_name" label="Last Name" placeholder="Last Name" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="middle_name" label="Middle Name" placeholder="Middle Name"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="email_private" label="Private Email" placeholder="Email" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="email_business" label="Business Email" placeholder="Business Email"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="mobile" label="Mobile" placeholder="Mobile Number" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="phone" label="Phone" placeholder="Phone Number" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>

                    <div class="col-span-2">
                        <flux:dropdown :disabled="$isUpdate && !$enableEdit"> Gender:
                            <flux:button icon:trailing="chevron-down">
                                {{ $gender ? ucfirst($gender) : 'Select Gender' }}
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item wire:click="$set('gender', 'M')">Male</flux:menu.item>
                                <flux:menu.item wire:click="$set('gender', 'F')">Female</flux:menu.item>
                                <flux:menu.item wire:click="$set('gender', 'O')">Others</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <div class="col-span-2">
                        <flux:dropdown :disabled="$isUpdate && !$enableEdit"> Marital status
                            <flux:button icon:trailing="chevron-down">
                                {{ $marital_status ? ucwords(str_replace('_', ' ', $marital_status)) : 'Select Marital
                                Status' }}
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item wire:click="$set('marital_status', 'Single')">Single</flux:menu.item>
                                <flux:menu.item wire:click="$set('marital_status', 'in_relationship')">In Relationship
                                </flux:menu.item>
                                <flux:menu.item wire:click="$set('marital_status', 'Married')">Married</flux:menu.item>
                                <flux:menu.item wire:click="$set('marital_status', 'Divorced')">Divorced
                                </flux:menu.item>
                                <flux:menu.item wire:click="$set('marital_status', 'Widowed')">Widowed</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <div class="col-span-2">
                        <flux:input type='date' wire:model="dob" label="Date of Birth" placeholder="YYYY-MM-DD"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input wire:model="designation" label="Designation" placeholder="Designation"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input type='date' wire:model="join_date" label="Join Date" placeholder="YYYY-MM-DD"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input type='text' wire:model="house_no" label="House Number" placeholder="House No"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input type='text' wire:model="street" label="Street" placeholder="Street"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input type='text' wire:model="city" label="City" placeholder="City"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-2">
                        <flux:input type='text' wire:model="country" label="Country" placeholder="Country"
                            class="w-full" :disabled="$isUpdate && !$enableEdit" />
                    </div>
                    <div class="col-span-4">
                        <flux:input wire:model="note" label="Note" placeholder="Additional Notes" class="w-full"
                            :disabled="$isUpdate && !$enableEdit" />
                    </div>
                </div>

                <div class="flex">
                    <flux:spacer />
                    <flux:button type="button" variant="ghost" icon="x-circle" wire:click="cancel"
                        x-on:click="Flux.modal('myModal').close()">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" wire:click="{{ $isUpdate ? 'update' : 'save' }}" icon="plus-circle"
                        variant="primary">
                        {{ $isUpdate ? 'Save changes' : 'Save' }}
                    </flux:button>
                </div>
            </div>
    </flux:modal>

    @if (session('success'))
    <div class="mt-2 text-center">
        <flux:callout variant="success" icon="check-circle" heading="{{ session('success') }}" />
    </div>
    @endif

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mt-2.5">
            <thead class="sticky top-0 bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Mobile</th>
                    <th class="px-6 py-3">Emergency contact</th>
                    <th class="px-6 py-3">Designation</th>
                    <th class="px-6 py-3">Join date</th>

                    <th class="px-6 py-3">Address</th>
                    <th class="px-6 py-3">Partner name</th>
                    <th class="px-6 py-3">Partner DOB</th>
                    <th colspan="2" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($Teams as $team)
                <tr wire:key="{{ $team->id }}"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <td class="px-2 py-1">{{ $team->id }}</td>
                    <td class="px-2 py-1">{{ $team->first_name }} {{ $team->last_name }}</td>
                    <td class="px-2 py-1">{{ $team->email_business }}</td>
                    <td class="px-2 py-1">{{ $team->mobile }}</td>
                    <td class="px-2 py-1">{{ $team->contact_number }}</td>
                    <td class="px-2 py-1">{{ $team->designation }}</td>
                    <td class="px-2 py-1">{{ $team->join_date }}</td>
                    <td class="px-2 py-1">{{ $team->house_no }}, {{ $team->street }}, {{ $team->city }}, {{
                        $team->countrey }}</td>
                    <td class="px-2 py-1">{{ $team->contact_person }}</td>
                    <td class="px-2 py-1">{{ $team->contact_dob }}</td>
                    <td class="px-2 py-1">
                        <flux:button variant="primary" icon="pencil-square" wire:click="edit({{ $team->id }})"
                            size="sm">Edit</flux:button>
                    </td>
                    {{-- <td class="px-2 py-1">
                        <flux:button variant="danger" icon="minus-circle" wire:click="delete({{ $team->id }})"
                            wire:confirm="Are you sure deleting this record?" size="sm">Delete</flux:button>
                    </td> --}}
                </tr>


                @empty
                <tr>
                    <td colspan="7" class="px-2 py-1 text-center font-medium text-gray-900 dark:text-white">
                        No records found
                    </td>
                </tr>
                @endforelse
                @if($active ==0)
                <tr>
                    <td colspan="7" align="center">
                        <em>May they all be filled with loving-kindness.<br>
                            May they all be free from suffering.<br>
                            May theý all be well.<br>
                            May they all be at peace.<br>
                            May they all be joyful.<br>
                        </em>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="container mx-auto w-100">{{ $Teams->links() }}</div>
    </div>
</div>