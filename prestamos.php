<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.php"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';
$usuario_id = $_SESSION['id'];

// Pedir libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['libro_id'])) {
    $libro_id = (int) $_POST['libro_id'];
    // Verificar disponibilidad
    $libro = $db->prepare("SELECT disponible FROM libros WHERE id=?");
    $libro->execute([$libro_id]);
    $lib = $libro->fetch();
    if ($lib && $lib['disponible']) {
        $db->prepare("INSERT INTO prestamos (usuario_id, libro_id) VALUES (?, ?)")->execute([$usuario_id, $libro_id]);
        $db->prepare("UPDATE libros SET disponible=0 WHERE id=?")->execute([$libro_id]);
        $msg = 'success:Libro solicitado correctamente.';
    } else {
        $msg = 'error:El libro no está disponible.';
    }
}

// Devolver libro
if (isset($_GET['devolver'])) {
    $prestamo_id = (int) $_GET['devolver'];
    $p = $db->prepare("SELECT libro_id FROM prestamos WHERE id=? AND usuario_id=?");
    $p->execute([$prestamo_id, $usuario_id]);
    $pr = $p->fetch();
    if ($pr) {
        $db->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion=CURDATE() WHERE id=?")->execute([$prestamo_id]);
        $db->prepare("UPDATE libros SET disponible=1 WHERE id=?")->execute([$pr['libro_id']]);
        $msg = 'success:Libro devuelto. ¡Gracias!';
    }
}

