const sqlite3 = require('sqlite3').verbose();
const path = require('path');

const dbPath = path.join(__dirname, '..', 'happypuppy.db');
const db = new sqlite3.Database(dbPath);

// Initialize database schema
db.serialize(() => {
  // Products table
  db.run(`CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    inventory INTEGER NOT NULL DEFAULT 0,
    out_of_stock INTEGER DEFAULT 0,
    image_url TEXT,
    widget_color TEXT DEFAULT '#3b82f6',
    sub_category TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )`);

  // Orders table
  db.run(`CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_name TEXT,
    customer_email TEXT,
    status TEXT DEFAULT 'pending',
    total REAL NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )`);

  // Order items table
  db.run(`CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price REAL NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
  )`);

  // Insert sample data if products table is empty
  db.get("SELECT COUNT(*) as count FROM products", (err, row) => {
    if (!err && row.count === 0) {
      const sampleProducts = [
        ['Dog Toy Ball', 'Durable rubber ball for fetch', 12.99, 50, 0, null, '#ef4444', 'Toys'],
        ['Premium Dog Food', 'High-quality nutrition for all breeds', 45.99, 30, 0, null, '#10b981', 'Food'],
        ['Comfortable Dog Bed', 'Soft and cozy bed for your puppy', 79.99, 15, 0, null, '#8b5cf6', 'Beds'],
        ['Dog Leash', 'Strong and reliable leash', 19.99, 40, 0, null, '#f59e0b', 'Accessories'],
        ['Dog Treats', 'Healthy and delicious treats', 8.99, 100, 0, null, '#06b6d4', 'Food']
      ];

      const stmt = db.prepare(`INSERT INTO products (name, description, price, inventory, out_of_stock, image_url, widget_color, sub_category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`);
      sampleProducts.forEach(product => {
        stmt.run(product);
      });
      stmt.finalize();
    }
  });
});

module.exports = db;
