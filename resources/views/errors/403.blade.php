@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-lock text-danger" style="font-size: 48px;"></i>
                </div>
                <h3 class="text-danger">Access Denied</h3>
                <p class="mb-4">You do not have permission to access this feature.</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:history.back()" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
