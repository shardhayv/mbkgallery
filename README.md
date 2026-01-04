# Maithili Bikash Kosh Gallery Management System

A complete web-based gallery management system for Maithili Bikash Kosh to manage Mithila paintings, artists, and sales.

## Features

### Customer Features
- Browse gallery of Mithila paintings
- View artist profiles and their work
- Shopping cart functionality
- Place orders with customer details
- Responsive design for mobile and desktop

### Admin Features
- Dashboard with statistics
- Manage artists (add, edit, delete)
- Manage paintings with image upload
- Track and update order status
- View sales reports

## Setup Instructions

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Install the System**
   - Copy the `gallery` folder to `/opt/lampp/htdocs/`
   - Open browser and go to `http://localhost/gallery/setup.php`
   - Click "Setup Database" to initialize

3. **Access the System**
   - Gallery: `http://localhost/gallery/`
   - Admin Panel: `http://localhost/gallery/admin/`

## File Structure

```
gallery/
├── config/
│   └── database.php          # Database configuration
├── admin/                    # Admin panel
│   ├── index.php            # Admin dashboard
│   ├── artists.php          # Manage artists
│   ├── paintings.php        # Manage paintings
│   └── orders.php           # Manage orders
├── uploads/                 # File uploads
│   ├── paintings/           # Painting images
│   └── artists/             # Artist profile images
├── index.php                # Main gallery page
├── artists.php              # Artists showcase
├── cart.php                 # Shopping cart
├── get_cart_items.php       # Cart API
├── place_order.php          # Order processing
├── database.sql             # Database schema
├── setup.php                # Setup script
└── README.md                # This file
```

## Database Tables

- **artists**: Artist information and profiles
- **categories**: Painting categories (Traditional, Modern, etc.)
- **paintings**: Painting details, prices, and status
- **orders**: Customer orders
- **order_items**: Individual items in orders

## Usage

### For Customers
1. Browse paintings on the main gallery page
2. Add paintings to cart
3. View cart and place order with contact details
4. Orders are automatically processed

### For Administrators
1. Access admin panel at `/admin/`
2. Add new artists with their information
3. Upload paintings with images and details
4. Monitor orders and update status
5. View sales statistics on dashboard

## Technical Details

- **Backend**: PHP with PDO for database operations
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **File Upload**: Supports image uploads for paintings
- **Security**: Prepared statements to prevent SQL injection

## Customization

- Modify CSS styles in each PHP file's `<style>` section
- Add new painting categories in the database
- Customize order status workflow
- Add payment gateway integration
- Implement user authentication for admin panel

## Support

For technical support or customization requests, contact the development team.

---

**Maithili Bikash Kosh Gallery Management System**  
Preserving and promoting the beautiful art of Mithila paintings.