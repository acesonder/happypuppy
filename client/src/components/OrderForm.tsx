import React, { useState } from 'react';
import { ordersAPI } from '../services/api';
import { Product } from '../types';
import '../styles/Order.css';

interface CartItem {
  product: Product;
  quantity: number;
}

interface OrderFormProps {
  cart: CartItem[];
  onOrderComplete: () => void;
  onClearCart: () => void;
}

const OrderForm: React.FC<OrderFormProps> = ({ cart, onOrderComplete, onClearCart }) => {
  const [deliveryType, setDeliveryType] = useState<'pickup' | 'delivery'>('pickup');
  const [scheduledDate, setScheduledDate] = useState('');
  const [scheduledTime, setScheduledTime] = useState('17:00');
  const [address, setAddress] = useState({
    street: '',
    city: '',
    zipCode: '',
  });
  const [notes, setNotes] = useState('');
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const getAvailableDates = () => {
    const dates = [];
    const today = new Date();
    for (let i = 0; i < 30; i++) {
      const date = new Date(today);
      date.setDate(today.getDate() + i);
      const dayOfWeek = date.getDay();
      // Only Wednesday (3) and Friday (5)
      if (dayOfWeek === 3 || dayOfWeek === 5) {
        dates.push(date.toISOString().split('T')[0]);
      }
    }
    return dates;
  };

  const timeSlots = [
    '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30'
  ];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setSuccess('');

    if (cart.length === 0) {
      setError('Your cart is empty');
      return;
    }

    try {
      const orderData = {
        items: cart.map((item) => ({
          product: item.product._id,
          quantity: item.quantity,
        })),
        deliveryType,
        scheduledDate,
        scheduledTime,
        address: deliveryType === 'delivery' ? address : undefined,
        notes,
      };

      await ordersAPI.create(orderData);
      setSuccess('Order placed successfully! You will receive a confirmation shortly.');
      onClearCart();
      setTimeout(() => {
        onOrderComplete();
      }, 2000);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Failed to place order');
    }
  };

  return (
    <div className="order-form-container">
      <h2>Complete Your Order</h2>
      
      {cart.length === 0 ? (
        <div className="empty-cart">Your cart is empty</div>
      ) : (
        <>
          <div className="cart-summary">
            <h3>Order Summary</h3>
            {cart.map((item, index) => (
              <div key={index} className="cart-item">
                <span>{item.product.name}</span>
                <span>x {item.quantity}</span>
              </div>
            ))}
          </div>

          {error && <div className="error-message">{error}</div>}
          {success && <div className="success-message">{success}</div>}

          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label>Delivery Type</label>
              <div className="radio-group">
                <label>
                  <input
                    type="radio"
                    value="pickup"
                    checked={deliveryType === 'pickup'}
                    onChange={(e) => setDeliveryType(e.target.value as 'pickup')}
                  />
                  Pickup
                </label>
                <label>
                  <input
                    type="radio"
                    value="delivery"
                    checked={deliveryType === 'delivery'}
                    onChange={(e) => setDeliveryType(e.target.value as 'delivery')}
                  />
                  Delivery
                </label>
              </div>
            </div>

            <div className="form-group">
              <label>Select Date (Wednesday or Friday only)</label>
              <select
                value={scheduledDate}
                onChange={(e) => setScheduledDate(e.target.value)}
                required
              >
                <option value="">Select a date</option>
                {getAvailableDates().map((date) => (
                  <option key={date} value={date}>
                    {new Date(date).toLocaleDateString('en-US', {
                      weekday: 'long',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    })}
                  </option>
                ))}
              </select>
            </div>

            <div className="form-group">
              <label>Select Time (5pm - 9pm)</label>
              <select
                value={scheduledTime}
                onChange={(e) => setScheduledTime(e.target.value)}
                required
              >
                {timeSlots.map((time) => (
                  <option key={time} value={time}>
                    {time}
                  </option>
                ))}
              </select>
            </div>

            {deliveryType === 'delivery' && (
              <>
                <div className="form-group">
                  <label>Street Address</label>
                  <input
                    type="text"
                    value={address.street}
                    onChange={(e) => setAddress({ ...address, street: e.target.value })}
                    required
                  />
                </div>
                <div className="form-row">
                  <div className="form-group">
                    <label>City</label>
                    <input
                      type="text"
                      value={address.city}
                      onChange={(e) => setAddress({ ...address, city: e.target.value })}
                      required
                    />
                  </div>
                  <div className="form-group">
                    <label>Zip Code</label>
                    <input
                      type="text"
                      value={address.zipCode}
                      onChange={(e) => setAddress({ ...address, zipCode: e.target.value })}
                      required
                    />
                  </div>
                </div>
              </>
            )}

            <div className="form-group">
              <label>Additional Notes</label>
              <textarea
                value={notes}
                onChange={(e) => setNotes(e.target.value)}
                rows={4}
                placeholder="Any special instructions or requests..."
              />
            </div>

            <button type="submit" className="btn-primary">Place Order</button>
          </form>
        </>
      )}
    </div>
  );
};

export default OrderForm;
