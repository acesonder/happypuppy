import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import ProductList from './ProductList';
import OrderForm from './OrderForm';
import MyOrders from './MyOrders';
import CheckInBot from './CheckInBot';
import NotificationBell from './NotificationBell';
import { Product } from '../types';
import '../styles/Dashboard.css';

interface CartItem {
  product: Product;
  quantity: number;
}

const Dashboard: React.FC = () => {
  const { user, logout } = useAuth();
  const [activeTab, setActiveTab] = useState<'browse' | 'cart' | 'orders'>('browse');
  const [cart, setCart] = useState<CartItem[]>([]);
  const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');

  const handleAddToCart = (product: Product, quantity: number) => {
    const existingItem = cart.find((item) => item.product._id === product._id);
    if (existingItem) {
      setCart(
        cart.map((item) =>
          item.product._id === product._id
            ? { ...item, quantity: item.quantity + quantity }
            : item
        )
      );
    } else {
      setCart([...cart, { product, quantity }]);
    }
    setActiveTab('cart');
  };

  const handleClearCart = () => {
    setCart([]);
  };

  const handleOrderComplete = () => {
    setActiveTab('orders');
  };

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <div className="header-left">
          <h1>🐕 HappyPuppy</h1>
          <p>Harm Reduction Services</p>
        </div>
        <div className="header-right">
          <CheckInBot />
          <NotificationBell />
          <div className="user-info">
            <span>Welcome, {user?.name}</span>
            <button onClick={logout} className="btn-logout">
              Logout
            </button>
          </div>
        </div>
      </header>

      <nav className="dashboard-nav">
        <button
          className={activeTab === 'browse' ? 'active' : ''}
          onClick={() => setActiveTab('browse')}
        >
          Browse Products
        </button>
        <button
          className={activeTab === 'cart' ? 'active' : ''}
          onClick={() => setActiveTab('cart')}
        >
          Cart {cart.length > 0 && `(${cart.length})`}
        </button>
        <button
          className={activeTab === 'orders' ? 'active' : ''}
          onClick={() => setActiveTab('orders')}
        >
          My Orders
        </button>
      </nav>

      <main className="dashboard-content">
        {activeTab === 'browse' && (
          <div>
            <div className="view-controls">
              <h2>Browse Products</h2>
              <div className="view-mode-toggle">
                <button
                  className={viewMode === 'grid' ? 'active' : ''}
                  onClick={() => setViewMode('grid')}
                >
                  Grid View
                </button>
                <button
                  className={viewMode === 'list' ? 'active' : ''}
                  onClick={() => setViewMode('list')}
                >
                  List View
                </button>
              </div>
            </div>
            <ProductList onAddToCart={handleAddToCart} viewMode={viewMode} />
          </div>
        )}

        {activeTab === 'cart' && (
          <OrderForm
            cart={cart}
            onOrderComplete={handleOrderComplete}
            onClearCart={handleClearCart}
          />
        )}

        {activeTab === 'orders' && <MyOrders />}
      </main>

      <footer className="dashboard-footer">
        <p>
          © 2024 HappyPuppy - Harm Reduction Services | 
          Available Wed & Fri, 5pm-9pm
        </p>
      </footer>
    </div>
  );
};

export default Dashboard;
