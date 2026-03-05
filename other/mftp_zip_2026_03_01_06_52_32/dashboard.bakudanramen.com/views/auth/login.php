<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
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

        <form method="POST" action="<?= APP_URL ?>/login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@taskflow.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-full" style="justify-content:center">
                Đăng nhập
            </button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:13px;color:var(--text-light)">
            Liên hệ admin để được cấp tài khoản
        </p>
    </div>
</body>
</html>
