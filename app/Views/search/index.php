<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Maithili Bikash Kosh Gallery</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Search-specific responsive styles */
        .search-header { 
            display: flex; 
            flex-direction: column;
            gap: 1rem; 
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-box input { 
            width: 100%; 
            padding: 0.75rem 1rem; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 1rem;
            min-height: 44px;
        }
        
        .filters { 
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem; 
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filter-group label { 
            font-weight: 600; 
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .filter-group select, 
        .filter-group input { 
            width: 100%;
            padding: 0.75rem; 
            border: 1px solid #ddd; 
            border-radius: 6px;
            font-size: 1rem;
            min-height: 44px;
        }
        
        .results-info { 
            display: flex; 
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .sort-options { 
            display: flex; 
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .sort-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .sort-row label {
            min-width: 60px;
            font-weight: 500;
        }
        
        .sort-row select {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 40px;
        }
        
        /* Use existing gallery grid from main.css */
        .search-results {
            margin-bottom: 3rem;
        }
        
        /* Pagination styles */
        .pagination { 
            display: flex; 
            justify-content: center; 
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .pagination a, 
        .pagination span { 
            padding: 0.75rem 1rem; 
            border: 1px solid #ddd; 
            text-decoration: none; 
            color: #333; 
            border-radius: 6px;
            min-width: 44px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .pagination .current { 
            background: #8B0000; 
            color: white; 
            border-color: #8B0000;
        }
        
        .no-results { 
            text-align: center; 
            padding: 3rem 1rem;
            color: #666;
        }
        
        /* Tablet Styles */
        @media (min-width: 768px) {
            .search-header {
                flex-direction: row;
                align-items: center;
            }
            
            .search-box {
                flex: 1;
            }
            
            .filters {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
            
            .results-info {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            
            .sort-options {
                flex-direction: row;
                align-items: center;
                gap: 1rem;
            }
        }
        
        /* Desktop Styles */
        @media (min-width: 1024px) {
            .filters {
                grid-template-columns: repeat(4, 1fr);
                align-items: end;
            }
        }
    </style>
</head>
<body>
    <?php include APP_ROOT . '/app/Views/partials/navbar.php'; ?>
    
    <div class="container">
        <div class="section-title">
            <h2>Search Results</h2>
            <?php if ($query): ?>
                <p>Results for "<?= htmlspecialchars($query) ?>"</p>
            <?php else: ?>
                <p>Browse and filter our collection</p>
            <?php endif; ?>
        </div>
        <div class="search-header">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search paintings, artists..." value="<?= htmlspecialchars($query) ?>">
            </div>
            <button class="btn" onclick="performSearch()">Search</button>
        </div>
        
        <div class="filters">
            <div class="filter-group">
                <label>Category:</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php foreach ($searchFilters['categories'] as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $filters['category'] == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Artist:</label>
                <select id="artistFilter">
                    <option value="">All Artists</option>
                    <?php foreach ($searchFilters['artists'] as $artist): ?>
                        <option value="<?= $artist['id'] ?>" <?= $filters['artist'] == $artist['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($artist['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Min Price:</label>
                <input type="number" id="minPrice" placeholder="₹0" value="<?= htmlspecialchars($filters['min_price']) ?>">
            </div>
            
            <div class="filter-group">
                <label>Max Price:</label>
                <input type="number" id="maxPrice" placeholder="₹50000" value="<?= htmlspecialchars($filters['max_price']) ?>">
            </div>
            
            <div class="filter-group">
                <label>&nbsp;</label>
                <button class="btn" onclick="applyFilters()">Apply Filters</button>
            </div>
        </div>
        
        <div class="results-info">
            <div>
                <strong><?= $totalCount ?></strong> paintings found
                <?php if ($query): ?>
                    for "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php endif; ?>
            </div>
            
            <div class="sort-options">
                <div class="sort-row">
                    <label>Sort:</label>
                    <select id="sortBy" onchange="applySort()">
                        <option value="created_at" <?= $filters['sort'] == 'created_at' ? 'selected' : '' ?>>Newest</option>
                        <option value="price" <?= $filters['sort'] == 'price' ? 'selected' : '' ?>>Price</option>
                        <option value="title" <?= $filters['sort'] == 'title' ? 'selected' : '' ?>>Title</option>
                        <option value="artist_name" <?= $filters['sort'] == 'artist_name' ? 'selected' : '' ?>>Artist</option>
                    </select>
                </div>
                
                <div class="sort-row">
                    <label>Order:</label>
                    <select id="sortOrder" onchange="applySort()">
                        <option value="DESC" <?= $filters['order'] == 'DESC' ? 'selected' : '' ?>>↓</option>
                        <option value="ASC" <?= $filters['order'] == 'ASC' ? 'selected' : '' ?>>↑</option>
                    </select>
                </div>
            </div>
        </div>
        
        <?php if (empty($paintings)): ?>
            <div class="no-results">
                <h3>No paintings found</h3>
                <p>Try adjusting your search criteria or browse all paintings.</p>
                <a href="/gallery/" class="btn">Browse All Paintings</a>
            </div>
        <?php else: ?>
            <div class="gallery">
                <?php foreach ($paintings as $painting): ?>
                    <div class="painting-card">
                        <?php if ($painting['image_path']): ?>
                            <img src="<?= htmlspecialchars($painting['image_path']) ?>" alt="<?= htmlspecialchars($painting['title']) ?>">
                        <?php else: ?>
                            <div class="no-image">🎨 No Image</div>
                        <?php endif; ?>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($painting['title']) ?></h3>
                            <div class="card-meta">
                                <span><strong>Artist:</strong> <?= htmlspecialchars($painting['artist_name']) ?></span>
                                <span><strong>Category:</strong> <?= htmlspecialchars($painting['category_name']) ?></span>
                            </div>
                            <div class="price">₹<?= number_format($painting['price']) ?></div>
                            <button class="btn" onclick="addToCart(<?= $painting['id'] ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">← Previous</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <?php include APP_ROOT . '/app/Views/partials/footer.php'; ?>
    
    <script src="/gallery/public/assets/js/main.js"></script>
    
    <script>
        // Touch-friendly interactions
        let touchStartY = 0;
        let touchEndY = 0;
        
        function performSearch() {
            const query = document.getElementById('searchInput').value;
            const params = new URLSearchParams(window.location.search);
            params.set('q', query);
            params.delete('page');
            window.location.href = '/gallery/search?' + params.toString();
        }
        
        function applyFilters() {
            const params = new URLSearchParams(window.location.search);
            params.set('category', document.getElementById('categoryFilter').value);
            params.set('artist', document.getElementById('artistFilter').value);
            params.set('min_price', document.getElementById('minPrice').value);
            params.set('max_price', document.getElementById('maxPrice').value);
            params.delete('page');
            window.location.href = '/gallery/search?' + params.toString();
        }
        
        function applySort() {
            const params = new URLSearchParams(window.location.search);
            params.set('sort', document.getElementById('sortBy').value);
            params.set('order', document.getElementById('sortOrder').value);
            params.delete('page');
            window.location.href = '/gallery/search?' + params.toString();
        }
        
        // Enhanced mobile interactions
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
        
        // Auto-apply filters on mobile after short delay
        let filterTimeout;
        function debounceFilter() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(applyFilters, 1000);
        }
        
        // Add debounced filter changes for better mobile UX
        document.getElementById('minPrice').addEventListener('input', debounceFilter);
        document.getElementById('maxPrice').addEventListener('input', debounceFilter);
        
        // Prevent zoom on double tap for iOS
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
        
        // Smooth scroll to results after filter change
        function scrollToResults() {
            if (window.innerWidth <= 768) {
                document.querySelector('.results-info').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }
    </script>
</body>
</html>