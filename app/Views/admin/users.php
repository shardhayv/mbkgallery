<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users - Maithili Bikash Kosh</title>
    <meta name="csrf-token" content="<?= CSRFProtection::generateToken() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
    <script src="/gallery/public/assets/js/csrf.js"></script>
</head>
<body>
    <div class="admin-container">
        <nav class="admin-nav">
            <div class="nav-brand">
                <h2><i class="fas fa-palette"></i> Admin Panel</h2>
            </div>
            <ul class="nav-menu">
                <li><a href="/gallery/admin"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                <li><a href="/gallery/admin/artists"><i class="fas fa-users"></i> Artists</a></li>
                <li><a href="/gallery/admin/paintings"><i class="fas fa-image"></i> Paintings</a></li>
                <li><a href="/gallery/admin/orders"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="/gallery/admin/users" class="active"><i class="fas fa-user-shield"></i> Admin Users</a></li>
                <li><a href="/gallery/admin/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="admin-main">
            <div class="page-header">
                <h1><i class="fas fa-user-shield"></i> Admin Users</h1>
                <button class="btn btn-primary" onclick="showAddUserModal()">
                    <i class="fas fa-plus"></i> Add Admin User
                </button>
            </div>

            <div class="content-card">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Last Login</th>
                                <th>Failed Attempts</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                                <td>
                                    <span class="badge <?= $user['failed_attempts'] > 0 ? 'badge-warning' : 'badge-success' ?>">
                                        <?= $user['failed_attempts'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['locked_until'] && strtotime($user['locked_until']) > time()): ?>
                                        <span class="badge badge-danger">Locked</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="resetPassword(<?= $user['id'] ?>)">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                    <?php if ($user['locked_until'] && strtotime($user['locked_until']) > time()): ?>
                                        <button class="btn btn-sm btn-success" onclick="unlockUser(<?= $user['id'] ?>)">
                                            <i class="fas fa-unlock"></i> Unlock
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($user['id'] != 1): // Don't allow deleting main admin ?>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Add Admin User</h3>
                <span class="close" onclick="closeModal('addUserModal')">&times;</span>
            </div>
            <form id="addUserForm">
                <?= CSRFProtection::getTokenField() ?>
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required minlength="3" maxlength="50">
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password:</label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            // CSRF token is automatically added by the form
            try {
                const response = await fetch('/gallery/admin/users/create', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Admin user created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to create user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        });

        async function resetPassword(userId) {
            if (!confirm('Are you sure you want to reset this user\'s password?')) return;
            
            const newPassword = prompt('Enter new password (minimum 6 characters):');
            if (!newPassword || newPassword.length < 6) {
                alert('Password must be at least 6 characters long');
                return;
            }
            
            try {
                const response = await fetch(`/gallery/admin/users/${userId}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        ...CSRF.addToHeaders()
                    },
                    body: JSON.stringify({ password: newPassword })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Password reset successfully!');
                } else {
                    alert('Error: ' + (result.message || 'Failed to reset password'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function unlockUser(userId) {
            if (!confirm('Are you sure you want to unlock this user?')) return;
            
            try {
                const response = await fetch(`/gallery/admin/users/${userId}/unlock`, {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User unlocked successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to unlock user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`/gallery/admin/users/${userId}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('User deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to delete user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addUserModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>