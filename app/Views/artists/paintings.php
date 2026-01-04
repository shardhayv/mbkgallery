<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($artist['name']) ?> - Paintings</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/main.css">
</head>
<body>
    <?php include APP_ROOT . '/app/Views/partials/navbar.php'; ?>

    <div class="hero" style="padding: 4rem 2rem;">
        <div class="hero-content">
            <h1><?= htmlspecialchars($artist['name']) ?></h1>
            <p class="subtitle">Explore the beautiful collection of Mithila paintings by this talented artist</p>
        </div>
    </div>

    <div class="container">
        <div class="section-title">
            <h2>Paintings by <?= htmlspecialchars($artist['name']) ?></h2>
            <?php if ($artist['bio']): ?>
                <p><?= htmlspecialchars($artist['bio']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="gallery">
            <?php if (empty($paintings)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #666;">
                    <h3>No paintings available</h3>
                    <p>This artist doesn't have any paintings available for sale at the moment.</p>
                    <a href="/gallery/artists" class="btn" style="margin-top: 1rem;">← Back to Artists</a>
                </div>
            <?php else: ?>
                <?php foreach ($paintings as $painting): ?>
                    <div class="painting-card">
                        <?php if ($painting['image_path']): ?>
                            <img src="/gallery/<?= htmlspecialchars($painting['image_path']) ?>" alt="<?= htmlspecialchars($painting['title']) ?>">
                        <?php else: ?>
                            <div class="no-image">No Image Available</div>
                        <?php endif; ?>
                        
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($painting['title']) ?></h3>
                            <div class="card-meta">
                                <span><strong>Category:</strong> <?= htmlspecialchars($painting['category_name']) ?></span>
                                <?php if ($painting['dimensions']): ?>
                                    <span><strong>Size:</strong> <?= htmlspecialchars($painting['dimensions']) ?></span>
                                <?php endif; ?>
                                <?php if ($painting['medium']): ?>
                                    <span><strong>Medium:</strong> <?= htmlspecialchars($painting['medium']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="price">₹<?= number_format($painting['price'], 2) ?></div>
                            <button class="btn" onclick="addToCart(<?= $painting['id'] ?>)">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="/gallery/artists" class="btn">← Back to Artists</a>
        </div>
    </div>

    <script src="/gallery/public/assets/js/main.js"></script>
</body>
</html>