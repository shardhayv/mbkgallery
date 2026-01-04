<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Artists - Maithili Bikash Kosh</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/main.css">
</head>
<body>
    <?php include APP_ROOT . '/app/Views/partials/navbar.php'; ?>

    <div class="hero" style="padding: 4rem 2rem;">
        <div class="hero-content">
            <h1>Meet Our Master Artists</h1>
            <p class="subtitle">Discover the talented artisans who preserve and create the timeless beauty of Mithila art. Each artist brings generations of tradition and their unique creative vision to every masterpiece.</p>
        </div>
    </div>

    <div class="container">
        <div class="section-title">
            <h2>Featured Artists</h2>
            <p>Explore the profiles and works of our skilled Mithila artists</p>
        </div>
        
        <div class="gallery">
            <?php foreach ($artists as $artist): ?>
                <div class="painting-card">
                    <?php if ($artist['profile_image']): ?>
                        <img src="/gallery/<?= htmlspecialchars($artist['profile_image']) ?>" alt="<?= htmlspecialchars($artist['name']) ?>">
                    <?php else: ?>
                        <div class="no-image" style="background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); color: white; font-size: 3rem; display: flex; align-items: center; justify-content: center;">
                            <?= strtoupper(substr($artist['name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($artist['name']) ?></h3>
                        <?php if ($artist['bio']): ?>
                            <div class="card-meta">
                                <span><?= htmlspecialchars(substr($artist['bio'], 0, 120)) ?>...</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-meta">
                            <?php if ($artist['email']): ?>
                                <span><strong>Email:</strong> <?= htmlspecialchars($artist['email']) ?></span>
                            <?php endif; ?>
                            <?php if ($artist['phone']): ?>
                                <span><strong>Phone:</strong> <?= htmlspecialchars($artist['phone']) ?></span>
                            <?php endif; ?>
                            <span><strong>Available Paintings:</strong> <?= $artist['painting_count'] ?></span>
                        </div>
                        <a href="/gallery/artists/<?= $artist['id'] ?>/paintings" class="btn">View Paintings</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="/gallery/public/assets/js/main.js"></script>
</body>
</html>