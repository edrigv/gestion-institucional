<aside class="sidebar">
    <!-- 1. CABECERA FIJA SUPERIOR -->
    <div class="sidebar-header brand">
        <div class="brand-row">
            <img src="{{ asset('images/escudo-cristo-rey.png') }}" class="brand-mark-img" alt="Escudo Cristo Rey">
            <div class="brand-info">
                <div class="brand-name">Cristo Rey</div>
                <div class="brand-sub">U.E. Particular</div>
                <div class="brand-inst">Gestión Institucional</div>
            </div>
        </div>
        <div class="brand-line"></div>
    </div>

    <!-- 2. CUERPO DE NAVEGACIÓN CON DESPLAZAMIENTO INTERNO -->
    <div class="sidebar-body">
        <div class="nav-label">Navegación</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">▦</span><span>Panel principal</span>
            </a>
            <a href="{{ route('requerimientos.index') }}" class="{{ request()->routeIs('requerimientos.*') ? 'active' : '' }}">
                <span class="nav-icon">▤</span><span>Requerimientos</span>
                @if(!empty($badgeReqPendientes) && $badgeReqPendientes > 0)
                    <span class="nav-badge gold">{{ $badgeReqPendientes }}</span>
                @endif
            </a>
            <a href="{{ route('tipos-requerimiento.index') }}" class="{{ request()->routeIs('tipos-requerimiento.*') ? 'active' : '' }}">
                <span class="nav-icon">◇</span><span>Tipos requerimiento</span>
            </a>
            <a href="{{ route('mensajes.index') }}" class="{{ request()->routeIs('mensajes.*') ? 'active' : '' }}">
                <span class="nav-icon">✉</span><span>Mensajes</span>
                @if(!empty($badgeTotalMensajes) && $badgeTotalMensajes > 0)
                    <span class="nav-badge sky">{{ $badgeTotalMensajes }}</span>
                @endif
            </a>
            <a href="{{ route('reservas.index') }}" class="{{ request()->routeIs('reservas.index') || request()->routeIs('reservas.show') ? 'active' : '' }}">
                <span class="nav-icon">▣</span><span>Mis reservas</span>
            </a>
        </nav>

        <div class="nav-label">Acciones</div>
        <nav class="nav">
            <a href="{{ route('requerimientos.create') }}">
                <span class="nav-icon">＋</span><span>Nuevo requerimiento</span>
            </a>
            <a href="{{ route('mensajes.create') }}">
                <span class="nav-icon">＋</span><span>Nuevo mensaje</span>
            </a>
            <a href="{{ route('reservas.horario') }}" class="{{ request()->routeIs('reservas.horario') ? 'active' : '' }}">
                <span class="nav-icon">▦</span><span>Horario y reservas</span>
            </a>
            <a href="{{ route('reservas.gestion') }}" class="{{ request()->routeIs('reservas.gestion') ? 'active' : '' }}">
                <span class="nav-icon">✓</span><span>Gestionar reservas</span>
                @if(!empty($badgeResPendientes) && $badgeResPendientes > 0)
                    <span class="nav-badge red">{{ $badgeResPendientes }}</span>
                @endif
            </a>
        </nav>

        @php
            $esAdminSidebar = strtoupper((string)($usuarioSesion?->perfil?->CODIGO_PFL ?? '')) === 'ADM'
                || str_contains(strtoupper((string)($usuarioSesion?->perfil?->NOMBRE_PFL ?? '')), 'ADMIN');
        @endphp

        @if($esAdminSidebar)
            <div class="nav-label">Administración</div>
            <nav class="nav">
                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') || request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    <span class="nav-icon">⚙</span><span>Panel de Administración</span>
                </a>
                <a href="{{ route('espacios.index') }}" class="{{ request()->routeIs('espacios.*') ? 'active' : '' }}">
                    <span class="nav-icon">⌂</span><span>Espacios</span>
                </a>
            </nav>
        @endif
    </div>

    <!-- 3. PARTE INFERIOR FIJA (USUARIO, TEMA, CERRAR SESIÓN, LEMA) -->
    <div class="sidebar-bottom">
        <div class="sidebar-account">
            <div class="sidebar-user-name">{{ $usuarioSesion->nombre_completo ?? 'Usuario' }}</div>
            <div class="sidebar-user-role">{{ $usuarioSesion?->perfil?->NOMBRE_PFL ?? 'Sin perfil' }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout">Cerrar sesión</button>
            </form>
        </div>

        <div class="sidebar-footer">
            <em>"Formando hombres y mujeres para los demás"</em>
            <span>U.E.P. Cristo Rey · Portoviejo</span>
        </div>
    </div>

    <script src="{{ asset('js/theme.js') }}"></script>
</aside>