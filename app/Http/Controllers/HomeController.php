<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $schedulers = User::where('role', User::USER_ROLE_SCHEDULER)->get();
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->get();
        $serviceCategories = ServiceCategory::all();

        return view('home', [
            'schedulers' => $schedulers,
            'serviceRequests' => $serviceRequests,
            'serviceCategories' => $serviceCategories
        ]);
    }
}
