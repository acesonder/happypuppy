# System Architecture

## Overview

HappyPuppy is built using a modern full-stack architecture with real-time capabilities.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT LAYER                             │
│                     (React + TypeScript)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Login/     │  │   Product    │  │   Order      │          │
│  │   Register   │  │   Browsing   │  │   Management │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │  Messaging   │  │Notifications │  │  Check-in    │          │
│  │   Modal      │  │     Bell     │  │     Bot      │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
         ┌────────────────────────────────────────┐
         │      API Layer (Axios + Socket.IO)     │
         └────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                         SERVER LAYER                             │
│                  (Node.js + Express + Socket.IO)                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │     Auth     │  │   Products   │  │    Orders    │          │
│  │    Routes    │  │    Routes    │  │    Routes    │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Messages   │  │Notifications │  │  Socket.IO   │          │
│  │    Routes    │  │    Routes    │  │   Handlers   │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│                   ┌──────────────────┐                           │
│                   │   Middleware     │                           │
│                   │  (Auth, CORS)    │                           │
│                   └──────────────────┘                           │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
         ┌────────────────────────────────────────┐
         │        MongoDB (Mongoose ODM)          │
         └────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       DATABASE LAYER                             │
│                         (MongoDB)                                │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │    Users     │  │   Products   │  │    Orders    │          │
│  │  Collection  │  │  Collection  │  │  Collection  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   Messages   │  │Notifications │  │   CheckIns   │          │
│  │  Collection  │  │  Collection  │  │  Collection  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow

### Authentication Flow
```
User → Login Form → API /auth/login → Validate Credentials → 
Generate JWT → Store in localStorage → Authenticate All Requests
```

### Order Creation Flow
```
Browse Products → Add to Cart → Fill Order Form → 
Validate Schedule → Check Inventory → Create Order → 
Update Inventory → Send Confirmation → Notify Admin
```

### Real-time Messaging Flow
```
User A → Send Message → Socket.IO Server → 
Save to Database → Emit to User B → Display in Modal → 
Create Notification → Update Badge Counter
```

### AI Check-in Flow
```
Start Session → 30s Interval Timer → Send Check-in Request → 
Wait for Response → Log Response → Check Missed Count → 
Alert if Needed → Repeat
```

## Technology Stack Breakdown

### Frontend (client/)
```
React 18
├── TypeScript (Type Safety)
├── Axios (HTTP Client)
├── Socket.IO Client (Real-time)
├── React Context (State Management)
└── Custom CSS (Styling)
```

### Backend (server/)
```
Node.js + Express
├── MongoDB + Mongoose (Database)
├── Socket.IO (Real-time)
├── JWT (Authentication)
├── bcryptjs (Password Hashing)
├── express-validator (Input Validation)
└── CORS (Security)
```

## Component Architecture

### Frontend Components
```
App.tsx
├── AuthContext (Global Auth State)
├── Login.tsx
├── Register.tsx
└── Dashboard.tsx
    ├── ProductList.tsx
    │   └── (Grid/List View Toggle)
    ├── OrderForm.tsx
    │   └── (Schedule Validation)
    ├── MyOrders.tsx
    │   └── (Order History)
    ├── MessagingModal.tsx
    │   └── (Real-time Chat)
    ├── NotificationBell.tsx
    │   └── (Dropdown Notifications)
    └── CheckInBot.tsx
        └── (AI Safety Monitor)
```

### Backend Routes
```
server/index.js (Main Server)
├── /api/auth
│   ├── POST /register
│   ├── POST /login
│   └── GET /me
├── /api/products
│   ├── GET /
│   ├── GET /:id
│   ├── POST / (admin)
│   ├── PUT /:id (admin)
│   └── DELETE /:id (admin)
├── /api/orders
│   ├── POST /
│   ├── GET /my-orders
│   ├── GET / (admin)
│   ├── GET /:id
│   └── PATCH /:id/status (admin)
├── /api/messages
│   ├── GET /:userId
│   ├── POST /
│   ├── PATCH /read/:userId
│   └── GET /unread/count
└── /api/notifications
    ├── GET /
    ├── PATCH /:id/read
    ├── PATCH /read-all
    └── GET /unread/count
```

## Database Schema

### Users Collection
```javascript
{
  _id: ObjectId,
  email: String (unique),
  password: String (hashed),
  name: String,
  phone: String,
  address: {
    street: String,
    city: String,
    zipCode: String
  },
  role: String (enum: 'user', 'admin'),
  isActive: Boolean,
  timestamps: true
}
```

