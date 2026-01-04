<?php
class AdminController extends BaseController {
    private $orderModel;
    private $paintingModel;
    private $artistModel;
    private $categoryModel;
    private $adminUserModel;
    
    public function __construct() {
        parent::__construct();
        $this->orderModel = new Order();
        $this->paintingModel = new Painting();
        $this->artistModel = new Artist();
        $this->categoryModel = new Category();
        $this->adminUserModel = new AdminUser();
    }
    
    public function dashboard() {
        AuthMiddleware::requireAuth();
        
        try {
            $stats = $this->getDashboardStats();
            $this->view('admin/dashboard', $stats);
        } catch (Exception $e) {
            $this->view('errors/500');
        }
    }
    
    // Artists Management
    public function artists() {
        AuthMiddleware::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->createArtist();
        }
        
        $artists = $this->artistModel->getActiveWithPaintingCount();
        $this->view('admin/artists', ['artists' => $artists]);
    }
    
    public function createArtist() {
        CSRFProtection::validateRequest();
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequest($data, [
            'name' => ['required' => true, 'type' => 'string', 'max_length' => 100],
            'email' => ['type' => 'email'],
            'phone' => ['type' => 'string', 'max_length' => 20]
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        try {
            $artistId = $this->artistModel->create($data);
            $this->json(['success' => true, 'id' => $artistId]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create artist'], 500);
        }
    }
    
    public function updateArtist($id) {
        AuthMiddleware::requireAuth();
        
        $data = $this->sanitizeInput($_POST);
        try {
            $this->artistModel->update($id, $data);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update artist'], 500);
        }
    }
    
    public function deleteArtist($id) {
        AuthMiddleware::requireAuth();
        
        try {
            $this->artistModel->update($id, ['status' => 'inactive']);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete artist'], 500);
        }
    }
    
    // Paintings Management
    public function paintings() {
        AuthMiddleware::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->createPainting();
        }
        
        $paintings = $this->paintingModel->getAvailable(100);
        $artists = $this->artistModel->findAll(['status' => 'active']);
        $categories = $this->categoryModel->findAll();
        
        $this->view('admin/paintings', [
            'paintings' => $paintings,
            'artists' => $artists,
            'categories' => $categories
        ]);
    }
    
    public function createPainting() {
        CSRFProtection::validateRequest();
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequest($data, [
            'title' => ['required' => true, 'type' => 'string', 'max_length' => 200],
            'artist_id' => ['required' => true, 'type' => 'integer'],
            'price' => ['required' => true, 'type' => 'decimal']
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        try {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $data['image_path'] = $this->uploadImage($_FILES['image'], 'paintings');
            }
            
            $paintingId = $this->paintingModel->create($data);
            $this->json(['success' => true, 'id' => $paintingId]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create painting'], 500);
        }
    }
    
    public function updatePainting($id) {
        AuthMiddleware::requireAuth();
        
        $data = $this->sanitizeInput($_POST);
        try {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $data['image_path'] = $this->uploadImage($_FILES['image'], 'paintings');
            }
            
            $this->paintingModel->update($id, $data);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update painting'], 500);
        }
    }
    
    public function deletePainting($id) {
        AuthMiddleware::requireAuth();
        
        try {
            $this->paintingModel->delete($id);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete painting'], 500);
        }
    }
    
    // Orders Management
    public function orders() {
        AuthMiddleware::requireAuth();
        
        $status = $_GET['status'] ?? 'all';
        $orders = $this->orderModel->getOrdersWithDetails($status);
        $this->view('admin/orders', ['orders' => $orders, 'current_status' => $status]);
    }
    
    public function updateOrderStatus($id) {
        AuthMiddleware::requireAuth();
        
        $status = $this->sanitizeInput($_POST['status'] ?? '');
        $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'message' => 'Invalid status'], 400);
            return;
        }
        
        try {
            $this->orderModel->update($id, ['status' => $status]);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to update order'], 500);
        }
    }
    
    // Reports
    public function reports() {
        AuthMiddleware::requireAuth();
        
        $period = $_GET['period'] ?? '30';
        $reports = $this->generateReports($period);
        $this->view('admin/reports', ['reports' => $reports, 'period' => $period]);
    }
    
    // Settings
    public function settings() {
        AuthMiddleware::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updateSettings();
        }
        
        $categories = $this->categoryModel->findAll();
        $this->view('admin/settings', ['categories' => $categories]);
    }
    
    public function updateSettings() {
        $data = $this->sanitizeInput($_POST);
        
        if (isset($data['new_category'])) {
            $this->categoryModel->create(['name' => $data['new_category']]);
        }
        
        $this->json(['success' => true]);
    }
    
    // Authentication
    public function login() {
        AuthMiddleware::redirectIfAuthenticated();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->authenticate();
        }
        $this->view('admin/login');
    }
    
    public function authenticate() {
        try {
            CSRFProtection::validateRequest();
            SecurityManager::rateLimit('admin_login', 5, 900); // 5 attempts per 15 minutes
            
            $username = SecurityManager::sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (!SecurityManager::validateInput($username, 'string', ['min_length' => 3, 'max_length' => 50])) {
                throw new Exception('Invalid username format');
            }
            
            if (strlen($password) < 6) {
                throw new Exception('Password too short');
            }
            
            // Check if user is locked
            $user = $this->adminUserModel->findOne(['username' => $username]);
            if ($user && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
                $lockTime = date('H:i:s', strtotime($user['locked_until']));
                throw new Exception("Account locked until $lockTime");
            }
            
            // Authenticate user
            $authenticatedUser = $this->adminUserModel->authenticate($username, $password);
            
            if ($authenticatedUser) {
                // Reset failed attempts on successful login
                $this->adminUserModel->update($authenticatedUser['id'], [
                    'failed_attempts' => 0,
                    'locked_until' => null
                ]);
                
                // Update last login
                $this->adminUserModel->updateLastLogin($authenticatedUser['id']);
                
                // Set session
                AuthMiddleware::login($authenticatedUser['id'], $authenticatedUser['username']);
                
                header('Location: /gallery/admin');
                exit;
            } else {
                // Handle failed login
                if ($user) {
                    $failedAttempts = ($user['failed_attempts'] ?? 0) + 1;
                    $updateData = ['failed_attempts' => $failedAttempts];
                    
                    // Lock account after 5 failed attempts
                    if ($failedAttempts >= 5) {
                        $updateData['locked_until'] = date('Y-m-d H:i:s', time() + 900); // 15 minutes
                    }
                    
                    $this->adminUserModel->update($user['id'], $updateData);
                }
                
                SecurityManager::logSecurityEvent('ADMIN_LOGIN_FAILED', "Username: $username");
                throw new Exception('Invalid credentials');
            }
            
        } catch (Exception $e) {
            $this->view('admin/login', ['error' => $e->getMessage()]);
        }
    }
    
    public function logout() {
        AuthMiddleware::logout();
        $this->redirect('/gallery/');
    }
    
    // User Management
    public function users() {
        AuthMiddleware::requireAuth();
        
        $users = $this->adminUserModel->findAll();
        $this->view('admin/users', ['users' => $users]);
    }
    
    public function createUser() {
        AuthMiddleware::requireAuth();
        CSRFProtection::validateRequest();
        
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequest($data, [
            'username' => ['required' => true, 'type' => 'string', 'min_length' => 3, 'max_length' => 50],
            'password' => ['required' => true, 'type' => 'string', 'min_length' => 6]
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Check if username already exists
        if ($this->adminUserModel->findOne(['username' => $data['username']])) {
            $this->json(['success' => false, 'message' => 'Username already exists'], 400);
            return;
        }
        
        try {
            $userId = $this->adminUserModel->createUser($data['username'], $data['password']);
            SecurityManager::logSecurityEvent('ADMIN_USER_CREATED', "New user: {$data['username']}");
            $this->json(['success' => true, 'id' => $userId]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to create user'], 500);
        }
    }
    
    public function resetUserPassword($id) {
        AuthMiddleware::requireAuth();
        
        // Handle both form data and JSON input
        $input = $_SERVER['CONTENT_TYPE'] === 'application/json' 
            ? json_decode(file_get_contents('php://input'), true)
            : $_POST;
            
        // Validate CSRF token
        if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!CSRFProtection::validateToken($token)) {
                $this->json(['success' => false, 'message' => 'CSRF token validation failed'], 403);
                return;
            }
        } else {
            CSRFProtection::validateRequest();
        }
        
        $password = $input['password'] ?? '';
        
        if (strlen($password) < 6) {
            $this->json(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
            return;
        }
        
        try {
            $user = $this->adminUserModel->findById($id);
            if (!$user) {
                $this->json(['success' => false, 'message' => 'User not found'], 404);
                return;
            }
            
            $this->adminUserModel->updatePassword($id, $password);
            SecurityManager::logSecurityEvent('ADMIN_PASSWORD_RESET', "User ID: $id, Username: {$user['username']}");
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to reset password'], 500);
        }
    }
    
    public function unlockUser($id) {
        AuthMiddleware::requireAuth();
        
        try {
            $user = $this->adminUserModel->findById($id);
            if (!$user) {
                $this->json(['success' => false, 'message' => 'User not found'], 404);
                return;
            }
            
            $this->adminUserModel->update($id, [
                'failed_attempts' => 0,
                'locked_until' => null
            ]);
            
            SecurityManager::logSecurityEvent('ADMIN_USER_UNLOCKED', "User ID: $id, Username: {$user['username']}");
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to unlock user'], 500);
        }
    }
    
    public function deleteUser($id) {
        AuthMiddleware::requireAuth();
        
        // Prevent deleting the main admin user
        if ($id == 1) {
            $this->json(['success' => false, 'message' => 'Cannot delete main admin user'], 400);
            return;
        }
        
        try {
            $user = $this->adminUserModel->findById($id);
            if (!$user) {
                $this->json(['success' => false, 'message' => 'User not found'], 404);
                return;
            }
            
            $this->adminUserModel->delete($id);
            SecurityManager::logSecurityEvent('ADMIN_USER_DELETED', "User ID: $id, Username: {$user['username']}");
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Failed to delete user'], 500);
        }
    }
    
    // Helper Methods
    private function getDashboardStats() {
        try {
            return [
                'artists' => count($this->artistModel->findAll(['status' => 'active'])),
                'available_paintings' => count($this->paintingModel->findAll(['status' => 'available'])),
                'sold_paintings' => count($this->paintingModel->findAll(['status' => 'sold'])),
                'total_orders' => count($this->orderModel->findAll()),
                'pending_orders' => count($this->orderModel->findAll(['status' => 'pending'])),
                'total_revenue' => $this->orderModel->getTotalRevenue(),
                'monthly_revenue' => $this->orderModel->getMonthlyRevenue(),
                'recent_orders' => $this->orderModel->getRecentOrders(10),
                'top_artists' => $this->artistModel->getTopArtists(5),
                'low_stock' => $this->paintingModel->getLowStock()
            ];
        } catch (Exception $e) {
            return [
                'artists' => 0,
                'available_paintings' => 0,
                'sold_paintings' => 0,
                'total_orders' => 0,
                'pending_orders' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
                'recent_orders' => [],
                'top_artists' => [],
                'low_stock' => []
            ];
        }
    }
    
    private function generateReports($period) {
        try {
            return [
                'sales_summary' => $this->orderModel->getSalesSummary($period),
                'artist_performance' => $this->artistModel->getPerformanceReport($period),
                'category_analysis' => $this->categoryModel->getCategoryAnalysis($period),
                'revenue_trend' => $this->orderModel->getRevenueTrend($period)
            ];
        } catch (Exception $e) {
            return [
                'sales_summary' => [],
                'artist_performance' => [],
                'category_analysis' => [],
                'revenue_trend' => []
            ];
        }
    }
    
    private function uploadImage($file, $folder) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type');
        }
        
        $uploadDir = APP_ROOT . "/public/uploads/$folder/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload file');
        }
        
        return "/gallery/public/uploads/$folder/$filename";
    }
}
?>