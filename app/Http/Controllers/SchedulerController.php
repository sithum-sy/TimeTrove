<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
}
