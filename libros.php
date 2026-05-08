<?php
session_start();
if (!isset($_SESSION['id'])) { header("Location: index.php"); exit(); }
require_once 'db.php';
$db = conectarDB();
$msg = '';

// Agregar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo   = trim($_POST['titulo']);
    $autor_id = (int) $_POST['autor_id'];
    if ($titulo && $autor_id) {
        $db->prepare("INSERT INTO libros (titulo, autor_id) VALUES (?, ?)")->execute([$titulo, $autor_id]);
        $msg = 'success:Libro agregado correctamente.';
    }
}

// Eliminar libro
if (isset($_GET['delete'])) {
    try {
        $db->prepare("DELETE FROM libros WHERE id=?")->execute([$_GET['delete']]);
        $msg = 'success:Libro eliminado.';
    } catch (Exception $e) {
        $msg = 'error:No se puede eliminar, tiene préstamos asociados.';
    }
}

$autores = $db->query("SELECT * FROM autores ORDER BY nombre ASC")->fetchAll();
$libros  = $db->query("
    SELECT l.id, l.titulo, l.disponible, l.created_at, a.nombre AS autor
    FROM libros l
    JOIN autores a ON l.autor_id = a.id
    ORDER BY l.titulo ASC
")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Libros — Biblioteca</title>
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
    .bib-input, .bib-select { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 14px; font-family: 'DM Sans', sans-serif; background: var(--surface2); color: var(--text); outline: none; margin-bottom: 12px; transition: border-color 0.2s, box-shadow 0.2s; }
    .bib-input:focus, .bib-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    .bib-input::placeholder { color: var(--text-faint); }
    .bib-select option { background: var(--surface2); }
    .bib-btn { background: var(--accent); color: #0a1a10; border: none; border-radius: 8px; padding: 10px 22px; font-size: 11px; font-family: 'DM Sans', sans-serif; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; transition: background 0.2s, transform 0.1s; }
    .bib-btn:hover { background: #6ee7a0; transform: translateY(-1px); }
    table { width: 100%; border-collapse: collapse; }
    th { font-size: 10px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 1.2px; padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; }
    td { padding: 11px 12px; border-bottom: 1px solid var(--border-light); font-size: 13.5px; color: var(--text); }
    tr:hover td { background: var(--surface2); }
    tr:last-child td { border-bottom: none; }
    .badge-disp { background: var(--success-bg); color: var(--accent); border: 1px solid var(--success-border); border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
    .badge-no { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
    .btn-del { background: none; border: 1px solid var(--danger-border); color: var(--danger); border-radius: 6px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.15s; }
    .btn-del:hover { background: var(--danger-bg); }
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
  <a href="libros.php" class="active"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <h1 class="page-title">Libros</h1>

  <?php if ($msg): ?>
    <?php [$tipo, $texto] = explode(':', $msg, 2); ?>
    <div class="<?= $tipo === 'success' ? 'alert-ok' : 'alert-err' ?>"><?= $texto ?></div>
  <?php endif; ?>

  <div class="bib-card">
    <h5>Agregar libro</h5>
    <?php if (empty($autores)): ?>
      <p style="color:var(--text-faint);font-size:14px;">Primero debes <a href="autores.php" style="color:var(--accent);font-weight:600;">agregar autores</a> antes de registrar libros.</p>
    <?php else: ?>
    <form method="POST">
      <label class="bib-label">Título del libro</label>
      <input class="bib-input" type="text" name="titulo" placeholder="Ej: Cien años de soledad" required>
      <label class="bib-label">Autor</label>
      <select class="bib-select" name="autor_id" required>
        <option value="">— Selecciona un autor —</option>
        <?php foreach ($autores as $a): ?>
          <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="bib-btn" type="submit">+ Agregar</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="bib-card">
    <h5>Lista de libros</h5>
    <?php if (empty($libros)): ?>
      <p style="color:var(--text-faint);font-size:14px;">Aún no hay libros registrados.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>#</th><th>Título</th><th>Autor</th><th>Estado</th><th>Acción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($libros as $l): ?>
          <tr>
            <td style="color:var(--text-faint)"><?= $l['id'] ?></td>
            <td><?= htmlspecialchars($l['titulo']) ?></td>
            <td style="color:var(--text-muted)"><?= htmlspecialchars($l['autor']) ?></td>
            <td><span class="<?= $l['disponible'] ? 'badge-disp' : 'badge-no' ?>"><?= $l['disponible'] ? 'Disponible' : 'Prestado' ?></span></td>
            <td>
              <a href="libros.php?delete=<?= $l['id'] ?>" onclick="return confirm('¿Eliminar este libro?')">
                <button class="btn-del">🗑 Eliminar</button>
              </a>
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
