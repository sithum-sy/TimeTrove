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

    /**
     * Show the form for creating a new service provider (Step 1).
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function serviceProviderForm1()
    {
        return view('scheduler/service_provider/form1');
    }

    /**
     * Show the form for creating a new service provider (Step 2).
     *
     * Validates request data and passes it to the view along with service categories.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * Store a new service provider in the database.
     *
     * Validates the request data, creates a new user with the role of 'service provider',
     * and associates it with the service provider's services. Uses a transaction to ensure data integrity.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
            // Create a new User with the role of 'service provider'
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

    public function deleteServiceProvider($id)
    {
        $serviceProvider = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->findOrFail($id);
        $serviceProvider->delete();

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'Service Provider was deleted successfully.'
        );
    }

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

    public function editServiceProvider($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        return view('scheduler.service_provider.edit', compact('serviceProvider'));
    }

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
            'gender' => 'required|in:male,female,other',
            'is_active' => 'sometimes|boolean',
        ]);

        $validatedData['is_active'] = $request->boolean('is_active');

        $serviceProvider->update($validatedData);

        return redirect()->route('scheduler.serviceProvider.view', $serviceProvider->id)
            ->with('status', 'Service provider updated successfully!');
    }

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
