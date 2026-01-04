<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-nav">
        <div class="nav-container">
            <a href="/gallery/admin" class="nav-brand">Admin Panel</a>
            
            <ul class="nav-links">
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings">Paintings</a></li>
                <li><a href="/gallery/admin/orders" class="active">Orders</a></li>
                <li><a href="/gallery/admin/reports">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <span>☰</span>
            </button>
        </div>
        
        <div class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings">Paintings</a></li>
                <li><a href="/gallery/admin/orders" class="active">Orders</a></li>
                <li><a href="/gallery/admin/reports">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Orders Management</h1>
                <p>Track and manage customer orders</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3>Filter Orders</h3>
            </div>
            <div class="card-body">
                <div class="filter-group" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <label>Status:</label>
                    <a href="/gallery/admin/orders" class="btn btn-sm <?= $current_status === 'all' ? 'btn-primary' : 'btn-secondary' ?>">All</a>
                    <a href="/gallery/admin/orders?status=pending" class="btn btn-sm <?= $current_status === 'pending' ? 'btn-primary' : 'btn-secondary' ?>">Pending</a>
                    <a href="/gallery/admin/orders?status=confirmed" class="btn btn-sm <?= $current_status === 'confirmed' ? 'btn-primary' : 'btn-secondary' ?>">Confirmed</a>
                    <a href="/gallery/admin/orders?status=shipped" class="btn btn-sm <?= $current_status === 'shipped' ? 'btn-primary' : 'btn-secondary' ?>">Shipped</a>
                    <a href="/gallery/admin/orders?status=delivered" class="btn btn-sm <?= $current_status === 'delivered' ? 'btn-primary' : 'btn-secondary' ?>">Delivered</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Orders</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th class="hide-mobile">Email</th>
                                <th class="hide-mobile">Phone</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="hide-mobile">Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($order['customer_email']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($order['customer_phone'] ?? '') ?></td>
                                <td><?= $order['item_count'] ?> items</td>
                                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="hide-mobile"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <select class="form-control" style="min-width: 120px;" onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)">
                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }

        async function updateOrderStatus(orderId, status) {
            const selectElement = event.target;
            const originalValue = selectElement.getAttribute('data-original') || selectElement.value;
            
            selectElement.disabled = true;
            
            try {
                const response = await fetch(`/gallery/admin/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `status=${status}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update the status badge
                    const row = selectElement.closest('tr');
                    const badge = row.querySelector('.status-badge');
                    badge.className = `status-badge status-${status}`;
                    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    selectElement.setAttribute('data-original', status);
                } else {
                    alert('Error: ' + result.message);
                    selectElement.value = originalValue;
                }
            } catch (error) {
                alert('Error: ' + error.message);
                selectElement.value = originalValue;
            } finally {
                selectElement.disabled = false;
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            
            if (!mobileNav.contains(event.target) && !mobileBtn.contains(event.target)) {
                mobileNav.classList.remove('active');
            }
        });

        // Store original values for selects
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select.form-control').forEach(select => {
                select.setAttribute('data-original', select.value);
            });
        });
    </script>
</body>
</html>