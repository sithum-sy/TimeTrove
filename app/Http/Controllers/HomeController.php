<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceProviderServices;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * Applies the 'auth' middleware to all methods in this controller,
     * ensuring that only authenticated users can access them.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * Retrieves and passes necessary data to the 'home' view, including:
     * - List of schedulers
     * - Service requests for the authenticated client
     * - All service categories
     * - Paginated list of pending service requests
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Retrieve all users with the role of 'scheduler'
        $schedulers = User::where('role', User::USER_ROLE_SCHEDULER)->orderBy('updated_at', 'desc')->paginate(8);
        $activeSchedulersCount = User::where('role', User::USER_ROLE_SCHEDULER)
            ->where('is_active', 1)
            ->count();

        // Retrieve service requests for the authenticated client, including related service categories
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->orderBy('updated_at', 'desc')
            ->get();


        // Retrieve all service categories
        $serviceCategories = ServiceCategory::where('is_active', 1)->get();

        // Retrieve all pending service requests with related client and service category,
        // ordered by date and paginated with 8 requests per page
        $clientServiceRequests = ServiceRequest::whereIn('status', ['pending', 'quoted', 're-quoted', 'pending-approval', 'confirmed', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc');

        $upcomingAppointments = ServiceRequest::whereIn('status', ['pending', 'confirmed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'upcomingAppointmentsPage');

        $quotations = ServiceRequest::whereIn('status', ['quoted', 're-quoted', 'pending-approval'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'quotationsPage');

        $completedAppointments = ServiceRequest::where('status', 'completed')
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'completedAppointmentsPage');

        //Get count of all unique client ids
        $totalClients = $clientServiceRequests->pluck('client_id')->unique()->count();


        // Retrieve assigned tasks for the logged-in service provider
        $assignedTasks = ServiceRequest::where('service_provider_id', Auth::id())
            ->whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8);

        // Pass the retrieved data to the 'home' view
        return view('home', [
            'schedulers' => $schedulers,
            'serviceRequests' => $serviceRequests,
            'serviceCategories' => $serviceCategories,
            'clientServiceRequests' => $clientServiceRequests,
            'assignedTasks' => $assignedTasks,
            'activeSchedulersCount' => $activeSchedulersCount,
            'totalClients' => $totalClients,
            'upcomingAppointments' => $upcomingAppointments,
            'quotations' => $quotations,
            'completedAppointments' => $completedAppointments,
        ]);
    }

    public function search(Request $request)
    {

        $clientServiceRequests = ServiceRequest::whereIn('status', ['pending', 'quoted', 're-quoted', 'pending-approval', 'confirmed', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc');

        $assignedTasks = ServiceRequest::where('service_provider_id', Auth::id())
            ->whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8)
            ->withQueryString();

        // Base query with relationships
        $baseQuery = ServiceRequest::query()
            ->with(['client', 'serviceCategory']);

        // Apply filters
        $filteredQuery = $this->applyFilters($baseQuery, $request);

        // Get data for different sections
        $upcomingAppointments = clone $filteredQuery;
        $quotations = clone $filteredQuery;
        $completedAppointments = clone $filteredQuery;

        // Get total unique clients based on filtered results
        $totalClients = $filteredQuery->pluck('client_id')->unique()->count();

        $data = [
            'upcomingAppointments' => $upcomingAppointments
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'quotations' => $quotations
                ->whereIn('status', ['quoted', 're-quoted', 'pending-approval'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'completedAppointments' => $completedAppointments
                ->where('status', 'completed')
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'serviceCategories' => ServiceCategory::all(),
            'totalClients' => $totalClients,
        ];

        return view('home', $data, [
            'clientServiceRequests' => $clientServiceRequests,
            'assignedTasks' => $assignedTasks,
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('client', function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%");
                })
                    ->orWhereHas('serviceCategory', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Service category filter
        if ($request->filled('service_category')) {
            $query->where('service_category_id', $request->service_category);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        return $query;
    }
}
