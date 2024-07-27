@extends('admin.dashboard')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">新しいプランを作成</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('plan.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="mb-4">
                                <label for="title" class="form-label">プラン名</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">詳細</label>
                                <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="images" class="form-label">画像（複数選択可）</label>
                                <input type="file" id="images" name="images[]" class="form-control @error('images') is-invalid @enderror" accept="image/*" multiple required>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="image-preview" class="mt-2 d-flex flex-wrap"></div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">開始日</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">終了日</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">部屋タイプと部屋数</label>
                                @foreach($roomTypes as $roomType)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="form-check flex-grow-1">
                                            <input class="form-check-input" type="checkbox" name="room_types[]" value="{{ $roomType->id }}" id="roomType{{ $roomType->id }}" {{ (is_array(old('room_types')) && in_array($roomType->id, old('room_types'))) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roomType{{ $roomType->id }}">
                                                {{ $roomType->name }}
                                            </label>
                                        </div>
                                        <div class="ms-3" style="width: 120px;">
                                            <input type="number" name="room_count[{{ $roomType->id }}]" class="form-control form-control-sm @error('room_count.'.$roomType->id) is-invalid @enderror" value="{{ old('room_count.'.$roomType->id, 0) }}" min="0" placeholder="部屋数">
                                        </div>
                                    </div>
                                    @error('room_count.'.$roomType->id)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                @endforeach
                                @error('room_types')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5">プラン作成</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function() {
        function initImagePreview() {
            const imageInput = document.getElementById('images');
            const imagePreview = document.getElementById('image-preview');

            imageInput.addEventListener('change', function(e) {
                imagePreview.innerHTML = '';
                for (let file of this.files) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail m-1';
                        img.style.maxWidth = '100px';
                        img.style.maxHeight = '100px';
                        imagePreview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        function init() {
            initImagePreview();
        }

        document.addEventListener('DOMContentLoaded', init);
    })();
</script>
@endpush