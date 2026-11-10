<?php
require_once __DIR__ . '/api/conexion.php';
header('Content-Type: text/html; charset=utf-8');

$sql = "SELECT e.id_equipo, e.nombre_equipo, e.marca, e.modelo, e.direccion_ip,
               e.numero_puertos, e.icono, t.nombre_tipo, t.ruta_icono,
               p.numero_piso, s.nombre_sede,
               (SELECT COUNT(*) FROM puertos pt
                INNER JOIN enlaces en ON en.id_puerto_origen = pt.id_puerto OR en.id_puerto_destino = pt.id_puerto
                WHERE pt.id_equipo = e.id_equipo) AS puertos_enlazados
        FROM equipos e
        LEFT JOIN tipos_equipo t ON t.id_tipo_equipo = e.id_tipo_equipo
        LEFT JOIN pisos p ON p.id_piso = e.id_piso
        LEFT JOIN sedes s ON s.id_sede = p.id_sede
        ORDER BY e.id_equipo DESC";
$equipos = [];
$resultado = $conexion->query($sql);
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $icono = trim((string)($fila['icono'] ?: $fila['ruta_icono']));
        $esUrl = (bool)preg_match('#^https?://#i', $icono);
        if ($icono === '' || (!$esUrl && !is_file(__DIR__ . '/' . ltrim($icono, '/')))) {
            $tipo = strtolower((string)$fila['nombre_tipo']);
            $icono = str_contains($tipo, 'comput') ? 'assets/icons/computer.svg'
                : (str_contains($tipo, 'router') ? 'assets/icons/router.svg'
                : (str_contains($tipo, 'servidor') ? 'assets/icons/server.svg'
                : (str_contains($tipo, 'teléfono') || str_contains($tipo, 'telefono') ? 'assets/icons/phone.svg'
                : (str_contains($tipo, 'switch') || str_contains($tipo, 'swiche') ? 'assets/icons/switch.svg' : 'assets/icons/network.svg'))));
        }
        $fila['icono_mostrar'] = $icono;
        $equipos[] = $fila;
    }
}
$conexion->close();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetMap · Inicio</title>
    <link rel="stylesheet" href="assets/css/netmap_global.css">
    <style>
        body { margin:0; font-family:Inter,system-ui,sans-serif; }
        main { width:min(1440px,94%); margin:30px auto 70px; }
        .welcome { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px; margin-bottom:24px; }
        .welcome h1 { margin:0 0 7px; font-size:clamp(1.6rem,3vw,2.2rem); }
        .welcome p { margin:0; color:var(--netmap-muted); }
        .quick-actions { display:flex; gap:9px; flex-wrap:wrap; }
        .quick-actions a { padding:10px 14px; border-radius:10px; text-decoration:none; color:#fff; background:#1d4267; border:1px solid #41658c; }
        .stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:13px; margin:20px 0 28px; }
        .stat { padding:17px; background:var(--netmap-surface); border:1px solid var(--netmap-border); border-radius:14px; }
        .stat span { display:block; color:var(--netmap-muted); font-size:.86rem; }
        .stat strong { display:block; margin-top:5px; font-size:1.7rem; }
        .section-title { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:0 0 14px; }
        .section-title h2 { margin:0; font-size:1.2rem; }
        .equipment-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:14px; }
        .equipment-card { min-width:0; padding:15px; border:1px solid var(--netmap-border); border-radius:15px; background:var(--netmap-surface); color:var(--netmap-text); text-decoration:none; transition:transform .18s,border-color .18s; }
        .equipment-card:hover { transform:translateY(-3px); border-color:#4b8bc5; }
        .equipment-visual { height:115px; display:grid; place-items:center; margin-bottom:12px; border-radius:11px; background:linear-gradient(145deg,#1b2c43,#0d1727); overflow:hidden; }
        .equipment-visual img { width:78%; height:90px; object-fit:contain; }
        .equipment-fallback { width:66px; height:66px; display:grid; place-items:center; border:2px dashed #8ba0bb; border-radius:14px; color:#dbeafe; font-size:2rem; background:#263a55; }
        .equipment-card h3 { margin:0 0 5px; font-size:1rem; }
        .equipment-card p { margin:3px 0; color:var(--netmap-muted); font-size:.82rem; }
        .equipment-meta { display:flex; justify-content:space-between; gap:8px; margin-top:12px; padding-top:10px; border-top:1px solid var(--netmap-border); color:var(--netmap-muted); font-size:.78rem; }
        .empty-state { padding:40px 20px; text-align:center; color:var(--netmap-muted); background:var(--netmap-surface); border:1px dashed var(--netmap-border); border-radius:16px; }
        .empty-state a { color:#60a5fa; }
        #lienzo-red { min-height:180px; margin-top:30px; padding:18px; border:1px dashed var(--netmap-border); border-radius:15px; background:var(--netmap-surface); }
        #lienzo-red h2 { margin:0 0 7px; font-size:1.1rem; }
        #lienzo-red p { margin:0; color:var(--netmap-muted); }
        @media(max-width:650px){ .stats{grid-template-columns:1fr;} }
    </style>
</head>
<body>
<header class="app-header"><strong>◈ NetMap</strong><span>Inventario y diagrama de infraestructura</span></header>
<main>
    <section class="welcome">
        <div><h1>Panel principal</h1><p>Resumen de los equipos registrados en tu infraestructura.</p></div>
        <div class="quick-actions">
            <a href="topologia.php">Abrir topología</a>
            <a href="equipos_agregar.php">＋ Registrar equipo</a>
            <a href="equipos_lista.php">Ver inventario</a>
            <a href="ubicaciones.php">Administrar ubicaciones</a>
        </div>
    </section>
    <section class="stats" aria-label="Resumen del inventario">
        <article class="stat"><span>Equipos registrados</span><strong><?= count($equipos) ?></strong></article>
        <article class="stat"><span>Equipos con puertos enlazados</span><strong><?= count(array_filter($equipos, static fn($e) => (int)$e['puertos_enlazados'] > 0)) ?></strong></article>
        <article class="stat"><span>Total de puertos registrados</span><strong><?= array_sum(array_map(static fn($e) => (int)$e['numero_puertos'], $equipos)) ?></strong></article>
    </section>
    <section>
        <div class="section-title"><h2>Equipos de la infraestructura</h2><a href="equipos_lista.php">Abrir inventario →</a></div>
        <?php if (!$equipos): ?>
            <div class="empty-state">Todavía no hay equipos registrados. <a href="equipos_agregar.php">Agrega el primero</a> para comenzar a construir tu infraestructura.</div>
        <?php else: ?>
            <div class="equipment-grid">
                <?php foreach ($equipos as $equipo): ?>
                    <a class="equipment-card" href="equipos_editar_puertos.php?id=<?= (int)$equipo['id_equipo'] ?>">
                        <div class="equipment-visual">
                            <img src="<?= htmlspecialchars($equipo['icono_mostrar'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($equipo['nombre_tipo'] ?? 'Equipo de red', ENT_QUOTES, 'UTF-8') ?>"
                                 onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                            <div class="equipment-fallback" hidden>◈</div>
                        </div>
                        <h3><?= htmlspecialchars($equipo['nombre_equipo'] ?: ($equipo['nombre_tipo'] . ' #' . $equipo['id_equipo']), ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars(trim(($equipo['marca'] ?? '') . ' ' . ($equipo['modelo'] ?? '')), ENT_QUOTES, 'UTF-8') ?: 'Marca/modelo no especificados' ?></p>
                        <p><?= htmlspecialchars(($equipo['nombre_sede'] ?? 'Sin sede') . ' · Piso ' . ($equipo['numero_piso'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="equipment-meta"><span><?= (int)$equipo['numero_puertos'] ?> puertos</span><span><?= (int)$equipo['puertos_enlazados'] ?> enlazados</span></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <section id="lienzo-red">
        <h2>Diagrama de red</h2>
        <p>La vista de topología ya consulta los equipos y enlaces registrados, permite moverlos, organizar automáticamente su distribución y exportar el mapa.</p>
        <p><a href="topologia.php">Abrir mapa de topología →</a></p>
    </section>
</main>
<script src="assets/js/netmap_global.js" defer></script>
</body>
</html>
