@extends('frontend.main')
@section('content')
    <!-- Room Area -->
    @include('frontend.home.room-plan', ['plans' => $plans])
    <!-- Room Area End -->

    <!-- FAQ Area -->
    @include('frontend.home.faq')
    <!-- FAQ Area End -->
@endsection
