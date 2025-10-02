# Deployment Guide

This guide covers deploying the HappyPuppy application to production.

## Deployment Architecture

The application consists of three components:
1. **Backend API** (Node.js/Express + Socket.IO)
2. **Frontend** (React application)
3. **Database** (MongoDB)

## Recommended Deployment Setup

### Option 1: All-in-One (Recommended for Small Scale)

**Platform**: Railway, Render, or Heroku

**Pros**:
- Simple setup
- Single deployment
- Built-in MongoDB support (or easy Atlas integration)

**Cons**:
- Less scalable
- Potentially higher costs at scale

### Option 2: Separate Services (Recommended for Production)

**Setup**:
- Frontend: Vercel or Netlify
- Backend: Railway, Render, or AWS
- Database: MongoDB Atlas

**Pros**:
- Better scalability
- Independent scaling
- Better performance

**Cons**:
- More complex setup
- More services to manage

## Step-by-Step: Deploy to Railway (Recommended)

Railway provides easy deployment for full-stack applications.

### Prerequisites
1. Create a [Railway account](https://railway.app/)
2. Install Railway CLI (optional): `npm i -g @railway/cli`

### Step 1: Prepare for Deployment

1. **Build frontend for production**:
```bash
cd client
npm run build
```

2. **Create production server script**:

Create `server/production.js`:
```javascript
const express = require('express');
const path = require('path');
const app = require('./index').app;

// Serve static files from React build
app.use(express.static(path.join(__dirname, '../client/build')));

// Serve React app for all other routes
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, '../client/build', 'index.html'));
});

const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`Production server running on port ${PORT}`);
});
```

3. **Update package.json**:
```json
{
  "scripts": {
    "start": "node server/index.js",
    "build": "cd client && npm install && npm run build",
    "production": "node server/production.js"
  }
}
```

### Step 2: Deploy to Railway

1. **Create new Railway project**:
   - Go to [Railway.app](https://railway.app/)
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Connect your GitHub account and select the repository

2. **Add MongoDB**:
   - In your Railway project, click "New"
   - Select "Database" → "MongoDB"
   - Railway will create a MongoDB instance

3. **Configure Environment Variables**:
   - Go to your service settings
   - Add environment variables:
     ```
     NODE_ENV=production
     JWT_SECRET=your-secure-random-string-here
     MONGODB_URI=${{MongoDB.DATABASE_URL}}
     CLIENT_URL=https://your-app.railway.app
     PORT=5000
     ```

4. **Deploy**:
   - Railway will automatically deploy from your main branch
   - Monitor deployment logs

### Step 3: Seed Production Database

Once deployed, seed the database:

```bash
railway run npm run seed
```

Or connect to the production MongoDB URL locally:
```bash
MONGODB_URI="your-production-mongodb-url" npm run seed
```

## Step-by-Step: Deploy to Heroku

### Prerequisites
1. Create a [Heroku account](https://www.heroku.com/)
2. Install [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli)

### Step 1: Prepare Application

1. **Create Procfile**:
```
web: node server/index.js
```

2. **Update package.json** (if not already done):
```json
{
  "engines": {
    "node": "16.x",
    "npm": "8.x"
  }
}
```

### Step 2: Deploy

```bash
# Login to Heroku
heroku login

# Create new Heroku app
heroku create happypuppy-app

# Set environment variables
heroku config:set NODE_ENV=production
heroku config:set JWT_SECRET=your-secure-random-string
heroku config:set CLIENT_URL=https://happypuppy-app.herokuapp.com

# Add MongoDB addon
heroku addons:create mongolab:sandbox

# Deploy
git push heroku main

# Run seed script
heroku run npm run seed
```

## Step-by-Step: Separate Frontend/Backend Deployment

### Deploy Backend to Railway/Render

Follow the Railway instructions above, but skip the static file serving.

### Deploy Frontend to Vercel

1. **Update client/.env.production**:
```env
REACT_APP_API_URL=https://your-backend-url.railway.app/api
REACT_APP_SOCKET_URL=https://your-backend-url.railway.app
```

2. **Install Vercel CLI**:
```bash
npm i -g vercel
```

3. **Deploy**:
```bash
cd client
vercel --prod
```

4. **Configure CORS on backend**:
Update `server/index.js`:
```javascript
const cors = require('cors');

app.use(cors({
  origin: ['https://your-frontend.vercel.app'],
  credentials: true
}));
```

### Deploy Frontend to Netlify

1. **Create netlify.toml** in client directory:
```toml
[build]
  base = "client"
  command = "npm run build"
  publish = "build"

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200
```

2. **Deploy via Netlify CLI**:
```bash
npm i -g netlify-cli
cd client
netlify deploy --prod
```

## MongoDB Atlas Setup

For production database:

1. **Create Atlas account**: https://www.mongodb.com/cloud/atlas
2. **Create cluster** (M0 free tier available)
3. **Create database user**
4. **Whitelist IP addresses** (or allow from anywhere for cloud deployments)
5. **Get connection string**
6. **Update MONGODB_URI** in your deployment platform

## Security Checklist

Before deploying to production:

- [ ] Change JWT_SECRET to a strong, unique value
- [ ] Set NODE_ENV=production
- [ ] Configure CORS with specific origins (not *)
- [ ] Enable HTTPS (most platforms do this automatically)
- [ ] Use environment-specific MongoDB instance
- [ ] Review and limit API rate limiting
- [ ] Enable request logging
- [ ] Set up error monitoring (Sentry, etc.)
- [ ] Regular security audits: `npm audit`
- [ ] Keep dependencies updated
- [ ] Implement proper backup strategy for database
- [ ] Set up monitoring and alerts

## Environment Variables Reference

### Backend (.env)
```env
# Server
PORT=5000
NODE_ENV=production

# Database
MONGODB_URI=mongodb+srv://user:pass@cluster.mongodb.net/happypuppy

# Authentication
JWT_SECRET=your-secure-random-256-bit-string

# Frontend URL (for CORS)
CLIENT_URL=https://your-frontend-url.com
```

### Frontend (client/.env.production)
```env
REACT_APP_API_URL=https://your-backend-url.com/api
REACT_APP_SOCKET_URL=https://your-backend-url.com
```

## Post-Deployment

### 1. Test All Features

- [ ] User registration and login
- [ ] Product browsing
- [ ] Order creation
- [ ] Message sending/receiving
- [ ] Notifications
- [ ] AI check-ins
- [ ] Admin functions

### 2. Monitor Performance

- Check response times
- Monitor error rates
- Watch database queries
- Check Socket.IO connections

### 3. Set Up Monitoring

Recommended tools:
- **Application Monitoring**: New Relic, Datadog
- **Error Tracking**: Sentry
- **Uptime Monitoring**: UptimeRobot
- **Log Management**: Papertrail, Loggly

### 4. Create Backups

- Set up automatic MongoDB backups (Atlas provides this)
- Export user data regularly
- Keep backup of environment variables

## Scaling Considerations

As your application grows:

1. **Database**: Upgrade MongoDB tier for better performance
2. **Backend**: Use multiple instances/containers
3. **Frontend**: CDN for static assets (Cloudflare, etc.)
4. **Caching**: Implement Redis for session management
5. **Load Balancing**: Use platform load balancers or AWS ALB
6. **File Storage**: Use S3 for user uploads instead of server storage

## Troubleshooting Deployment Issues

### Build Fails

**Check**:
- All dependencies in package.json
- Node version compatibility
- Build logs for specific errors

### Database Connection Issues

**Check**:
- MONGODB_URI is correct
- IP whitelist in MongoDB Atlas
- Database user credentials
- Network connectivity

### Socket.IO Not Connecting

**Check**:
- WebSocket support on platform
- CORS configuration
- Client URL configuration
- Firewall rules

### Static Files Not Serving

**Check**:
- Build directory path
- Static file middleware configuration
- Deployment build step

## Rolling Back

If deployment has issues:

**Railway**: Redeploy previous deployment from dashboard

**Heroku**:
```bash
heroku releases
heroku rollback v[previous-version]
```

**Vercel**:
```bash
vercel rollback
```

## Continuous Deployment

Set up CI/CD for automatic deployments:

### GitHub Actions Example

Create `.github/workflows/deploy.yml`:
```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Use Node.js
        uses: actions/setup-node@v2
        with:
          node-version: '16'
      - run: npm install
      - run: cd client && npm install && npm run build
      # Add deployment steps for your platform
```

## Cost Optimization

**Free Tiers**:
- MongoDB Atlas: M0 (512MB)
- Railway: $5 credit/month
- Vercel: Generous free tier
- Heroku: No longer offers free tier

**Estimated Monthly Costs**:
- **Small Scale** (< 100 users): $0-20/month
- **Medium Scale** (100-1000 users): $20-100/month
- **Large Scale** (1000+ users): $100+/month

## Support

For deployment help:
- Railway: https://railway.app/help
- Heroku: https://help.heroku.com/
- Vercel: https://vercel.com/support
- MongoDB Atlas: https://support.mongodb.com/

---

**Good luck with your deployment!** 🚀
