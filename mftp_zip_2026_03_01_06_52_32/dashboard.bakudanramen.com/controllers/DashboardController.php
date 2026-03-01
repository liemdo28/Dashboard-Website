<?php
class DashboardController {
    private $taskModel;
    private $projectModel;
    private $userModel;

    public function __construct() {
        $this->taskModel = new Task();
        $this->projectModel = new Project();
        $this->userModel = new User();
    }

    public function index() {
        $userId = $_SESSION['user_id'];

        // Stats
        $totalProjects = $this->projectModel->count();
        $totalTasks = $this->taskModel->totalCount();
        $completedTasks = $this->taskModel->completedCount();
        $totalMembers = $this->userModel->count();

        // My tasks
        $myTasks = $this->taskModel->getByUser($userId, 10);
        $upcomingTasks = $this->taskModel->getUpcoming($userId, 7);
        $overdueTasks = $this->taskModel->getOverdue($userId);

        // Task status distribution
        $tasksByStatus = $this->taskModel->countByStatus();

        // Projects
        $projects = $this->projectModel->getByUser($userId);

        require __DIR__ . '/../views/dashboard/index.php';
    }
}
