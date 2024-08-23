@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Service Request Details: {{ $serviceRequest->serviceCategory->name }}</h2>
                <form action="{{ route('provider.serviceRequest.reject', $serviceRequest->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to reject this request?');">
                    @csrf
                    <button type=" submit" class="btn btn-danger">Reject Request</button>
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

                @if ($serviceRequest->status === 'assigned')

                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            Quotation For Service Request
                        </div>
                        <div class="card-body">
                            <form action="{{ route('provider.storeQuotation', $serviceRequest->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

                                <div class="mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" required>
                                </div>

                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" required>
                                </div>

                                <div class="mb-3">
                                    <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                    <input type="number" class="form-control" id="materials_cost" name="materials_cost" required>
                                </div>

                                <div class="mb-3">
                                    <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                    <input type="number" class="form-control" id="additional_charges" name="additional_charges" value="0">
                                </div>

                                <div class="mb-3">
                                    <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                    <input type="number" class="form-control" id="total_charges" name="total_charges" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Send Quotation</button>
                            </form>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 'quoted' || $serviceRequest->status === 'confirmed')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            Quotation For Service Request
                        </div>
                        <div class="card-body">
                            @csrf
                            <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

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
                @elseif ($serviceRequest->status === 'new-quote-requested')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            Quotation For Service Request
                        </div>
                        <div class="card-body">
                            <form action="{{ route('provider.reQuote', $quotation->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

                                <div class="mb-3">
                                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                                    <input type="number" class="form-control" id="estimated_hours" name="estimated_hours" value="{{ $quotation->estimated_hours ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" value="{{ $quotation->hourly_rate ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="materials_cost" class="form-label">Materials Cost (Rs)</label>
                                    <input type="number" class="form-control" id="materials_cost" name="materials_cost" value="{{ $quotation->materials_cost ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="additional_charges" class="form-label">Additional Charges (Rs)</label>
                                    <input type="number" class="form-control" id="additional_charges" name="additional_charges" value="{{ $quotation->additional_charges ?? '0' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="total_charges" class="form-label"><strong>Total Charges (Rs)</strong></label>
                                    <input type="number" class="form-control" id="total_charges" name="total_charges" value="{{ $quotation->total_charges ?? '' }}">
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $quotation->notes ?? '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Updated Quotation</button>
                            </form>
                        </div>
                    </div>
                </div>
                @elseif ($serviceRequest->status === 're-quoted')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            Re-Quotation For Service Request
                        </div>
                        <div class="card-body">
                            @csrf
                            <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

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
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function calculateTotal() {
            const estimatedHours = parseFloat(document.getElementById('estimated_hours').value) || 0;
            const hourlyRate = parseFloat(document.getElementById('hourly_rate').value) || 0;
            const materialsCost = parseFloat(document.getElementById('materials_cost').value) || 0;
            const additionalCharges = parseFloat(document.getElementById('additional_charges').value) || 0;

            const totalCharges = (estimatedHours * hourlyRate) + materialsCost + additionalCharges;

            document.getElementById('total_charges').value = totalCharges.toFixed(2);
        }

        document.getElementById('estimated_hours').addEventListener('input', calculateTotal);
        document.getElementById('hourly_rate').addEventListener('input', calculateTotal);
        document.getElementById('materials_cost').addEventListener('input', calculateTotal);
        document.getElementById('additional_charges').addEventListener('input', calculateTotal);
    });
</script>

@endsection