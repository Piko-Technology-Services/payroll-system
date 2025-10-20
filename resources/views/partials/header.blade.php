<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin6">
            <a class="navbar-brand" href="/">
                <b class="logo-icon">
                    <img width="100px" src="{{ asset('assets/images/logo-icon.png') }}" alt="homepage" class="dark-logo" />
                </b>
                
            </a>
            <a class="nav-toggler d-block d-md-none text-dark" href="javascript:void(0)">
                <i class="ti-menu ti-close"></i>
            </a>
        </div>
        <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
            <ul class="navbar-nav me-auto mt-md-0">
                <li class="nav-item hidden-sm-down">
                    <form class="app-search ps-3">
                        <input type="text" class="form-control" placeholder="Search...">
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav">
                @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">Sign up</a>
                </li>
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-bs-toggle="dropdown">
                       {{ auth()->user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                            @csrf
                            <button type="submit" class="btn btn-link dropdown-item">Logout</button>
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </nav>
</header>
