@extends('frontend.main')
@section('content')
            <!-- Inner Banner -->
            <div class="inner-banner inner-bg6">
                <div class="container">
                    <div class="inner-title">
                        <ul>
                            <li>
                                <a href="index.html">Home</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>User Dashboard</li>
                        </ul>
                        <h3>User Dashboard</h3>
                    </div>
                </div>
            </div>
            <!-- Inner Banner End -->

            <!-- Service Details Area -->
            <div class="service-details-area pt-100 pb-70">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3">
                            @include('frontend.dashboard.menu')
                        </div>
                        <div class="col-lg-9">
                            <div class="service-article">
                                <section class="checkout-area pb-70">
                                    <div class="container">
                                        <form>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="billing-details">
                                                        <h3 class="title">あなたのプロフィール</h3>
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label>お名前<span class="required">*</span></label>
                                                                    <input name="name" value="{{ $profileData->name }}" type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label>メールアドレス<span class="required">*</span></label>
                                                                    <input name="email" value="{{ $profileData->email }}" type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label>住所<span class="required">*</span></label>
                                                                    <input name="address" value="{{ $profileData->address }}" type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label>電話番号<span class="required">*</span></label>
                                                                    <input name="phone" value="{{ $profileData->phone }}" type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label>画像<span class="required">*</span></label>
                                                                    <input type="file" name="photo" id="image" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-md-6">
                                                                <div class="form-group">
                                                                    <label>Town / City <span class="required">*</span></label>
                                                                    <input type="text" class="form-control">
                                                                </div>
                                                            </div>
                                                            <button type="submit" class="btn btn-danger">Save Changes </button>
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