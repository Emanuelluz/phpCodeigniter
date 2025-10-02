<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; background:#f5f6f8; margin:0; display:flex; align-items:center; justify-content:center; height:100vh; }
    .card { background:#fff; padding:24px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.06); width:100%; max-width:360px; }
    h1 { font-size:20px; margin:0 0 16px; }
    .field { margin-bottom:12px; }
    label { display:block; font-size:12px; color:#444; margin-bottom:6px; }
    input { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; font-size:14px; }
    button { width:100%; padding:12px; background:#1f6feb; color:#fff; border:none; border-radius:8px; font-size:14px; cursor:pointer; }
    .error { color:#b00020; font-size:12px; margin:8px 0; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Acessar</h1>
    <?php if(session()->getFlashdata('error')): ?>
      <div class="error" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('success')): ?>
      <div class="success" role="status" style="color:#0a7; font-size:12px; margin:8px 0;">
        <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>
    <form method="post" action="/login">
      <div class="field">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="field">
        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>