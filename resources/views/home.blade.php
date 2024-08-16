@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-8">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif


            @auth
            @if (Auth::user()->role === 'admin')

            @include('admin/admin-panel')

            @elseif (Auth::user()->role === 'scheduler')

            @include('scheduler/scheduler-panel')

            @elseif (Auth::user()->role === 'service_provider')

            @include('provider/panel')

            @else (Auth::user()->role === 'client')

            @include('client/panel')

            @endif
            @endauth
        </div>
    </div>
</div>
</div>
@endsection