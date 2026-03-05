<?php
$pageTitle = e($project['name']);
$currentPage = 'projects';
$extraJs = ['board.js', 'timeline.js'];

ob_start();
?>

<!-- View Tabs & Actions -->
<div class="flex-between mb-4">
    <div class="view-tabs">
        <a href="?view=board" class="view-tab <?= $view === 'board' ? 'active' : '' ?>">📋 Board</a>
        <a href="?view=list" class="view-tab <?= $view === 'list' ? 'active' : '' ?>">📝 List</a>
        <a href="?view=timeline" class="view-tab <?= $view === 'timeline' ? 'active' : '' ?>">📅 Timeline</a>
    </div>
    <div class="flex gap-2">
        <a href="<?= APP_URL ?>/projects/<?= $project['id'] ?>/edit" class="btn btn-outline btn-sm">⚙️ Settings</a>
        <button class="btn btn-primary btn-sm" onclick="openNewTaskModal()">+ Thêm Task</button>
    </div>
</div>

<!-- ==================== BOARD VIEW ==================== -->
<?php if ($view === 'board'): ?>
<div class="board-container" id="boardContainer">
    <?php foreach ($sections as $section): ?>
    <div class="board-column" data-section-id="<?= $section['id'] ?>">
        <div class="column-header">
            <span><?= e($section['name']) ?></span>
            <span class="count"><?= count($tasksBySection[$section['id']] ?? []) ?></span>
        </div>
        <div class="column-tasks" data-section-id="<?= $section['id'] ?>">
            <?php foreach ($tasksBySection[$section['id']] ?? [] as $task): ?>
            <div class="task-card" data-task-id="<?= $task['id'] ?>" draggable="true" onclick="openTaskDetail(<?= $task['id'] ?>)">
                <div class="task-title"><?= e($task['title']) ?></div>
                <div class="task-meta">
                    <span class="tag priority-<?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?></span>
                    <?php if ($task['due_date']): ?>
                    <span class="due-date <?= $task['due_date'] < date('Y-m-d') ? 'overdue' : '' ?>">
                        <?= date('d/m', strtotime($task['due_date'])) ?>
                    </span>
                    <?php endif; ?>
                    <?php if ($task['assignee_name']): ?>
                    <span class="assignee-small" title="<?= e($task['assignee_name']) ?>">
                        <?= strtoupper(substr($task['assignee_name'], 0, 1)) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="add-task-btn" onclick="quickAddTask(<?= $section['id'] ?>)">
            + Thêm task
        </div>
        <div class="quick-add" id="quickAdd-<?= $section['id'] ?>">
            <form onsubmit="submitQuickTask(event, <?= $project['id'] ?>, <?= $section['id'] ?>)">
                <input type="text" placeholder="Tên task..." name="title" autofocus>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="add-column-btn" onclick="addNewSection(<?= $project['id'] ?>)">+ Thêm cột</div>
</div>

