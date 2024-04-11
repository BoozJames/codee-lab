<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Requests;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $requestQuery = Requests::query();

        // if ($request->filled('role')) {
        //     $requestQuery->where('role', $request->role);
        // }

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

        return view('requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $request = Requests::findOrFail($id);
        return view('requests.edit', compact('request'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $validatedData = $request->validate([
            'reference_number' => 'required|string',
            'items' => 'required|array',
            'requestors' => 'required|array',
            'item_variants' => 'array',
            // Add more validation rules as needed
        ]);

        // Find the request by ID
        $request = Requests::findOrFail($id);

        // Update request with validated data
        $request->update($validatedData);

        // Redirect with success message
        return redirect()->route('requests.index')->with('success', 'Request updated successfully!');
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

    public function showCreateForm()
    {
        // Retrieve all items
        $items = Items::with('itemVariants')->get();

        // Retrieve item variants from the request
        $requestItemVariants = Requests::pluck('item_variants')->flatten()->unique()->toArray();

        // Filter out items whose variants are already in the request
        $items = $items->reject(function ($item) use ($requestItemVariants) {
            foreach ($item->itemVariants as $variant) {
                if (in_array($variant->id, $requestItemVariants)) {
                    return true; // Exclude this item
                }
            }
            return false; // Include this item
        });

        // Pass the filtered items to the view
        return view('create-request-form', compact('items'));
    }

    public function showLogList()
    {
        $requests = Requests::all();

        if ($requests) {
            // Request found, display details
            return view('log-list-form', compact('requests'));
        } else {
            // Request not found
            return back()->with('error', 'Request not found.');
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
