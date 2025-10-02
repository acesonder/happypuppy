# Getting Started with HappyPuppy

Welcome! This checklist will help you get HappyPuppy up and running.

## 📋 Pre-Setup Checklist

Before you begin, make sure you have:

- [ ] Node.js (v14 or higher) installed
  - Check: `node --version`
  - Download: https://nodejs.org/

- [ ] MongoDB installed OR MongoDB Atlas account
  - Local: `mongod --version`
  - Cloud: https://www.mongodb.com/cloud/atlas

- [ ] Git installed (to clone the repo)
  - Check: `git --version`
  - Download: https://git-scm.com/

## 🚀 Quick Start (5 Minutes)

### Step 1: Install Dependencies

```bash
# Install backend dependencies
npm install

# Install frontend dependencies
cd client
npm install
cd ..
```

**Expected time:** 2-3 minutes

### Step 2: Configure Environment

```bash
# Copy the example environment file
cp .env.example .env
```

**Edit `.env` and set:**
- `MONGODB_URI` - Your MongoDB connection string
- `JWT_SECRET` - Any random string for development

**Minimal .env for local development:**
```
PORT=5000
MONGODB_URI=mongodb://localhost:27017/happypuppy
JWT_SECRET=my-secret-key-change-in-production
NODE_ENV=development
CLIENT_URL=http://localhost:3000
```

### Step 3: Seed the Database

```bash
npm run seed
```

This creates:
- ✅ Admin user: `admin@happypuppy.com` / `admin123`
- ✅ Test user: `user@test.com` / `user123`
- ✅ 10 sample products

**Expected output:**
```
Connected to MongoDB
Cleared existing data
Admin user created: admin@happypuppy.com / admin123
Test user created: user@test.com / user123
10 products created
Database seeded successfully!
```

### Step 4: Start the Application

**Open two terminal windows:**

**Terminal 1 - Backend:**
```bash
npm run server
```

Wait for: `Server running on port 5000`

**Terminal 2 - Frontend:**
```bash
npm run client
```

Wait for: `webpack compiled successfully`

### Step 5: Access the Application

1. Browser should auto-open to `http://localhost:3000`
2. If not, manually open: http://localhost:3000
3. You should see the login screen

## ✅ Verification Checklist

Test each feature to ensure everything works:

### Authentication
- [ ] Register a new account
- [ ] Login with test user (`user@test.com` / `user123`)
- [ ] View your profile
- [ ] Logout and login again

### Product Browsing
- [ ] View products in Grid view
- [ ] Switch to List view
- [ ] Add a product to cart
- [ ] Adjust quantity
- [ ] View cart (should show 1 item)

### Order Creation
- [ ] Go to Cart tab
- [ ] Select Delivery Type (Pickup or Delivery)
- [ ] Try to select a Monday (should not be available)
- [ ] Select a Wednesday or Friday
- [ ] Select a time between 5:00 PM - 9:00 PM
- [ ] Add notes
- [ ] Submit order
- [ ] See confirmation message

### Order Tracking
- [ ] Go to "My Orders" tab
- [ ] See your newly created order
- [ ] Order status should be "pending"
- [ ] Click "Message Support"

### Messaging
- [ ] Message modal should open
- [ ] Type and send a message
- [ ] Message should appear in chat
- [ ] Close and reopen modal
- [ ] Messages should persist

### Notifications
- [ ] Check notification bell (top right)
- [ ] Should have badge with count
- [ ] Click bell to see dropdown
- [ ] Click a notification to mark as read
- [ ] Badge count should decrease

### AI Check-in Bot
- [ ] Enable "AI Safety Check-ins" toggle
- [ ] Wait 30 seconds
- [ ] Check-in modal should appear
- [ ] Try "I'm OK" response
- [ ] Check-in should close
- [ ] Wait another 30 seconds for next check-in
- [ ] Disable toggle to stop

### Admin Features (Login as admin)
- [ ] Logout and login as admin (`admin@happypuppy.com` / `admin123`)
- [ ] All features above should work
- [ ] Create a new product (if admin panel implemented)
- [ ] Update order status (if admin panel implemented)

## 🐛 Troubleshooting

### Issue: MongoDB Connection Failed

