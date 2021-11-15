<div class="nav flex-column nav-pills">
    {{-- <a class="nav-link {{ (request()->is('master/channel')) ? 'active' : '' }}" href="{{ route('channel.index') }}">Data Channel</a> --}}
    <a class="nav-link {{ (request()->is('master/pengguna')) ? 'active' : '' }}" href="{{ route('pengguna.index') }}">Data Pengguna</a>
    <a class="nav-link {{ (request()->is('master/kecamatan') || request()->is('master/kelurahan*')) ? 'active' : '' }}" href="{{ route('kecamatan.index') }}">Data Kecamatan</a>
</div>