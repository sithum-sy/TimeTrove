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
    /**
     * Store a new scheduler in the database.
     *
     * Validates the request data, creates a new scheduler user, and redirects to the home route with a success message.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

        // Create a new User with the role of 'scheduler'
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
            'is_active' => true, // Default to active
            'is_default_password_changed' => false, // Default to not changed
        ]);

        return redirect()->route('home')->with(
            'status',
            'New Scheduler was added successfully.'
        );
    }

    /**
     * Display the list of service providers.
     *
     * Retrieves all users with the role of 'service provider' and passes them to the view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function serviceProviderIndex()
    {
        $serviceProviders = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->get();

        return view('scheduler/service_provider/index', [
            'serviceProviders' => $serviceProviders,
        ]);
    }

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

    public function viewRequest($request_id, $client_id)
    {
        $serviceRequest = ServiceRequest::where('id', $request_id)
            ->where('client_id', $client_id)
            ->with('serviceCategory')
            ->firstOrFail();

        $serviceCategoryId = $serviceRequest->serviceCategory->id;
        $serviceProviders = User::whereHas('serviceProviderServices', function ($query) use ($serviceCategoryId) {
            $query->where('service_category_id', $serviceCategoryId);
        })->get()->sortBy('name');

        $quotation = Quotation::where('service_request_id', $serviceRequest->id)->latest()->first();

        return view('scheduler/client-request-view', [
            'serviceRequest' => $serviceRequest,
            'serviceProviders' => $serviceProviders,
            'quotation' => $quotation,
        ]);
    }

    public function assignProvider(Request $request, $request_id)
    {
        $serviceRequest = ServiceRequest::findOrFail($request_id);

        $serviceRequest->service_provider_id = $request->input('service_provider_id');
        $serviceRequest->status = 'assigned';
        $serviceRequest->save();

        return redirect('home')->with('status', 'Service provider assigned successfully.');
    }

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