<!-- ==================== LIST VIEW ==================== -->
<?php elseif ($view === 'list'): ?>
<div class="list-view">
    <div class="card">
        <div class="card-body" style="padding:0">
            <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <div class="icon">📝</div>
                <h3>Chưa có task nào</h3>
                <p>Thêm task đầu tiên cho project này</p>
            </div>
            <?php else: ?>
            <table class="list-table">
                <thead>
                    <tr>
                        <th style="width:30px"></th>
                        <th>Task</th>
                        <th>Người thực hiện</th>
                        <th>Hạn</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td>
                            <div class="task-check <?= $task['is_completed'] ? 'completed' : '' ?>"
                                 onclick="toggleTask(<?= $task['id'] ?>)">
                                <?= $task['is_completed'] ? '✓' : '' ?>
                            </div>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick="openTaskDetail(<?= $task['id'] ?>)"
                               style="font-weight:500;<?= $task['is_completed'] ? 'text-decoration:line-through;color:var(--text-light)' : '' ?>">
                                <?= e($task['title']) ?>
                            </a>
                        </td>
                        <td class="text-muted text-sm"><?= e($task['assignee_name'] ?? 'Chưa gán') ?></td>
                        <td class="text-sm <?= ($task['due_date'] && $task['due_date'] < date('Y-m-d') && !$task['is_completed']) ? 'overdue' : 'text-muted' ?>">
                            <?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '-' ?>
                        </td>
                        <td><span class="tag priority-<?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?></span></td>
                        <td><span class="badge badge-<?= $task['status'] === 'done' ? 'active' : 'member' ?>"><?= ucfirst(str_replace('_', ' ', $task['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ==================== TIMELINE VIEW ==================== -->
<?php elseif ($view === 'timeline'): ?>
<div class="timeline-container" id="timelineContainer">
    <?php
    // Calculate date range
    $today = new DateTime();
    $startDate = (clone $today)->modify('-3 days');
    $endDate = (clone $today)->modify('+30 days');
    $totalDays = $startDate->diff($endDate)->days + 1;
    ?>
    <div class="timeline-header" style="padding-left:200px">
        <?php
        $d = clone $startDate;
        for ($i = 0; $i < $totalDays; $i++):
            $isToday = $d->format('Y-m-d') === $today->format('Y-m-d');
        ?>
        <div class="day <?= $isToday ? 'today' : '' ?>" style="min-width:40px">
            <div><?= $d->format('d') ?></div>
            <div><?= $d->format('D') ?></div>
        </div>
        <?php $d->modify('+1 day'); endfor; ?>
    </div>

    <?php foreach ($tasks as $task):
        if (!$task['due_date']) continue;
        $taskStart = $task['start_date'] ? new DateTime($task['start_date']) : new DateTime($task['due_date']);
        $taskEnd = new DateTime($task['due_date']);
        if ($taskEnd < $startDate || $taskStart > $endDate) continue;

        $leftDays = max(0, $startDate->diff($taskStart)->days);
        $duration = max(1, $taskStart->diff($taskEnd)->days + 1);
        $left = $leftDays * 40;
        $width = $duration * 40;
    ?>
    <div class="timeline-row">
        <div class="timeline-label" title="<?= e($task['title']) ?>"><?= e($task['title']) ?></div>
        <div class="timeline-bar-container">
            <div class="timeline-bar priority-<?= $task['priority'] ?>"
                 style="left:<?= $left ?>px;width:<?= $width ?>px"
                 onclick="openTaskDetail(<?= $task['id'] ?>)">
                <?= e(mb_substr($task['title'], 0, 20)) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty(array_filter($tasks, function($t){ return !empty($t['due_date']); }))): ?>
    <div class="empty-state">
        <div class="icon">📅</div>
        <h3>Chưa có task nào có deadline</h3>
        <p>Thêm due date cho tasks để hiển thị trên timeline</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ==================== TASK DETAIL MODAL ==================== -->
<div class="modal-overlay" id="taskModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTaskTitle">Task Detail</h3>
            <button class="btn btn-ghost" onclick="closeTaskModal()">✕</button>
        </div>
        <div class="modal-body" id="modalTaskBody">
            <p>Loading...</p>
        </div>
    </div>
</div>

<!-- ==================== NEW TASK MODAL ==================== -->
<div class="modal-overlay" id="newTaskModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Tạo Task Mới</h3>
            <button class="btn btn-ghost" onclick="closeNewTaskModal()">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="<?= APP_URL ?>/tasks">
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">

                <div class="form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Tên task...">
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" placeholder="Mô tả chi tiết..."></textarea>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Cột (Section)</label>
                        <select name="section_id" class="form-control">
                            <?php foreach ($sections as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Người thực hiện</label>
                        <select name="assignee_id" class="form-control">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($members as $m): ?>
                            <option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label>Ưu tiên</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control">
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Tạo Task</button>
                    <button type="button" class="btn btn-secondary" onclick="closeNewTaskModal()">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const APP_URL = '<?= APP_URL ?>';
const PROJECT_ID = <?= $project['id'] ?>;

function openNewTaskModal() {
    document.getElementById('newTaskModal').classList.add('active');
}
function closeNewTaskModal() {
    document.getElementById('newTaskModal').classList.remove('active');
}

function openTaskDetail(taskId) {
    const modal = document.getElementById('taskModal');
    const body = document.getElementById('modalTaskBody');
    modal.classList.add('active');
    body.innerHTML = '<p>Đang tải...</p>';

    fetch(APP_URL + '/api/tasks/' + taskId)
        .then(r => r.json())
        .then(data => {
            const t = data.task;
            const comments = data.comments || [];
            const attachments = data.attachments || [];

            document.getElementById('modalTaskTitle').textContent = t.title;

            let html = `
                <form method="POST" action="${APP_URL}/tasks/${t.id}">
                    <div class="form-group">
                        <label>Tiêu đề</label>
                        <input type="text" name="title" class="form-control" value="${escHtml(t.title)}">
                    </div>
                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control">${escHtml(t.description || '')}</textarea>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>Ưu tiên</label>
                            <select name="priority" class="form-control">
                                ${['low','medium','high','urgent'].map(p => `<option value="${p}" ${t.priority===p?'selected':''}>${p.charAt(0).toUpperCase()+p.slice(1)}</option>`).join('')}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                ${['todo','in_progress','review','done'].map(s => `<option value="${s}" ${t.status===s?'selected':''}>${s.replace('_',' ')}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label>Deadline</label>
                            <input type="date" name="due_date" class="form-control" value="${t.due_date || ''}">
                        </div>
                        <div class="form-group">
                            <label>Người thực hiện</label>
                            <select name="assignee_id" class="form-control">
                                <option value="">-- Chọn --</option>
                                <?php foreach ($members as $m): ?>
                                <option value="<?= $m['id'] ?>" ${t.assignee_id == <?= $m['id'] ?> ? 'selected' : ''}><?= e($m['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                        <a href="${APP_URL}/tasks/${t.id}/delete" class="btn btn-danger btn-sm" onclick="return confirm('Xóa task này?')">Xóa</a>
                    </div>
                </form>

                <!-- Attachments -->
                <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px">
                    <h4 style="font-size:14px;margin-bottom:8px">📎 Files đính kèm</h4>
                    <ul class="attachment-list">
                        ${attachments.map(a => `
                            <li class="attachment-item">
                                <span class="file-icon">📄</span>
                                <a href="${APP_URL}/attachments/${a.id}/download">${escHtml(a.original_name)}</a>
                                <span class="file-size">${formatBytes(a.file_size)}</span>
                                <a href="${APP_URL}/attachments/${a.id}/delete" class="btn btn-ghost btn-sm" onclick="return confirm('Xóa?')">🗑</a>
                            </li>
                        `).join('')}
                    </ul>
                    <form id="uploadForm" style="margin-top:8px">
                        <input type="file" id="fileInput" onchange="uploadFile(${t.id})" style="font-size:13px">
                    </form>
                </div>

                <!-- Comments -->
                <div class="comments-section">
                    <h4 style="font-size:14px;margin-bottom:12px">💬 Bình luận (${comments.length})</h4>
                    <div id="commentsList">
                        ${comments.map(c => `
                            <div class="comment-item">
                                <div class="user-avatar" style="width:32px;height:32px;font-size:12px;flex-shrink:0">${c.user_name.charAt(0).toUpperCase()}</div>
                                <div class="comment-body">
                                    <div class="flex-between">
                                        <span class="comment-author">${escHtml(c.user_name)}</span>
                                        <span class="comment-time">${c.created_at}</span>
                                    </div>
                                    <div class="comment-text">${escHtml(c.content)}</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                    <form class="comment-form" onsubmit="submitComment(event, ${t.id})">
                        <input type="text" placeholder="Viết bình luận..." id="commentInput" required>
                        <button type="submit" class="btn btn-primary btn-sm">Gửi</button>
                    </form>
                </div>
            `;

            body.innerHTML = html;
        })
        .catch(() => {
            body.innerHTML = '<p class="text-muted">Không thể tải thông tin task.</p>';
        });
}

function closeTaskModal() {
    document.getElementById('taskModal').classList.remove('active');
}

function toggleTask(taskId) {
    fetch(APP_URL + '/tasks/' + taskId + '/toggle', { method: 'POST' })
        .then(() => location.reload());
}

function quickAddTask(sectionId) {
    const el = document.getElementById('quickAdd-' + sectionId);
    el.classList.toggle('active');
    if (el.classList.contains('active')) {
        el.querySelector('input').focus();
    }
}

function submitQuickTask(e, projectId, sectionId) {
    e.preventDefault();
    const input = e.target.querySelector('input');
    const title = input.value.trim();
    if (!title) return;

    const form = new FormData();
    form.append('project_id', projectId);
    form.append('section_id', sectionId);
    form.append('title', title);

    fetch(APP_URL + '/tasks', {
        method: 'POST',
        body: form
    }).then(() => location.reload());
}

function addNewSection(projectId) {
    const name = prompt('Tên cột mới:');
    if (!name) return;

    const form = new FormData();
    form.append('name', name);

    fetch(APP_URL + '/projects/' + projectId + '/sections', {
        method: 'POST',
        body: form
    }).then(() => location.reload());
}

function submitComment(e, taskId) {
    e.preventDefault();
    const input = document.getElementById('commentInput');
    const content = input.value.trim();
    if (!content) return;

    const form = new FormData();
    form.append('content', content);

    fetch(APP_URL + '/tasks/' + taskId + '/comments', {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(data => {
        if (data.success) {
            input.value = '';
            openTaskDetail(taskId); // reload modal
        }
    });
}

function uploadFile(taskId) {
    const fileInput = document.getElementById('fileInput');
    if (!fileInput.files[0]) return;

    const form = new FormData();
    form.append('file', fileInput.files[0]);

    fetch(APP_URL + '/tasks/' + taskId + '/upload', {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(data => {
        if (data.success) {
            openTaskDetail(taskId);
        } else {
            alert(data.error || 'Upload thất bại');
        }
    });
}

function escHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatBytes(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$headerActions = '';
require __DIR__ . '/../layouts/main.php';
