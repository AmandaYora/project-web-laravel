<nav class="sidebar">
    <div class="sidebar-header">
      <a href="{{ route('users.index') }}" class="sidebar-brand">
        Task<span>Manager</span>
      </a>
      <div class="sidebar-toggler not-active">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div class="sidebar-body">
      <ul class="nav">
        @if(session('user.role') == 'admin')
        <li class="nav-item nav-category">Master Data</li>
        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
          <a href="{{ route('users.index') }}" class="nav-link">
            <i class="link-icon" data-feather="users"></i>
            <span class="link-title">Users</span>
          </a>
        </li>
        <li class="nav-item {{ request()->routeIs('atms.*') ? 'active' : '' }}">
          <a href="{{ route('atms.index') }}" class="nav-link">
            <i class="link-icon" data-feather="briefcase"></i>
            <span class="link-title">Atm</span>
          </a>
        </li>
        <li class="nav-item {{ request()->routeIs('checks.*') ? 'active' : '' }}">
          <a href="{{ route('checks.index') }}" class="nav-link">
            <i class="link-icon" data-feather="check-circle"></i>
            <span class="link-title">Check</span>
          </a>
        </li>
        <li class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
          <a href="{{ route('tasks.index') }}" class="nav-link">
            <i class="link-icon" data-feather="file"></i>
            <span class="link-title">Task</span>
          </a>
        </li>
        @endif
        <li class="nav-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
          <a href="{{ route('activities.index') }}" class="nav-link">
            <i class="link-icon" data-feather="activity"></i>
            <span class="link-title">Activity</span>
          </a>
        </li>
      </ul>
    </div>
</nav>
  