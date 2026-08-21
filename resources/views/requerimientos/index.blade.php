<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requerimientos · Unidad Educativa Cristo Rey</title>
    <link rel="icon" type="image/png" href="{{ asset('images/escudo-cristo-rey.png') }}">
    <link rel="stylesheet" href="{{ asset('css/institutional.css') }}">
</head>
<body>
<div class="app-shell">
    @include('partials.sidebar')
    <main class="app-main">
        <header class="topbar">
            <div class="topbar-title">Requerimientos</div>
            <div class="topbar-right"><span class="user-pill">Modo prototipo</span></div>
        </header>
        <div class="page-content">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            <div class="page-header">
                <div><h1 class="page-title">Requerimientos</h1><p class="page-subtitle">Consulta, filtra y gestiona los expedientes registrados.</p></div>
                <div class="actions"><a href="{{ route('requerimientos.create') }}" class="btn btn-gold">＋ Nuevo requerimiento</a></div>
            </div>

            <form action="{{ route('requerimientos.index') }}" method="GET" class="filter-bar">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por número o asunto..." style="flex:1;min-width:220px!important">
                <select name="departamento">
                    <option value="">Todos los departamentos</option>
                    @foreach($departamentos as $dep)
                        <option value="{{ $dep->SERIAL_DEP }}" @selected(request('departamento') == $dep->SERIAL_DEP)>{{ $dep->DESCRIPCION_DEP }}</option>
                    @endforeach
                </select>
                <select name="estado">
                    <option value="">Todos los estados</option>
                    @foreach(['ENVIADO','RECIBIDO','DERIVADO','ASIGNADO','EN_PROCESO','PENDIENTE_CORRECCION','PENDIENTE_APROBACION','APROBADO','PENDIENTE_FIRMA','FIRMADO','ATENDIDO','CERRADO','RECHAZADO','ANULADO'] as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ str_replace('_',' ',$estado) }}</option>
                    @endforeach
                </select>
                <select name="prioridad">
                    <option value="">Todas las prioridades</option>
                    @foreach(['BAJA','MEDIA','ALTA','URGENTE'] as $prioridad)
                        <option value="{{ $prioridad }}" @selected(request('prioridad') === $prioridad)>{{ $prioridad }}</option>
                    @endforeach
                </select>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" title="Desde fecha" style="width:140px!important">
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" title="Hasta fecha" style="width:140px!important">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('requerimientos.index') }}" class="btn">Limpiar</a>
            </form>

            <div class="table-wrap">
                <table>
                    <thead><tr><th>Número</th><th>Asunto</th><th>Tipo</th><th>Prioridad</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr></thead>
                    <tbody>
                    @forelse($requerimientos as $requerimiento)
                        <tr>
                            <td class="req-number">{{ $requerimiento->NUMERO_REQ }}</td>
                            <td class="truncate">{{ $requerimiento->ASUNTO_REQ }}</td>
                            <td>{{ $requerimiento->tipo->NOMBRE_TREQ ?? 'Sin tipo' }}</td>
                            <td><span class="priority priority-{{ $requerimiento->PRIORIDAD_REQ }}">{{ $requerimiento->PRIORIDAD_REQ }}</span></td>
                            <td><span class="badge estado-{{ $requerimiento->ESTADO_REQ }}">{{ str_replace('_',' ',$requerimiento->ESTADO_REQ) }}</span></td>
                            <td>{{ optional($requerimiento->FECHA_CREACION_REQ)->format('d/m/Y H:i') }}</td>
                            <td><div class="actions"><a href="{{ route('requerimientos.show',$requerimiento) }}" class="btn">Ver</a>@if(!in_array($requerimiento->ESTADO_REQ,['CERRADO','ANULADO']))<a href="{{ route('requerimientos.edit',$requerimiento) }}" class="btn">Editar</a>@endif</div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="empty">No existen requerimientos con los filtros seleccionados.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>