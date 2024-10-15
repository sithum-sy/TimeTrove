@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="mb-4">Service Request Details: {{ $serviceRequest->serviceCategory->name }}</h2>
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
                            <p><strong>Location:</strong> {{ $serviceRequest->location}}</p>
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
                </div>

                @if ($serviceRequest->status === 'pending')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ $serviceRequest->serviceCategory->name }} Service Providers
                        </div>

                        <div class="card-body">
                            <form action="{{ route('scheduler.assignProvider', $serviceRequest->id) }}" method="POST">
                                @csrf
                                @method('POST')
                                <div class="form-group">
                                    <label for="service_provider">Select Service Provider</label>
                                    <select id="service_provider" name="service_provider_id" class="form-control">
                                        @foreach($serviceProviders as $provider)
                                        <option value="{{ $provider->id }}">
                                            {{ $provider->first_name }} {{ $provider->last_name }} - Rating:
                                            ( {{ number_format($provider->averageRating(), 1) }}/5.0)
                                            ({{ $provider->ratingCount() }} {{ Str::plural('review', $provider->ratingCount()) }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Assign Provider</button>
                            </form>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 'quoted' || $serviceRequest->status === 're-quoted')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">Quotation For Service Request</div>
                        <div class="card-body">
                            @csrf
                            <div class="mb-3">
                                <label for="service_provider_id" class="form-label">Service Provider ID</label>
                                <input type="text" class="form-control" id="service_provider_id" name="service_provider_id" value="{{ $quotation->service_provider_id ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="service_provider_name" class="form-label">Service Provider Name</label>
                                <input type="text" class="form-control" id="service_provider_name" name="service_provider_name" value="{{ $quotation->serviceProvider->first_name ?? '' }} {{ $quotation->serviceProvider->last_name ?? '' }}"
                                    readonly>
                            </div>
                            <div class="mb-3">
                                <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" value="{{ $quotation->estimated_hours ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $quotation->hourly_rate ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                <input type="number" class="form-control" id="materials_cost" name="materials_cost" value="{{ $quotation->materials_cost ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                <input type="number" class="form-control" id="additional_charges" name="additional_charges" value="{{ $quotation->additional_charges ?? '0' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                <input type="number" class="form-control" id="total_charges" name="total_charges" value="{{ $quotation->total_charges ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" readonly>{{ $quotation->notes ?? '' }}</textarea>
                            </div>

                            <div class="d-flex mt-4">
                                <a href="{{ route('scheduler.requestNewQuote', $serviceRequest->id) }}" class="btn btn-warning me-2">Request New Quote</a>
                                <a href="{{ route('scheduler.passToClient', $serviceRequest->id) }}" class="btn btn-success">Pass to Client</a>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 'pending-approval')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">Quotation For Service Request</div>
                        <div class="card-body">
                            @csrf
                            <div class="mb-3">
                                <label for="service_provider_id" class="form-label">Service Provider ID</label>
                                <input type="text" class="form-control" id="service_provider_id" name="service_provider_id" value="{{ $quotation->service_provider_id ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="service_provider_name" class="form-label">Service Provider Name</label>
                                <input type="text" class="form-control" id="service_provider_name" name="service_provider_name" value="{{ $quotation->serviceProvider->first_name ?? '' }} {{ $quotation->serviceProvider->last_name ?? '' }}"
                                    readonly>
                            </div>
                            <div class="mb-3">
                                <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" value="{{ $quotation->estimated_hours ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $quotation->hourly_rate ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                <input type="number" class="form-control" id="materials_cost" name="materials_cost" value="{{ $quotation->materials_cost ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                <input type="number" class="form-control" id="additional_charges" name="additional_charges" value="{{ $quotation->additional_charges ?? '0' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                <input type="number" class="form-control" id="total_charges" name="total_charges" value="{{ $quotation->total_charges ?? '' }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" readonly>{{ $quotation->notes ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 'pending-payment' || $serviceRequest->status === 'completed')
                <div class="col-md-8">
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
                                    value="{{ $invoice->id }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="actual_hours" class="form-label">Actual Hours Worked</label>
                                <input type="number" step="0.5" class="form-control" id="actual_hours" name="actual_hours"
                                    value="{{ $invoice->actual_hours }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="final_hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                <input type="number" class="form-control" id="final_hourly_rate" name="final_hourly_rate"
                                    value="{{ $invoice->final_hourly_rate }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="final_materials_cost" class="form-label">Materials Cost (Rs)</label>
                                <input type="number" class="form-control" id="final_materials_cost" name="final_materials_cost"
                                    value="{{ $invoice->final_materials_cost }}" required>
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
                                <textarea class="form-control" id="invoice_notes" name="invoice_notes" rows="3">{{ $invoice->invoice_notes }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection