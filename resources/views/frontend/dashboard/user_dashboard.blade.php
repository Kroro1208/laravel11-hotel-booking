@extends('frontend.main')
@section('content')
@if(session('message'))
    <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
        <div class="d-flex justify-content-center align-items-center">
            <i class="bi me-2 
                @if(session('alert-type') == 'success') bi-check-circle-fill text-success
                @elseif(session('alert-type') == 'danger') bi-exclamation-triangle-fill text-danger
                @elseif(session('alert-type') == 'info') bi-exclamation-circle-fill text-info
                @else bi-info-circle-fill text-info
                @endif" style="font-size: 1.25rem;"></i>
            <strong class="flex-grow-1 text-center">{{ session('message') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="service-details-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @include('frontend.dashboard.menu')
            </div>
            <div class="col-lg-9">
                <div class="service-article">
                    <div class="service-article-title">
                        <h2>User Dashboard</h2>
                    </div>
                    <div class="service-article-content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                                    <div class="card-header">全ての予約</div>
                                        <div class="card-body">
                                            <h1 class="card-title" style="font-size: 45px;">3 Total</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card text-white bg-warning mb-3" style="max-width: 18rem;">
                                        <div class="card-header">Pending Booking</div>
                                            <div class="card-body">
                                                <h1 class="card-title" style="font-size: 45px;">3 Pending</h1>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card text-white bg-success mb-3" style="max-width: 18rem;">
                                            <div class="card-header">Complete Booking</div>
                                                <div class="card-body">
                                                    <h1 class="card-title" style="font-size: 45px;">3 Complete</h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service Details Area End -->
@endsection