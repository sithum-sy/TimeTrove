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
            <div class="card">
                <div class="card-header">Service Provider Section</div>

                <div class="card-body">
                    <p>Welcome, Author! Here are your author tasks.</p>

                    <a href="" class="btn btn-primary btn-lg mb-3">Create Publication</a>
                    <a href="" class="btn btn-primary btn-lg mb-3">View Publication</a>

                </div>
            </div>
            @else (Auth::user()->role === 'client')
            <div class="row bg-light mx-2 p-3">
                <h2 class="text-center mb-4">All Publications</h2>

            </div>


            @endif
            @endauth
        </div>
    </div>
</div>
</div>
@endsection