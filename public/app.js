// HappyPuppy Admin Application - Vanilla JavaScript
const API_BASE = 'http://localhost:3001/api';

// Application State
let state = {
    products: [],
    orders: [],
    cart: [],
    currentView: 'inventory',
    inventoryView: 'grid',
    orderView: 'widget',
    subcategories: [],
    editingProduct: null
};

// Initialize application
document.addEventListener('DOMContentLoaded', async () => {
    await fetchProducts();
    await fetchOrders();
    await fetchSubcategories();
    render();
    setupEventListeners();
});

// API Functions
async function fetchProducts() {
    try {
        const response = await fetch(`${API_BASE}/products`);
        state.products = await response.json();
    } catch (error) {
        console.error('Error fetching products:', error);
    }
}

async function fetchOrders() {
    try {
        const response = await fetch(`${API_BASE}/orders`);
        state.orders = await response.json();
    } catch (error) {
        console.error('Error fetching orders:', error);
    }
}

async function fetchSubcategories() {
    try {
        const response = await fetch(`${API_BASE}/subcategories`);
        state.subcategories = await response.json();
    } catch (error) {
        console.error('Error fetching subcategories:', error);
    }
}

async function saveProduct(product) {
    try {
        const method = product.id ? 'PUT' : 'POST';
        const url = product.id ? `${API_BASE}/products/${product.id}` : `${API_BASE}/products`;
        
        await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(product)
        });
        
        await fetchProducts();
        await fetchSubcategories();
    } catch (error) {
        console.error('Error saving product:', error);
    }
}

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    try {
        await fetch(`${API_BASE}/products/${id}`, { method: 'DELETE' });
        await fetchProducts();
    } catch (error) {
        console.error('Error deleting product:', error);
    }
}

async function uploadImage(productId, file) {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch(`${API_BASE}/products/${productId}/upload`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        return data.image_url;
    } catch (error) {
        console.error('Error uploading image:', error);
    }
}

async function createOrder() {
    const total = state.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    try {
        await fetch(`${API_BASE}/orders`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_name: 'Walk-in Customer',
                customer_email: '',
                total: total,
                items: state.cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity,
                    price: item.price
                }))
            })
        });
        
        state.cart = [];
        await fetchOrders();
        alert('Order placed successfully!');
        render();
    } catch (error) {
        console.error('Error creating order:', error);
    }
}

// Render Functions
function render() {
    const root = document.getElementById('root');
    root.innerHTML = `
        ${renderHeader()}
        <div class="content">
            ${state.currentView === 'inventory' ? renderInventoryView() : renderOrderMenuView()}
        </div>
        ${state.cart.length > 0 ? renderCart() : ''}
    `;
    
    attachEventHandlers();
}

function renderHeader() {
    return `
        <div class="header">
            <h1>🐶 HappyPuppy Admin</h1>
            <div class="nav">
                <button class="nav-button ${state.currentView === 'inventory' ? 'active' : ''}" 
                        onclick="switchView('inventory')">
                    📦 Inventory Control
                </button>
                <button class="nav-button ${state.currentView === 'orders' ? 'active' : ''}"
                        onclick="switchView('orders')">
                    🛒 Order Menu
                </button>
            </div>
        </div>
    `;
}

function renderInventoryView() {
    return `
        <div class="inventory-controls">
            <button class="button" onclick="openAddProductModal()">
                ➕ Add New Product
            </button>
            <div class="view-toggle">
                <button class="button ${state.inventoryView === 'grid' ? '' : 'button-secondary'}"
                        onclick="setInventoryView('grid')">
                    🎨 Grid View
                </button>
                <button class="button ${state.inventoryView === 'table' ? '' : 'button-secondary'}"
                        onclick="setInventoryView('table')">
                    📋 Table View
                </button>
            </div>
        </div>
        ${state.inventoryView === 'grid' ? renderProductsGrid() : renderProductsTable()}
    `;
}

function renderProductsGrid() {
    return `
        <div class="products-grid">
            ${state.products.map(product => renderProductCard(product)).join('')}
        </div>
    `;
}

