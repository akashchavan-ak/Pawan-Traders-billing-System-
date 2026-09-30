<?php /** Login page markup. Expects $error to be set. */ ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login - Pawan Traders</title>
        <style>
            *{box-sizing:border-box}
            body{margin:0;font-family:Arial,sans-serif;background:#eef2ff;min-height:100vh;display:flex;align-items:center;justify-content:center}
            .login{width:390px;max-width:92%;background:#fff;border-radius:22px;padding:34px;box-shadow:0 20px 60px rgba(15,23,42,.15)}
            .logo{width:76px;height:76px;margin:0 auto 15px;border-radius:22px;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;box-shadow:0 10px 25px rgba(37,99,235,.25)}
            h1{text-align:center;margin:0;color:#111827}
            .sub{text-align:center;color:#64748b;margin:7px 0 25px}
            label{display:block;font-weight:700;margin:12px 0 7px}
            input{width:100%;padding:13px;border:1px solid #dbe1ea;border-radius:10px;font-size:15px}
            button{width:100%;padding:13px;margin-top:20px;border:0;border-radius:10px;background:#2563eb;color:#fff;font-size:16px;font-weight:700;cursor:pointer}
            .err{background:#fee2e2;color:#991b1b;padding:10px;border-radius:9px;text-align:center}
            .hint{text-align:center;color:#94a3b8;font-size:12px;margin-top:18px}
        </style>
    </head>
    <body>
        <div class="login">
            <div class="logo">PT</div>
            <h1>PAWAN TRADERS</h1>
            <div class="sub">Smart Billing System</div>
            <?php if($error): ?><div class="err"><?=e($error)?></div><?php endif; ?>
            <form method="post">
                <label>Username</label>
                <input name="username" required autocomplete="username">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
                <button name="login">Login</button>
            </form>
            <div class="hint">Default: admin / admin123</div>
        </div>
    </body>
    </html>

