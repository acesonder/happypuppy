# HappyPuppy - Harm Reduction Ordering System

A comprehensive harm reduction ordering system with real-time messaging, AI safety check-ins, and inventory management.

## Features

- **User Authentication**: Secure login and registration system
- **Product Ordering**: Browse products with two layout views (grid/list)
- **Inventory Management**: Real-time inventory tracking and control
- **Scheduled Delivery**: Pickup/delivery only on Wednesdays and Fridays, 5pm-9pm
- **Order Confirmation**: Complete order tracking and status updates
- **Instant Messaging**: Real-time communication with support team via Socket.IO
- **In-App Notifications**: Bell notification system with unread counts
- **Message Bubble Modals**: Pop-up messaging interface
- **AI Safety Bot**: Automated check-ins every 30 seconds for harm reduction
- **Direct Communication**: Message support team about orders

## Technology Stack

### Backend
- Node.js + Express
- MongoDB with Mongoose
- Socket.IO for real-time messaging
- JWT authentication
- bcryptjs for password hashing

### Frontend
- React with TypeScript
- Socket.IO Client
- Axios for API calls
- CSS Modules for styling

## Setup Instructions

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (local or cloud instance)

### Installation

1. Clone the repository:
```bash
git clone https://github.com/acesonder/happypuppy.git
cd happypuppy
```

2. Install backend dependencies:
```bash
npm install
```

3. Install frontend dependencies:
```bash
cd client
npm install
cd ..
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Configure your `.env` file:
```
PORT=5000
MONGODB_URI=mongodb://localhost:27017/happypuppy
JWT_SECRET=your-secret-key-here
NODE_ENV=development
```

### Running the Application

1. Start MongoDB (if running locally):
```bash
mongod
```

2. Start the backend server:
```bash
npm run server
```

3. In a new terminal, start the frontend:
```bash
npm run client
```

4. Access the application at `http://localhost:3000`

## Features Overview

### Authentication System
- User registration with email, password, name, phone, and address
- Secure JWT-based authentication
- Role-based access (user/admin)

### Ordering System
- **Grid View**: Card-based product display with images
- **List View**: Compact list view for easier browsing
- Add products to cart with quantity selection
- Schedule pickup or delivery
- **Restricted scheduling**: Only Wednesday and Friday, 5pm-9pm
- Order confirmation and tracking

### Inventory Management
- Real-time inventory updates
- Product categories and safety information
- Stock availability tracking
- Admin controls for product management

### Messaging System
- Real-time instant messaging using Socket.IO
- Message bubble modal interface
- Unread message indicators
- Conversation history
- Direct communication with support team

### Notification System
- In-app notification bell with badge counter
- Notification types: orders, messages, system, check-ins
- Mark as read functionality
- Notification dropdown with recent alerts

### AI Safety Check-in Bot
- Automated check-ins every 30 seconds when activated
- Three response options: OK, Need Help, Emergency
- Optional text responses
- Alert system for missed check-ins
- Safety-focused harm reduction approach

## API Endpoints

### Authentication
- POST `/api/auth/register` - Register new user
- POST `/api/auth/login` - User login
- GET `/api/auth/me` - Get current user

### Products
- GET `/api/products` - Get all products
- GET `/api/products/:id` - Get product by ID
- POST `/api/products` - Create product (admin)
- PUT `/api/products/:id` - Update product (admin)
- DELETE `/api/products/:id` - Delete product (admin)

### Orders
- POST `/api/orders` - Create new order
- GET `/api/orders/my-orders` - Get user's orders
- GET `/api/orders` - Get all orders (admin)
- GET `/api/orders/:id` - Get order by ID
- PATCH `/api/orders/:id/status` - Update order status (admin)

### Messages
- GET `/api/messages/:userId` - Get conversation
- POST `/api/messages` - Send message
- PATCH `/api/messages/read/:userId` - Mark messages as read
- GET `/api/messages/unread/count` - Get unread count

### Notifications
- GET `/api/notifications` - Get user notifications
- PATCH `/api/notifications/:id/read` - Mark as read
- PATCH `/api/notifications/read-all` - Mark all as read
- GET `/api/notifications/unread/count` - Get unread count

## Socket.IO Events

### Client to Server
- `authenticate` - Authenticate user connection
- `send_message` - Send a message
- `start_checkin_session` - Start AI check-in session
- `stop_checkin_session` - Stop AI check-in session
- `checkin_response` - Respond to check-in

### Server to Client
- `receive_message` - Receive new message
- `message_sent` - Message send confirmation
- `new_notification` - New notification
- `checkin_request` - AI check-in request
- `checkin_alert` - Alert for missed check-ins

## Project Structure

```
happypuppy/
├── server/
│   ├── models/          # MongoDB models
│   ├── routes/          # Express routes
│   ├── middleware/      # Auth middleware
│   ├── utils/           # Utility functions
│   └── index.js         # Server entry point
├── client/
│   ├── src/
│   │   ├── components/  # React components
│   │   ├── contexts/    # React contexts
│   │   ├── services/    # API and Socket services
│   │   ├── styles/      # CSS files
│   │   ├── types/       # TypeScript types
│   │   └── App.tsx      # Main app component
│   └── public/          # Static files
├── .env.example         # Environment variables template
└── package.json         # Dependencies
```

## Security Features

- JWT token authentication
- Password hashing with bcryptjs
- Protected API routes
- Role-based access control
- Input validation
- CORS configuration

## Harm Reduction Focus

This system is designed with harm reduction principles:
- Non-judgmental approach
- Safety-focused check-ins
- Direct communication channels
- Emergency response options
- Educational safety information
- Support team accessibility

## License

ISC

## Support

For support or questions, please contact the support team through the in-app messaging system.