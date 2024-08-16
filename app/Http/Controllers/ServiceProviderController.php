<?php

namespace App\Http\Controllers;

use App\Models\ServiceProviderServices;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function panel()
    {
        $assignedTasks = ServiceProviderServices::with('serviceRequest')
            ->where('service_provider_id', auth()->id())
            ->get();

        return view('provider.panel', [
            'assignedTasks' => $assignedTasks,
        ]);
    }
}
