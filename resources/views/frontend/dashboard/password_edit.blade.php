@extends('frontend.main')
@section('content')
    <!-- Service Details Area -->
    <div class="service-details-area pt-100 pb-70">
        <div class="container">
            @if(session('message'))
                <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                    <div class="d-flex justify-content-center align-items-center">
                        <i class="bi me-2
                            @if(session('alert-type') == 'success') bi-check-circle-fill text-success
                            @elseif(session('alert-type') == 'danger') bi-exclamation-triangle-fill text-danger
                            @elseif(session('alert-type') == 'warning') bi-exclamation-circle-fill text-warning
                            @else bi-info-circle-fill text-info
                            @endif" style="font-size: 1.25rem;"></i>
                        <strong class="flex-grow-1 text-center">{{ session('message') }}</strong>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-3">
                    @include('frontend.dashboard.menu')
                </div>
                <div class="col-lg-9">
                    <div class="service-article">
                        <section class="checkout-area pb-70">
                            <div class="container">
                                <form action="{{ route('user.password.update') }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="billing-details">
                                                <h3 class="title">パスワード更新</h3>
                                                <div class="row mt-5">
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="form-group">
                                                            <label>旧パスワード<span class="required">*</span></label>
                                                            <input name='old_password' id="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" />
                                                            @error('old_password')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="form-group">
                                                            <label>新パスワード<span class="required">*</span></label>
                                                            <input name='new_password' id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" />
                                                            @error('new_password')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="form-group">
                                                            <label>確認用パスワード<span class="required">*</span></label>
                                                            <input name='new_password_confirmation' id="new_password_confirmation" type="password" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12">
                                                        <div class="form-group">
                                                            <button type="submit" class="btn btn-primary w-100">更新する</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Service Details Area End -->
@endsection