<ul class="nav nav-tabs mt-5">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard.perbulan') }}">Ringkasan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.form') }}">Laporan</a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link {{ request()->is('upload*') ? 'active' : '' }}" href="{{ route('upload.index') }}">Upload Realisasi</a>
    </li> --}}
    {{-- <li class="nav-item">
        <a class="nav-link {{ request()->is('master*') ? 'active' : '' }}" href="{{ route('pengguna.index') }}">Master Data</a>
    </li> --}}
    {{-- <li class="nav-item">
        <a class="nav-link {{ (request()->is('laporan*')) ? 'active' : '' }}" href="{{ route('laporan.index') }}">Laporan</a>
    </li> --}}
</ul>