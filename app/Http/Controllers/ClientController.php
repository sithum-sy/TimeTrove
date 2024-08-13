<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function panel()
    {
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->get();
        $serviceCategories = ServiceCategory::all();
        // dd($serviceRequests, $serviceCategories);
        return view('client.panel', [
            'serviceRequests' => $serviceRequests,
            'serviceCategories' => $serviceCategories
        ]);
    }


    public function addRequest(Request $request)
    {
        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        $validatedData['client_id'] = Auth::id();
        $validatedData['status'] = 'pending';

        if ($request->hasFile('request_picture')) {
            $path = $request->file('request_picture')->store('request_pictures', 'public');
            $validatedData['request_picture'] = $path;
        }

        ServiceRequest::create($validatedData);

        return redirect()->route('client.panel')->with('success', 'Service request added successfully.');
    }

    public function updateRequest(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $this->authorize('update', $serviceRequest);

        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('request_picture')) {
            $path = $request->file('request_picture')->store('request_pictures', 'public');
            $validatedData['request_picture'] = $path;
        }

        $serviceRequest->update($validatedData);

        return redirect()->route('client.panel')->with('success', 'Service request updated successfully.');
    }

    public function deleteRequest($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $this->authorize('delete', $serviceRequest);

        $serviceRequest->delete();

        return redirect()->route('client.panel')->with('success', 'Service request deleted successfully.');
    }
}
