@extends('frontend.main')
@section('content')
    <div class="banner-area" style="height: 480px;">
        <div class="container">
            <div class="banner-content">
                <h1>みんなの予約旅</h1>
            </div>
        </div>
    </div>

    <!-- Room Area -->
    @include('frontend.home.room-plan', ['plans' => $plans])
    <!-- Room Area End -->

    <!-- FAQ Area -->
    @include('frontend.home.faq')
    <!-- FAQ Area End -->
@endsection
