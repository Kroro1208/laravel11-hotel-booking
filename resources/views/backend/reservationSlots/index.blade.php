@extends('admin.dashboard')

@section('content')
<div class="container">
    <h1 class="mb-4">予約枠一覧</h1>

    <div class="mb-3">
        <a href="{{ route('reservationSlot.create') }}" class="btn btn-primary">新規予約枠作成</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>部屋タイプ</th>
                <th>日付</th>
                <th>利用可能な部屋数</th>
                <th>価格</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservationSlots as $slot)
                <tr>
                    <td>{{ $slot->id }}</td>
                    <td>{{ $slot->roomType->name }}</td>
                    <td>{{ $slot->date->format('Y-m-d') }}</td>
                    <td>{{ $slot->available_rooms }}</td>
                    <td>{{$slot->price}}</td>
                    <td>
                        <a href="{{ route('reservationSlot.edit', $slot) }}" class="btn btn-sm btn-primary">編集</a>
                        <form action="{{ route('reservationSlot.destroy', $slot) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $reservationSlots->links() }}
</div>
@endsection