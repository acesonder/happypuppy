# HappyPuppy Setup Guide

This guide will walk you through setting up the HappyPuppy harm reduction ordering system on your local machine.

## Prerequisites

Before you begin, ensure you have the following installed:

1. **Node.js** (v14 or higher)
   - Download from: https://nodejs.org/
   - Verify installation: `node --version`

2. **MongoDB**
   - **Option A**: Local Installation
     - Download from: https://www.mongodb.com/try/download/community
     - Start MongoDB: `mongod`
   - **Option B**: MongoDB Atlas (Cloud)
     - Sign up at: https://www.mongodb.com/cloud/atlas
     - Create a free cluster
     - Get your connection string

3. **Git** (for cloning the repository)
   - Download from: https://git-scm.com/

## Step-by-Step Setup

### 1. Clone the Repository

```bash
git clone https://github.com/acesonder/happypuppy.git
cd happypuppy
```

### 2. Backend Setup

#### Install Backend Dependencies

```bash
npm install
```

#### Configure Environment Variables

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` with your preferred text editor and update the following:

```env
PORT=5000
MONGODB_URI=mongodb://localhost:27017/happypuppy
JWT_SECRET=your-secure-random-string-here
NODE_ENV=development
CLIENT_URL=http://localhost:3000
```

**Important**: 
- Change `JWT_SECRET` to a secure random string in production
- If using MongoDB Atlas, replace `MONGODB_URI` with your connection string

#### Seed the Database

Populate the database with initial data (admin user, test user, and products):

```bash
npm run seed
```

This will create:
- **Admin Account**: admin@happypuppy.com / admin123
- **Test User**: user@test.com / user123
- **Sample Products**: 10 harm reduction products

### 3. Frontend Setup

#### Navigate to Client Directory

```bash
cd client
```

#### Install Frontend Dependencies

```bash
npm install
```

#### Configure Client Environment

Create a `.env` file in the client directory:

```bash
# From the client directory
cat > .env << EOF
REACT_APP_API_URL=http://localhost:5000/api
REACT_APP_SOCKET_URL=http://localhost:5000
EOF
```

Or manually create `client/.env` with:

```env
REACT_APP_API_URL=http://localhost:5000/api
REACT_APP_SOCKET_URL=http://localhost:5000
```

### 4. Running the Application

You'll need two terminal windows/tabs:

#### Terminal 1: Start Backend Server

```bash
# From the root directory
npm run server
```

You should see:
```
Connected to MongoDB
Server running on port 5000
```

#### Terminal 2: Start Frontend Development Server

```bash
# From the root directory
npm run client
```

Or from the client directory:
```bash
cd client
npm start
```

The React app will automatically open in your browser at `http://localhost:3000`

### 5. First Time Login

1. Open your browser to `http://localhost:3000`
2. Click "Register" to create a new account, or use test credentials:
   - Email: `user@test.com`
   - Password: `user123`

## Testing the Features

### 1. Authentication
- ✓ Register a new account
- ✓ Login with existing account
- ✓ View your profile information

### 2. Product Browsing
- ✓ Switch between Grid and List views
- ✓ View product details and safety information
- ✓ Add products to cart

### 3. Ordering
- ✓ Create an order from your cart
- ✓ Select Pickup or Delivery
- ✓ Schedule for Wednesday or Friday, 5pm-9pm
- ✓ View order confirmation

### 4. Messaging
- ✓ Click "Message Support" on an order
- ✓ Send and receive real-time messages
- ✓ View message history

### 5. Notifications
- ✓ Check notification bell for alerts
- ✓ View unread notification count
- ✓ Mark notifications as read

### 6. AI Safety Check-ins
- ✓ Enable the AI Safety Check-ins toggle
- ✓ Respond to check-in prompts every 30 seconds
- ✓ Test OK, Need Help, and Emergency responses

### 7. Order Management
- ✓ View order history in "My Orders"
- ✓ Check order status updates
- ✓ Contact support about specific orders

## Admin Features

Login as admin to access additional features:

**Admin Credentials**: admin@happypuppy.com / admin123

Admin can:
- View all orders
- Update order status
- Manage product inventory
- Add/edit/delete products

## Troubleshooting

### MongoDB Connection Issues

**Error**: "MongoDB connection error"

**Solutions**:
1. Ensure MongoDB is running: `mongod`
2. Check your `MONGODB_URI` in `.env`
3. For MongoDB Atlas, check your IP whitelist and credentials

### Port Already in Use

**Error**: "Port 5000 is already in use"

**Solutions**:
1. Kill the process using port 5000:
   ```bash
   # On Mac/Linux
   lsof -ti:5000 | xargs kill
   
   # On Windows
   netstat -ano | findstr :5000
   taskkill /PID <PID> /F
   ```
