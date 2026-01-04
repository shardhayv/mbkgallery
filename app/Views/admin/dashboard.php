<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Maithili Bikash Kosh</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-nav">
        <div class="nav-container">
            <a href="/gallery/admin" class="nav-brand">Admin Panel</a>
            
            <ul class="nav-links">
                <li><a href="/gallery/admin" class="active">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings">Paintings</a></li>
                <li><a href="/gallery/admin/orders">Orders</a></li>
                <li><a href="/gallery/admin/reports">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <span>☰</span>
            </button>
        </div>
        
        <div class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="/gallery/admin" class="active">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings">Paintings</a></li>
                <li><a href="/gallery/admin/orders">Orders</a></li>
                <li><a href="/gallery/admin/reports">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Gallery management overview and statistics</p>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card stat-artists">
                <div class="stat-number"><?= $artists ?></div>
                <div class="stat-label">Active Artists</div>
            </div>
            <div class="stat-card stat-available">
                <div class="stat-number"><?= $available_paintings ?></div>
                <div class="stat-label">Available Paintings</div>
            </div>
            <div class="stat-card stat-sold">
                <div class="stat-number"><?= $sold_paintings ?></div>
                <div class="stat-label">Sold Paintings</div>
            </div>
            <div class="stat-card stat-orders">
                <div class="stat-number"><?= $total_orders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card stat-orders">
                <div class="stat-number"><?= $pending_orders ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card stat-revenue">
                <div class="stat-number">₹<?= number_format($total_revenue, 0) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="/gallery/admin/artists" class="action-btn">
                <h4>Manage Artists</h4>
                <p>Add, edit, or remove artists</p>
            </a>
            <a href="/gallery/admin/paintings" class="action-btn">
                <h4>Manage Paintings</h4>
                <p>Upload and manage artwork</p>
            </a>
            <a href="/gallery/admin/orders" class="action-btn">
                <h4>Process Orders</h4>
                <p>View and update order status</p>
            </a>
            <a href="/gallery/admin/reports" class="action-btn">
                <h4>View Reports</h4>
                <p>Sales analytics and insights</p>
            </a>
        </div>

        <?php if (!empty($low_stock)): ?>
        <div class="alert alert-warning">
            <strong>Low Stock Alert:</strong> Some artists have very few available paintings. Consider adding more inventory.
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Recent Orders</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th class="hide-mobile">Email</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="hide-mobile">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($order['customer_email']) ?></td>
                                <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td class="hide-mobile"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($top_artists)): ?>
        <div class="card">
            <div class="card-header">
                <h3>Top Performing Artists</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Artist</th>
                                <th>Sales Count</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_artists as $artist): ?>
                            <tr>
                                <td><?= htmlspecialchars($artist['name']) ?></td>
                                <td><?= $artist['sales_count'] ?></td>
                                <td>₹<?= number_format($artist['total_revenue'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileNav = document.getElementById('mobileNav');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            
            if (!mobileNav.contains(event.target) && !mobileBtn.contains(event.target)) {
                mobileNav.classList.remove('active');
            }
        });
    </script>
</body>
</html>