<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Biblioteca — Iniciar sesión</title>
    <link href="./wwwroot/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./wwwroot/css/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
      * { box-sizing: border-box; }

      :root {
        --bg: #0d1117;
        --surface: #161b22;
        --surface2: #1c2330;
        --border: #2a3444;
        --accent: #4ade80;
        --accent-dim: #1a3d28;
        --text: #e6edf3;
        --text-muted: #7d8590;
        --text-faint: #444c56;
        --gold: #f0c040;
      }

      body {
        margin: 0;
        min-height: 100vh;
        background-color: var(--bg);
        background-image:
          radial-gradient(ellipse 80% 60% at 50% -10%, rgba(74,222,128,0.07) 0%, transparent 60%),
          linear-gradient(180deg, #0d1117 0%, #0a0f14 100%);
        font-family: 'DM Sans', sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
      }

      .bib-wrap {
        display: flex;
        width: 100%;
        max-width: 860px;
        min-height: 520px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: 0 32px 80px rgba(0,0,0,0.6);
      }

      .bib-side {
        width: 300px;
        background: linear-gradient(160deg, #1a3d28 0%, #0d2318 60%, #0a1a10 100%);
        padding: 3rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
      }

      .bib-side::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 70%);
      }

      .bib-side-title {
        font-family: 'DM Serif Display', serif;
        font-style: italic;
        font-size: 36px;
        color: var(--accent);
        line-height: 1.1;
        margin: 0 0 12px;
      }

      .bib-side-sub {
        font-size: 13px;
        color: rgba(74,222,128,0.55);
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
      }

      .bib-side-footer {
        font-size: 11px;
        color: rgba(74,222,128,0.3);
        letter-spacing: 1px;
        line-height: 1.8;
      }

      .bib-side-deco {
        width: 40px; height: 2px;
        background: var(--accent);
        margin: 1.5rem 0;
        opacity: 0.4;
      }

      .bib-card {
        flex: 1;
        background: var(--surface);
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }

      .bib-title {
        font-family: 'DM Serif Display', serif;
        font-size: 26px;
        color: var(--text);
        margin: 0 0 6px;
      }

      .bib-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0 0 2rem;
        font-weight: 300;
      }

      .bib-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 6px;
        display: block;
      }

      .bib-input {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 11px 14px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        background: var(--surface2);
        color: var(--text);
        margin-bottom: 18px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
      }

      .bib-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(74,222,128,0.1);
      }

      .bib-input::placeholder { color: var(--text-faint); }

      .bib-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        margin-top: -8px;
      }

      .bib-remember input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--accent);
        cursor: pointer;
      }

      .bib-remember label {
        font-size: 13px;
        color: var(--text-muted);
        cursor: pointer;
      }

      .bib-btn {
        width: 100%;
        background: var(--accent);
        color: #0a1a10;
        border: none;
        border-radius: 8px;
        padding: 13px;
        font-size: 12px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
      }

      .bib-btn:hover { background: #6ee7a0; transform: translateY(-1px); }
      .bib-btn:active { transform: translateY(0); }

      .bib-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: var(--text-muted);
      }

      .bib-footer a {
        color: var(--accent);
        font-weight: 500;
        text-decoration: none;
      }

      .bib-footer a:hover { text-decoration: underline; }

      .bib-error {
        background: rgba(248,113,113,0.1);
        border: 1px solid rgba(248,113,113,0.3);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        color: #fca5a5;
        margin-bottom: 16px;
      }

      @media (max-width: 640px) {
        .bib-side { display: none; }
        .bib-wrap { max-width: 420px; }
      }
    </style>
  </head>
  <body>

    <div class="bib-wrap">
      <div class="bib-side">
        <div>
          <p class="bib-side-sub">Sistema de</p>
          <h2 class="bib-side-title">Gestión<br>Biblio&shy;teca</h2>
          <div class="bib-side-deco"></div>
          <p style="color:rgba(74,222,128,0.45);font-size:13px;line-height:1.7;font-weight:300;">
            Administra autores, libros y préstamos desde un solo lugar.
          </p>
        </div>
        <div class="bib-side-footer">
          SISTEMA BIBLIOTECARIO<br>
          © 2025
        </div>
      </div>

      <div class="bib-card">
        <h1 class="bib-title">Iniciar sesión</h1>
        <p class="bib-subtitle">Accede a tu cuenta de lector</p>

        <form method="POST" action="login.php">
          <label class="bib-label" for="email">Correo electrónico</label>
          <input class="bib-input" type="email" id="email" name="email"
            placeholder="tu@correo.com"
            value="<?= isset($_COOKIE['recordar_email']) ? htmlspecialchars($_COOKIE['recordar_email']) : '' ?>"
            required>

          <label class="bib-label" for="pwd">Contraseña</label>
          <input class="bib-input" type="password" id="pwd" name="pwd" placeholder="••••••••" required>

          <div class="bib-remember">
            <input type="checkbox" id="recordar" name="recordar" value="1"
              <?= isset($_COOKIE['recordar_email']) ? 'checked' : '' ?>>
            <label for="recordar">Recórdame</label>
          </div>

          <button class="bib-btn" type="submit">Iniciar sesión</button>
        </form>

        <p class="bib-footer">
          ¿No tienes cuenta? <a href="registro.html">Crear cuenta</a>
        </p>
      </div>
    </div>

  </body>
</html>
