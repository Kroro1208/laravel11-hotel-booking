<header class="top-header top-header-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-2 pr-0">
                <div class="language-list">
                    <select class="language-list-item">
                        <option>English</option>
                        <option>العربيّة</option>
                        <option>Deutsch</option>
                        <option>Português</option>
                        <option>简体中文</option>
                    </select>
                </div>
            </div>

            <div class="col-lg-9 col-md-10">
                <div class="header-right">
                    <ul>
                        <li>
                            <i class='bx bx-home-alt'></i>
                            <a href="#">東京都港区東新橋6-18-5</a>
                        </li>
                        <li>
                            <i class='bx bx-phone-call'></i>
                            <a href="tel:+1-(123)-456-7890">090-9533-7513</a>
                        </li>
                        @auth
                            <li>
                                <i class='bx bxs-user-pin'></i>
                                <a href="{{ route('dashboard') }}">ダッシュボード</a>
                            </li>
                            <li>
                                <i class='bx bxs-user-rectangle'></i>
                                <a href="{{ route('user.logout') }}">ログアウト</a>
                            </li>
                        @else
                            <li>
                                <i class='bx bxs-user-pin'></i>
                                <a href="{{ route('admin.login') }}">管理人ログイン</a>
                            </li>
                            <li>
                                <i class='bx bxs-user-pin'></i>
                                <a href="{{ route('login') }}">ログイン</a>
                            </li>
                            <li>
                                <i class='bx bxs-user-rectangle'></i>
                                <a href="{{ route('register') }}">登録</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>