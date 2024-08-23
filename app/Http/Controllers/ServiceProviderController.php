<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\ServiceProviderServices;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function panel()
    {
        $assignedTasks = ServiceProviderServices::with('serviceRequest')
            ->where('service_provider_id', auth()->id())->where('status', 'assigned')
            ->get();

        return view('provider.panel', [
            'assignedTasks' => $assignedTasks,
        ]);
    }

    public function viewRequest($task_id, $client_id)
    {
        $serviceRequest = ServiceRequest::where('id', $task_id)
            ->where('client_id', $client_id)
            ->with('serviceCategory')
            ->firstOrFail();

        $serviceCategoryId = $serviceRequest->serviceCategory->id;
        $serviceProviders = User::whereHas('serviceProviderServices', function ($query) use ($serviceCategoryId) {
            $query->where('service_category_id', $serviceCategoryId);
        })->get()->sortBy('name');

        $quotation = Quotation::where('service_request_id', $serviceRequest->id)->latest()->first();

        return view('provider.service-request-view', [
            'serviceRequest' => $serviceRequest,
            'serviceProviders' => $serviceProviders,
            'quotation' => $quotation,
        ]);
    }

    public function rejectRequest(Request $request, $request_id)
    {

        $serviceRequest = ServiceRequest::findOrFail($request_id);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'pending';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Client service request rejected.');
    }

    public function storeQuotation(Request $request, $serviceRequestId)
    {
        // Validate the request
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'estimated_hours' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'materials_cost' => 'required|numeric',
            'additional_charges' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        // Calculate the total charges
        $totalCharges = ($validated['estimated_hours'] * $validated['hourly_rate']) +
            $validated['materials_cost'] +
            $validated['additional_charges'];

        // Store the data in the database
        Quotation::create([
            'service_request_id' => $serviceRequestId,
            'service_provider_id' => $validated['service_provider_id'],
            'estimated_hours' => $validated['estimated_hours'],
            'hourly_rate' => $validated['hourly_rate'],
            'materials_cost' => $validated['materials_cost'],
            'additional_charges' => $validated['additional_charges'],
            'total_charges' => $totalCharges,
            'notes' => $validated['notes'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($serviceRequestId);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'quoted';
        $serviceRequest->save();

        // Redirect back with a success message
        return redirect('home')->with('status', 'Quotation submitted successfully.');
    }

    public function reQuote(Request $request, $quotationId)
    {
        // Validate the request
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'estimated_hours' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'materials_cost' => 'required|numeric',
            'additional_charges' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        // Fetch the existing quotation
        $quotation = Quotation::findOrFail($quotationId);

        // Calculate the total charges
        $totalCharges = ($validated['estimated_hours'] * $validated['hourly_rate']) +
            $validated['materials_cost'] +
            $validated['additional_charges'];

        // Update the quotation with new data
        $quotation->update([
            'service_provider_id' => $validated['service_provider_id'],
            'estimated_hours' => $validated['estimated_hours'],
            'hourly_rate' => $validated['hourly_rate'],
            'materials_cost' => $validated['materials_cost'],
            'additional_charges' => $validated['additional_charges'],
            'total_charges' => $totalCharges,
            'notes' => $validated['notes'],
        ]);

        // Update the service request status
        $serviceRequest = ServiceRequest::findOrFail($quotation->service_request_id);
        $serviceRequest->status = 're-quoted';
        $serviceRequest->save();

        // Redirect back with a success message
        return redirect('home')->with('status', 'Quotation updated successfully.');
    }
}
