<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $date = new Carbon();
        $before = $date->subYears(18)->format('Y-m-d');

        $message = [
            'before' => 'You must be 18 years or older to register',
            'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, png, jpg, gif.',
            'profile_picture.max' => 'The profile picture may not be greater than 2048 kilobytes.',
        ];

        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:' . $before],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], $message);
    }

    protected function create(array $data)
    {
        $validatedData['profile_picture'] = null;

        if (array_key_exists('profile_picture', $data) && $data['profile_picture'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $data['profile_picture'];
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/profile_pictures/' . $fileName;
            $file->move(public_path('uploads/profile_pictures'), $fileName);

            $validatedData['profile_picture'] = $filePath;
        }

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => User::USER_ROLE_CLIENT,
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => $data['date_of_birth'],
            'address' => $data['address'],
            'gender' => $data['gender'],
            'profile_picture' => $validatedData['profile_picture'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
