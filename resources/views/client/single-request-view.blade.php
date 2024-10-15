@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Display Success Message -->
            @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif

            <!-- Display Error Message -->
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2>Service Request Details: {{ $serviceRequest->serviceCategory->name }}</h2>
                <div class="ms-auto">
                    @if(!in_array($serviceRequest->status, ['completed', 'started', 'pending-payment']))
                    <form action="{{ route('client.deleteRequest', $serviceRequest->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger me-2">
                            <i class="bi bi-x-circle"></i> Reject Request
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-backward"></i> Back to Dashboard
                    </a>
                </div>
            </div>


            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">Request Information</div>
                        <div class="card-body">
                            <p><strong>Request ID:</strong> {{ $serviceRequest->id }}</p>
                            <p><strong>Client:</strong> {{ $serviceRequest->client->first_name }} {{ $serviceRequest->client->last_name }}</p>
                            <p><strong>Email:</strong> {{ $serviceRequest->client->email }}</p>
                            <p><strong>Service Category:</strong> {{ $serviceRequest->serviceCategory->name }}</p>
                            <p><strong>Location:</strong> {{ $serviceRequest->location }}</p>
                            <p><strong>Date:</strong> {{ $serviceRequest->date }}</p>
                            <p><strong>Time:</strong> {{ $serviceRequest->time }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($serviceRequest->status) }}</p>
                            <p><strong>Service Provider:</strong> {{ $serviceRequest->serviceProvider->first_name }} {{ $serviceRequest->serviceProvider->last_name }}
                                ( <i class="fas fa-star" style="color: gold;"></i> {{ number_format($serviceRequest->serviceProvider->averageRating(), 1) }}/5.0)
                                ({{ $serviceRequest->serviceProvider->ratingCount() }} {{ Str::plural('review', $serviceRequest->serviceProvider->ratingCount()) }})
                            </p>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">Description</div>
                        <div class="card-body">
                            <p>{{ $serviceRequest->description }}</p>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">Pictures</div>
                        <div class="card-body">
                            @if($serviceRequest->request_picture)
                            <img src="{{ asset('uploads/request_pictures/' . $serviceRequest->request_picture) }}" alt="Request Picture" class="img-fluid">
                            @else
                            <p>No pictures provided</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($serviceRequest->status === 'pending-approval')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">Quotation Details</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="service_provider_name" class="form-label">Service Provider</label>
                                <input type="text" class="form-control" id="service_provider_name" value="{{ $quotation->serviceProvider->first_name }} {{ $quotation->serviceProvider->last_name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimated_hours" value="{{ $quotation->estimated_hours }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                <input type="number" class="form-control" id="hourly_rate" value="{{ $quotation->hourly_rate }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                <input type="number" class="form-control" id="materials_cost" value="{{ $quotation->materials_cost }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                <input type="number" class="form-control" id="additional_charges" value="{{ $quotation->additional_charges }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                <input type="number" class="form-control" id="total_charges" value="{{ $quotation->total_charges }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" rows="3" readonly>{{ $quotation->notes }}</textarea>
                            </div>
                            <div class="d-flex mt-4">
                                <a href="{{ route('client.requestNewQuote', $serviceRequest->id) }}" class="btn btn-warning me-2">Request New Quote</a>
                                <a href="{{ route('client.confirm', $serviceRequest->id) }}" class="btn btn-success me-2">Accept</a>
                                <a href="{{ route('client.rejectQuote', $serviceRequest->id) }}" class="btn btn-danger"
                                    onclick="event.preventDefault(); document.getElementById('reject-quote-form').submit();">Reject</a>

                                <form id="reject-quote-form" action="{{ route('client.rejectQuote', $serviceRequest->id) }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 'confirmed')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">Quotation Details</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="service_provider_name" class="form-label">Service Provider</label>
                                <input type="text" class="form-control" id="service_provider_name" value="{{ $quotation->serviceProvider->first_name }} {{ $quotation->serviceProvider->last_name }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimated_hours" value="{{ $quotation->estimated_hours }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                <input type="number" class="form-control" id="hourly_rate" value="{{ $quotation->hourly_rate }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                <input type="number" class="form-control" id="materials_cost" value="{{ $quotation->materials_cost }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                <input type="number" class="form-control" id="additional_charges" value="{{ $quotation->additional_charges }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                <input type="number" class="form-control" id="total_charges" value="{{ $quotation->total_charges }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" rows="3" readonly>{{ $quotation->notes }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            @elseif (($serviceRequest->status === 'pending-payment') || ($serviceRequest->status === 'completed'))
            <div class="col-md-8">
                @if($serviceRequest->status === 'completed' && !$serviceRequest->rating)
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        Rate Service Provider
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.rateServiceProvider', $serviceRequest->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating (1-5 stars)</label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="">Select rating</option>
                                    <option value="1">1 star</option>
                                    <option value="2">2 stars</option>
                                    <option value="3">3 stars</option>
                                    <option value="4">4 stars</option>
                                    <option value="5">5 stars</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Review</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                        </form>
                    </div>
                </div>
                @elseif($serviceRequest->rating)
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        Your Rating
                    </div>
                    <div class="card-body">
                        <p><strong>Rating:</strong> {{ $serviceRequest->rating->rating }} stars</p>
                        <p><strong>Review:</strong> {{ $serviceRequest->rating->comment }}</p>
                    </div>
                </div>
                @endif
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        Invoice Details
                    </div>
                    <div class="card-body">
                        @csrf
                        <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

                        <div class="mb-3">
                            <label for="id" class="form-label">Invoice ID</label>
                            <input type="number" step="0.5" class="form-control" id="id" name="id"
                                value="{{ $invoice->id }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="actual_hours" class="form-label">Actual Hours Worked</label>
                            <input type="number" step="0.5" class="form-control" id="actual_hours" name="actual_hours"
                                value="{{ $invoice->actual_hours }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="final_hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                            <input type="number" class="form-control" id="final_hourly_rate" name="final_hourly_rate"
                                value="{{ $invoice->final_hourly_rate }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="final_materials_cost" class="form-label">Materials Cost (Rs)</label>
                            <input type="number" class="form-control" id="final_materials_cost" name="final_materials_cost"
                                value="{{ $invoice->final_materials_cost }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="final_additional_charges" class="form-label">Additional Charges (Rs)</label>
                            <input type="number" class="form-control" id="final_additional_charges" name="final_additional_charges"
                                value="{{ $invoice->final_additional_charges }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="final_total_amount" class="form-label"><strong>Total Amount (Rs)</strong></label>
                            <input type="number" class="form-control" id="final_total_amount" name="final_total_amount" value="{{ $invoice->final_total_amount ?? '' }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="invoice_notes" class="form-label">Invoice Notes</label>
                            <textarea class="form-control" id="invoice_notes" name="invoice_notes" rows="3" readonly>{{ $invoice->invoice_notes }}</textarea>
                        </div>
                        @if($serviceRequest->status === 'pending-payment')
                        <div class="d-flex mt-4">
                            <a href="{{ route('client.completeServiceRequest', $serviceRequest->id) }}" class="btn btn-success me-2">Pay Now</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ratingModalLabel">Rate the Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ratingForm">
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <div class="star-rating">
                            @for ($i = 1; $i <= 10; $i++)
                                <span class="star" data-rating="{{ $i }}">&#9733;</span>
                                @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitRating">Submit Rating</button>
            </div>
        </div>
    </div>
</div>


@endsection