function renderProductCard(product) {
    const imageHtml = product.image_url 
        ? `<img src="${product.image_url}" alt="${product.name}" class="product-image" />`
        : `<div class="product-image">📦 No Image</div>`;
    
    const inventoryBadgeClass = product.inventory > 10 ? 'badge-success' : 
                                 product.inventory > 0 ? 'badge-warning' : 'badge-danger';
    
    const statusBadgeClass = product.out_of_stock ? 'badge-danger' : 'badge-success';
    
    return `
        <div class="product-card" style="--widget-color: ${product.widget_color}">
            ${imageHtml}
            <div class="product-name">${product.name}</div>
            <div class="product-description">${product.description || ''}</div>
            <div class="product-details">
                <div class="product-detail">
                    <strong>Price:</strong>
                    <span>$${product.price.toFixed(2)}</span>
                </div>
                <div class="product-detail">
                    <strong>Inventory:</strong>
                    <span class="badge ${inventoryBadgeClass}">
                        ${product.inventory} units
                    </span>
                </div>
                <div class="product-detail">
                    <strong>Status:</strong>
                    <span class="badge ${statusBadgeClass}">
                        ${product.out_of_stock ? 'Out of Stock' : 'In Stock'}
                    </span>
                </div>
                <div class="product-detail">
                    <strong>Category:</strong>
                    <span>${product.sub_category || 'Uncategorized'}</span>
                </div>
            </div>
            <div class="product-actions">
                <button class="button" onclick="openEditProductModal(${product.id})">
                    ✏️ Edit
                </button>
                <button class="button button-danger" onclick="deleteProduct(${product.id})">
                    🗑️ Delete
                </button>
            </div>
        </div>
    `;
}

function renderProductsTable() {
    return `
        <table class="products-table">
            <thead>
                <tr>
                    <th>Color</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Inventory</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${state.products.map(product => `
                    <tr>
                        <td>
                            <div class="color-indicator" style="background-color: ${product.widget_color}"></div>
                        </td>
                        <td>
                            ${product.image_url 
                                ? `<img src="${product.image_url}" alt="${product.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" />`
                                : '📦'
                            }
                        </td>
                        <td>${product.name}</td>
                        <td>${product.sub_category || 'Uncategorized'}</td>
                        <td>$${product.price.toFixed(2)}</td>
                        <td>${product.inventory}</td>
                        <td>
                            <span class="badge ${product.out_of_stock ? 'badge-danger' : 'badge-success'}">
                                ${product.out_of_stock ? 'Out of Stock' : 'In Stock'}
                            </span>
                        </td>
                        <td>
                            <button class="button" style="margin-right: 10px; padding: 8px 16px;" 
                                    onclick="openEditProductModal(${product.id})">
                                ✏️ Edit
                            </button>
                            <button class="button button-danger" style="padding: 8px 16px;" 
                                    onclick="deleteProduct(${product.id})">
                                🗑️
                            </button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderOrderMenuView() {
    return `
        <div class="inventory-controls">
            <h2>Order Management</h2>
            <div class="view-toggle">
                <button class="button ${state.orderView === 'widget' ? '' : 'button-secondary'}"
                        onclick="setOrderView('widget')">
                    🎨 Widget View
                </button>
                <button class="button ${state.orderView === 'table' ? '' : 'button-secondary'}"
                        onclick="setOrderView('table')">
                    📋 Table View
                </button>
            </div>
        </div>
        ${state.orderView === 'widget' ? renderOrderWidgets() : renderOrdersTable()}
    `;
}

function renderOrderWidgets() {
    const availableProducts = state.products.filter(p => !p.out_of_stock && p.inventory > 0);
    
    return `
        <div class="orders-grid">
            ${availableProducts.map(product => `
                <div class="order-widget" onclick="addToCart(${product.id})">
                    <div class="order-widget-icon" style="background: ${product.widget_color}">
                        ${product.image_url ? '🖼️' : '📦'}
                    </div>
                    <div class="order-widget-name">${product.name}</div>
                    <div class="order-widget-price">$${product.price.toFixed(2)}</div>
                </div>
            `).join('')}
        </div>
    `;
}

function renderOrdersTable() {
    return `
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                ${state.orders.map(order => `
                    <tr>
                        <td>#${order.id}</td>
                        <td>${order.customer_name || 'N/A'}</td>
                        <td>${order.customer_email || 'N/A'}</td>
                        <td>$${order.total.toFixed(2)}</td>
                        <td>
                            <span class="badge ${order.status === 'pending' ? 'badge-warning' : 'badge-success'}">
                                ${order.status}
                            </span>
                        </td>
                        <td>${new Date(order.created_at).toLocaleDateString()}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
}

function renderCart() {
    const itemCount = state.cart.reduce((sum, item) => sum + item.quantity, 0);
    return `
        <div class="cart" onclick="createOrder()">
            <div class="cart-count">${itemCount}</div>
            <div class="cart-label">CHECKOUT</div>
        </div>
    `;
}

// Event Handlers
function switchView(view) {
    state.currentView = view;
    render();
}

function setInventoryView(view) {
    state.inventoryView = view;
    render();
}

function setOrderView(view) {
    state.orderView = view;
    render();
}

function addToCart(productId) {
    const product = state.products.find(p => p.id === productId);
    if (!product) return;
    
    const existingItem = state.cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        state.cart.push({ ...product, quantity: 1 });
    }
    render();
}

