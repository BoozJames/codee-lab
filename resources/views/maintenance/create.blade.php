<style>
    #create_btn:disabled {
    background-color: #dddddd; /* Change the background color */
    color: #999999; /* Change the text color */
    cursor: not-allowed; /* Change the cursor */
}
</style>

<div id="loading-spinner" class="hidden fixed top-0 left-0 w-full h-full bg-gray-900 opacity-50 flex justify-center items-center">
    <div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-50 h-12 w-12"></div>
    <p class="text-white ml-2">Loading...</p>
</div>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Maintenance Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                <div class="p-6 bg-white border-b border-gray-200">
                <nav class="flex flex-wrap mb-4">
                    <div class="ml-auto">
                        <a href="{{ route('maintenance.index') }}" class="mb-2 py-2 px-4 bg-gray-300 rounded">Back</a>
                    </div>
                </nav>
                <form method="POST" action="{{ route('maintenance.store') }}" >
                        @csrf
                        @method('POST')

                        <div class="flex mb-4">
                            <div class="mr-4 w-1/3">
                                <label for="conducted_by" class="block text-sm font-medium text-gray-700">Conducted By</label>
                                <input required type="text" name="conducted_by" id="conducted_by"
                                    value="{{ Auth::user()->name }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mr-4 w-1/3">
                                <label for="year" class="block text-sm font-medium text-gray-700">Verified By</label>
                                <input required type="text" name="verified_by" id="verified_by"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <div class="w-1/3">
                                <label for="verified_by" class="block text-sm font-medium text-gray-700">Year</label>
                                <input required type="number" name="year" id="year" value="{{ date('Y') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                            <select 
                                name="category_id"
                                id="category_id" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                                <option value=""></option>
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- loop item variants here -->
                        <div id="item_variants_container"></div>
                        
                        <div class="flex items-center justify-end">
                            <button type="submit"
                                data-swal-trigger
                                id="create_btn"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                <x-swal title="Confirm Action" text="Save Maintenance Report?" icon="info" confirmButtonText="Save" cancelButtonText="Cancel" />
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('category_id').addEventListener('change', function() {
            var categoryId = this.value;
            var itemVariantsContainer = document.getElementById('item_variants_container');
            // Clear existing rows
            itemVariantsContainer.innerHTML = '';
            // Fetch item variants based on the selected category, show loading spinner
            document.getElementById('loading-spinner').classList.remove('hidden');
            fetch("{{ route('maintenance.getvariant', ':id') }}".replace(':id', categoryId))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if data array is empty
                    if (data.length === 0) {
                        // If no records found, display a message
                        var message = document.createElement('p');
                        var message = document.createElement('div');
                        message.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 mb-3 px-4 py-3 rounded relative'; // Apply Tailwind CSS classes for a highlighted warning message
                        message.textContent = 'No records found.';
                        document.getElementById('create_btn').disabled = true;
                        setTimeout(function() {
                            document.getElementById('loading-spinner').classList.add('hidden');
                            itemVariantsContainer.appendChild(message);

                        }, 500);
                        return;
                    }

                    
                    document.getElementById('create_btn').disabled = false;
                    // Create rows for each item variant
                    data.forEach(itemVariant => {
                        var row = document.createElement('div');
                        row.classList.add('flex', 'mb-4');

                        var itemIdInput = document.createElement('input');
                        itemIdInput.type = 'hidden';
                        itemIdInput.name = 'item_id[]';
                        itemIdInput.value = itemVariant.item_id;

                        var variantIdInput = document.createElement('input');
                        variantIdInput.type = 'hidden';
                        variantIdInput.name = 'variant_id[]';
                        variantIdInput.value = itemVariant.id;

                        var equipmentLabelInput = document.createElement('input');
                        equipmentLabelInput.type = 'hidden';
                        equipmentLabelInput.name = 'equipment_label[]';
                        equipmentLabelInput.value = itemVariant.equipment_label;

                        var equipmentLabelInput = createInputElement('text', 'equipment_label[]', itemVariant.equipment_label, 'Equipment Label');
                        var statusInput = createSelectElement('status[]', ['Newly Acquired', 'Functional', 'Non-Functional', 'For Condemn', 'Defective', 'Obsolete'], 'Functional');
                        var remarksInput = createInputElement('text', 'remarks[]', '', 'Remarks');
                        var correctiveActionInput = createInputElement('text', 'corrective_action[]', '', 'Corrective Action');

                        row.appendChild(itemIdInput);
                        row.appendChild(variantIdInput);
                        row.appendChild(equipmentLabelInput);
                        row.appendChild(statusInput);
                        row.appendChild(remarksInput);
                        row.appendChild(correctiveActionInput);

                        itemVariantsContainer.appendChild(row);
                    });

                    setTimeout(function() {
                        document.getElementById('loading-spinner').classList.add('hidden');
                    }, 1000);
                })
                .catch(error => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    console.error('Error:', error);
                });
        });
    });

    // Function to create input element
    function createInputElement(type, name, value, placeholder) {
        var input = document.createElement('input');
        input.type = type;
        input.name = name;
        input.value = value  || '';
        input.placeholder = placeholder || 'Name';
        input.classList.add('mr-4', 'w-1/3', 'rounded-md', 'border-gray-300', 'shadow-sm', 'focus:border-indigo-300', 'focus:ring', 'focus:ring-indigo-200', 'focus:ring-opacity-50');
        return input;
    }

    // Function to create select element
    function createSelectElement(name, options, selectedValue) {
        var select = document.createElement('select');
        select.name = name;
        select.classList.add('mr-4', 'w-1/3', 'rounded-md', 'border-gray-300', 'shadow-sm', 'focus:border-indigo-300', 'focus:ring', 'focus:ring-indigo-200', 'focus:ring-opacity-50');
        
        options.forEach(optionValue => {
            var option = document.createElement('option');
            option.value = option.textContent = optionValue;
            if (selectedValue === optionValue) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        return select;
    }
</script>