<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            ABSENSI<span>APP</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div class="sidebar-body">
        <ul class="nav">
            <!-- Dashboard Tampil untuk Semua Role -->
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i>
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            @if(session('user.role') === 'admin')
                <li class="nav-item nav-category">Master Data</li>

                <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="users"></i>
                        <span class="link-title">Users</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('guru.*') ? 'active' : '' }}">
                    <a href="{{ route('guru.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="user-check"></i>
                        <span class="link-title">Guru</span>
                    </a>
                </li>
            @endif

            @if(session('user.role') === 'admin' || session('user.role') === 'guru')
                <li class="nav-item {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="user"></i>
                        <span class="link-title">Siswa</span>
                    </a>
                </li>
            @endif

            @if(session('user.role') === 'admin')
                <li class="nav-item nav-category">Academic</li>

                <li class="nav-item {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <a href="{{ route('subjects.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="book"></i>
                        <span class="link-title">Subjects</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <a href="{{ route('classes.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="grid"></i>
                        <span class="link-title">Classes</span>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('jurusan.*') ? 'active' : '' }}">
                    <a href="{{ route('jurusan.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="layers"></i>
                        <span class="link-title">Jurusan</span>
                    </a>
                </li>
            @endif

            @if(session('user.role') === 'admin' || session('user.role') === 'guru' || session('user.role') === 'siswa')
                <li class="nav-item nav-category">Schedule & Attendance</li>
            @endif

            @if(session('user.role') === 'admin' || session('user.role') === 'guru' || session('user.role') === 'siswa')
                <li class="nav-item {{ request()->routeIs('mapel.*') ? 'active' : '' }}">
                    <a href="{{ route('mapel.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="calendar"></i>
                        <span class="link-title">Schedule</span>
                    </a>
                </li>
            @endif

            @if(session('user.role') === 'admin' || session('user.role') === 'guru')
                <li class="nav-item {{ request()->routeIs('sessions.*') ? 'active' : '' }}">
                    <a href="{{ route('sessions.index') }}" class="nav-link">
                        <i class="link-icon" data-feather="clock"></i>
                        <span class="link-title">Class Sessions</span>
                    </a>
                </li>
            @endif

            <li class="nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                <a href="{{ route('attendance.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="check-square"></i>
                    <span class="link-title">Attendance</span>
                </a>
            </li>

            @if(session('user.role') === 'siswa')
                <li class="nav-item {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <a href="{{ route('attendance.scan') }}" class="nav-link">
                        <i class="link-icon" data-feather="camera"></i>
                        <span class="link-title">Scan Attendance</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
