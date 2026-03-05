<?php $user = currentUser(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>✓</text></svg>">
</head>
<body>
<div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h1>Task<span>Flow</span></h1>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <a href="<?= APP_URL ?>/dashboard" class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="<?= APP_URL ?>/projects" class="nav-item <?= ($currentPage ?? '') === 'projects' ? 'active' : '' ?>">
                    <span class="icon">📁</span> Projects
                </a>
            </div>

            <?php
            $projectModel = new Project();
            $sidebarProjects = $projectModel->getByUser($_SESSION['user_id']);
            if (!empty($sidebarProjects)):
            ?>
            <div class="nav-section">
                <div class="nav-section-title">Projects</div>
                <?php foreach (array_slice($sidebarProjects, 0, 8) as $sp): ?>
                <a href="<?= APP_URL ?>/projects/<?= $sp['id'] ?>" class="nav-item <?= (isset($project) && $project['id'] == $sp['id']) ? 'active' : '' ?>">
                    <span class="project-dot" style="background:<?= e($sp['color']) ?>"></span>
                    <?= e($sp['name']) ?>
                    <span class="badge"><?= $sp['task_count'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
            <div class="nav-section">
                <div class="nav-section-title">Admin</div>
                <a href="<?= APP_URL ?>/admin/users" class="nav-item <?= ($currentPage ?? '') === 'admin-users' ? 'active' : '' ?>">
                    <span class="icon">👥</span> Quản lý Users
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <div class="user-details">
                    <div class="name"><?= e($user['name']) ?></div>
                    <div class="role"><?= ucfirst($user['role']) ?></div>
                </div>
                <a href="<?= APP_URL ?>/logout" class="btn-ghost" title="Đăng xuất">🚪</a>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <header class="page-header">
            <div class="flex-center gap-2">
                <div class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</div>
                <h2><?= e($pageTitle ?? 'Dashboard') ?></h2>
            </div>
            <div class="header-actions">
                <?= $headerActions ?? '' ?>
            </div>
        </header>

        <div class="content-area">
            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success">✅ <?= e($msg) ?></div>
            <?php endif; ?>
            <?php if ($msg = flash('error')): ?>
                <div class="alert alert-error">❌ <?= e($msg) ?></div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </div>
    </main>
</div>

<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<?php if (!empty($extraJs)): ?>
    <?php foreach ($extraJs as $js): ?>
    <script src="<?= APP_URL ?>/assets/js/<?= $js ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
