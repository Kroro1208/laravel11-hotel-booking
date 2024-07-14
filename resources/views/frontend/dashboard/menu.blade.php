<div class="service-side-bar">
    <div class="services-bar-widget">
        <h3 class="title">ユーザー管理用</h3>
        <div class="side-bar-categories">
            <img src="{{ asset('frontend/assets/img/blog/blog-profile1.jpg') }}" class="rounded mx-auto d-block" alt="Image" style="width:100px; height:100px;"> <br><br>
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}">ダッシュボード</a>
                    </li>
                    <li>
                        <a href="{{ route('user.profile') }}">プロフィール</a>
                    </li>
                    <li>
                        <a href="#">パスワード変更</a>
                    </li>
                    <li>
                        <a href="#">予約詳細</a>
                    </li>
                    <li>
                        <a href="#">ログアウト</a>
                    </li>
                </ul>
        </div>
    </div>
</div>