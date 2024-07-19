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
                            <label for="price" class="form-label">基本価格</label>
                            <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required min="0" step="1">
                            @error('price')
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
                            <label for="image" class="form-label">画像</label>
                            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="mt-2 text-center">
                                <img id="showImage" src="{{ asset('upload/no_image.jpg') }}" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
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
                            <label class="form-label">部屋タイプと予約枠</label>
                            <div id="room-types-container">
                                @foreach(old('room_types', [1]) as $index => $roomType)
                                <div class="room-type-entry mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">部屋タイプ</span>
                                        <select name="room_types[]" class="form-select @error('room_types.*') is-invalid @enderror" required>
                                            <option value="">選択してください</option>
                                            <option value="洋室のFamily" {{ old('room_types.'.$index) == '洋室のFamily' ? 'selected' : '' }}>洋室のFamily</option>
                                            <option value="洋室のSingle" {{ old('room_types.'.$index) == '洋室のSingle' ? 'selected' : '' }}>洋室のSingle</option>
                                            <option value="洋室のDouble" {{ old('room_types.'.$index) == '洋室のDouble' ? 'selected' : '' }}>洋室のDouble</option>
                                            <option value="和室のFamily" {{ old('room_types.'.$index) == '和室のFamily' ? 'selected' : '' }}>和室のFamily</option>
                                            <option value="和室のSingle" {{ old('room_types.'.$index) == '和室のSingle' ? 'selected' : '' }}>和室のSingle</option>
                                            <option value="和室のDouble" {{ old('room_types.'.$index) == '和室のDouble' ? 'selected' : '' }}>和室のDouble</option>
                                        </select>
                                        <span class="input-group-text">予約枠</span>
                                        <input type="number" name="room_counts[]" class="form-control @error('room_counts.*') is-invalid @enderror" placeholder="部屋数" value="{{ old('room_counts.'.$index) }}" required min="1">
                                        <button type="button" class="btn btn-danger remove-room-type">削除</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-secondary mt-2" id="add-room-type">部屋タイプを追加</button>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 mb-5">プラン作成</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // 即時関数を使用してグローバルスコープの汚染を防ぐ
    (function() {
        function initializeImagePreview() {
            const imageInput = document.getElementById('image');
            const showImage = document.getElementById('showImage');
            if (imageInput && showImage) {
                imageInput.addEventListener('change', function(e) {
                    console.log('Image input changed');
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            showImage.src = e.target.result;
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            } else {
                console.error('Image input or preview element not found');
            }
        }
    
        function initializeRoomTypeManagement() {
            const addButton = document.getElementById('add-room-type');
            const container = document.getElementById('room-types-container');
            
            if (addButton && container) {
                addButton.addEventListener('click', function() {
                    console.log('Add button clicked');
                    const template = document.querySelector('.room-type-entry');
                    if (template) {
                        const newEntry = template.cloneNode(true);
                        // 新しい要素のIDを一意にする
                        newEntry.id = 'room-type-' + Date.now();
                        container.appendChild(newEntry);
                        console.log('New entry added');
                    } else {
                        console.error('Room type entry template not found');
                    }
                });
    
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-room-type')) {
                        console.log('Remove button clicked');
                        const entry = e.target.closest('.room-type-entry');
                        if (entry && container.children.length > 1) {
                            entry.remove();
                            console.log('Entry removed');
                        }
                    }
                });
            } else {
                console.error('Add button or container not found');
            }
        }
    
        function initialize() {
            console.log('Initializing functionality');
            initializeImagePreview();
            initializeRoomTypeManagement();
            
            // デバッグ情報
            console.log('Total room type entries: ' + document.querySelectorAll('.room-type-entry').length);
            console.log('Add room type button exists: ' + (document.getElementById('add-room-type') !== null));
            console.log('Remove room type buttons exist: ' + (document.querySelectorAll('.remove-room-type').length > 0));
        }
    
        // DOMContentLoadedイベントを使用
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initialize);
        } else {
            // DOMがすでに読み込まれている場合は即時実行
            initialize();
        }
    })();
</script>
@endsection
