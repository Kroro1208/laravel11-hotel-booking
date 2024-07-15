@php
    $id = Auth::user()->id;
    $profileData = App\Models\User::find($id);
@endphp
<div class="service-side-bar">
    <div class="services-bar-widget">
        <h3 class="title">ユーザー管理用</h3>
        <div class="side-bar-categories">
            <img src="{{ (!empty($profileData->photo)) ? asset('upload/user_images/'. $profileData->photo) : asset('upload/no_image.jpg') }}"
                class="rounded mx-auto d-block" alt="Image" style="width:100px; height:100px;">
                <br>
                <center>
                    <p>{{ $profileData->name }}</p>
                    <p>{{ $profileData->email }}</p>
                </center>
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}">ダッシュボード</a>
                    </li>
                    <li>
                        <a href="{{ route('user.profile') }}">プロフィール</a>
                    </li>
                    <li>
                        <a href="{{ route('user.password.edit') }}">パスワード変更</a>
                    </li>
                    <li>
                        <a href="#">予約詳細</a>
                    </li>
                    <li>
                        <a href="{{ route('user.logout')}} ">ログアウト</a>
                    </li>
                </ul>
        </div>
    </div>
</div>