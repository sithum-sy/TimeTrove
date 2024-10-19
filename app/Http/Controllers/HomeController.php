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
        // Retrieves all unique status values
        $statuses = ServiceRequest::distinct()->pluck('status');

        // Retrieve all users with the role of 'scheduler' to Admin Dashboard
        $schedulers = User::where('role', User::USER_ROLE_SCHEDULER)->orderBy('updated_at', 'desc')->paginate(12);
        $activeSchedulersCount = User::where('role', User::USER_ROLE_SCHEDULER)
            ->where('is_active', 1)
            ->count();

        // Retrieve service requests for the authenticated client, including related service categories in Client Dashboard
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        //Get counts for statistics in Client Dashboard
        $totalServiceRequests = ServiceRequest::where('client_id', Auth::id())->count();
        $totalPendingRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'pending')->count();
        $totalConfirmedRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'confirmed')->count();
        $totalPendingPayments = ServiceRequest::where('client_id', Auth::id())->where('status', 'pending-payment')->count();
        $totalCompletedRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'completed')->count();

        // Retrieve all active service categories
        $serviceCategories = ServiceCategory::where('is_active', 1)->get();

        // Retrieve all pending service requests with related client and service category to Scheduler Dashboard
        $clientServiceRequests = ServiceRequest::whereIn('status', ['pending', 'quoted', 're-quoted', 'pending-approval', 'confirmed', 'started', 'pending-payment', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc');

        $upcomingAppointments = ServiceRequest::whereIn('status', ['pending', 'confirmed', 'started', 'assigned'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'upcomingAppointmentsPage');

        $quotations = ServiceRequest::whereIn('status', ['quoted', 're-quoted', 'pending-approval'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'quotationsPage');

        $completedAppointments = ServiceRequest::whereIn('status', ['pending-payment', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(8, ['*'], 'completedAppointmentsPage');

        $tasks = ServiceRequest::whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'started', 'pending-payment',  'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(12);

        //Get counts for statistics in Scheduler Dashboard
        $totalAppointments = ServiceRequest::count();
        $totalUpcomingAppointments = ServiceRequest::whereIn('status', ['pending', 'confirmed', 'started', 'assigned'])->count();
        $totalCompletedAppointments = ServiceRequest::whereIn('status', ['pending-payment', 'completed'])->with(['client', 'serviceCategory'])->count();
        $totalClients = $clientServiceRequests->pluck('client_id')->unique()->count();

        // Retrieve tasks for the logged-in service provider dashboard
        $assignedTasks = ServiceRequest::where('service_provider_id', Auth::id())
            ->whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'pending-payment', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(12);

        //Get counts for statistics in service provider dashboard
        $totalAssignedTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'assigned')->count();
        $totalUpcomingTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'confirmed')->count();
        $totalCompletedTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'completed')->count();

        // Pass the retrieved data to the 'home' view
        return view('home', compact(
            'schedulers',
            'serviceRequests',
            'serviceCategories',
            'clientServiceRequests',
            'assignedTasks',
            'activeSchedulersCount',
            'totalClients',
            'upcomingAppointments',
            'quotations',
            'completedAppointments',
            'tasks',
            'statuses',
            'totalAppointments',
            'totalUpcomingAppointments',
            'totalCompletedAppointments',
            'totalAssignedTasks',
            'totalUpcomingTasks',
            'totalCompletedTasks',
            'totalServiceRequests',
            'totalPendingRequests',
            'totalConfirmedRequests',
            'totalPendingPayments',
            'totalCompletedRequests'
        ));
    }

    public function search(Request $request)
    {
        $user = Auth::user();

        $clientServiceRequests = ServiceRequest::whereIn('status', ['pending', 'quoted', 're-quoted', 'pending-approval', 'confirmed', 'started', 'pending-payment', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc');

        //Get counts for statistics in Client Dashboard
        $totalServiceRequests = ServiceRequest::where('client_id', Auth::id())->count();
        $totalPendingRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'pending')->count();
        $totalConfirmedRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'confirmed')->count();
        $totalPendingPayments = ServiceRequest::where('client_id', Auth::id())->where('status', 'pending-payment')->count();
        $totalCompletedRequests = ServiceRequest::where('client_id', Auth::id())->where('status', 'completed')->count();

        // Retrieve tasks for search in logged-in service provider dashboard
        $assignedTasks = ServiceRequest::where('service_provider_id', Auth::id())
            ->whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'started', 'pending-payment', 'completed'])
            ->with(['client', 'serviceCategory'])
            ->orderBy('date', 'desc')
            ->paginate(12);

        //Get counts for statistics in service provider dashboard
        $totalAssignedTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'assigned')->count();
        $totalUpcomingTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'confirmed')->count();
        $totalCompletedTasks = ServiceRequest::where('service_provider_id', Auth::id())->where('status', 'completed')->count();

        // Base query with relationships
        $baseQuery = ServiceRequest::query()
            ->with(['client', 'serviceCategory']);

        // If it's a service provider, limit to their assigned tasks
        if ($user->role == 'service_provider') {
            $baseQuery->where('service_provider_id', $user->id)
                ->whereIn('status', ['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'started', 'pending-payment', 'completed']);
        }

        // Apply filters
        $filteredQuery = $this->applyFilters($baseQuery, $request);

        // Get data for different sections
        $upcomingAppointments = clone $filteredQuery;
        $quotations = clone $filteredQuery;
        $completedAppointments = clone $filteredQuery;
        $tasks = clone $filteredQuery;
        $serviceRequests = clone $filteredQuery;

        // Get total unique clients based on filtered results in Scheduler Dashboard
        $totalClients = $filteredQuery->pluck('client_id')->unique()->count();
        $totalAppointments = ServiceRequest::count();
        $totalUpcomingAppointments = ServiceRequest::whereIn('status', ['pending', 'confirmed', 'started', 'assigned'])->count();
        $totalCompletedAppointments = ServiceRequest::whereIn('status', ['pending-payment', 'completed'])->with(['client', 'serviceCategory'])->count();

        // Retrieves all unique status values
        $statuses = ServiceRequest::distinct()->pluck('status');

        $data = [
            'upcomingAppointments' => $upcomingAppointments
                ->whereIn('status', ['pending', 'confirmed', 'started', 'assigned'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'quotations' => $quotations
                ->whereIn('status', ['quoted', 're-quoted', 'pending-approval'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'completedAppointments' => $completedAppointments
                ->whereIn('status', ['pending-payment', 'completed'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),

            'tasks' => $tasks
                ->latest()
                ->paginate(8)
                ->withQueryString(),

            'serviceRequests' => $serviceRequests
                ->latest()
                ->paginate(8)
                ->withQueryString(),

            'serviceCategories' => ServiceCategory::all(),
            'totalClients' => $totalClients,
            'totalAppointments' => $totalAppointments,
            'totalUpcomingAppointments' => $totalUpcomingAppointments,
            'totalCompletedAppointments' => $totalCompletedAppointments,
            'totalAssignedTasks' => $totalAssignedTasks,
            'totalUpcomingTasks' => $totalUpcomingTasks,
            'totalCompletedTasks' => $totalCompletedTasks,
            'totalServiceRequests' => $totalServiceRequests,
            'totalPendingRequests' => $totalPendingRequests,
            'totalConfirmedRequests' => $totalConfirmedRequests,
            'totalPendingPayments' => $totalPendingPayments,
            'totalCompletedRequests' => $totalCompletedRequests,
            'statuses' => $statuses,
            'clientServiceRequests' => $clientServiceRequests,
            'assignedTasks' => $assignedTasks,
        ];

        return view('home', $data);
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
