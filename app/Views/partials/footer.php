<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Maithili Bikash Kosh</h3>
                <p>Preserving and promoting the beautiful art of Mithila paintings through authentic works by master artists from Bihar.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/gallery/">Gallery</a></li>
                    <li><a href="/gallery/artists">Artists</a></li>
                    <li><a href="/gallery/search">Search</a></li>
                    <li><a href="/gallery/cart">Cart</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Categories</h4>
                <ul>
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                            <li><a href="/gallery/category/<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="/gallery/search?category=1">Traditional Mithila</a></li>
                        <li><a href="/gallery/search?category=2">Modern Mithila</a></li>
                        <li><a href="/gallery/search?category=3">Religious</a></li>
                        <li><a href="/gallery/search?category=4">Nature</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact Info</h4>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> info@maithilibikashkosh.com</p>
                    <p><i class="fas fa-phone"></i> +91 9876543210</p>
                    <p><i class="fas fa-map-marker-alt"></i> Bihar, India</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="developer-info">
                    <p>Developed by <strong>Shardhay Vatshyayan</strong> | <strong>श्रद्धेय वात्स्यायन</strong></p>
                    <p>
                        <a href="mailto:shardhayvatshyayan7@gmail.com"><i class="fas fa-envelope"></i> shardhayvatshyayan7@gmail.com</a> | 
                        <a href="tel:+9779844361480"><i class="fas fa-phone"></i> +977 9844361480</a>
                    </p>
                </div>
                <div class="copyright">
                    <p>&copy; <?= date('Y') ?> Maithili Bikash Kosh. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>