// Libros disponibles
$disponibles = $db->query("
    SELECT l.id, l.titulo, a.nombre AS autor
    FROM libros l
    JOIN autores a ON l.autor_id = a.id
    WHERE l.disponible = 1
    ORDER BY l.titulo ASC
")->fetchAll();

// Mis préstamos
$misprestamos = $db->prepare("
    SELECT p.id, l.titulo, a.nombre AS autor, p.fecha_prestamo, p.fecha_devolucion, p.estado
    FROM prestamos p
    JOIN libros l ON p.libro_id = l.id
    JOIN autores a ON l.autor_id = a.id
    WHERE p.usuario_id = ?
    ORDER BY p.fecha_prestamo DESC
");
$misprestamos->execute([$usuario_id]);
$misprestamos = $misprestamos->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Préstamos — Biblioteca</title>
  <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0d1117;
      --surface: #161b22;
      --surface2: #1c2330;
      --border: #2a3444;
      --border-light: #1f2937;
      --accent: #4ade80;
      --accent-dim: #1a3d28;
      --accent-glow: rgba(74,222,128,0.12);
      --text: #e6edf3;
      --text-muted: #7d8590;
      --text-faint: #444c56;
      --danger: #f87171;
      --danger-bg: rgba(248,113,113,0.08);
      --danger-border: rgba(248,113,113,0.25);
      --success-bg: rgba(74,222,128,0.08);
      --success-border: rgba(74,222,128,0.25);
      --warn-bg: rgba(251,191,36,0.08);
      --warn-border: rgba(251,191,36,0.25);
      --warn: #fbbf24;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; }
    header { background: var(--surface); border-bottom: 1px solid var(--border); padding: 0 2rem; height: 58px; display: flex; align-items: center; justify-content: space-between; position: fixed; top: 0; left: 0; right: 0; z-index: 200; }
    .header-logo { font-family: 'DM Serif Display', serif; font-style: italic; color: var(--accent); font-size: 20px; display: flex; align-items: center; gap: 10px; }
    .header-logo::before { content: ''; display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 8px var(--accent); }
    .header-nav { display: flex; align-items: center; gap: 4px; }
    .header-nav a { color: var(--text-muted); text-decoration: none; font-size: 12px; font-weight: 500; letter-spacing: 0.8px; text-transform: uppercase; padding: 6px 12px; border-radius: 6px; transition: all 0.15s; display: flex; align-items: center; gap: 6px; }
    .header-nav a:hover { color: var(--text); background: var(--surface2); }
    aside { position: fixed; top: 58px; left: 0; bottom: 0; width: 230px; background: var(--surface); border-right: 1px solid var(--border); padding: 1.5rem 0; overflow-y: auto; z-index: 100; }
    aside .section-label { font-size: 9px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 2px; padding: 0 1.2rem; margin-bottom: 4px; margin-top: 1.2rem; }
    aside a { display: flex; align-items: center; gap: 10px; padding: 9px 1.2rem; font-size: 13.5px; color: var(--text-muted); text-decoration: none; font-weight: 400; border-left: 2px solid transparent; transition: all 0.12s; }
    aside a:hover { background: var(--surface2); color: var(--text); border-left-color: var(--border); }
    aside a.active { background: var(--accent-dim); color: var(--accent); border-left-color: var(--accent); font-weight: 600; }
    aside i { font-size: 15px; }
    main { margin-left: 230px; margin-top: 58px; padding: 2rem 2.5rem; }
    .page-title { font-family: 'DM Serif Display', serif; color: var(--text); font-size: 26px; margin: 0 0 1.5rem; display: flex; align-items: center; gap: 12px; }
    .page-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(to right, var(--border), transparent); }
    .bib-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem; }
    .bib-card h5 { font-family: 'DM Serif Display', serif; color: var(--text); font-size: 16px; margin: 0 0 1.2rem; font-weight: 400; }
    .bib-label { font-size: 10px; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; display: block; }
    .bib-select { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 14px; font-family: 'DM Sans', sans-serif; background: var(--surface2); color: var(--text); outline: none; margin-bottom: 12px; transition: border-color 0.2s, box-shadow 0.2s; }
    .bib-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .bib-select option { background: var(--surface2); }
    .bib-btn { background: var(--accent); color: #0a1a10; border: none; border-radius: 8px; padding: 10px 22px; font-size: 11px; font-family: 'DM Sans', sans-serif; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; transition: background 0.2s, transform 0.1s; }
    .bib-btn:hover { background: #6ee7a0; transform: translateY(-1px); }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 10px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 1.2px; padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; }
    td { padding: 11px 12px; border-bottom: 1px solid var(--border-light); font-size: 13.5px; color: var(--text); }
    tr:hover td { background: var(--surface2); }
    tr:last-child td { border-bottom: none; }
    .badge-activo { background: var(--warn-bg); color: var(--warn); border: 1px solid var(--warn-border); border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
    .badge-devuelto { background: var(--success-bg); color: var(--accent); border: 1px solid var(--success-border); border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
    .btn-dev { background: none; border: 1px solid var(--border); color: var(--text-muted); border-radius: 6px; padding: 4px 12px; font-size: 11px; cursor: pointer; font-weight: 600; font-family: 'DM Sans', sans-serif; transition: all 0.15s; }
    .btn-dev:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-dim); }
    .alert-ok { background: var(--success-bg); border: 1px solid var(--success-border); border-radius: 8px; padding: 10px 16px; color: var(--accent); font-size: 13px; margin-bottom: 1.2rem; }
    .alert-err { background: var(--danger-bg); border: 1px solid var(--danger-border); border-radius: 8px; padding: 10px 16px; color: var(--danger); font-size: 13px; margin-bottom: 1.2rem; }
  </style>
</head>
<body>

<header>
  <span class="header-logo">Biblioteca</span>
  <nav class="header-nav">
    <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a>
  </nav>
</header>

<aside>
  <div class="section-label">Menú</div>
  <a href="dashboard.php"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php" class="active"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <h1 class="page-title">Préstamos</h1>

  <?php if ($msg): ?>
    <?php [$tipo, $texto] = explode(':', $msg, 2); ?>
    <div class="<?= $tipo === 'success' ? 'alert-ok' : 'alert-err' ?>"><?= $texto ?></div>
  <?php endif; ?>

  <div class="bib-card">
    <h5>Pedir un libro</h5>
    <?php if (empty($disponibles)): ?>
      <p style="color:var(--text-faint);font-size:14px;">No hay libros disponibles en este momento.</p>
    <?php else: ?>
    <form method="POST">
      <label class="bib-label">Selecciona un libro disponible</label>
      <select class="bib-select" name="libro_id" required>
        <option value="">— Elige un libro —</option>
        <?php foreach ($disponibles as $l): ?>
          <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['titulo']) ?> — <?= htmlspecialchars($l['autor']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="bib-btn" type="submit">Pedir préstamo</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="bib-card">
    <h5>Mis préstamos</h5>
    <?php if (empty($misprestamos)): ?>
      <p style="color:var(--text-faint);font-size:14px;">No has pedido ningún libro todavía.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Libro</th><th>Autor</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($misprestamos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td style="color:var(--text-muted)"><?= htmlspecialchars($p['autor']) ?></td>
            <td style="color:var(--text-muted)"><?= date('d/m/Y', strtotime($p['fecha_prestamo'])) ?></td>
            <td><span class="<?= $p['estado'] === 'activo' ? 'badge-activo' : 'badge-devuelto' ?>"><?= ucfirst($p['estado']) ?></span></td>
            <td>
              <?php if ($p['estado'] === 'activo'): ?>
                <a href="prestamos.php?devolver=<?= $p['id'] ?>" onclick="return confirm('¿Devolver este libro?')">
                  <button class="btn-dev">↩ Devolver</button>
                </a>
              <?php else: ?>
                <span style="color:var(--text-faint);font-size:12px;"><?= $p['fecha_devolucion'] ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
