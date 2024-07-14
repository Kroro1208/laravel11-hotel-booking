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
                            <li>User Dashboard </li>
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
                            <div class="service-side-bar">
                                <div class="services-bar-widget">
                                    <h3 class="title">Others Services</h3>
                                    <div class="side-bar-categories">
                                        <img src="assets/img/blog/blog-profile1.jpg" class="rounded mx-auto d-block" alt="Image" style="width:100px; height:100px;"> <br><br>
                                        <ul>
                                            <li>
                                                <a href="#">User Dashboard</a>
                                            </li>
                                            <li>
                                                <a href="#">User Profile </a>
                                            </li>
                                            <li>
                                                <a href="#">Change Password</a>
                                            </li>
                                            <li>
                                                <a href="#">Booking Details </a>
                                            </li>
                                            <li>
                                                <a href="#">Logout </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="service-article">
                <section class="checkout-area pb-70">
                    <div class="container">
                        <form>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="billing-details">
                                        <h3 class="title">User Profile</h3>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label>First Name <span class="required">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label>Last Name <span class="required">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label>Company Name</label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label>Email Address <span class="required">*</span></label>
                                                    <input type="email" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="form-group">
                                                    <label>Phone <span class="required">*</span></label>
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-6">
                                                <div class="form-group">
                                                    <label>User Profile  <span class="required">*</span></label>
                                                    <input type="file" class="form-control">
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
<!-- Service Details Area End -->
@endsection