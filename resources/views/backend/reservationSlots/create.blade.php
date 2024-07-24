@extends('admin.dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">予約枠の作成</h1>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reservationSlot.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="room_type_id" class="form-label">部屋タイプ</label>
                    <select name="room_type_id" id="room_type_id" class="form-select @error('room_type_id') is-invalid @enderror" required>
                        <option value="">部屋タイプを選択してください</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (最大: {{ $type->number_of_rooms }}部屋)
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">開始日</label>
                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                        value="{{ old('start_date') }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">終了日</label>
                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                        value="{{ old('end_date') }}" required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="available_rooms" class="form-label">利用可能な部屋数</label>
                    <input type="number" name="available_rooms" id="available_rooms" class="form-control @error('available_rooms') is-invalid @enderror"
                        value="{{ old('available_rooms') }}" min="1" required>
                    @error('available_rooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">料金</label>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price') }}" min="0" step="0.01" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">予約枠を作成</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection