<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <div class="navbar-header" data-logobg="skin6">
            <a class="navbar-brand" href="/">
                <b class="logo-icon">
                    <img src="{{ asset('assets/images/logo-icon.png') }}" alt="homepage" class="dark-logo" />
                </b>
                <span class="logo-text">
                    <img src="{{ asset('assets/images/logo-text.png') }}" alt="homepage" class="dark-logo" />
                </span>
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/images/users/1.jpg') }}" alt="user" class="profile-pic me-2">Admin
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
