<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
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
    // Store a new scheduler by admin
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

    //view a scheduler by admin
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

    //toggle 'is_active' status by admin
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

    //go to edit view of a scheduler by admin
    public function editScheduler($id)
    {
        $scheduler = User::where(
            'role',
            User::USER_ROLE_SCHEDULER
        )->findOrFail($id);

        return view('scheduler.edit', compact('scheduler'));
    }

    //update the service provider details by admin
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

    //delete a scheduler by admin
    public function deleteScheduler($id)
    {
        $scheduler = User::where('role', User::USER_ROLE_SCHEDULER)->findOrFail($id);
        $scheduler->delete();

        return redirect()->route('home')->with(
            'status',
            'Scheduler was deleted successfully.'
        );
    }

    //Display the list of service providers in Scheduler Dashboard
    public function serviceProviderIndex()
    {
        $serviceProviders = ServiceProviderServices::with(['user', 'serviceCategory'])
            ->whereHas('user', function ($query) {
                $query->where('role', User::USER_ROLE_SERVICE_PROVIDER);
            })
            ->paginate(10);

        $serviceCategories = ServiceCategory::where('is_active', 1)->get();

        $activeServiceProvidersCount = ServiceProviderServices::whereHas('user', function ($query) {
            $query->where('is_active', 1)
                ->where('role', User::USER_ROLE_SERVICE_PROVIDER);
        })->count();

        return view('scheduler/service_provider/index', compact(
            'serviceProviders',
            'activeServiceProvidersCount',
            'serviceCategories'
        ));
    }

    // Search and filter for service providers in scheduler dashboard
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
        $activeServiceProvidersCount = $serviceProviders->where('is_active', 1)->count();

        $serviceCategories = ServiceCategory::where('is_active', 1)->get();

        return view('scheduler.service_provider.index', compact(
            'serviceProviders',
            'activeServiceProvidersCount',
            'serviceCategories'
        ));
    }

    //View a single service request which is to be assigned to relevant service provider by scheduler
    public function viewRequest($request_id, $client_id)
    {
        $serviceRequest = ServiceRequest::where('id', $request_id)
            ->where('client_id', $client_id)
            ->with('serviceCategory')
            ->firstOrFail();

        // Extract latitude and longitude values for the client's location.
        $clientLat = $serviceRequest->latitude;
        $clientLng = $serviceRequest->longitude;

        // Get the service category ID from the service request.
        $serviceCategoryId = $serviceRequest->serviceCategory->id;

        // Define a distance threshold (in kilometers) for filtering service providers.
        $distanceThreshold = 10;

        // Retrieve active service providers who offer services in the same category,
        // ensuring they have valid latitude and longitude coordinates.
        $serviceProviders = User::where('is_active', 1)
            ->whereHas('serviceProviderServices', function ($query) use ($serviceCategoryId) {
                $query->where('service_category_id', $serviceCategoryId)
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude');
            })
            ->select('users.*')
            ->get();

        // Filter the service providers to only include those within the distance threshold.
        $filteredProviders = $serviceProviders->filter(function ($provider) use ($clientLat, $clientLng, $distanceThreshold) {
            // Get the first service provider's latitude and longitude values.
            $latitude = $provider->serviceProviderServices->first()->latitude;
            $longitude = $provider->serviceProviderServices->first()->longitude;

            // Calculate the distance between the client and the service provider using the Haversine formula.
            $distance = (6371 * acos(
                cos(deg2rad($clientLat)) *
                    cos(deg2rad($latitude)) *
                    cos(deg2rad($longitude) - deg2rad($clientLng)) +
                    sin(deg2rad($clientLat)) *
                    sin(deg2rad($latitude))
            ));

            // Include the provider if the distance is within the specified threshold.
            return $distance <= $distanceThreshold;
        });

        // Retrieve the latest quotation associated with the service request, if available.
        $quotation = Quotation::where('service_request_id', $serviceRequest->id)->latest()->first();

        // Retrieve the latest invoice associated with the service request, if available.
        $invoice = Invoice::where('service_request_id', $serviceRequest->id)->latest()->first();

        return view('scheduler/client-request-view', [
            'serviceRequest' => $serviceRequest,
            'serviceProviders' => $filteredProviders,
            'quotation' => $quotation,
            'invoice' => $invoice,
        ]);
    }

    // Assign service request to service provider by scheduler
    public function assignProvider(Request $request, $request_id)
    {
        $serviceRequest = ServiceRequest::findOrFail($request_id);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'assigned';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Service provider assigned successfully.');
    }

    //request a new quote from service provider by scheduler
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

    //pass the quote to client by scheduler
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


    /********************Servicer Provider Controller Functiions By Scheduler********************************/

    // Show the form 1 for creating a new service provider
    public function serviceProviderForm1()
    {
        return view('scheduler/service_provider/form1');
    }

    //  Show the form 2 for creating a new service provider 
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

    // Store a new service provider
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
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
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
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
            ]);
        });

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'New Service Provider was added successfully.'
        );
    }

    // Toggle 'is_active' status
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

    // View a single service provier
    public function viewServiceProvider($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        $serviceProviderServices = ServiceProviderServices::where('service_provider_id', $serviceProvider->id)->get();

        return view('scheduler.service_provider.view', compact('serviceProvider', 'serviceProviderServices'));
    }

    // Go to edit view of a service provider
    public function editServiceProvider($id)
    {
        $serviceProvider = User::where(
            'role',
            User::USER_ROLE_SERVICE_PROVIDER
        )->findOrFail($id);

        return view('scheduler.service_provider.edit', compact('serviceProvider'));
    }

    // Update the service provider details
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

    // Delete a service provider
    public function deleteServiceProvider($id)
    {
        $serviceProvider = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->findOrFail($id);
        $serviceProvider->delete();

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'Service Provider was deleted successfully.'
        );
    }

    // View service categories
    public function serviceCategoriesIndex()
    {
        $serviceCategories = ServiceCategory::paginate(10);

        $statistics = [
            'total' => ServiceCategory::count(),
            'active' => ServiceCategory::where('is_active', true)->count(),
            'inactive' => ServiceCategory::where('is_active', false)->count(),
        ];

        return view('scheduler.serviceCategoriesIndex', compact('serviceCategories', 'statistics'));
    }

    // Add a new service category
    public function storeServiceCategory(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $serviceCategory = ServiceCategory::create([
            'name' => $validatedData['name'],
            'created_by' => Auth::check() ? Auth::id() : null,
        ]);

        return redirect()->route('scheduler.serviceCategories')->with('status', 'Service category created successfully');
    }

    // Toggle status of service categories
    public function toggleServiceCategoryStatus(ServiceCategory $serviceCategory)
    {
        $serviceCategory->update([
            'is_active' => !$serviceCategory->is_active
        ]);

        return redirect()->back()->with('status', 'Service category status updated successfully');
    }

    // Update the service category details
    public function updateServiceCategories(Request $request, ServiceCategory $serviceCategory)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $serviceCategory->update([
            'name' => $validatedData['name'],
            'updated_by' => Auth::check() ? Auth::id() : null,
        ]);

        return redirect()->route('scheduler.serviceCategories')
            ->with('status', 'Service category updated successfully!');
    }


    // Delete a service category
    public function deleteServiceCategory(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();

        return redirect()->back()->with('status', 'Service category deleted successfully');
    }
}
