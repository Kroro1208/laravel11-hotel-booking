@extends('admin.dashboard')
@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center">ホテル予約プラン一覧</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        @foreach($plans as $plan)
            <div class="col-md-6">
                <div class="card plan-card mb-3">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{{ asset('storage/' . $plan->image) }}" alt="プラン画像" class="img-fluid img-thumbnail">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{ $plan->title }}</h5>
                                <p class="card-text">{{ $plan->description }}</p>
                                <span class="badge {{ $plan->is_reserved ? 'bg-danger' : 'bg-success' }}">
                                    {{ $plan->is_reserved ? '満室' : '予約可能' }}
                                </span>
                                <div class="mt-3">
                                    {{-- {{ route('plans.edit', $plan->id) }} --}}
                                    <a href="" class="btn btn-sm btn-primary">編集</a>
                                    <form method="POST" style="display:inline;">
                                        {{-- action="{{ route('plans.destroy', $plan->id) }}" --}}
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">削除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .plan-card img {
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection
