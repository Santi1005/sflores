<?php
session_start();
if(!isset($_COOKIE["id_usuario"])) {
  $_SESSION['id_usuario'] = $_COOKIE["id_usuario"];
    header("Location: index.php");
    exit();
}
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Biblioteca — Dashboard</title>
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
    }

    *, *::before, *::after { box-sizing: border-box; }

    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); margin: 0; }

    header {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 0 2rem; height: 58px;
      display: flex; align-items: center; justify-content: space-between;
      position: fixed; top: 0; left: 0; right: 0; z-index: 200;
    }
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

    .welcome-card {
      background: linear-gradient(135deg, var(--accent-dim) 0%, #0f2a1c 100%);
      border: 1px solid rgba(74,222,128,0.2);
      border-radius: 12px;
      padding: 2rem 2.25rem;
      margin-bottom: 1.5rem;
      position: relative;
      overflow: hidden;
    }
    .welcome-card::after {
      content: '';
      position: absolute;
      top: -40px; right: -40px;
      width: 180px; height: 180px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(74,222,128,0.1) 0%, transparent 70%);
    }
    .welcome-card h2 { font-family: 'DM Serif Display', serif; color: var(--accent); font-size: 22px; margin: 0 0 6px; font-style: italic; }
    .welcome-card p { color: rgba(74,222,128,0.5); margin: 0; font-size: 13px; font-weight: 300; }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1.4rem 1.5rem;
      position: relative;
      overflow: hidden;
      transition: border-color 0.2s, transform 0.2s;
    }
    .stat-card:hover { border-color: rgba(74,222,128,0.3); transform: translateY(-2px); }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 2px;
      background: linear-gradient(to right, var(--accent), transparent);
      opacity: 0;
      transition: opacity 0.2s;
    }
    .stat-card:hover::before { opacity: 1; }

    .stat-card .num {
      font-family: 'DM Serif Display', serif;
      font-size: 38px;
      color: var(--accent);
      font-weight: 400;
      line-height: 1;
      margin-bottom: 6px;
    }
    .stat-card .lbl {
      font-size: 11px;
      color: var(--text-faint);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-weight: 700;
    }

    .quick-links {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
    }

    .quick-link {
      background: var(--surface);
      border: 1px solid var(--border);
      color: var(--text-muted);
      border-radius: 12px;
      padding: 1.5rem;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: all 0.2s;
      position: relative;
      overflow: hidden;
    }
    .quick-link i { font-size: 24px; color: var(--accent); }
    .quick-link:hover {
      border-color: rgba(74,222,128,0.4);
      background: var(--accent-dim);
      color: var(--accent);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    .quick-link .ql-arrow {
      position: absolute;
      bottom: 1rem; right: 1rem;
      font-size: 16px;
      opacity: 0;
      transition: opacity 0.2s, transform 0.2s;
      transform: translate(-4px, 4px);
    }
    .quick-link:hover .ql-arrow { opacity: 1; transform: translate(0,0); }
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
  <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Inicio</a>
  <div class="section-label">Catálogo</div>
  <a href="autores.php"><i class="bi bi-person-lines-fill"></i> Autores</a>
  <a href="libros.php"><i class="bi bi-book"></i> Libros</a>
  <div class="section-label">Préstamos</div>
  <a href="prestamos.php"><i class="bi bi-bookmark-check"></i> Mis préstamos</a>
</aside>

<main>
  <div class="welcome-card">
    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></h2>
    <p>¿Qué quieres hacer hoy?</p>
  </div>

  <?php
  require_once 'db.php';
  $db = conectarDB();
  $nAutores     = $db->query("SELECT COUNT(*) FROM autores")->fetchColumn();
  $nLibros      = $db->query("SELECT COUNT(*) FROM libros")->fetchColumn();
  $nDisponibles = $db->query("SELECT COUNT(*) FROM libros WHERE disponible=1")->fetchColumn();
  $nPrestamos   = $db->query("SELECT COUNT(*) FROM prestamos WHERE estado='activo'")->fetchColumn();
  ?>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="num"><?= $nAutores ?></div>
      <div class="lbl">Autores</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nLibros ?></div>
      <div class="lbl">Libros</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nDisponibles ?></div>
      <div class="lbl">Disponibles</div>
    </div>
    <div class="stat-card">
      <div class="num"><?= $nPrestamos ?></div>
      <div class="lbl">Préstamos activos</div>
    </div>
  </div>

  <div class="quick-links">
    <a class="quick-link" href="autores.php">
      <i class="bi bi-person-plus"></i>
      Agregar autor
      <span class="ql-arrow"><i class="bi bi-arrow-up-right"></i></span>
    </a>
    <a class="quick-link" href="libros.php">
      <i class="bi bi-book"></i>
      Agregar libro
      <span class="ql-arrow"><i class="bi bi-arrow-up-right"></i></span>
    </a>
    <a class="quick-link" href="prestamos.php">
      <i class="bi bi-bookmark-plus"></i>
      Pedir libro
      <span class="ql-arrow"><i class="bi bi-arrow-up-right"></i></span>
    </a>
  </div>
</main>

</body>
</html>
