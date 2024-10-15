@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Service Request Details: {{ $serviceRequest->serviceCategory->name }}</h2>
                <form action="{{ route('provider.serviceRequest.reject', $serviceRequest->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to reject this request?');">
                    @csrf
                    @if(!in_array($serviceRequest->status, ['confirmed', 'completed', 'started', 'pending-payment']))
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                    @endif

                </form>
            </div>
            {{-- Success Message --}}
            @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    @if($serviceRequest->status === 'confirmed')
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            Start Service
                        </div>
                        <div class="card-body">
                            <form action="{{ route('provider.startService', $serviceRequest) }}" method="POST">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <label for="security_code" class="form-label">Enter Security Code</label>
                                        <input type="text" class="form-control @error('security_code') is-invalid @enderror"
                                            id="security_code" name="security_code"
                                            placeholder="Enter 6-digit security code" maxlength="6" required>
                                        @error('security_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">Start Service</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

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
                            <p><strong>Status:</strong> <label class="badge bg-{{ $serviceRequest->status == 'assigned' ? 'warning' : 'success' }}">{{ ucfirst($serviceRequest->status) }}</label></p>
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

                @elseif (($serviceRequest->status === 'quoted') || ($serviceRequest->status === 'confirmed'))
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

                @elseif ($serviceRequest->status === 'started')
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            Generate Invoice
                        </div>
                        <div class="card-body">
                            <form action="{{ route('provider.storeInvoice', $serviceRequest->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="service_provider_id" value="{{ auth()->user()->id }}">

                                <div class="mb-3">
                                    <label for="actual_hours" class="form-label">Actual Hours Worked</label>
                                    <input type="number" step="0.5" class="form-control" id="actual_hours" name="actual_hours"
                                        value="{{ $quotation->estimated_hours }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="final_hourly_rate" class="form-label">Hourly Rate (Rs)</label>
                                    <input type="number" class="form-control" id="final_hourly_rate" name="final_hourly_rate"
                                        value="{{ $quotation->hourly_rate }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="final_materials_cost" class="form-label">Materials Cost (Rs)</label>
                                    <input type="number" class="form-control" id="final_materials_cost" name="final_materials_cost"
                                        value="{{ $quotation->materials_cost }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="final_additional_charges" class="form-label">Additional Charges (Rs)</label>
                                    <input type="number" class="form-control" id="final_additional_charges" name="final_additional_charges"
                                        value="{{ $quotation->additional_charges }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="final_total_amount" class="form-label"><strong>Total Amount (Rs)</strong></label>
                                    <input type="number" class="form-control" id="final_total_amount" name="final_total_amount" value="{{ $quotation->total_charges ?? '' }}" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="invoice_notes" class="form-label">Invoice Notes</label>
                                    <textarea class="form-control" id="invoice_notes" name="invoice_notes" rows="3">{{ $quotation->notes }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Send Invoice to Client</button>
                            </form>
                        </div>
                    </div>
                </div>

                @elseif ($serviceRequest->status === 'pending-payment')
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

    function calculateFinalTotal() {
        // Get the values of each input field
        var actualHours = parseFloat(document.getElementById('actual_hours').value) || 0;
        var finalHourlyRate = parseFloat(document.getElementById('final_hourly_rate').value) || 0;
        var finalMaterialsCost = parseFloat(document.getElementById('final_materials_cost').value) || 0;
        var finalAdditionalCharges = parseFloat(document.getElementById('final_additional_charges').value) || 0;

        // Calculate the total amount
        var totalAmount = (actualHours * finalHourlyRate) + finalMaterialsCost + finalAdditionalCharges;

        // Set the calculated total to the final_total_amount input field
        document.getElementById('final_total_amount').value = totalAmount.toFixed(2); // Rounding to 2 decimal places
    }

    // Attach the function to the oninput event of relevant fields
    document.getElementById('actual_hours').oninput = calculateFinalTotal;
    document.getElementById('final_hourly_rate').oninput = calculateFinalTotal;
    document.getElementById('final_materials_cost').oninput = calculateFinalTotal;
</script>

@endsection