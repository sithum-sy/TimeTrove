<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Rating;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceProviderConfirmationEmail;
use App\Mail\ClientConfirmationEmail;
use App\Mail\ClientReceipt;
use App\Mail\ServiceProviderReceipt;
use App\Models\Invoice;
use App\Models\ServiceRequestSecurity;

class ClientController extends Controller
{
    /**
     * Show the client panel with their service requests and categories.
     *
     * @return \Illuminate\View\View
     */
    public function panel()
    {
        // Retrieve service requests for the authenticated client, including related service categories
        $serviceRequests = ServiceRequest::where('client_id', Auth::id())
            ->with('serviceCategory')
            ->get();

        // Retrieve all service categories
        $serviceCategories = ServiceCategory::all();

        // Pass the retrieved data to the view
        return view('client.panel', [
            'serviceRequests' => $serviceRequests,
            'serviceCategories' => $serviceCategories
        ]);
    }

    public function profileView()
    {
        $client = Auth::user();

        if ($client->role !== User::USER_ROLE_CLIENT) {
            abort(403, 'Unauthorized action.');
        }

        return view('client.view', [
            'client' => $client,
        ]);
    }

    public function editProfile()
    {
        $client = Auth::user();

        return view('client.edit', [
            'client' => $client,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $client = Auth::user();

        if (! $client instanceof User || $client->role !== User::USER_ROLE_CLIENT) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $client->id,
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // If a picture is uploaded, store it and add its path to the data
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $filePath = 'uploads/profile_pictures/' . $fileName;
            $file->move('uploads/profile_pictures/', $fileName);

            $validatedData['profile_picture'] = $filePath;
        }


        $client->update($validatedData);

        return redirect()->route('client.profileView')
            ->with('status', 'Profile updated successfully!');
    }

    /**
     * Handle the creation of a new service request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addRequest(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string',
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Add the authenticated client's ID and default status to the validated data
        $validatedData['client_id'] = Auth::id();
        $validatedData['status'] = 'pending';

        // If a picture is uploaded, store it and add its path to the data
        if ($request->hasFile('request_picture')) {
            $file = $request->file('request_picture');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $filePath = 'uploads/request_pictures/' . $fileName;
            $file->move('uploads/request_pictures/', $fileName);

            $validatedData['request_picture'] = $filePath;
        }


        // Create a new service request record
        ServiceRequest::create($validatedData);

        // Redirect back to the client panel with a success message
        return redirect()->route('home')->with('success', 'Service request added successfully.');
    }

    public function editRequest($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceCategories = ServiceCategory::all();

        return view("client.edit-request", compact('serviceRequest', 'serviceCategories'));
    }



    /**
     * Handle the update of an existing service request.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRequest(Request $request, $requestId)
    {
        // Find the service request by ID, or fail if not found
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        // Authorize the user to update the service request
        // $this->authorize('update', $serviceRequest);

        // Validate the request data
        $validatedData = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'date' => 'required|date',
            'time' => 'required',
            'request_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'description' => 'nullable|string',
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // If a new picture is uploaded, store it and update the path
        if ($request->hasFile('request_picture')) {
            $file = $request->file('request_picture');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $filePath = 'uploads/request_pictures/' . $fileName;
            $file->move('uploads/request_pictures/', $fileName);

            $validatedData['request_picture'] = $filePath;
        }

        // Update the existing service request with the new data
        $serviceRequest->update($validatedData);

        // Redirect back to the client panel with a success message
        return redirect()->route('client.singleRequest.view', $requestId)->with('status', 'Service request updated successfully.');
    }

    /**
     * Handle the deletion of an existing service request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteRequest($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        if ($serviceRequest->client_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this request.');
        }

        if ($serviceRequest->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot cancel a completed service request.');
        }

        $serviceRequest->delete();

        return redirect()->route('home')->with('status', 'Service request cancelled successfully.');
    }

    public function singleRequest($requestId)
    {
        $serviceRequest = ServiceRequest::where('id', $requestId)
            ->where('client_id', Auth::id())
            ->with('serviceCategory')
            ->firstOrFail();

        $quotation = Quotation::where('service_request_id', $serviceRequest->id)->latest()->first();
        $invoice = Invoice::where('service_request_id', $serviceRequest->id)->latest()->first();

        return view('client.single-request-view', [
            'serviceRequest' => $serviceRequest,
            'quotation' => $quotation,
            'invoice' => $invoice,
        ]);
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

    public function confirm(Request $request, $requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $serviceProviderId = $request->input('service_provider_id');
        if ($serviceProviderId) {
            $serviceRequest->service_provider_id = $serviceProviderId;
        }

        $serviceRequest->status = 'confirmed';
        $serviceRequest->save();

        // Generate a random 6-digit security code
        $securityCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save the security code
        ServiceRequestSecurity::create([
            'service_request_id' => $serviceRequest->id,
            'service_provider_id' => $serviceRequest->service_provider_id,
            'security_code' => $securityCode,
        ]);

        Mail::to($serviceRequest->client->email)->send(new ClientConfirmationEmail($serviceRequest, $securityCode));
        Mail::to($serviceRequest->serviceProvider->email)->send(new ServiceProviderConfirmationEmail($serviceRequest, $securityCode));

        return redirect('home')->with('status', 'Appointment for service request confirmed successfully.');
    }


    public function rejectQuote($requestId)
    {
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        if ($serviceRequest->status === 'pending-approval') {
            $serviceRequest->service_provider_id = null;

            $serviceRequest->status = 'pending';

            $serviceRequest->save();

            Quotation::where('service_request_id', $requestId)->delete();

            return redirect('home')->with('status', 'Service rejected.');
        }

        return redirect()->back()->with('error', 'This service request cannot be rejected.');
    }

    public function completeServiceRequest($requestId)
    {
        // Find the service request
        $serviceRequest = ServiceRequest::findOrFail($requestId);

        $invoice = Invoice::where('service_request_id', $requestId)
            ->firstOrFail();

        // Update status to completed
        $serviceRequest->status = 'completed';
        $serviceRequest->save();

        Mail::to($serviceRequest->client->email)->send(new ClientReceipt($serviceRequest, $invoice));
        Mail::to($serviceRequest->serviceProvider->email)->send(new ServiceProviderReceipt($serviceRequest, $invoice));

        return redirect()->back()->with('status', 'Payment Successful & Thank You for choosing TimeTove for your service request.');
    }

    public function rateServiceProvider(Request $request, $serviceRequestId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($serviceRequestId);

        if ($serviceRequest->status !== 'completed' || $serviceRequest->rating) {
            return redirect()->back()->with('error', 'Unable to submit rating.');
        }

        Rating::create([
            'service_request_id' => $serviceRequest->id,
            'client_id' => auth()->id(),
            'service_provider_id' => $serviceRequest->service_provider_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('status', 'Rating submitted successfully.');
    }
}
