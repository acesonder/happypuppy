# HappyPuppy Admin System 🐶

A complete inventory control and order management system for HappyPuppy store.

## Features

### Inventory Control Page
- ✅ Edit all product fields (name, description, price, inventory, etc.)
- ✅ Upload images for each product
- ✅ Update inventory numbers in real-time
- ✅ Mark products as out of stock
- ✅ Change widget bubble color for visual categorization
- ✅ Assign products to sub-categories
- ✅ Two view modes: Grid View and Table View

### Order Menu
- ✅ Table view for viewing all orders
- ✅ Icon/widget view for easy click and add checkout
- ✅ Real-time cart management
- ✅ Quick checkout functionality

## Installation

1. Install dependencies:
```bash
npm install
```

2. Start the server:
```bash
npm start
```

3. Open your browser and navigate to:
```
http://localhost:3001
```

## Usage

### Inventory Control
- Click "📦 Inventory Control" to manage products
- Use "➕ Add New Product" to create new products
- Switch between Grid and Table views using the view toggle buttons
- Click "✏️ Edit" on any product to modify its details
- Upload product images using the file upload feature
- Change widget colors using the color picker
- Assign products to categories using the dropdown

### Order Menu
- Click "🛒 Order Menu" to access the ordering interface
- Switch between Widget View (for quick ordering) and Table View (to see order history)
- In Widget View, click on any product to add it to cart
- Click the floating cart button to checkout
- View order history in Table View

## Technology Stack

- **Backend**: Node.js, Express.js
- **Database**: SQLite3
- **Frontend**: React (via CDN)
- **File Upload**: Multer
- **Styling**: Custom CSS with gradients and animations

## API Endpoints

- `GET /api/products` - Get all products
- `GET /api/products/:id` - Get single product
- `POST /api/products` - Create new product
- `PUT /api/products/:id` - Update product
- `DELETE /api/products/:id` - Delete product
- `POST /api/products/:id/upload` - Upload product image
- `GET /api/orders` - Get all orders
- `POST /api/orders` - Create new order
- `GET /api/subcategories` - Get all sub-categories

## Database Schema

### Products Table
- id, name, description, price, inventory, out_of_stock, image_url, widget_color, sub_category, created_at, updated_at

### Orders Table
- id, customer_name, customer_email, status, total, created_at, updated_at

### Order Items Table
- id, order_id, product_id, quantity, price