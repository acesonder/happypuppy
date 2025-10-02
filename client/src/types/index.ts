export interface User {
  id: string;
  email: string;
  name: string;
  phone: string;
  role: 'user' | 'admin';
  address?: {
    street: string;
    city: string;
    zipCode: string;
  };
}

export interface Product {
  _id: string;
  name: string;
  description: string;
  category: string;
  quantity: number;
  unit: string;
  safetyInfo: string;
  isAvailable: boolean;
  image?: string;
}

export interface OrderItem {
  product: Product;
  quantity: number;
}

export interface Order {
  _id: string;
  user: User;
  items: OrderItem[];
  deliveryType: 'pickup' | 'delivery';
  scheduledDate: string;
  scheduledTime: string;
  status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'completed' | 'cancelled';
  address?: {
    street: string;
    city: string;
    zipCode: string;
  };
  notes: string;
  createdAt: string;
  updatedAt: string;
}

export interface Message {
  _id: string;
  from: User;
  to: User;
  content: string;
  read: boolean;
  orderId?: string;
  createdAt: string;
}

export interface Notification {
  _id: string;
  user: string;
  title: string;
  message: string;
  type: 'order' | 'message' | 'system' | 'checkin';
  read: boolean;
  relatedId?: string;
  createdAt: string;
}

export interface CheckInRequest {
  checkInId: string;
  message: string;
}
