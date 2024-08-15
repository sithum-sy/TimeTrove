<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Show the client panel with their service requests and categories.
     *
     * @return \Illuminate\View\View
     */
    public function panel()
    {
        // Retrieve service requests for the authenticated client, including related service categories
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->get();

        // Retrieve all service categories
        $serviceCategories = ServiceCategory::all();

        // Pass the retrieved data to the view
        return view('client.panel', [
            'serviceRequests' => $serviceRequests,
            'serviceCategories' => $serviceCategories
        ]);
    }

    /**
     * Handle the creation of a new service request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addRequest(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        // Add the authenticated client's ID and default status to the validated data
        $validatedData['client_id'] = Auth::id();
        $validatedData['status'] = 'pending';

        // If a picture is uploaded, store it and add its path to the data
        if ($request->hasFile('request_picture')) {
            $path = $request->file('request_picture')->store('request_pictures', 'public');
            $validatedData['request_picture'] = $path;
        }

        // Create a new service request record
        ServiceRequest::create($validatedData);

        // Redirect back to the client panel with a success message
        return redirect()->route('client.panel')->with('success', 'Service request added successfully.');
    }

    /**
     * Handle the update of an existing service request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRequest(Request $request, $id)
    {
        // Find the service request by ID, or fail if not found
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Authorize the user to update the service request
        $this->authorize('update', $serviceRequest);

        // Validate the request data
        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        // If a new picture is uploaded, store it and update the path
        if ($request->hasFile('request_picture')) {
            $path = $request->file('request_picture')->store('request_pictures', 'public');
            $validatedData['request_picture'] = $path;
        }

        // Update the existing service request with the new data
        $serviceRequest->update($validatedData);

        // Redirect back to the client panel with a success message
        return redirect()->route('client.panel')->with('success', 'Service request updated successfully.');
    }

    /**
     * Handle the deletion of an existing service request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteRequest($id)
    {
        // Find the service request by ID, or fail if not found
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Authorize the user to delete the service request
        $this->authorize('delete', $serviceRequest);

        // Delete the service request record
        $serviceRequest->delete();

        // Redirect back to the client panel with a success message
        return redirect()->route('client.panel')->with('success', 'Service request deleted successfully.');
    }
}
