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

        //Get count of all unique clied ids
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
}
