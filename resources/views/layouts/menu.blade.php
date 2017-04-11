<header class="menu-lateral" role="banner">
    <div class="nav-header">
        <a class="logo" href="{{ url('/home') }}">
            <img src="{{ url('img/logo_FRONT-WHITE.png') }}" alt="Diakonia Logo" class="img-responsive" />
        </a>
    </div>
    <nav class="nav" role="navigation">
        <ul class="nav__list">
            @can('user-list')
                @include('layouts.menus.usuarios')
            @endcan

            @can('membro-list')
                @include('layouts.menus.membros')
            @endcan

            @include('layouts.menus.eventos')
            @include('layouts.menus.geral')

        </ul>
    </nav>
</header>
