import React, { useState, useEffect } from 'react';
import { productsAPI } from '../services/api';
import { Product } from '../types';
import '../styles/Products.css';

interface ProductListProps {
  onAddToCart: (product: Product, quantity: number) => void;
  viewMode: 'grid' | 'list';
}

const ProductList: React.FC<ProductListProps> = ({ onAddToCart, viewMode }) => {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [quantities, setQuantities] = useState<{ [key: string]: number }>({});

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      const response = await productsAPI.getAll();
      setProducts(response.data);
    } catch (error) {
      console.error('Failed to fetch products:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleQuantityChange = (productId: string, value: number) => {
    setQuantities({ ...quantities, [productId]: value });
  };

  const handleAddToCart = (product: Product) => {
    const quantity = quantities[product._id] || 1;
    onAddToCart(product, quantity);
    setQuantities({ ...quantities, [product._id]: 1 });
  };

  if (loading) {
    return <div className="loading">Loading products...</div>;
  }

  return (
    <div className={`products-container ${viewMode}`}>
      {products.map((product) => (
        <div key={product._id} className="product-card">
          {product.image && (
            <div className="product-image">
              <img src={product.image} alt={product.name} />
            </div>
          )}
          <div className="product-info">
            <h3>{product.name}</h3>
            <p className="product-category">{product.category}</p>
            <p className="product-description">{product.description}</p>
            <div className="product-safety">
              <strong>Safety Info:</strong>
              <p>{product.safetyInfo}</p>
            </div>
            <div className="product-stock">
              Available: {product.quantity} {product.unit}
            </div>
            <div className="product-actions">
              <input
                type="number"
                min="1"
                max={product.quantity}
                value={quantities[product._id] || 1}
                onChange={(e) => handleQuantityChange(product._id, parseInt(e.target.value))}
                className="quantity-input"
              />
              <button
                onClick={() => handleAddToCart(product)}
                disabled={product.quantity === 0}
                className="btn-add-cart"
              >
                Add to Order
              </button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
};

export default ProductList;
