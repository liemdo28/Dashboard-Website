<?php
/**
 * TaskFlow - Entry Point & Router
 */

session_start();

// Load config
require_once __DIR__ . '/config/database.php';

// Load models
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Project.php';
require_once __DIR__ . '/models/Task.php';
require_once __DIR__ . '/models/Comment.php';

// Load controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/ProjectController.php';
require_once __DIR__ . '/controllers/TaskController.php';
require_once __DIR__ . '/controllers/CommentController.php';

// Helper functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    static $user = null;
    if ($user === null) {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
    }
    return $user;
}

function isAdmin() {
    $user = currentUser();
    return $user && $user['role'] === 'admin';
}

function redirect($path) {
    header("Location: " . rtrim(APP_URL, '/') . "/" . ltrim($path, '/'));
    exit;
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
    } else {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' năm trước';
    if ($diff->m > 0) return $diff->m . ' tháng trước';
    if ($diff->d > 0) return $diff->d . ' ngày trước';
    if ($diff->h > 0) return $diff->h . ' giờ trước';
    if ($diff->i > 0) return $diff->i . ' phút trước';
    return 'Vừa xong';
}

// ---- ROUTING ----
$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$method = $_SERVER['REQUEST_METHOD'];

// Public routes (no auth required)
$publicRoutes = ['login', 'auth/login'];

// Check authentication
if (!isLoggedIn() && !in_array($route, $publicRoutes)) {
    redirect('login');
}

// Route handling
switch (true) {
    // Auth routes
    case $route === 'login':
        $ctrl = new AuthController();
        $method === 'POST' ? $ctrl->login() : $ctrl->showLogin();
        break;

    case $route === 'logout':
        $ctrl = new AuthController();
        $ctrl->logout();
        break;

    // Dashboard
    case $route === '' || $route === 'dashboard':
        $ctrl = new DashboardController();
        $ctrl->index();
        break;

    // Projects
    case $route === 'projects':
        $ctrl = new ProjectController();
        $method === 'POST' ? $ctrl->store() : $ctrl->index();
        break;

    case preg_match('/^projects\/create$/', $route):
        $ctrl = new ProjectController();
        $ctrl->create();
        break;

    case preg_match('/^projects\/(\d+)$/', $route, $m):
        $ctrl = new ProjectController();
        $ctrl->show($m[1]);
        break;

    case preg_match('/^projects\/(\d+)\/edit$/', $route, $m):
        $ctrl = new ProjectController();
        $method === 'POST' ? $ctrl->update($m[1]) : $ctrl->edit($m[1]);
        break;

    case preg_match('/^projects\/(\d+)\/delete$/', $route, $m):
        $ctrl = new ProjectController();
        $ctrl->delete($m[1]);
        break;

    case preg_match('/^projects\/(\d+)\/members$/', $route, $m):
        $ctrl = new ProjectController();
        $method === 'POST' ? $ctrl->addMember($m[1]) : null;
        break;

    case preg_match('/^projects\/(\d+)\/members\/(\d+)\/remove$/', $route, $m):
        $ctrl = new ProjectController();
        $ctrl->removeMember($m[1], $m[2]);
        break;

    // Sections
    case preg_match('/^projects\/(\d+)\/sections$/', $route, $m):
        $ctrl = new ProjectController();
        if ($method === 'POST') $ctrl->addSection($m[1]);
        break;

    case preg_match('/^sections\/(\d+)\/delete$/', $route, $m):
        $ctrl = new ProjectController();
        $ctrl->deleteSection($m[1]);
        break;

    // Tasks
    case $route === 'tasks' && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->store();
        break;

    case preg_match('/^tasks\/(\d+)$/', $route, $m):
        $ctrl = new TaskController();
        if ($method === 'POST') {
            $ctrl->update($m[1]);
        } else {
            $ctrl->show($m[1]);
        }
        break;

    case preg_match('/^tasks\/(\d+)\/delete$/', $route, $m):
        $ctrl = new TaskController();
        $ctrl->delete($m[1]);
        break;

    case preg_match('/^tasks\/(\d+)\/toggle$/', $route, $m):
        $ctrl = new TaskController();
        $ctrl->toggleComplete($m[1]);
        break;

    case $route === 'tasks/reorder' && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->reorder();
        break;

    case preg_match('/^tasks\/(\d+)\/move$/', $route, $m) && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->move($m[1]);
        break;

    // Comments
    case preg_match('/^tasks\/(\d+)\/comments$/', $route, $m) && $method === 'POST':
        $ctrl = new CommentController();
        $ctrl->store($m[1]);
        break;

    case preg_match('/^comments\/(\d+)\/delete$/', $route, $m):
        $ctrl = new CommentController();
        $ctrl->delete($m[1]);
        break;

    // Attachments
    case preg_match('/^tasks\/(\d+)\/upload$/', $route, $m) && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->upload($m[1]);
        break;

    case preg_match('/^attachments\/(\d+)\/delete$/', $route, $m):
        $ctrl = new TaskController();
        $ctrl->deleteAttachment($m[1]);
        break;

    case preg_match('/^attachments\/(\d+)\/download$/', $route, $m):
        $ctrl = new TaskController();
        $ctrl->downloadAttachment($m[1]);
        break;

    // Admin routes
    case $route === 'admin/users':
        if (!isAdmin()) redirect('dashboard');
        $ctrl = new AuthController();
        $method === 'POST' ? $ctrl->createUser() : $ctrl->listUsers();
        break;

    case preg_match('/^admin\/users\/(\d+)\/toggle$/', $route, $m):
        if (!isAdmin()) redirect('dashboard');
        $ctrl = new AuthController();
        $ctrl->toggleUser($m[1]);
        break;

    case preg_match('/^admin\/users\/(\d+)\/delete$/', $route, $m):
        if (!isAdmin()) redirect('dashboard');
        $ctrl = new AuthController();
        $ctrl->deleteUser($m[1]);
        break;

    // API endpoints (for AJAX)
    case $route === 'api/tasks/reorder' && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->reorder();
        break;

    case preg_match('/^api\/tasks\/(\d+)\/move$/', $route, $m) && $method === 'POST':
        $ctrl = new TaskController();
        $ctrl->move($m[1]);
        break;

    case preg_match('/^api\/tasks\/(\d+)$/', $route, $m):
        $ctrl = new TaskController();
        $ctrl->getJson($m[1]);
        break;

    // 404
    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        break;
}
