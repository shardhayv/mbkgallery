<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paintings Management - Admin</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-nav">
        <div class="nav-container">
            <a href="/gallery/admin" class="nav-brand">Admin Panel</a>
            
            <ul class="nav-links">
                <li><a href="/gallery/admin">Dashboard</a></li>
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings" class="active">Paintings</a></li>
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
                <li><a href="/gallery/admin/artists">Artists</a></li>
                <li><a href="/gallery/admin/paintings" class="active">Paintings</a></li>
                <li><a href="/gallery/admin/orders">Orders</a></li>
                <li><a href="/gallery/admin/reports">Reports</a></li>
                <li><a href="/gallery/admin/logout">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Paintings Management</h1>
                <p>Manage artwork collection and inventory</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('addPaintingModal')">
                <span>+</span> Add New Painting
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Paintings</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th class="hide-mobile">Artist</th>
                                <th class="hide-mobile">Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paintings as $painting): ?>
                            <tr>
                                <td>
                                    <?php if ($painting['image_path']): ?>
                                        <img src="<?= $painting['image_path'] ?>" alt="<?= htmlspecialchars($painting['title']) ?>" class="painting-image">
                                    <?php else: ?>
                                        <div class="painting-image" style="background: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #666;">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($painting['title']) ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($painting['artist_name'] ?? 'Unknown') ?></td>
                                <td class="hide-mobile"><?= htmlspecialchars($painting['category_name'] ?? 'Uncategorized') ?></td>
                                <td>₹<?= number_format($painting['price'], 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $painting['status'] ?>">
                                        <?= ucfirst($painting['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" onclick="editPainting(<?= $painting['id'] ?>)">Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deletePainting(<?= $painting['id'] ?>)">Delete</button>
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

    <!-- Edit Painting Modal -->
    <div id="editPaintingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Painting</h3>
                <button type="button" class="modal-close" onclick="closeModal('editPaintingModal')">&times;</button>
            </div>
            
            <form id="editPaintingForm" enctype="multipart/form-data">
                <input type="hidden" id="editPaintingId" name="id">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" id="editTitle" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Artist *</label>
                    <select id="editArtist" name="artist_id" class="form-control" required>
                        <option value="">Select Artist</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select id="editCategory" name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" id="editPrice" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Dimensions</label>
                    <input type="text" id="editDimensions" name="dimensions" class="form-control">
                </div>
                <div class="form-group">
                    <label>Medium</label>
                    <input type="text" id="editMedium" name="medium" class="form-control">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="editDescription" name="description" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="editStatus" name="status" class="form-control">
                        <option value="available">Available</option>
                        <option value="sold">Sold</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>New Image (optional)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editPaintingModal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Painting</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Add Painting Modal -->
    <div id="addPaintingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Painting</h3>
                <button type="button" class="modal-close" onclick="closeModal('addPaintingModal')">&times;</button>
            </div>
            
            <form id="addPaintingForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Artist *</label>
                    <select name="artist_id" class="form-control" required>
                        <option value="">Select Artist</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Dimensions</label>
                    <input type="text" name="dimensions" class="form-control" placeholder="e.g., 24x36 inches">
                </div>
                <div class="form-group">
                    <label>Medium</label>
                    <input type="text" name="medium" class="form-control" placeholder="e.g., Oil on Canvas">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addPaintingModal')">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Painting</button>
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

        document.getElementById('addPaintingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Adding...';
            
            try {
                const response = await fetch('/gallery/admin/paintings', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to add painting'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Painting';
            }
        });

        async function deletePainting(id) {
            if (!confirm('Are you sure you want to delete this painting? This action cannot be undone.')) return;
            
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`/gallery/admin/paintings/${id}`, {
                    method: 'POST',
                    body: formData
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
        
        async function editPainting(id) {
            try {
                // Get painting data
                const paintings = <?= json_encode($paintings) ?>;
                const painting = paintings.find(p => p.id == id);
                
                if (!painting) {
                    alert('Painting not found');
                    return;
                }
                
                // Populate form
                document.getElementById('editPaintingId').value = painting.id;
                document.getElementById('editTitle').value = painting.title;
                document.getElementById('editArtist').value = painting.artist_id;
                document.getElementById('editCategory').value = painting.category_id;
                document.getElementById('editPrice').value = painting.price;
                document.getElementById('editDimensions').value = painting.dimensions || '';
                document.getElementById('editMedium').value = painting.medium || '';
                document.getElementById('editDescription').value = painting.description || '';
                document.getElementById('editStatus').value = painting.status;
                
                openModal('editPaintingModal');
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }
        
        document.getElementById('editPaintingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = formData.get('id');
            const submitBtn = this.querySelector('button[type="submit"]');
            formData.append('_method', 'PUT');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Updating...';
            
            try {
                const response = await fetch(`/gallery/admin/paintings/${id}`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to update painting'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update Painting';
            }
        });

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