**Symptoms:** `MongoDB connection error` in terminal

**Solutions:**
1. Start MongoDB: `mongod`
2. Check MongoDB is running: `ps aux | grep mongod`
3. Check connection string in `.env`
4. Use MongoDB Atlas if local doesn't work

### Issue: Port Already in Use

**Symptoms:** `Error: listen EADDRINUSE: address already in use :::5000`

**Solutions:**
```bash
# Find process using port 5000
lsof -ti:5000

# Kill the process
kill -9 <PID>

# Or change port in .env
PORT=5001
```

### Issue: Cannot Find Module

**Symptoms:** `Error: Cannot find module 'express'`

**Solution:**
```bash
# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

### Issue: React Won't Start

**Symptoms:** Various npm errors in client

**Solution:**
```bash
cd client
rm -rf node_modules package-lock.json
npm install
npm start
```

### Issue: No Products Showing

**Symptoms:** Empty product list

**Solution:**
```bash
# Re-run seed script
npm run seed
```

### Issue: Authentication Not Working

**Symptoms:** "Invalid token" errors

**Solution:**
1. Logout and login again
2. Clear browser localStorage
3. Check JWT_SECRET is set in `.env`
4. Re-seed database with `npm run seed`

## 📚 Next Steps

Once everything is working:

1. **Read the Documentation:**
   - [README.md](README.md) - Overview and features
   - [FEATURES.md](FEATURES.md) - Detailed feature list
   - [API.md](API.md) - API documentation
   - [ARCHITECTURE.md](ARCHITECTURE.md) - System design

2. **Customize the Application:**
   - Modify products in `server/seed.js`
   - Adjust styling in `client/src/styles/`
   - Add new features as needed

3. **Prepare for Deployment:**
   - Read [DEPLOYMENT.md](DEPLOYMENT.md)
   - Choose hosting platform
   - Set up production MongoDB
   - Configure environment variables

## 💡 Tips

### Development Workflow
- Keep both terminal windows visible
- Watch for errors in backend terminal
- Use browser console (F12) for frontend debugging
- Backend changes require restart (`Ctrl+C` then `npm run server`)
- Frontend auto-reloads on changes

### Testing Different Users
1. Create multiple accounts
2. Use different browsers/incognito
3. Test messaging between accounts
4. Test admin vs user permissions

### Database Management
- Use MongoDB Compass for visual database exploration
- Keep seed script updated with test data
- Regular database backups in production

### Code Changes
- Backend changes: Restart server
- Frontend changes: Auto-reload (usually)
- Database schema changes: May need to clear DB and re-seed
- Environment variable changes: Restart both servers

## 🎯 Success Criteria

You've successfully set up HappyPuppy if:

✅ Both servers start without errors
✅ You can login with test credentials
✅ Products are visible and can be added to cart
✅ Orders can be created with schedule validation
✅ Messages send and receive in real-time
✅ Notifications appear with badge counts
✅ AI check-ins trigger every 30 seconds

## 📞 Need Help?

If you're stuck:

1. **Check the logs** in both terminal windows
2. **Review** [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed setup
3. **Read** [TROUBLESHOOTING](#-troubleshooting) section above
4. **Verify** all environment variables are set correctly
5. **Try** re-running the seed script

## 🎉 You're All Set!

Congratulations! You now have a fully functional harm reduction ordering system running locally.

Happy developing! 🐕

---

**Quick Reference:**

- **Start Backend:** `npm run server` (port 5000)
- **Start Frontend:** `npm run client` (port 3000)
- **Seed Database:** `npm run seed`
- **Test Login:** user@test.com / user123
- **Admin Login:** admin@happypuppy.com / admin123

**Documentation:**
- Overview: [README.md](README.md)
- Quick Start: [QUICKSTART.md](QUICKSTART.md)
- Full Setup: [SETUP_GUIDE.md](SETUP_GUIDE.md)
- Features: [FEATURES.md](FEATURES.md)
- API Docs: [API.md](API.md)
- Architecture: [ARCHITECTURE.md](ARCHITECTURE.md)
- Deployment: [DEPLOYMENT.md](DEPLOYMENT.md)
- Summary: [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)
