<?php $user = currentUser(); $notifModel = new Notification(); $unreadCount = $unreadCount ?? $notifModel->getUnreadCount($user['id']); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TaskFlow">
    <link rel="apple-touch-icon" href="<?= APP_URL ?>/assets/icons/icon-192.png">
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
                <a href="<?= APP_URL ?>/inbox" class="nav-item <?= ($currentPage ?? '') === 'inbox' ? 'active' : '' ?>">
                    <span class="icon">📨</span> Inbox
                    <?php if ($unreadCount > 0): ?><span class="badge danger"><?= $unreadCount ?></span><?php endif; ?>
                </a>
                <a href="<?= APP_URL ?>/my-tasks" class="nav-item <?= ($currentPage ?? '') === 'my-tasks' ? 'active' : '' ?>">
                    <span class="icon">📌</span> Task của tôi
                </a>
                <a href="<?= APP_URL ?>/calendar" class="nav-item <?= ($currentPage ?? '') === 'calendar' ? 'active' : '' ?>">
                    <span class="icon">📅</span> Calendar
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
                <div class="nav-section-title">Dự án</div>
                <?php foreach (array_slice($sidebarProjects, 0, 10) as $sp): ?>
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
                <div class="user-avatar"><?= strtoupper(mb_substr($user['name'], 0, 1)) ?></div>
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
                <!-- Notification Bell -->
                <div style="position:relative" id="notifWrap">
                    <button class="notif-btn" onclick="toggleNotifDropdown()">
                        🔔
                        <?php if ($unreadCount > 0): ?><span class="notif-badge"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span><?php endif; ?>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-dropdown-header">
                            <span>Thông báo</span>
                            <button class="btn btn-sm btn-secondary" onclick="markAllNotifRead()">Đọc hết</button>
                        </div>
                        <div id="notifList"><div style="padding:20px;text-align:center;color:var(--text-muted);font-size:12px">Đang tải...</div></div>
                    </div>
                </div>
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
<script>
// Register PWA
if ('serviceWorker' in navigator) { navigator.serviceWorker.register('<?= APP_URL ?>/sw.js').catch(()=>{}); }
</script>
</body>
</html>
