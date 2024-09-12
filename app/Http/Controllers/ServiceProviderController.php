<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\ServiceCategory;
use App\Models\ServiceProviderServices;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceProviderController extends Controller
{
    // Show the form for creating a new service provider (Step 1).
    public function serviceProviderForm1()
    {
        return view('scheduler/service_provider/form1');
    }

    //  Show the form for creating a new service provider (Step 2).
    public function serviceProviderForm2(Request $request)
    {
        $serviceCategories = ServiceCategory::all();

        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', function ($attribute, $value, $fail) {
                $age = Carbon::parse($value)->age;
                if ($age < 18) {
                    $fail('The ' . $attribute . ' must be at least 18 years old');
                }
            }],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
        ]);

        return view('scheduler/service_provider/form2', [
            'data' => $validatedData,
            'serviceCategories' => $serviceCategories
        ]);
    }

    //    Store a new service provider
    public function serviceProviderStore(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'description' => ['required', 'string'],
            'availability' => ['required', 'string'],
            'rate' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'service_category_id' => ['required', 'exists:service_categories,id'],
        ]);

        DB::transaction(function () use ($validatedData) {
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'role' => User::USER_ROLE_SERVICE_PROVIDER,
                'email' => $validatedData['email'],
                'phone_number' => $validatedData['phone_number'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'address' => $validatedData['address'],
                'gender' => $validatedData['gender'],
                'password' => Hash::make($validatedData['email']),
                'created_by' => Auth::check() ? Auth::id() : null,
                'is_active' => true,
                'is_default_password_changed' => false,
            ]);

            // Create a record in ServiceProviderServices for the newly created service provider
            ServiceProviderServices::create([
                'service_provider_id' => $user->id,
                'service_category_id' => $validatedData['service_category_id'],
                'description' => $validatedData['description'],
                'availability' => $validatedData['availability'],
                'rate' => $validatedData['rate'],
                'city' => $validatedData['city'],
            ]);
        });

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'New Service Provider was added successfully.'
        );
    }

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

    // toggle 'is_active' status
    public function toggleStatus($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        $serviceProvider->is_active = !$serviceProvider->is_active;
        $serviceProvider->save();

        return redirect()->route('scheduler.serviceProvider')->with('status', 'Service Provider status updated successfully.');
    }

    //view a single service provier
    public function viewServiceProvider($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        return view('scheduler.service_provider.view', [
            'serviceProvider' => $serviceProvider,
        ]);
    }

    //go to edit view of a service provider
    public function editServiceProvider($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        return view('scheduler.service_provider.edit', compact('serviceProvider'));
    }

    //update the service provider details
    public function updateServiceProvider(Request $request, $id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'is_active' => 'sometimes|boolean',
        ]);

        $validatedData['is_active'] = $request->boolean('is_active');

        $serviceProvider->update($validatedData);

        return redirect()->route('scheduler.serviceProvider.view', $serviceProvider->id)
            ->with('status', 'Service provider updated successfully!');
    }

    //delete a service provider
    public function deleteServiceProvider($id)
    {
        $serviceProvider = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->findOrFail($id);
        $serviceProvider->delete();

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'Service Provider was deleted successfully.'
        );
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
    public function reQuote(Request $request, $quotationId)
    {
        $validated = $request->validate([
            'service_provider_id' => 'required|exists:users,id',
            'estimated_hours' => 'required|numeric',
            'hourly_rate' => 'required|numeric',
            'materials_cost' => 'required|numeric',
            'additional_charges' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $quotation = Quotation::findOrFail($quotationId);

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
}
