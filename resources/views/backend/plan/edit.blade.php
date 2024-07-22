@extends('admin.dashboard')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <div class="page-content">
        <div class="container">
            <div class="main-body">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">プランを編集</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('plan.update', $plan) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-4">
                                        <label for="title" class="form-label">プラン名</label>
                                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $plan->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="price" class="form-label">基本価格</label>
                                        <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $plan->price) }}" required min="0" step="1">
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="description" class="form-label">詳細</label>
                                        <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $plan->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="image" class="form-label">画像</label>
                                        <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-2 text-center">
                                            <img id="showImage" src="{{ asset($plan->image ? 'storage/'.$plan->image : 'upload/no_image.jpg') }}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">開始日</label>
                                            <input type="date" id="start_date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $plan->start_date->format('Y-m-d')) }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">終了日</label>
                                            <input type="date" id="end_date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $plan->end_date->format('Y-m-d')) }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">部屋タイプと予約枠</label>
                                        <div id="room-types-container">
                                            @foreach($plan->planRooms as $planRoom)
                                                <div class="room-type-entry mb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text">部屋タイプ</span>
                                                        <select name="room_types[]" class="form-select @error('room_types.*') is-invalid @enderror" required>
                                                            <option value="">選択してください</option>
                                                            @foreach($roomTypes as $type)
                                                                <option value="{{ $type->id }}" {{ $planRoom->roomType->id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="input-group-text">予約枠</span>
                                                        <input type="number" name="room_counts[]" class="form-control @error('room_counts.*') is-invalid @enderror" placeholder="部屋数" required min="1" value="{{ $planRoom->room_count }}">
                                                        <button type="button" class="btn btn-danger remove-room-type">削除</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-secondary mt-2" id="add-room-type">部屋タイプを追加</button>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg px-5 mb-5">プラン更新</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
        function initImagePreview() {
            $('#image').change(function(e) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#showImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.target.files[0]);
            });
        }

        function initRoomTypeManagement() {
            const addButton = document.getElementById('add-room-type');
            const container = document.getElementById('room-types-container');

            addButton.addEventListener('click', () => {
                const template = document.querySelector('.room-type-entry');
                if (!template) return console.error('クラスが見つかりません');

                const newEntry = template.cloneNode(true);
                newEntry.querySelector('select').selectedIndex = 0;
                newEntry.querySelector('input[type="number"]').value = '';
                container.appendChild(newEntry);
            });

            container.addEventListener('click', e => {
                if (e.target.classList.contains('remove-room-type')) {
                    const entry = e.target.closest('.room-type-entry');
                    if (entry && container.children.length > 1) entry.remove();
                }
            });
        }

        initImagePreview();
        initRoomTypeManagement();
    });
</script>
@endsection