2. Or change the port in `.env`:
   ```env
   PORT=5001
   ```

### React App Won't Start

**Error**: Various npm errors

**Solutions**:
1. Clear node_modules and reinstall:
   ```bash
   cd client
   rm -rf node_modules package-lock.json
   npm install
   ```

2. Clear React cache:
   ```bash
   npm start -- --reset-cache
   ```

### Socket.IO Connection Issues

**Error**: "Socket connection failed"

**Solutions**:
1. Verify backend server is running
2. Check `REACT_APP_SOCKET_URL` in `client/.env`
3. Check browser console for CORS errors
4. Ensure firewall allows connections to port 5000

## Project Structure

```
happypuppy/
├── server/                 # Backend Node.js/Express
│   ├── models/            # MongoDB schemas
│   │   ├── User.js
│   │   ├── Product.js
│   │   ├── Order.js
│   │   ├── Message.js
│   │   ├── Notification.js
│   │   └── CheckIn.js
│   ├── routes/            # API endpoints
│   │   ├── auth.js
│   │   ├── products.js
│   │   ├── orders.js
│   │   ├── messages.js
│   │   └── notifications.js
│   ├── middleware/        # Auth middleware
│   │   └── auth.js
│   ├── utils/            # Helper functions
│   │   └── scheduleValidator.js
│   ├── index.js          # Server entry point
│   └── seed.js           # Database seeder
├── client/                # Frontend React app
│   ├── public/           # Static files
│   └── src/
│       ├── components/   # React components
│       │   ├── Login.tsx
│       │   ├── Register.tsx
│       │   ├── Dashboard.tsx
│       │   ├── ProductList.tsx
│       │   ├── OrderForm.tsx
│       │   ├── MyOrders.tsx
│       │   ├── MessagingModal.tsx
│       │   ├── NotificationBell.tsx
│       │   └── CheckInBot.tsx
│       ├── contexts/     # React context
│       │   └── AuthContext.tsx
│       ├── services/     # API services
│       │   ├── api.ts
│       │   └── socket.ts
│       ├── styles/       # CSS files
│       ├── types/        # TypeScript types
│       └── App.tsx       # Main component
├── .env                  # Backend environment (not in git)
├── .env.example          # Environment template
├── .gitignore           # Git ignore rules
├── package.json         # Backend dependencies
└── README.md            # Project documentation
```

## Development Tips

### Hot Reloading

Both frontend and backend support hot reloading:
- Frontend: Changes to React files will automatically refresh
- Backend: Restart server manually or use `nodemon` for auto-restart

### Installing Nodemon (Optional)

For automatic backend restarts on file changes:

```bash
npm install -g nodemon
```

Then run:
```bash
nodemon server/index.js
```

### Testing Socket.IO Events

Open browser console (F12) to see Socket.IO connection logs and debug messages.

### Database Queries

Use MongoDB Compass (GUI) or mongo shell to inspect database:

```bash
mongo happypuppy
db.users.find()
db.products.find()
db.orders.find()
```

## Security Notes

### For Development
- The default JWT secret is for development only
- .env files are gitignored to protect credentials

### For Production
- Use strong, unique JWT_SECRET
- Enable HTTPS
- Use environment-specific MongoDB instance
- Configure CORS properly
- Set NODE_ENV=production
- Use proper authentication for admin users
- Implement rate limiting
- Add input sanitization
- Set up proper logging and monitoring

## Getting Help

If you encounter issues:

1. Check the Troubleshooting section above
2. Review the console logs (both frontend and backend)
3. Check MongoDB is running and accessible
4. Verify all environment variables are set correctly
5. Ensure all dependencies are installed

## Next Steps

After successful setup:

1. Explore the codebase to understand the architecture
2. Customize products in the seed.js file
3. Modify styling in the CSS files
4. Add additional features as needed
5. Deploy to production (Heroku, AWS, etc.)

## Production Deployment

For production deployment, consider:

1. **Hosting Options**:
   - Backend: Heroku, AWS, DigitalOcean, Railway
   - Frontend: Netlify, Vercel, AWS S3 + CloudFront
   - Database: MongoDB Atlas (recommended)

2. **Environment Setup**:
   - Set NODE_ENV=production
   - Use production MongoDB instance
   - Configure production URLs in environment variables
   - Set up SSL certificates

3. **Security Hardening**:
   - Use strong JWT secrets
   - Enable rate limiting
   - Configure CORS for production domains only
   - Set up logging and monitoring
   - Regular security updates

---

**Congratulations!** You now have a fully functional harm reduction ordering system running locally. 🎉
