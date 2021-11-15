<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
   
    <a class="navbar-brand px-2" href="{{ route('dashboard.index', date('Y')) }}" target="_blank">
        <a class="navbar-brand" href="{{ route('dashboard.index', date('Y')) }}">DW UTS DUDY ALI</a>
        <div class="float-right text-white"><small><i>NIM 20200804033</i></small></div>
    </a>
    <ul class="navbar-nav ml-auto mr-3">
        @if (Auth::check())
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-default text-white">
                        Logout
                    </button>
                </form>
            </li>
        @endif
    </ul>
</nav>