import React, { useState, useEffect } from 'react';
import { ordersAPI } from '../services/api';
import { Order } from '../types';
import MessagingModal from './MessagingModal';
import '../styles/Orders.css';

const MyOrders: React.FC = () => {
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedRecipient, setSelectedRecipient] = useState<{
    id: string;
    name: string;
  } | null>(null);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      const response = await ordersAPI.getMyOrders();
      setOrders(response.data);
    } catch (error) {
      console.error('Failed to fetch orders:', error);
    } finally {
      setLoading(false);
    }
  };

  const getStatusColor = (status: string) => {
    const colors: { [key: string]: string } = {
      pending: '#FFA500',
      confirmed: '#4CAF50',
      preparing: '#2196F3',
      ready: '#9C27B0',
      completed: '#4CAF50',
      cancelled: '#F44336',
    };
    return colors[status] || '#757575';
  };

  if (loading) {
    return <div className="loading">Loading orders...</div>;
  }

  return (
    <div className="orders-container">
      <h2>My Orders</h2>
      {orders.length === 0 ? (
        <div className="no-orders">You haven't placed any orders yet.</div>
      ) : (
        <div className="orders-list">
          {orders.map((order) => (
            <div key={order._id} className="order-card">
              <div className="order-header">
                <div>
                  <h3>Order #{order._id.substring(0, 8)}</h3>
                  <span
                    className="order-status"
                    style={{ backgroundColor: getStatusColor(order.status) }}
                  >
                    {order.status}
                  </span>
                </div>
                <div className="order-date">
                  {new Date(order.createdAt).toLocaleDateString()}
                </div>
              </div>

              <div className="order-items">
                <h4>Items:</h4>
                {order.items.map((item, index) => (
                  <div key={index} className="order-item">
                    <span>{item.product.name}</span>
                    <span>x{item.quantity}</span>
                  </div>
                ))}
              </div>

              <div className="order-details">
                <div className="detail-row">
                  <strong>Delivery Type:</strong>
                  <span>{order.deliveryType}</span>
                </div>
                <div className="detail-row">
                  <strong>Scheduled:</strong>
                  <span>
                    {new Date(order.scheduledDate).toLocaleDateString()} at{' '}
                    {order.scheduledTime}
                  </span>
                </div>
                {order.address && (
                  <div className="detail-row">
                    <strong>Address:</strong>
                    <span>
                      {order.address.street}, {order.address.city},{' '}
                      {order.address.zipCode}
                    </span>
                  </div>
                )}
                {order.notes && (
                  <div className="detail-row">
                    <strong>Notes:</strong>
                    <span>{order.notes}</span>
                  </div>
                )}
              </div>

              <div className="order-actions">
                <button
                  onClick={() =>
                    setSelectedRecipient({ id: 'admin', name: 'Support Team' })
                  }
                  className="btn-message"
                >
                  💬 Message Support
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {selectedRecipient && (
        <MessagingModal
          recipientId={selectedRecipient.id}
          recipientName={selectedRecipient.name}
          onClose={() => setSelectedRecipient(null)}
        />
      )}
    </div>
  );
};

export default MyOrders;
