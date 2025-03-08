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
            <li class="nav-item nav-category">Master Data</li>

            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Users</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
