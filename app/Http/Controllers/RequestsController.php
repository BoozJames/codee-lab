<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\ItemVariants;
use App\Models\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $requestQuery = Requests::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $requestQuery->where(function ($query) use ($search) {
                $query->where('reference_number', 'like', "%$search%")
                    ->orWhere('items', 'like', "%$search%")
                    ->orWhere('requestors', 'like', "%$search%");
            });
        }

        $requests = $requestQuery->paginate(5);

        return view('requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            // 'reference_number' => 'required|string',
            'items' => 'required|array',
            'requestors' => 'required|array',
            // Add more validation rules as needed
        ]);

        // Create request
        Requests::create($validatedData);

        // Redirect with success message
        return redirect()->route('requests.index')->with('success', 'Request created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $request = Requests::findOrFail($id);
        $itemIds = array_column(array_filter($request->items, fn ($item) => empty($item['options']['requestor'])), 'id');
        $itemVariants = ItemVariants::whereIn('item_id', $itemIds)->get();
        Log::info('Item Variants Available:', [$itemVariants]);
        $savedItemVariants = json_decode($request->item_variants, true);

        return view('requests.show', compact('request', 'itemVariants', 'savedItemVariants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the request by ID
        $request = Requests::findOrFail($id);

        // Filter the item IDs excluding requestor items
        $itemIds = array_column(array_filter($request->items, fn ($item) => empty($item['options']['requestor'])), 'id');

        Log::info($itemIds);

        // Fetch item variants based on item IDs
        $itemVariants = ItemVariants::whereIn('item_id', $itemIds)->get();
        Log::info('Item Variants Available:', [$itemVariants]);

        // Decode the saved item variants
        $savedItemVariants = json_decode($request->item_variants, true);

        Log::info('Request data:', [
            'items' => $request->items,
            'item_variants' => $request->item_variants,
        ]);

        Log::info('Item Variants', [
            'item_variants' => $request->item_variants,
        ]);

        // Pass the necessary data to the view
        return view('requests.edit', compact('request', 'itemVariants', 'savedItemVariants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Log the incoming request data
        Log::info('Request data:', $request->all());

        try {
            // Find the request by ID
            $requestData = Requests::findOrFail($id);
            Log::info('Found request:', $requestData->toArray());

            // Extract item_id values from item_variants
            $itemVariantIds = $request->input('item_variants', []);
            Log::info('Item Variant IDs:', $itemVariantIds);

            // Ensure item variants are not empty
            if (!empty($itemVariantIds)) {
                $itemVariantData = ItemVariants::whereIn('id', $itemVariantIds)->pluck('item_id')->toArray();
            } else {
                $itemVariantData = [];
            }
            Log::info('Item Variant Data (item_id):', $itemVariantData);

            // Update request with item_variant data
            $requestData->item_variants = json_encode($itemVariantData); // Save item_id values as JSON

            // Update the 'completed' field
            $requestData->completed = $request->has('completed');
            Log::info('Request data before save:', $requestData->toArray());

            // Save the updated request data
            $requestData->save();

            // Log success
            Log::info('Request updated successfully.', [
                'request_id' => $id,
                'item_variants' => $requestData->item_variants,
                'completed' => $requestData->completed,
            ]);

            // Redirect with success message
            return redirect()->route('requests.index')->with('success', 'Request updated successfully!');
        } catch (\Exception $e) {
            // Log error
            Log::error('Failed to update request.', [
                'request_id' => $id,
                'error' => $e->getMessage(),
            ]);

            // Redirect back with error message
            return back()->withInput()->withErrors(['error' => 'Failed to update request. Please try again.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $requests = Requests::findOrFail($id);
        $requests->delete();

        return redirect()->route('requests.index')->with('success', 'User deleted successfully.');
    }

    public function showCreateForm(Request $request)
    {
        // Start building the query
        $itemsQuery = Items::with('itemVariants')
                        ->whereNull('deleted_at')  // Exclude soft deleted items
                        ->orderBy('name');

        // Apply search filters if the search input is present
        if ($request->filled('search')) {
            $search = $request->input('search');
            $itemsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhereHas('itemVariants', function ($variantQuery) use ($search) {
                        $variantQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        // Retrieve the filtered items
        $items = $itemsQuery->get();

        // Log the initial items count
        Log::info('Total Items Retrieved:', [$items->count()]);

        // Pass the items and their available counts to the view
        $items->each(function ($item) {
            $item->available_count = $item->availableItemVariantsCount();
            Log::info('Item ID: ' . $item->id . ' - Available Count: ' . $item->available_count);
        });

        return view('create-request-form', compact('items'));
    }

    public function showLogList()
    {
        // Fetch only the not completed request
        $requests = Requests::where('completed', false)->get();

        if ($requests->isNotEmpty()) {
            // Requests found, display details
            return view('log-list-form', compact('requests'));
        } else {
            // No completed requests found
            return back()->with('error', 'No completed requests found.');
        }
    }

    /**
     * Show the form for tracking a request.
     */
    public function showTrackForm()
    {
        $requests = Requests::all();

        if ($requests) {
            // Request found, display details
            return view('track-request-form', compact('requests'));
        } else {
            // Request not found
            return back()->with('error', 'Request not found.');
        }
    }

    /**
     * Track a request by its reference number.
     */
    public function trackRequest(Request $request)
    {
        $referenceNumber = $request->input('reference_number');

        $request = Requests::where('reference_number', $referenceNumber)->first();

        if ($request) {
            // Request found, display details
            return view('track-request-form', compact('request'));
        } else {
            // Request not found
            return back()->with('error', 'Request not found.');
        }
    }
}
