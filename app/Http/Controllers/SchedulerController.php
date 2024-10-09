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
        // Retrieve service provider services with associated user and service category
        $serviceProviders = ServiceProviderServices::with(['user', 'serviceCategory'])
            ->whereHas('user', function ($query) {
                $query->where('role', User::USER_ROLE_SERVICE_PROVIDER);
            })
            ->paginate(10);

        // Get active service categories
        $serviceCategories = ServiceCategory::where('is_active', 1)->get();

        // Count active service providers
        $activeServiceProvidersCount = ServiceProviderServices::whereHas('user', function ($query) {
            $query->where('is_active', 1)
                ->where('role', User::USER_ROLE_SERVICE_PROVIDER);
        })->count();

        return view('scheduler/service_provider/index', [
            'serviceProviders' => $serviceProviders,
            'activeServiceProvidersCount' => $activeServiceProvidersCount,
            'serviceCategories' => $serviceCategories,
        ]);
    }

    public function serviceProviderSearch(Request $request)
    {
        $query = ServiceProviderServices::with(['user', 'serviceCategory'])
            ->whereHas('user', function ($query) {
                $query->where('role', User::USER_ROLE_SERVICE_PROVIDER);
            });

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            });
        }

        // Apply service category filter
        if ($request->filled('service_category')) {
            $query->where('service_category_id', $request->input('service_category'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->whereHas('user', function ($query) {
                    $query->where('is_active', 1);
                });
            } elseif ($status === 'inactive') {
                $query->whereHas('user', function ($query) {
                    $query->where('is_active', 0);
                });
            }
        }

        $serviceProviders = $query->paginate(10);

        // Count active service providers
        $activeServiceProvidersCount = $serviceProviders->where('user.is_active', 1)->count();

        return view('scheduler.service_provider.index', [
            'serviceProviders' => $serviceProviders,
            'activeServiceProvidersCount' => $activeServiceProvidersCount,
            'serviceCategories' => ServiceCategory::where('is_active', 1)->get(),
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

    /********************Servicer Provider Controller Functiions********************************/

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

    // toggle 'is_active' status
    public function toggleServiceProviderStatus($id)
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
}