function openAddProductModal() {
    state.editingProduct = {
        name: '',
        description: '',
        price: 0,
        inventory: 0,
        out_of_stock: 0,
        widget_color: '#3b82f6',
        sub_category: '',
        image_url: null
    };
    showProductModal();
}

function openEditProductModal(productId) {
    const product = state.products.find(p => p.id === productId);
    if (product) {
        state.editingProduct = { ...product };
        showProductModal();
    }
}

function showProductModal() {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                ${state.editingProduct.id ? 'Edit Product' : 'Add New Product'}
            </div>
            <form id="product-form">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="${state.editingProduct.name}" required />
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description">${state.editingProduct.description || ''}</textarea>
                </div>

                <div class="form-group">
                    <label>Price *</label>
                    <input type="number" name="price" step="0.01" value="${state.editingProduct.price}" required />
                </div>

                <div class="form-group">
                    <label>Inventory Count *</label>
                    <input type="number" name="inventory" value="${state.editingProduct.inventory}" required />
                </div>

                <div class="form-group">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="out_of_stock" ${state.editingProduct.out_of_stock ? 'checked' : ''} />
                        <span>Mark as Out of Stock</span>
                    </label>
                </div>

                <div class="form-group">
                    <label>Widget Bubble Color</label>
                    <div class="color-picker-wrapper">
                        <input type="color" name="widget_color" value="${state.editingProduct.widget_color || '#3b82f6'}" />
                        <input type="text" name="widget_color_text" value="${state.editingProduct.widget_color || '#3b82f6'}" style="flex: 1" />
                    </div>
                </div>

                <div class="form-group">
                    <label>Sub-Category</label>
                    <select name="sub_category">
                        <option value="">Select a category</option>
                        ${state.subcategories.map(cat => `
                            <option value="${cat}" ${state.editingProduct.sub_category === cat ? 'selected' : ''}>${cat}</option>
                        `).join('')}
                    </select>
                    <input type="text" name="sub_category_custom" placeholder="Or enter new category" style="margin-top: 10px" />
                </div>

                <div class="form-group">
                    <label>Product Image</label>
                    <div class="file-upload">
                        <label for="image-upload" class="file-upload-label">
                            📁 Choose image file
                        </label>
                        <input id="image-upload" type="file" accept="image/*" />
                    </div>
                    ${state.editingProduct.image_url ? `
                        <img src="${state.editingProduct.image_url}" alt="Preview" class="image-preview" id="preview-image" />
                    ` : '<img id="preview-image" class="image-preview" style="display: none;" />'}
                </div>

                <div class="form-actions">
                    <button type="submit" class="button">
                        💾 Save Product
                    </button>
                    <button type="button" class="button button-secondary" onclick="closeModal()">
                        ❌ Cancel
                    </button>
                </div>
            </form>
        </div>
    `;
    
    modal.onclick = closeModal;
    document.body.appendChild(modal);
    
    // Setup form handlers
    const form = document.getElementById('product-form');
    const colorPicker = form.querySelector('input[name="widget_color"]');
    const colorText = form.querySelector('input[name="widget_color_text"]');
    const fileInput = document.getElementById('image-upload');
    const previewImage = document.getElementById('preview-image');
    
    // Sync color inputs
    colorPicker.addEventListener('input', (e) => {
        colorText.value = e.target.value;
    });
    
    colorText.addEventListener('input', (e) => {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            colorPicker.value = e.target.value;
        }
    });
    
    // Handle image preview
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Handle form submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const product = {
            ...state.editingProduct,
            name: formData.get('name'),
            description: formData.get('description'),
            price: parseFloat(formData.get('price')),
            inventory: parseInt(formData.get('inventory')),
            out_of_stock: formData.get('out_of_stock') ? 1 : 0,
            widget_color: formData.get('widget_color'),
            sub_category: formData.get('sub_category_custom') || formData.get('sub_category')
        };
        
        await saveProduct(product);
        
        // Handle image upload if product has an ID and file is selected
        if (state.editingProduct.id && fileInput.files[0]) {
            const imageUrl = await uploadImage(state.editingProduct.id, fileInput.files[0]);
            if (imageUrl) {
                product.image_url = imageUrl;
                await saveProduct(product);
            }
        }
        
        closeModal();
        render();
    });
}

function closeModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

function setupEventListeners() {
    // Setup any global event listeners here if needed
}

function attachEventHandlers() {
    // This function is called after render to attach event handlers to dynamically created elements
    // Most event handlers are inline for simplicity
}
