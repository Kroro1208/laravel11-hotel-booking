@extends('admin.dashboard')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
    <div class="container">
        <div class="main-body">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <form action="{{ route('plan.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <h6 class="mb-0">見出し</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" />
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <h6 class="mb-0">詳細</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <textarea class="form-control" id="input40" name="description" rows="3" placeholder="プランの詳細">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-2">
                                        <h6 class="mb-0">画像</h6>
                                    </div>
                                    <div class="form-group col-sm-9 text-secondary">
                                        <input class="form-control" name="image" type="file" id="image">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 text-center">
                                        <img id="showImage" src="{{ asset('upload/no_image.jpg') }}" alt="Image" class="rounded-circle p-1 bg-primary" width="100" height="100">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <input type="submit" class="btn btn-primary px-4" value="Save Changes" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    });
</script>
@endsection
