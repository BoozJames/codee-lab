<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List Inventory Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto p-6 bg-white border-b border-gray-200">
                    <nav class="flex flex-wrap mb-4">
                        <div class="ml-auto">
                            <a href="{{ route('inventory.create') }}" class="mb-2 py-2 px-4 bg-gray-300 rounded">Create Report</a>
                        </div>
                    </nav>
                    <div class="min-w-full align-middle">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead>
                                <tr>
                                    <!-- Table Headers -->
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date Created</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Prepared By</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date Prepared</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Verified By</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date Verified</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Checked By</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date Checked</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Noted By</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Date Noted</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">
                                        <span class="text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</span>
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                            @foreach ($inventory as $report)    
                            <tr class="bg-white">
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->created_at }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->prepared_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->date_prepared_by  }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->verified_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->date_verified_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->checked_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->date_checked_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->noted_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        {{ $report->date_noted_by }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">
                                        <a href="{{ route('inventory.show', $report->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                        <button type="button" onclick="confirmDelete('{{ $report->id }}')" class="text-red-500 hover:text-red-700">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="mt-2">
                            {{ $inventory->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- SweetAlert2 JavaScript -->
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this report!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Construct the form action URL dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('inventory.destroy', '') }}/' + id;

                // Add CSRF token input
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add method field for DELETE request
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });


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
