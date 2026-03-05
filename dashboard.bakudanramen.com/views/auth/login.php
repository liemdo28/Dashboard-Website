<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✓</text></svg>">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <h1>Task<span>Flow</span></h1>
            <p>Đăng nhập để tiếp tục</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error">❌ <?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success">✅ <?= e($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@company.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full" style="justify-content:center">
                Đăng nhập
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-muted)">
            Chưa có tài khoản? <a href="<?= APP_URL ?>/register" style="font-weight:600">Đăng ký ngay</a>
        </p>
    </div>
</body>
</html>
