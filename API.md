# API Documentation

Complete REST API documentation for HappyPuppy backend.

## Base URL

```
Development: http://localhost:5000/api
Production: https://your-domain.com/api
```

## Authentication

Most endpoints require authentication via JWT token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

Tokens are obtained via login or registration and expire after 7 days.

---

## Authentication Endpoints

### Register User

Create a new user account.

**Endpoint**: `POST /auth/register`

**Body**:
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "John Doe",
  "phone": "555-0123",
  "address": {
    "street": "123 Main St",
    "city": "Springfield",
    "zipCode": "12345"
  }
}
```

**Response** (201 Created):
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "507f1f77bcf86cd799439011",
    "email": "user@example.com",
    "name": "John Doe",
    "role": "user"
  }
}
```

**Errors**:
- `400`: Validation errors or email already exists
- `500`: Registration failed

---

### Login

Authenticate user and receive JWT token.

**Endpoint**: `POST /auth/login`

**Body**:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response** (200 OK):
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "507f1f77bcf86cd799439011",
    "email": "user@example.com",
    "name": "John Doe",
    "role": "user"
  }
}
```

**Errors**:
- `401`: Invalid credentials
- `403`: Account is inactive
- `500`: Login failed

---

### Get Current User

Get authenticated user's profile.

**Endpoint**: `GET /auth/me`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "id": "507f1f77bcf86cd799439011",
  "email": "user@example.com",
  "name": "John Doe",
  "phone": "555-0123",
  "address": {
    "street": "123 Main St",
    "city": "Springfield",
    "zipCode": "12345"
  },
  "role": "user",
  "isActive": true,
  "createdAt": "2024-01-01T00:00:00.000Z",
  "updatedAt": "2024-01-01T00:00:00.000Z"
}
```

**Errors**:
- `401`: Not authenticated
- `404`: User not found

---

## Product Endpoints

### Get All Products

Retrieve all available products.

**Endpoint**: `GET /products`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
[
  {
    "_id": "507f1f77bcf86cd799439011",
    "name": "Naloxone Nasal Spray",
    "description": "Emergency opioid overdose reversal medication",
    "category": "Harm Reduction",
    "quantity": 50,
    "unit": "doses",
    "safetyInfo": "Use immediately if opioid overdose is suspected...",
    "isAvailable": true,
    "image": "",
    "createdAt": "2024-01-01T00:00:00.000Z",
    "updatedAt": "2024-01-01T00:00:00.000Z"
  }
]
```

---

### Get Product by ID

Get details of a specific product.

**Endpoint**: `GET /products/:id`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "_id": "507f1f77bcf86cd799439011",
  "name": "Naloxone Nasal Spray",
  "description": "Emergency opioid overdose reversal medication",
  "category": "Harm Reduction",
  "quantity": 50,
  "unit": "doses",
  "safetyInfo": "Use immediately if opioid overdose is suspected...",
  "isAvailable": true,
  "image": ""
}
```

**Errors**:
- `404`: Product not found

---

### Create Product (Admin Only)

Create a new product.

**Endpoint**: `POST /products`

**Headers**: `Authorization: Bearer <admin-token>`

**Body**:
```json
{
  "name": "Product Name",
  "description": "Product description",
  "category": "Category Name",
  "quantity": 100,
  "unit": "pieces",
  "safetyInfo": "Safety information",
  "isAvailable": true,
  "image": "https://example.com/image.jpg"
}
```

**Response** (201 Created):
```json
{
  "_id": "507f1f77bcf86cd799439011",
  "name": "Product Name",
  ...
}
```

**Errors**:
- `401`: Not authenticated
- `403`: Admin access required

---

### Update Product (Admin Only)

Update product details.

**Endpoint**: `PUT /products/:id`

**Headers**: `Authorization: Bearer <admin-token>`

**Body**: Same as create (partial updates supported)

**Response** (200 OK): Updated product object

---

### Delete Product (Admin Only)

Delete a product.

**Endpoint**: `DELETE /products/:id`

**Headers**: `Authorization: Bearer <admin-token>`

**Response** (200 OK):
```json
{
  "message": "Product deleted successfully"
}
```

---

## Order Endpoints

### Create Order

Create a new order.

**Endpoint**: `POST /orders`

**Headers**: `Authorization: Bearer <token>`

**Body**:
```json
{
  "items": [
    {
      "product": "507f1f77bcf86cd799439011",
      "quantity": 2
    }
  ],
  "deliveryType": "delivery",
  "scheduledDate": "2024-01-10",
  "scheduledTime": "17:00",
  "address": {
    "street": "123 Main St",
    "city": "Springfield",
    "zipCode": "12345"
  },
  "notes": "Please ring doorbell"
}
```

**Response** (201 Created):
```json
{
  "_id": "507f1f77bcf86cd799439012",
  "user": "507f1f77bcf86cd799439011",
  "items": [
    {
      "product": {
        "_id": "507f1f77bcf86cd799439011",
        "name": "Naloxone Nasal Spray",
        ...
      },
      "quantity": 2
    }
  ],
  "deliveryType": "delivery",
  "scheduledDate": "2024-01-10T00:00:00.000Z",
  "scheduledTime": "17:00",
  "status": "pending",
  "address": {
    "street": "123 Main St",
    "city": "Springfield",
    "zipCode": "12345"
  },
  "notes": "Please ring doorbell",
  "createdAt": "2024-01-01T00:00:00.000Z"
}
```

**Errors**:
- `400`: Invalid schedule or insufficient inventory
- `404`: Product not found

