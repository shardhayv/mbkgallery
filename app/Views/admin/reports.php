<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
    <style>
        .reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        @media (min-width: 768px) { .reports-grid { gap: 2rem; } }
        .metric { text-align: center; padding: 1rem; }
        .metric-value { font-size: clamp(1.5rem, 4vw, 2rem); font-weight: bold; color: #8B0000; }
        .metric-label { color: #666; margin-top: 0.5rem; font-size: clamp(0.9rem, 2vw, 1rem); }
        .chart-placeholder { height: 200px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #666; text-align: center; padding: 1rem; }
        .period-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        @media (min-width: 768px) { .period-buttons { gap: 1rem; } }
    </style>
</head>
<body>
    <div class="admin-nav">
        <div class="nav-container">
            <a href="/gallery/admin" class="nav-brand">Admin Panel</a>
            
            <ul class="nav-links">
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings">Paintings</a></li>
                <li><a href="/gallery/admin/orders">Orders</a></li>
                <li><a href="/gallery/admin/reports" class="active">Reports</a></li>
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
                <li><a href="/gallery/admin/orders">Orders</a></li>
                <li><a href="/gallery/admin/reports" class="active">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Reports & Analytics</h1>
                <p>Sales insights and performance metrics</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3>Time Period</h3>
            </div>
            <div class="card-body">
                <div class="period-buttons">
                    <a href="/gallery/admin/reports?period=7" class="btn btn-sm <?= $period == '7' ? 'btn-primary' : 'btn-secondary' ?>">Last 7 Days</a>
                    <a href="/gallery/admin/reports?period=30" class="btn btn-sm <?= $period == '30' ? 'btn-primary' : 'btn-secondary' ?>">Last 30 Days</a>
                    <a href="/gallery/admin/reports?period=90" class="btn btn-sm <?= $period == '90' ? 'btn-primary' : 'btn-secondary' ?>">Last 90 Days</a>
                </div>
            </div>
        </div>

        <div class="reports-grid">
            <!-- Sales Summary -->
            <div class="card">
                <div class="card-header">
                    <h3>Sales Summary (Last <?= $period ?> Days)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($reports['sales_summary'])): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($reports['sales_summary'], 0, 10) as $day): ?>
                                    <tr>
                                        <td><?= date('M j', strtotime($day['date'])) ?></td>
                                        <td><?= $day['orders'] ?></td>
                                        <td>₹<?= number_format($day['revenue'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 2rem;">No sales data available for this period.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Artist Performance -->
            <div class="card">
                <div class="card-header">
                    <h3>Top Artists (Last <?= $period ?> Days)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($reports['artist_performance'])): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Artist</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports['artist_performance'] as $artist): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($artist['name']) ?></td>
                                        <td><?= $artist['paintings_sold'] ?></td>
                                        <td>₹<?= number_format($artist['revenue'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 2rem;">No artist performance data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Category Analysis -->
            <div class="card">
                <div class="card-header">
                    <h3>Category Performance (Last <?= $period ?> Days)</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($reports['category_analysis'])): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Sales</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports['category_analysis'] as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['name']) ?></td>
                                        <td><?= $category['sales_count'] ?></td>
                                        <td>₹<?= number_format($category['revenue'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 2rem;">No category data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <div class="chart-placeholder">
                        <div>
                            <strong>Revenue Trend</strong><br>
                            <?= count($reports['revenue_trend'] ?? []) ?> data points available<br>
                            <small style="opacity: 0.7;">Chart visualization can be implemented with Chart.js or similar library</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Metrics -->
        <div class="card">
            <div class="card-header">
                <h3>Key Metrics Summary</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div class="metric">
                        <div class="metric-value"><?= count($reports['sales_summary'] ?? []) ?></div>
                        <div class="metric-label">Active Days</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value"><?= array_sum(array_column($reports['sales_summary'] ?? [], 'orders')) ?></div>
                        <div class="metric-label">Total Orders</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value">₹<?= number_format(array_sum(array_column($reports['sales_summary'] ?? [], 'revenue')), 0) ?></div>
                        <div class="metric-label">Total Revenue</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value"><?= count($reports['artist_performance'] ?? []) ?></div>
                        <div class="metric-label">Active Artists</div>
                    </div>
                </div>
            </div>
        </div>
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