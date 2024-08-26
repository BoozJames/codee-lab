<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <nav class="flex flex-wrap mb-4">
                        <a href="#" onclick="resetFilters()" class="mr-4 mb-2 py-2 px-4 bg-gray-300 rounded">
                            Reset Filters
                        </a>
                        <div class="ml-auto">
                            <a href="{{ route('items.create') }}" class="mb-2 py-2 px-4 bg-gray-300 rounded">Create
                                item</a>
                        </div>
                    </nav>
                    <div class="min-w-full align-middle">
                        <div class="my-2 bg-white">
                            <div class="flex flex-wrap items-center justify-between">
                                <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap items-center">
                                    <div class="flex flex-wrap items-center ml-auto">
                                        <input type="text" name="search" placeholder="Search..."
                                            class="mr-2 px-4 py-2 border rounded">
                                        <button type="submit" class="bg-gray-300 px-4 py-2 rounded">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Image</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Name</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Description</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                                @foreach ($items as $item)
                                    <tr class="bg-white">
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                            <img src="{{ Storage::url($item->image) }}" alt="item_image" style="max-width: 100px; max-height: 100px;">
                                        </td>   
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                            {{ $item->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                            {{ $item->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                            <a href="{{ route('items.show', $item->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">Show</a>
                                            <a href="{{ route('items.edit', $item->id) }}" class="text-green-500 hover:text-green-700 mr-2">Edit</a>
                                            <form id="delete-form-{{ $item->id }}" action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="text-red-500 hover:text-red-700">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        {{ $items->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this item?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function resetFilters() {
        // Replace "items.index" with the appropriate route to reset filters
        window.location.href = "{{ route('items.index') }}";
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    });
</script>
