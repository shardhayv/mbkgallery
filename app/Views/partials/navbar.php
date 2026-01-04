<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = str_replace('/gallery', '', $currentPath);

// Get categories for navigation and footer
$categoryModel = new Category();
$categories = $categoryModel->getAllCategories();
?>
<nav class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="/gallery/">Maithili Bikash Kosh</a></div>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        <ul class="nav-links">
            <li><a href="/gallery/" <?= $currentPath === '/' ? 'class="active"' : '' ?>>Gallery</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" <?= strpos($currentPath, '/category') === 0 ? 'class="active"' : '' ?>>Categories <span class="dropdown-arrow">▼</span></a>
                <ul class="dropdown-menu">
                    <?php foreach ($categories as $category): ?>
                        <li><a href="/gallery/category/<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/gallery/artists" <?= strpos($currentPath, '/artists') === 0 ? 'class="active"' : '' ?>>Artists</a></li>
            <li><a href="/gallery/cart" <?= $currentPath === '/cart' ? 'class="active"' : '' ?>>Cart<span class="cart-badge" id="cart-count">0</span></a></li>
            <li><a href="/gallery/admin" <?= strpos($currentPath, '/admin') === 0 ? 'class="active"' : '' ?>>Admin</a></li>
        </ul>
    </div>
    <div class="mobile-nav" id="mobile-nav">
        <ul>
            <li><a href="/gallery/" <?= $currentPath === '/' ? 'class="active"' : '' ?>>Gallery</a></li>
            <li class="mobile-dropdown">
                <a href="#" class="mobile-dropdown-toggle">Categories <span class="mobile-dropdown-arrow">▼</span></a>
                <ul class="mobile-dropdown-menu">
                    <?php foreach ($categories as $category): ?>
                        <li><a href="/gallery/category/<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li><a href="/gallery/artists" <?= strpos($currentPath, '/artists') === 0 ? 'class="active"' : '' ?>>Artists</a></li>
            <li><a href="/gallery/cart" <?= $currentPath === '/cart' ? 'class="active"' : '' ?>>Cart<span class="cart-badge" id="cart-count-mobile">0</span></a></li>
            <li><a href="/gallery/admin" <?= strpos($currentPath, '/admin') === 0 ? 'class="active"' : '' ?>>Admin</a></li>
        </ul>
    </div>
</nav>