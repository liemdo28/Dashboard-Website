<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

ob_start();
?>

<!-- Stats -->
<div class="grid grid-4 mb-4">
    <div class="stat-card">
        <div class="stat-icon red">📁</div>
        <div>
            <div class="stat-value"><?= $totalProjects ?></div>
            <div class="stat-label">Projects</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon dark">📋</div>
        <div>
            <div class="stat-value"><?= $totalTasks ?></div>
            <div class="stat-label">Tổng Tasks</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value"><?= $completedTasks ?></div>
            <div class="stat-label">Hoàn thành</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-value"><?= $totalMembers ?></div>
            <div class="stat-label">Thành viên</div>
        </div>
    </div>
</div>

<div class="grid grid-2 mb-4">
    <!-- Task Status Chart -->
    <div class="card">
        <div class="card-header">
            <h3>Phân bố Tasks</h3>
        </div>
        <div class="card-body">
            <?php
            $statusMap = ['todo' => 0, 'in_progress' => 0, 'review' => 0, 'done' => 0];
            foreach ($tasksByStatus as $s) {
                $statusMap[$s['status']] = $s['count'];
            }
            $maxVal = max(1, max($statusMap));
            $statusLabels = ['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'];
            $statusColors = ['todo' => 'var(--gray-400)', 'in_progress' => '#2563eb', 'review' => '#d97706', 'done' => '#16a34a'];
            ?>
            <div class="chart-bar-container">
                <?php foreach ($statusMap as $key => $val): ?>
                <div class="chart-bar-wrap">
                    <div class="chart-bar-value"><?= $val ?></div>
                    <div class="chart-bar" style="height:<?= ($val / $maxVal * 100) ?>%;background:<?= $statusColors[$key] ?>;min-height:4px;"></div>
                    <div class="chart-bar-label"><?= $statusLabels[$key] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Overdue Tasks -->
    <div class="card">
        <div class="card-header">
            <h3>Tasks quá hạn</h3>
            <span class="badge badge-admin"><?= count($overdueTasks) ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($overdueTasks)): ?>
                <p class="text-muted text-center" style="padding:20px">Không có task quá hạn 🎉</p>
            <?php else: ?>
                <?php foreach (array_slice($overdueTasks, 0, 5) as $task): ?>
                <div class="flex-between" style="padding:8px 0;border-bottom:1px solid var(--gray-100)">
                    <div>
                        <a href="<?= APP_URL ?>/tasks/<?= $task['id'] ?>" style="font-size:14px;font-weight:500"><?= e($task['title']) ?></a>
                        <div class="text-sm text-muted"><?= e($task['project_name']) ?></div>
                    </div>
                    <span class="due-date overdue"><?= date('d/m', strtotime($task['due_date'])) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- My Tasks -->
<div class="card mb-4">
    <div class="card-header">
        <h3>Tasks của tôi</h3>
        <span class="text-muted text-sm"><?= count($myTasks) ?> tasks</span>
    </div>
    <div class="card-body" style="padding:0">
        <?php if (empty($myTasks)): ?>
            <div class="empty-state">
                <div class="icon">📝</div>
                <h3>Chưa có task nào</h3>
                <p>Bạn chưa được gán task nào</p>
            </div>
        <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Project</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Hạn</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myTasks as $task): ?>
                <tr>
                    <td><a href="<?= APP_URL ?>/tasks/<?= $task['id'] ?>"><?= e($task['title']) ?></a></td>
                    <td class="text-muted"><?= e($task['project_name'] ?? '-') ?></td>
                    <td><span class="tag priority-<?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?></span></td>
                    <td><span class="badge badge-<?= $task['status'] === 'done' ? 'active' : 'member' ?>"><?= ucfirst(str_replace('_', ' ', $task['status'])) ?></span></td>
                    <td class="<?= ($task['due_date'] && $task['due_date'] < date('Y-m-d')) ? 'overdue' : 'text-muted' ?>">
                        <?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Projects -->
<div class="flex-between mb-3">
    <h3>Projects gần đây</h3>
    <a href="<?= APP_URL ?>/projects/create" class="btn btn-primary btn-sm">+ Tạo Project</a>
</div>
<div class="grid grid-3">
    <?php foreach (array_slice($projects, 0, 6) as $proj):
        $pct = $proj['task_count'] > 0 ? round($proj['completed_count'] / $proj['task_count'] * 100) : 0;
    ?>
    <a href="<?= APP_URL ?>/projects/<?= $proj['id'] ?>" class="project-card">
        <div class="card-accent" style="background:<?= e($proj['color']) ?>"></div>
        <div class="card-content">
            <h3><?= e($proj['name']) ?></h3>
            <p><?= e(mb_substr($proj['description'] ?? 'Chưa có mô tả', 0, 60)) ?></p>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= e($proj['color']) ?>"></div>
            </div>
            <div class="card-footer">
                <span><?= $pct ?>% hoàn thành</span>
                <span><?= $proj['task_count'] ?> tasks</span>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
$headerActions = '<a href="' . APP_URL . '/projects/create" class="btn btn-primary">+ Tạo Project</a>';
require __DIR__ . '/../layouts/main.php';
