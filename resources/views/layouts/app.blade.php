<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Best Choice Manufacturing Payroll')</title>

    <!-- SEO & Company Meta -->
    <meta name="description" content="Best Choice Trading and Manufacturing Ltd Payroll Management System. Efficient, secure, and accurate payroll management for employees in Zambia.">
    <meta name="keywords" content="Payroll, Best Choice Trading, Manufacturing, Zambia, Employee Management, Salary, Leave, HR">
    <meta name="author" content="Best Choice Trading and Manufacturing Ltd">
    <meta name="company" content="Best Choice Trading and Manufacturing Ltd">
    <meta name="contact" content="Phone: +260 772809898 / +260 975232444, Email: info@bestchoicezambia.com, sohel@bestchoicezambia.com">
    <meta name="address" content="Plot: 10096/7 Off Mumbwa Rd, Chinka Industrial Area, Private Bag: E891-15, Post.Net Lusaka-Zambia">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/logo-single.png') }}">

    <!-- CSS Plugins -->
    <link href="{{ asset('assets/plugins/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body>
    <div class="preloader">
        <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
    </div>

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full">
        @include('partials.header')
        @include('partials.sidebar')

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-md-6 col-8 align-self-center">
                        <h3 class="page-title mb-0 p-0">@yield('page-title')</h3>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @if(request()->routeIs('dashboard'))
                                <li class="breadcrumb-item active">@yield('page-title')</li>
                            @else
                                <li class="breadcrumb-item active">@yield('page-title')</li>
                            @endif
                        </ol>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                @yield('content')
            </div>

            <footer class="footer text-center">
                © {{ date('Y') }} Monster Admin by 
                <a href="https://www.wrappixel.com/">wrappixel.com</a>
            </footer>
        </div>
    </div>

    <!-- Core JS (jQuery, Bootstrap, etc.) -->
    <script src="{{ asset('assets/plugins/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('js/waves.js') }}"></script>
    <script src="{{ asset('js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <!-- Bootstrap 5 Bundle (for modals, dropdowns, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Place this at the very end -->
    @yield('scripts')
</body>
</html>
