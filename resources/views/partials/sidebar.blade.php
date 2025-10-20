<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('employees.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                        <i class="bi bi-people me-2"></i>Employees
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('payslips.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark {{ request()->routeIs('payslips.*') ? 'active' : '' }}" href="{{ route('payslips.index') }}">
                        <i class="bi bi-file-earmark-text me-2"></i>Payslips
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