---

### Get My Orders

Get orders for authenticated user.

**Endpoint**: `GET /orders/my-orders`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK): Array of orders

---

### Get All Orders (Admin Only)

Get all orders.

**Endpoint**: `GET /orders`

**Headers**: `Authorization: Bearer <admin-token>`

**Response** (200 OK): Array of all orders with user details

---

### Get Order by ID

Get specific order details.

**Endpoint**: `GET /orders/:id`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK): Order object with populated products and user

**Errors**:
- `403`: Access denied (not your order)
- `404`: Order not found

---

### Update Order Status (Admin Only)

Update order status.

**Endpoint**: `PATCH /orders/:id/status`

**Headers**: `Authorization: Bearer <admin-token>`

**Body**:
```json
{
  "status": "confirmed"
}
```

**Valid statuses**: pending, confirmed, preparing, ready, completed, cancelled

**Response** (200 OK): Updated order object

---

## Message Endpoints

### Get Conversation

Get messages between current user and another user.

**Endpoint**: `GET /messages/:userId`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
[
  {
    "_id": "507f1f77bcf86cd799439013",
    "from": {
      "id": "507f1f77bcf86cd799439011",
      "name": "John Doe",
      "email": "john@example.com"
    },
    "to": {
      "id": "507f1f77bcf86cd799439012",
      "name": "Support",
      "email": "support@example.com"
    },
    "content": "Hello, I have a question about my order",
    "read": false,
    "orderId": "507f1f77bcf86cd799439014",
    "createdAt": "2024-01-01T00:00:00.000Z"
  }
]
```

---

### Send Message

Send a new message.

**Endpoint**: `POST /messages`

**Headers**: `Authorization: Bearer <token>`

**Body**:
```json
{
  "to": "507f1f77bcf86cd799439012",
  "content": "Hello, I have a question",
  "orderId": "507f1f77bcf86cd799439014"
}
```

**Response** (201 Created): Message object

---

### Mark Messages as Read

Mark messages from a user as read.

**Endpoint**: `PATCH /messages/read/:userId`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "message": "Messages marked as read"
}
```

---

### Get Unread Message Count

Get count of unread messages.

**Endpoint**: `GET /messages/unread/count`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "count": 5
}
```

---

## Notification Endpoints

### Get Notifications

Get user's notifications.

**Endpoint**: `GET /notifications`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
[
  {
    "_id": "507f1f77bcf86cd799439015",
    "user": "507f1f77bcf86cd799439011",
    "title": "Order Confirmed",
    "message": "Your order has been confirmed",
    "type": "order",
    "read": false,
    "relatedId": "507f1f77bcf86cd799439014",
    "createdAt": "2024-01-01T00:00:00.000Z"
  }
]
```

---

### Mark Notification as Read

Mark specific notification as read.

**Endpoint**: `PATCH /notifications/:id/read`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK): Updated notification

---

### Mark All as Read

Mark all notifications as read.

**Endpoint**: `PATCH /notifications/read-all`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "message": "All notifications marked as read"
}
```

---

### Get Unread Count

Get count of unread notifications.

**Endpoint**: `GET /notifications/unread/count`

**Headers**: `Authorization: Bearer <token>`

**Response** (200 OK):
```json
{
  "count": 3
}
```

---

## Error Responses

All error responses follow this format:

```json
{
  "error": "Error message description"
}
```

### Common HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request (validation error)
- `401`: Unauthorized (not authenticated)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `500`: Internal Server Error

---

## Socket.IO Events

For real-time features, connect to the Socket.IO server.

### Client to Server Events

#### authenticate
```javascript
socket.emit('authenticate', userId);
```

#### send_message
```javascript
socket.emit('send_message', {
  from: userId,
  to: recipientId,
  content: 'Message text',
  orderId: orderId // optional
});
```

#### start_checkin_session
```javascript
socket.emit('start_checkin_session', userId);
```

#### stop_checkin_session
```javascript
socket.emit('stop_checkin_session', userId);
```

#### checkin_response
```javascript
socket.emit('checkin_response', {
  checkInId: checkInId,
  response: 'Response text',
  status: 'ok' // or 'needs_help' or 'emergency'
});
```

### Server to Client Events

#### receive_message
```javascript
socket.on('receive_message', (message) => {
  // Handle received message
});
```

#### message_sent
```javascript
socket.on('message_sent', (message) => {
  // Confirmation of sent message
});
```

#### new_notification
```javascript
socket.on('new_notification', (notification) => {
  // Handle new notification
});
```

#### checkin_request
```javascript
socket.on('checkin_request', (data) => {
  // data: { checkInId, message }
});
```

#### checkin_alert
```javascript
socket.on('checkin_alert', (data) => {
  // data: { message }
});
```

---

## Rate Limiting

Currently not implemented. Recommended for production:
- Authentication endpoints: 5 requests per minute
- General API: 100 requests per minute
- Socket.IO messages: 20 per minute

---

## Pagination

Currently not implemented. All list endpoints return full results.

For future implementation:
```
GET /products?page=1&limit=20
```

---

## Testing the API

### Using cURL

```bash
# Register
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123","name":"Test User","phone":"555-0100"}'

# Login
curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"test123"}'

# Get products (with token)
curl http://localhost:5000/api/products \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Postman

1. Import the API endpoints
2. Set up environment variables for base URL and token
3. Use the Authorization tab to set Bearer token
4. Test each endpoint with sample data

---

For more information, see [README.md](README.md) and [SETUP_GUIDE.md](SETUP_GUIDE.md).
