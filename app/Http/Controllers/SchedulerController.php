<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\ServiceCategory;
use App\Models\ServiceProviderServices;
use App\Models\ServiceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchedulerController extends Controller
{
    //Store a new scheduler
    public function store(Request $request)
    {
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

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'role' => User::USER_ROLE_SCHEDULER,
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

        return redirect()->route('home')->with(
            'status',
            'New Scheduler was added successfully.'
        );
    }

    //view a scheduler
    public function viewScheduler($id)
    {
        $scheduler = User::where(
            'role',
            User::USER_ROLE_SCHEDULER
        )->findOrFail($id);

        return view('scheduler.view', [
            'scheduler' => $scheduler,
        ]);
    }

    //toggle 'is_active' status
    public function toggleStatus($id)
    {
        $scheduler = User::where(
            'role',
            User::USER_ROLE_SCHEDULER
        )->findOrFail($id);

        $scheduler->is_active = !$scheduler->is_active;
        $scheduler->save();

        return redirect()->route('home')->with('status', 'Scheduler status updated successfully.');
    }

    //go to edit view of a scheduler
    public function editScheduler($id)
    {
        $scheduler = User::where(
            'role',
            User::USER_ROLE_SCHEDULER
        )->findOrFail($id);

        return view('scheduler.edit', compact('scheduler'));
    }

    //update the service provider details
    public function updateScheduler(Request $request, $id)
    {
        $scheduler = User::where(
            'role',
            User::USER_ROLE_SCHEDULER
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

        $scheduler->update($validatedData);

        return redirect()->route('scheduler.view', $scheduler->id)
            ->with('status', 'Scheduler data updated successfully!');
    }

    //delete a scheduler
    public function deleteScheduler($id)
    {
        $scheduler = User::where('role', User::USER_ROLE_SCHEDULER)->findOrFail($id);
        $scheduler->delete();

        return redirect()->route('home')->with(
            'status',
            'Scheduler was deleted successfully.'
        );
    }

    //Display the list of service providers
    public function serviceProviderIndex()
    {
        $serviceProviders = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->get();

        return view('scheduler/service_provider/index', [
            'serviceProviders' => $serviceProviders,
        ]);
    }


    //View a single service request which is to be assigned to relevant service provider
    public function viewRequest($request_id, $client_id)
    {
        $serviceRequest = ServiceRequest::where('id', $request_id)
            ->where('client_id', $client_id)
            ->with('serviceCategory')
            ->firstOrFail();

        $serviceCategoryId = $serviceRequest->serviceCategory->id;
        $serviceProviders = User::where('is_active', 1)
            ->whereHas('serviceProviderServices', function ($query) use ($serviceCategoryId) {
                $query->where('service_category_id', $serviceCategoryId);
            })
            ->get()
            ->sortBy('name');


        $quotation = Quotation::where('service_request_id', $serviceRequest->id)->latest()->first();

        return view('scheduler/client-request-view', [
            'serviceRequest' => $serviceRequest,
            'serviceProviders' => $serviceProviders,
            'quotation' => $quotation,
        ]);
    }

    //assign service request to service provider
    public function assignProvider(Request $request, $request_id)
    {
        $serviceRequest = ServiceRequest::findOrFail($request_id);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'assigned';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Service provider assigned successfully.');
    }

    //request a new quote from service provider
    public function requestNewQuote(Request $request, $requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $serviceProviderId = $request->input('service_provider_id');
        if ($serviceProviderId) {
            $serviceRequest->service_provider_id = $serviceProviderId;
        }
        $serviceRequest->status = 'new-quote-requested';
        $serviceRequest->save();

        return redirect('home')->with('status', 'New quote requested.');
    }

    //pass the quote to be approved by client
    public function passToClient(Request $request, $requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $serviceProviderId = $request->input('service_provider_id');
        if ($serviceProviderId) {
            $serviceRequest->service_provider_id = $serviceProviderId;
        }
        $serviceRequest->status = 'pending-approval';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Quote passed to client successfully.');
    }
}
