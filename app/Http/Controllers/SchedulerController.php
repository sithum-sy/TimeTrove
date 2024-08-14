<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceProviderServices;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchedulerController extends Controller
{
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
            'gender' => ['required', 'string',],
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
            'is_active' => true, //default true
            'is_default_password_changed' => false,

        ]);

        return redirect()->route('home')->with(
            'status',
            'New Scheduler was added successfully.'
        );
    }

    public function serviceProviderIndex()
    {
        $serviceProviders = User::where('role', User::USER_ROLE_SERVICE_PROVIDER)->get();


        return view('scheduler/service_provider/index', [
            'serviceProviders' => $serviceProviders,
        ]);
    }

    public function serviceProviderForm1()
    {
        return view('scheduler/service_provider/form1');
    }

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
            'gender' => ['required', 'string',],
        ]);

        return view('scheduler/service_provider/form2', [
            'data' => $validatedData,
            'serviceCategories' => ServiceCategory::all()
        ]);
    }

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

            ServiceProviderServices::create([
                'service_provider_id' => $user->id,
                'service_category_id' => $validatedData['service_category_id'],
                'description' => $validatedData['description'],
                'availability' => $validatedData['availability'],
                'rate' => $validatedData['rate'],
                'city' => $validatedData['city'],
            ]);
            return $user;
        });

        return redirect()->route('scheduler.serviceProvider')->with(
            'status',
            'New Service Provider was added successfully.'
        );
    }
}
