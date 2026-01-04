# Maithili Bikash Kosh Gallery - Codebase Index

## Overview
Enterprise-grade gallery management system for Mithila paintings with MVC architecture, REST APIs, and mobile app integration support.

## Core Architecture

### Entry Points
- **`index.php`** - Main application entry point, initializes and runs the app
- **`bootstrap.php`** - Application bootstrap with autoloader, routing, and core initialization
- **`setup.php`** - Database setup and initialization script

### Core Framework (`/core/`)

#### Database Layer
- **`DatabaseManager.php`** - Singleton database connection manager with PDO
  - Connection pooling and error handling
  - Query execution methods (query, fetchOne, fetchAll, execute)
  - Transaction support (begin, commit, rollback)

#### Security Layer
- **`SecurityManager.php`** - Security utilities and validation
- **`Validator.php`** - Input validation rules
- **`Sanitizer.php`** - Data sanitization

#### Error Handling
- **`ErrorHandler.php`** - Global error handler with logging
- **`Logger.php`** - Structured logging system

## Application Layer (`/app/`)

### Models (`/app/Models/`)
All models extend `BaseModel` with CRUD operations:

- **`BaseModel.php`** - Abstract base model with:
  - Database connection management
  - Generic CRUD operations (find, findAll, create, update, delete)
  - Fillable field filtering
  - Pagination support

- **`Painting.php`** - Painting management
  - `getAvailable()` - Fetch available paintings with artist/category info
  - `getByArtist($artistId)` - Get paintings by specific artist
  - `getByIds($ids)` - Bulk fetch by IDs for cart operations
  - `markAsSold($id)` - Update painting status

- **`Artist.php`** - Artist management
  - `getActiveWithPaintingCount()` - Artists with painting counts

- **`Order.php`** - Order processing
- **`Category.php`** - Painting categories

### Controllers (`/app/Controllers/`)

#### Base Controller
- **`BaseController.php`** - Abstract base controller with:
  - Database access
  - View rendering (`view()`)
  - JSON responses (`json()`)
  - Request validation (`validateRequest()`)
  - Input sanitization (`sanitizeInput()`)

#### Web Controllers
- **`HomeController.php`** - Main gallery page
  - `index()` - Display available paintings
- **`ArtistController.php`** - Artist showcase
- **`CartController.php`** - Shopping cart management
- **`AdminController.php`** - Admin panel operations

#### API Controllers (`/app/Controllers/API/`)
RESTful API endpoints with CORS support:

- **`PaintingController.php`** - Painting API
  - `index()` - List paintings with pagination and filtering
  - Rate limiting (200 requests/hour)
  - Status filtering (available, sold, reserved)

- **`ArtistController.php`** - Artist API
- **`OrderController.php`** - Order processing API
- **`CartController.php`** - Cart operations API
- **`CategoryController.php`** - Category management API

### Views (`/app/Views/`)
Template system organized by feature:
- **`home/`** - Gallery views
- **`admin/`** - Admin panel templates
- **`artists/`** - Artist showcase
- **`cart/`** - Shopping cart
- **`partials/`** - Reusable components
- **`errors/`** - Error pages (404, 500)

### Middleware (`/app/Middleware/`)
- **`AuthMiddleware.php`** - Authentication checks
- **`CorsMiddleware.php`** - CORS handling
- **`RateLimitMiddleware.php`** - Request throttling

## Database Schema (`database.sql`)

### Tables
- **`admin_users`** - Admin authentication
- **`artists`** - Artist profiles and information
- **`categories`** - Painting categories
- **`paintings`** - Painting details, pricing, and status
- **`orders`** - Customer orders
- **`order_items`** - Order line items

### Relationships
- Paintings → Artists (many-to-one)
- Paintings → Categories (many-to-one)
- Orders → Order Items (one-to-many)
- Order Items → Paintings (many-to-one)

## Public Assets (`/public/`)

### Static Assets (`/public/assets/`)
- **`css/main.css`** - Main stylesheet
- **`js/main.js`** - JavaScript functionality
- **`images/`** - Static images

### File Uploads (`/public/uploads/`)
- **`paintings/`** - Painting images
- **`artists/`** - Artist profile photos

## Routing System

### Web Routes
- `/` - Gallery homepage
- `/artists` - Artist showcase
- `/artists/{id}/paintings` - Artist's paintings
- `/cart` - Shopping cart
- `/admin` - Admin dashboard
- `/admin/login` - Admin authentication

### API Routes
- `GET /api/paintings` - List paintings
- `GET /api/artists` - List artists
- `GET /api/categories` - List categories
- `POST /api/cart/items` - Cart operations
- `POST /api/orders` - Create orders
- `GET /api/artists/{id}/paintings` - Artist's paintings

## Key Features

### Security
- SQL injection prevention with prepared statements
- Input validation and sanitization
- Rate limiting for API endpoints
- CORS support for mobile apps
- Error logging and monitoring

### Performance
- Database connection pooling
- Query optimization with proper indexing
- Pagination for large datasets
- Caching layer ready

### Mobile App Support
- RESTful API design
- JSON responses
- CORS enabled
- Rate limiting
- Standardized error handling

### Admin Features
- Artist management
- Painting inventory
- Order tracking
- Sales reporting
- Dashboard analytics

### Customer Features
- Gallery browsing
- Artist profiles
- Shopping cart
- Order placement
- Responsive design

## Development Guidelines

### Code Standards
- PSR-4 autoloading
- MVC architecture
- Repository pattern ready
- Dependency injection support
- Comprehensive error handling

### Testing Structure (`/tests/`)
- Unit tests for models and services
- Integration tests for API endpoints
- Test database configuration

### Deployment
- Apache configuration (`.htaccess`)
- Environment-based configuration
- Logging and monitoring
- Asset optimization ready

## Configuration

### Environment Variables
- `DB_HOST` - Database host
- `DB_NAME` - Database name
- `DB_USER` - Database username
- `DB_PASS` - Database password
- `APP_ENV` - Application environment
- `APP_DEBUG` - Debug mode

### File Structure Summary
```
gallery/
├── Core framework and utilities
├── MVC application structure
├── RESTful API endpoints
├── Database schema and migrations
├── Public assets and uploads
├── Admin panel and management
├── Mobile app integration
└── Testing and deployment
```

This codebase provides a solid foundation for:
- Scalable web application development
- Mobile app backend services
- Enterprise-grade security
- Performance optimization
- Team collaboration
- Maintenance and updates