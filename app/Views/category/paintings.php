<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> - Maithili Bikash Kosh Gallery</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include APP_ROOT . '/app/Views/partials/navbar.php'; ?>
    
    <div class="container">
        <div class="section-title">
            <h2><?= htmlspecialchars($category['name']) ?></h2>
            <?php if ($category['description']): ?>
                <p><?= htmlspecialchars($category['description']) ?></p>
            <?php else: ?>
                <p>Explore our collection of <?= htmlspecialchars($category['name']) ?> paintings</p>
            <?php endif; ?>
        </div>
        
        <!-- Price Range Filter -->
        <div class="filter-section">
            <form method="GET" class="price-filter">
                <div class="filter-header">
                    <h3>Filter by Price</h3>
                    <button type="button" class="filter-toggle" onclick="toggleFilter()">Filter</button>
                </div>
                <div class="filter-content <?= (!empty($filters['min_price']) || !empty($filters['max_price'])) ? 'active' : '' ?>" id="filterContent">
                    <div class="price-inputs">
                        <div class="price-input">
                            <label>Min Price:</label>
                            <input type="number" name="min_price" placeholder="₹<?= number_format($priceRange['min_price'] ?? 0) ?>" value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                        </div>
                        <div class="price-input">
                            <label>Max Price:</label>
                            <input type="number" name="max_price" placeholder="₹<?= number_format($priceRange['max_price'] ?? 50000) ?>" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <select name="sort">
                            <option value="created_at" <?= ($filters['sort'] ?? '') == 'created_at' ? 'selected' : '' ?>>Newest</option>
                            <option value="price" <?= ($filters['sort'] ?? '') == 'price' ? 'selected' : '' ?>>Price</option>
                            <option value="title" <?= ($filters['sort'] ?? '') == 'title' ? 'selected' : '' ?>>Title</option>
                        </select>
                        <select name="order">
                            <option value="DESC" <?= ($filters['order'] ?? '') == 'DESC' ? 'selected' : '' ?>>High to Low</option>
                            <option value="ASC" <?= ($filters['order'] ?? '') == 'ASC' ? 'selected' : '' ?>>Low to High</option>
                        </select>
                        <button type="submit" class="btn">Apply</button>
                        <a href="<?= '/gallery/category/' . $category['id'] ?>" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if (empty($paintings)): ?>
            <div class="no-results">
                <h3>No paintings found</h3>
                <p>No paintings in this category match your current filter criteria. Try adjusting your price range or clearing filters.</p>
                <a href="<?= '/gallery/category/' . $category['id'] ?>" class="btn">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="gallery">
                <?php foreach ($paintings as $painting): ?>
                    <div class="painting-card">
                        <?php if ($painting['image_path']): ?>
                            <img src="<?= htmlspecialchars($painting['image_path']) ?>" alt="<?= htmlspecialchars($painting['title']) ?>">
                        <?php else: ?>
                            <div class="no-image">🎨 No Image Available</div>
                        <?php endif; ?>
                        
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($painting['title']) ?></h3>
                            <div class="card-meta">
                                <span><strong>Artist:</strong> <?= htmlspecialchars($painting['artist_name']) ?></span>
                                <?php if ($painting['dimensions']): ?>
                                    <span><strong>Size:</strong> <?= htmlspecialchars($painting['dimensions']) ?></span>
                                <?php endif; ?>
                                <?php if ($painting['medium']): ?>
                                    <span><strong>Medium:</strong> <?= htmlspecialchars($painting['medium']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="price">₹<?= number_format($painting['price']) ?></div>
                            <button class="btn" onclick="addToCart(<?= $painting['id'] ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include APP_ROOT . '/app/Views/partials/footer.php'; ?>

    <script src="/gallery/public/assets/js/main.js"></script>
    <script>
        function toggleFilter() {
            const content = document.getElementById('filterContent');
            content.classList.toggle('active');
        }
        
        // Auto-scroll to gallery after page load if filters are applied
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('min_price') || urlParams.has('max_price') || urlParams.has('sort')) {
                setTimeout(() => {
                    window.scrollTo({ 
                        top: document.querySelector('.gallery').offsetTop - 100, 
                        behavior: 'smooth' 
                    });
                }, 100);
            }
        });
    </script>
</body>
</html>