<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artists Management - Admin</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-nav">
        <div class="nav-container">
            <a href="/gallery/admin" class="nav-brand">Admin Panel</a>
            
            <ul class="nav-links">
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists" class="active">Artists</a></li>
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
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists" class="active">Artists</a></li>
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
                <h1>Artists Management</h1>
                <p>Manage artist profiles and information</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('addArtistModal')">
                <span>+</span> Add New Artist
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Artists</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th class="hide-mobile">Email</th>
                                <th class="hide-mobile">Phone</th>
                                <th>Paintings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($artists as $artist): ?>
                            <tr>
                                <td><?= $artist['id'] ?></td>
                                <td><?= htmlspecialchars($artist['name']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($artist['email'] ?? '') ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($artist['phone'] ?? '') ?></td>
                                <td><?= $artist['painting_count'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= $artist['status'] ?>">
                                        <?= ucfirst($artist['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" onclick="editArtist(<?= $artist['id'] ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteArtist(<?= $artist['id'] ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Artist Modal -->
    <div id="addArtistModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Artist</h3>
                <button type="button" class="modal-close" onclick="closeModal('addArtistModal')">&times;</button>
            </div>
            
            <form id="addArtistForm">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addArtistModal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Artist</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }

        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    closeModal(modal.id);
                }
            });
        }

        document.getElementById('addArtistForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Adding...';
            
            try {
                const response = await fetch('/gallery/admin/artists', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to add artist'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Artist';
            }
        });

        async function deleteArtist(id) {
            if (!confirm('Are you sure you want to delete this artist? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`/gallery/admin/artists/${id}`, {
                    method: 'DELETE'
                });
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
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
    </script>
</body>
</html>