# Quick Start Guide

Get HappyPuppy running in 5 minutes!

## Prerequisites

- Node.js installed
- MongoDB running (or MongoDB Atlas account)

## Quick Setup

### 1. Install Dependencies

```bash
# Install backend dependencies
npm install

# Install frontend dependencies
cd client
npm install
cd ..
```

### 2. Configure Environment

```bash
# Copy example env file
cp .env.example .env

# Edit .env and set:
# - MONGODB_URI (your MongoDB connection string)
# - JWT_SECRET (any random string for development)
```

### 3. Seed Database

```bash
npm run seed
```

This creates:
- Admin user: `admin@happypuppy.com` / `admin123`
- Test user: `user@test.com` / `user123`
- 10 sample harm reduction products

### 4. Start the Application

**Terminal 1 - Backend:**
```bash
npm run server
```

**Terminal 2 - Frontend:**
```bash
npm run client
```

### 5. Access the Application

Open your browser to: `http://localhost:3000`

Login with:
- **User**: user@test.com / user123
- **Admin**: admin@happypuppy.com / admin123

## What to Try

1. **Browse Products**: Switch between Grid and List views
2. **Create an Order**: Add products to cart and checkout
3. **Schedule Delivery**: Try Wednesday/Friday 5pm-9pm only
4. **Send Messages**: Click "Message Support" on an order
5. **Enable Check-ins**: Turn on AI Safety Check-ins toggle
6. **View Notifications**: Check the bell icon for alerts

## Troubleshooting

**MongoDB Error?**
- Make sure MongoDB is running: `mongod`
- Or use MongoDB Atlas free tier

**Port in Use?**
- Change PORT in `.env` to 5001 or another available port

**Can't Login?**
- Run `npm run seed` again to create test users

## Next Steps

- Read [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed setup
- Check [README.md](README.md) for full feature list
- See [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment

---

**Happy Testing!** 🐕