### Products Collection
```javascript
{
  _id: ObjectId,
  name: String,
  description: String,
  category: String,
  quantity: Number,
  unit: String,
  safetyInfo: String,
  isAvailable: Boolean,
  image: String,
  timestamps: true
}
```

### Orders Collection
```javascript
{
  _id: ObjectId,
  user: ObjectId (ref: 'User'),
  items: [{
    product: ObjectId (ref: 'Product'),
    quantity: Number
  }],
  deliveryType: String (enum: 'pickup', 'delivery'),
  scheduledDate: Date,
  scheduledTime: String,
  status: String (enum),
  address: Object,
  notes: String,
  timestamps: true
}
```

### Messages Collection
```javascript
{
  _id: ObjectId,
  from: ObjectId (ref: 'User'),
  to: ObjectId (ref: 'User'),
  content: String,
  read: Boolean,
  orderId: ObjectId (ref: 'Order'),
  timestamps: true
}
```

### Notifications Collection
```javascript
{
  _id: ObjectId,
  user: ObjectId (ref: 'User'),
  title: String,
  message: String,
  type: String (enum),
  read: Boolean,
  relatedId: ObjectId,
  timestamps: true
}
```

### CheckIns Collection
```javascript
{
  _id: ObjectId,
  user: ObjectId (ref: 'User'),
  status: String (enum: 'ok', 'needs_help', 'emergency'),
  botMessage: String,
  userResponse: String,
  sessionId: String,
  responded: Boolean,
  timestamps: true
}
```

## Security Architecture

### Authentication Flow
```
1. User submits credentials
2. Server validates against database
3. Password compared using bcryptjs
4. JWT token generated with user ID and role
5. Token sent to client
6. Client stores token in localStorage
7. Token included in all subsequent requests
8. Server validates token on protected routes
```

### Authorization Levels
```
Public Routes:
- POST /auth/register
- POST /auth/login

User Routes (JWT Required):
- All product viewing
- Order creation and viewing own orders
- Messaging
- Notifications
- Check-ins

Admin Routes (JWT + Admin Role):
- Product management (CRUD)
- View all orders
- Update order status
```

## Deployment Architecture

### Recommended Production Setup
```
┌─────────────────────────┐
│   Frontend (Vercel)     │
│   - Static React Build  │
│   - CDN Distribution    │
└───────────┬─────────────┘
            │
            ↓
┌─────────────────────────┐
│   Backend (Railway)     │
│   - Express Server      │
│   - Socket.IO Server    │
└───────────┬─────────────┘
            │
            ↓
┌─────────────────────────┐
│  Database (MongoDB)     │
│   - MongoDB Atlas       │
│   - Automated Backups   │
└─────────────────────────┘
```

## Scalability Considerations

### Horizontal Scaling
- Backend can run multiple instances
- Socket.IO sticky sessions required
- Load balancer distribution
- Redis for session management (future)

### Database Optimization
- Indexes on frequently queried fields
- Connection pooling
- Query optimization
- Sharding for large datasets (future)

### Caching Strategy
- Redis for session data (future)
- CDN for static assets
- API response caching (future)
- Client-side data caching

## Performance Metrics

### Target Metrics
- API Response Time: < 200ms
- Page Load Time: < 3s
- Real-time Message Latency: < 100ms
- Database Query Time: < 50ms
- Socket.IO Connection Time: < 1s

### Monitoring Points
- API endpoint response times
- Database query performance
- Socket.IO connection counts
- Error rates and types
- User session duration

## Development Environment

### Local Development Setup
```
┌──────────────────────────────┐
│  Terminal 1: Backend         │
│  npm run server              │
│  Port 5000                   │
└──────────────────────────────┘

┌──────────────────────────────┐
│  Terminal 2: Frontend        │
│  npm run client              │
│  Port 3000                   │
└──────────────────────────────┘

┌──────────────────────────────┐
│  Terminal 3: MongoDB         │
│  mongod                      │
│  Port 27017                  │
└──────────────────────────────┘
```

## Testing Strategy

### Unit Tests (Future)
- Model validation
- Utility functions
- Component rendering
- API endpoint logic

### Integration Tests (Future)
- API endpoints
- Database operations
- Socket.IO events
- Authentication flow

### E2E Tests (Future)
- Complete user flows
- Order creation
- Messaging
- Check-in system

## Backup and Recovery

### Database Backup Strategy
- MongoDB Atlas automated backups
- Point-in-time recovery
- Regular export scripts
- Configuration backups

### Disaster Recovery
- Database restore procedures
- Environment variable backup
- Code repository backup (GitHub)
- Documentation versioning

---

This architecture provides a solid foundation for a scalable, maintainable, and secure harm reduction ordering system.
