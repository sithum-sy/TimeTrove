@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Service Request Details: {{ $serviceRequest->serviceCategory->name }}</h2>
                <form action="" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                    @csrf
                    <button type=" submit" class="btn btn-danger">Cancel Request</button>
                </form>
            </div>
            {{-- Success Message --}}
            @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
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
                            <div class="d-flex mt-4">
                                <a href="{{ route('client.completeServiceRequest', $serviceRequest->id) }}" class="btn btn-success me-2">Complete Service Request</a>

                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-8">


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