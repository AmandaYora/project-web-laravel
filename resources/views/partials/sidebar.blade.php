<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('users.index') }}" class="sidebar-brand">
            SLB<span>APP</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">

            @if (Auth::check())
                <li class="nav-item nav-category">Main Menu</li>
                <li class="nav-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="home"></i>
                        <span class="link-title">Dashboard</span>
                    </a>
                </li>
            @endif

            @if (Auth::check() && Auth::user()->role === 'admin')
            <li class="nav-item nav-category">Master Data</li>
                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Users</span>
                    </a>
                </li>
            @endif
            <li class="nav-item nav-category">Project Management</li>
            @if (Auth::check() && in_array(Auth::user()->role, ['admin', 'manager']))
                <li class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <a href="{{ route('projects.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="briefcase"></i>
                        <span class="link-title">Projects</span>
                    </a>
                </li>
            @endif
            @if (Auth::check() && in_array(Auth::user()->role, ['admin', 'manager', 'pengawas']))
                <li class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <a href="{{ route('tasks.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="check-square"></i>
                        <span class="link-title">Tasks</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <a href="{{ route('documents.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="file-text"></i>
                        <span class="link-title">Documents</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
