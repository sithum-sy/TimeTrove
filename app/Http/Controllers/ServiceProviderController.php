<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\ServiceCategory;
use App\Models\ServiceProviderServices;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestSecurity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceProviderController extends Controller
{
    //view service requests assigned for the service provider
    public function panel()
    {
        $assignedTasks = ServiceProviderServices::with('serviceRequest')
            ->where('service_provider_id', auth()->id())->where('status', 'assigned')
            ->get();

        return view('provider.panel', [
            'assignedTasks' => $assignedTasks,
        ]);
    }

    //view a single request assigned for the service provider
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

    //reject the assigned service request
    public function rejectRequest(Request $request, $request_id)
    {

        $serviceRequest = ServiceRequest::findOrFail($request_id);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'pending';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Client service request rejected.');
    }

    //store the quote data for a service request
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

        $totalCharges = ($validated['estimated_hours'] * $validated['hourly_rate']) +
            $validated['materials_cost'] +
            $validated['additional_charges'];

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

        return redirect('home')->with('status', 'Quotation submitted successfully.');
    }

    //edit and update the data of a quote
    public function reQuote(Request $request, $id)
    {
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'estimated_hours' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'materials_cost' => 'required|numeric',
            'additional_charges' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $quotation = Quotation::findOrFail($id);

        $totalCharges = ($validated['estimated_hours'] * $validated['hourly_rate']) +
            $validated['materials_cost'] +
            $validated['additional_charges'];

        $quotation->update([
            'service_provider_id' => $validated['service_provider_id'],
            'estimated_hours' => $validated['estimated_hours'],
            'hourly_rate' => $validated['hourly_rate'],
            'materials_cost' => $validated['materials_cost'],
            'additional_charges' => $validated['additional_charges'],
            'total_charges' => $totalCharges,
            'notes' => $validated['notes'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($quotation->service_request_id);
        $serviceRequest->status = 're-quoted';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Quotation updated successfully.');
    }

    public function startService(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'security_code' => 'required|string|size:6',
        ]);

        $security = ServiceRequestSecurity::where('service_request_id', $serviceRequest->id)
            ->where('service_provider_id', Auth::id())
            ->where('security_code', $request->security_code)
            ->where('is_used', false)
            ->first();

        if (!$security) {
            return back()->withErrors(['security_code' => 'Invalid security code']);
        }

        // Mark the security code as used
        $security->is_used = true;
        $security->save();

        // Update the service request status
        $serviceRequest->status = 'started';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Service started successfully.');
    }

    //store the invoice data for a service request
    public function storeInvoice(Request $request, $serviceRequestId)
    {
        // Validate the request
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'actual_hours' => 'required|numeric',
            'final_hourly_rate' => 'required|numeric',
            'final_materials_cost' => 'required|numeric',
            'final_additional_charges' => 'required|numeric',
            'invoice_notes' => 'nullable|string',
        ]);

        $finalTotalAmount = ($validated['actual_hours'] * $validated['final_hourly_rate']) +
            $validated['final_materials_cost'] +
            $validated['final_additional_charges'];

        Invoice::create([
            'service_request_id' => $serviceRequestId,
            'service_provider_id' => $validated['service_provider_id'],
            'actual_hours' => $validated['actual_hours'],
            'final_hourly_rate' => $validated['final_hourly_rate'],
            'final_materials_cost' => $validated['final_materials_cost'],
            'final_additional_charges' => $validated['final_additional_charges'],
            'final_total_amount' => $finalTotalAmount,
            'invoice_notes' => $validated['invoice_notes'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($serviceRequestId);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'pending-payment';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Invoice submitted successfully.');
    }



    //view service provier profile data
    public function profileView()
    {
        $serviceProvider = Auth::user();

        $serviceProviderServices = ServiceProviderServices::where('service_provider_id', $serviceProvider->id)->get();

        if ($serviceProvider->role !== User::USER_ROLE_SERVICE_PROVIDER) {
            abort(403, 'Unauthorized action.');
        }

        return view('provider.view', [
            'serviceProvider' => $serviceProvider,
            'serviceProviderServices' => $serviceProviderServices
        ]);
    }

    public function editProfile()
    {
        $serviceProvider = Auth::user();

        return view('provider.edit', [
            'serviceProvider' => $serviceProvider,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $serviceProvider = Auth::user();

        if (! $serviceProvider instanceof User || $serviceProvider->role !== User::USER_ROLE_SERVICE_PROVIDER) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $serviceProvider->id,
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
        ]);

        $serviceProvider->update($validatedData);

        return redirect()->route('provider.profileView')
            ->with('status', 'Profile updated successfully!');
    }

    //add services which are provided by service providers
    public function addService()
    {
        $serviceCategories = ServiceCategory::all();

        return view('provider.add-service', [
            'serviceCategories' => $serviceCategories,
        ]);
    }

    //Store a new service
    public function serviceStore(Request $request)
    {
        $validatedData = $request->validate([
            'description' => ['required', 'string'],
            'availability' => ['required', 'string'],
            'rate' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'service_category_id' => ['required', 'exists:service_categories,id'],
        ]);

        DB::transaction(function () use ($validatedData) {
            $serviceProvider = Auth::user();

            // Create a record in ServiceProviderServices for the newly created service provider
            ServiceProviderServices::create([
                'service_provider_id' => $serviceProvider->id,
                'service_category_id' => $validatedData['service_category_id'],
                'description' => $validatedData['description'],
                'availability' => $validatedData['availability'],
                'rate' => $validatedData['rate'],
                'city' => $validatedData['city'],
            ]);
        });

        return redirect()->route('provider.profileView')->with(
            'status',
            'New Service was added successfully.'
        );
    }

    //go to edit-service view
    public function editService($id)
    {
        $serviceProviderService = ServiceProviderServices::findOrFail($id);

        $serviceCategories = ServiceCategory::all();

        return view('provider.edit-service', [
            'serviceProviderService' => $serviceProviderService,
            'serviceCategories' => $serviceCategories
        ]);
    }

    //update the service data
    public function updateService(Request $request, $id)
    {
        $validatedData = $request->validate([
            'description' => ['required', 'string'],
            'availability' => ['required', 'string'],
            'rate' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'service_category_id' => ['required', 'exists:service_categories,id'],
        ]);

        $serviceProviderService = ServiceProviderServices::findOrFail($id);

        $serviceProviderService->update($validatedData);

        return redirect()->route('provider.profileView')->with(
            'status',
            'Service was updated successfully.'
        );
    }

    //delete a service
    public function deleteService($id)
    {
        $serviceProviderService = ServiceProviderServices::findOrFail($id);
        $serviceProviderService->delete();

        return redirect()->route('provider.profileView')->with(
            'status',
            'Service was deleted successfully.'
        );
    }
}
