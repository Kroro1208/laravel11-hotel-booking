@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">プラン編集: {{ $plan->title }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('plan.update', $plan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <div class="mb-3">
                    <label for="title" class="form-label">タイトル</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $plan->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">説明</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">開始日</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">終了日</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">部屋タイプ</label>
                    @foreach($roomTypes as $roomType)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="room_types[]" value="{{ $roomType->id }}" id="roomType{{ $roomType->id }}" 
                                {{ in_array($roomType->id, old('room_types', $plan->roomTypes->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <label class="form-check-label" for="roomType{{ $roomType->id }}">
                                {{ $roomType->name }}
                            </label>
                            <input type="number" class="form-control form-control-sm d-inline-block w-auto ms-2" 
                                name="room_count[{{ $roomType->id }}]" 
                                value="{{ old('room_count.' . $roomType->id, $plan->roomTypes->find($roomType->id)->pivot->room_count ?? 1) }}" 
                                min="1">
                            <span class="ms-1">部屋</span>
                        </div>
                    @endforeach
                    @error('room_types')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">画像</label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images[]" multiple>
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">現在の画像</label>
                    <div class="row">
                        @foreach($plan->images as $image)
                            <div class="col-md-3 mb-2">
                                <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail" alt="プラン画像">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="{{ route('plan.index') }}" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection