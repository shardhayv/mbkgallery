<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Maithili Bikash Kosh</title>
    <link rel="stylesheet" href="/gallery/public/assets/css/main.css">
    <style>
        .cart-item { background: white; border-radius: 12px; margin: 1rem 0; padding: 1rem; display: flex; flex-direction: column; gap: 1rem; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        @media (min-width: 768px) { .cart-item { flex-direction: row; padding: 1.5rem; } }
        .cart-item img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; }
        @media (min-width: 768px) { .cart-item img { width: 150px; height: 100px; } }
        .item-details { flex: 1; }
        .item-details h3 { font-size: clamp(1rem, 3vw, 1.2rem); margin-bottom: 0.5rem; line-height: 1.3; }
        .item-details p { font-size: clamp(0.9rem, 2vw, 1rem); margin-bottom: 0.3rem; line-height: 1.4; }
        .item-details .price { font-size: clamp(1.1rem, 3vw, 1.3rem); font-weight: bold; color: #e74c3c; margin: 0.5rem 0; }
        .total { font-size: clamp(1.2rem, 4vw, 1.5rem); font-weight: bold; text-align: center; margin: 2rem 0; color: #2c3e50; }
        @media (min-width: 768px) { .total { text-align: right; } }
        .form-group { margin: 1rem 0; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #2c3e50; font-size: clamp(0.9rem, 2vw, 1rem); }
        .form-group input, .form-group textarea { width: 100%; padding: 0.7rem; border: 1px solid #ddd; border-radius: 8px; font-size: clamp(0.9rem, 2vw, 1rem); }
        .checkout-form { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-top: 2rem; }
        @media (min-width: 768px) { .checkout-form { padding: 2rem; } }
        .checkout-form h3 { font-size: clamp(1.2rem, 3vw, 1.5rem); margin-bottom: 1.5rem; color: #2c3e50; }
    </style>
</head>
<body>
    <?php include APP_ROOT . '/app/Views/partials/navbar.php'; ?>

    <div class="hero" style="padding: 4rem 2rem;">
        <div class="hero-content">
            <h1>Shopping Cart</h1>
            <p class="subtitle">Review your selected paintings and complete your order</p>
        </div>
    </div>

    <div class="container">
        <div id="cart-items"></div>
        <div class="total" id="total">Total: ₹0.00</div>
        
        <div id="checkout-form" class="checkout-form" style="display:none;">
            <h3>Customer Details</h3>
            <form id="order-form">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="customer_name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="customer_email" required>
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="tel" name="customer_phone" required>
                </div>
                <div class="form-group">
                    <label>Address:</label>
                    <textarea name="customer_address" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn">Place Order</button>
            </form>
        </div>
    </div>

    <script src="/gallery/public/assets/js/main.js"></script>
    <script>
        let cartItems = [];
        let total = 0;

        async function loadCartItems() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (cart.length === 0) {
                document.getElementById('cart-items').innerHTML = '<p style="text-align: center; padding: 2rem; color: #666;">Your cart is empty.</p>';
                return;
            }

            try {
                cartItems = await window.galleryApp.loadCartItems();
                displayCartItems();
            } catch (error) {
                document.getElementById('cart-items').innerHTML = '<p style="text-align: center; padding: 2rem; color: #e74c3c;">Error loading cart items.</p>';
            }
        }

        function displayCartItems() {
            const container = document.getElementById('cart-items');
            container.innerHTML = '';
            total = 0;

            cartItems.forEach(item => {
                total += parseFloat(item.price);
                container.innerHTML += `
                    <div class="cart-item">
                        <img src="/gallery/${item.image_path || 'public/assets/images/placeholder.jpg'}" alt="${item.title}">
                        <div class="item-details">
                            <h3>${item.title}</h3>
                            <p><strong>Artist:</strong> ${item.artist_name}</p>
                            <p class="price">₹${parseFloat(item.price).toFixed(2)}</p>
                        </div>
                        <button class="btn btn-danger" onclick="removeFromCart(${item.id})">Remove</button>
                    </div>
                `;
            });

            document.getElementById('total').textContent = `Total: ₹${total.toFixed(2)}`;
            document.getElementById('checkout-form').style.display = cartItems.length > 0 ? 'block' : 'none';
        }

        function removeFromCart(paintingId) {
            window.removeFromCart(paintingId);
            loadCartItems();
        }

        document.getElementById('order-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const orderData = {
                customer_name: formData.get('customer_name'),
                customer_email: formData.get('customer_email'),
                customer_phone: formData.get('customer_phone'),
                customer_address: formData.get('customer_address'),
                total: total
            };

            try {
                await window.galleryApp.placeOrder(orderData);
                window.galleryApp.showNotification('Order placed successfully!', 'success');
                setTimeout(() => window.location.href = '/gallery/', 2000);
            } catch (error) {
                window.galleryApp.showNotification('Error placing order. Please try again.', 'error');
            }
        });

        // Load cart items when page loads
        document.addEventListener('DOMContentLoaded', loadCartItems);
    </script>
</body>
</html>