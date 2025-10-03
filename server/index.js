const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mongoose = require('mongoose');
const cors = require('cors');
require('dotenv').config();

const authRoutes = require('./routes/auth');
const productRoutes = require('./routes/products');
const orderRoutes = require('./routes/orders');
const messageRoutes = require('./routes/messages');
const notificationRoutes = require('./routes/notifications');

const Message = require('./models/Message');
const Notification = require('./models/Notification');
const CheckIn = require('./models/CheckIn');
const User = require('./models/User');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
  cors: {
    origin: process.env.CLIENT_URL || 'http://localhost:3000',
    methods: ['GET', 'POST']
  }
});

// Middleware
app.use(cors());
app.use(express.json());

// Routes
app.use('/api/auth', authRoutes);
app.use('/api/products', productRoutes);
app.use('/api/orders', orderRoutes);
app.use('/api/messages', messageRoutes);
app.use('/api/notifications', notificationRoutes);

// Socket.IO connection handling
const userSockets = new Map();
const checkInSessions = new Map();

io.on('connection', (socket) => {
  console.log('New client connected:', socket.id);

  socket.on('authenticate', (userId) => {
    userSockets.set(userId, socket.id);
    socket.userId = userId;
    console.log(`User ${userId} authenticated with socket ${socket.id}`);
  });

  socket.on('send_message', async (data) => {
    try {
      const message = new Message({
        from: data.from,
        to: data.to,
        content: data.content,
        orderId: data.orderId
      });
      await message.save();
      await message.populate('from', 'name email');
      await message.populate('to', 'name email');

      // Send to recipient
      const recipientSocketId = userSockets.get(data.to);
      if (recipientSocketId) {
        io.to(recipientSocketId).emit('receive_message', message);
      }

      // Send confirmation to sender
      socket.emit('message_sent', message);

      // Create notification for recipient
      const notification = new Notification({
        user: data.to,
        title: 'New Message',
        message: `New message from ${message.from.name}`,
        type: 'message',
        relatedId: message._id
      });
      await notification.save();

      if (recipientSocketId) {
        io.to(recipientSocketId).emit('new_notification', notification);
      }
    } catch (error) {
      socket.emit('message_error', { error: 'Failed to send message' });
    }
  });

  socket.on('start_checkin_session', async (userId) => {
    const sessionId = `session_${userId}_${Date.now()}`;
    checkInSessions.set(userId, {
      sessionId,
      active: true,
      missedCheckins: 0
    });

    // Start check-in interval (every 30 seconds)
    const intervalId = setInterval(async () => {
      const session = checkInSessions.get(userId);
      if (!session || !session.active) {
        clearInterval(intervalId);
        return;
      }

      const botMessage = 'Hey, checking in! How are you doing? Reply "ok" if you\'re good, or let me know if you need help.';
      
      const checkIn = new CheckIn({
        user: userId,
        status: 'ok',
        botMessage,
        sessionId: session.sessionId,
        responded: false
      });
      await checkIn.save();

      const socketId = userSockets.get(userId);
      if (socketId) {
        io.to(socketId).emit('checkin_request', {
          checkInId: checkIn._id,
          message: botMessage
        });
      }

      // Wait 10 seconds for response
      setTimeout(async () => {
        const updatedCheckIn = await CheckIn.findById(checkIn._id);
        if (!updatedCheckIn.responded) {
          session.missedCheckins++;
          
          if (session.missedCheckins >= 3) {
            // Alert admin or emergency contact
            const notification = new Notification({
              user: userId,
              title: 'Check-in Alert',
              message: 'Multiple check-ins missed. Please respond.',
              type: 'checkin',
              relatedId: checkIn._id
            });
            await notification.save();

            if (socketId) {
              io.to(socketId).emit('new_notification', notification);
              io.to(socketId).emit('checkin_alert', {
                message: 'You\'ve missed multiple check-ins. Are you okay?'
              });
            }
          }
        }
      }, 10000);
    }, 30000);

    socket.on('disconnect', () => {
      const session = checkInSessions.get(userId);
      if (session) {
        session.active = false;
      }
      clearInterval(intervalId);
    });
  });

  socket.on('checkin_response', async (data) => {
    try {
      const { checkInId, response, status } = data;
      await CheckIn.findByIdAndUpdate(checkInId, {
        userResponse: response,
        status: status || 'ok',
        responded: true
      });

      const session = checkInSessions.get(socket.userId);
      if (session) {
        session.missedCheckins = 0;
      }

      socket.emit('checkin_recorded', { message: 'Response recorded' });
    } catch (error) {
      socket.emit('checkin_error', { error: 'Failed to record response' });
    }
  });

  socket.on('stop_checkin_session', (userId) => {
    const session = checkInSessions.get(userId);
    if (session) {
      session.active = false;
      checkInSessions.delete(userId);
    }
  });

  socket.on('disconnect', () => {
    if (socket.userId) {
      userSockets.delete(socket.userId);
    }
    console.log('Client disconnected:', socket.id);
  });
});

// Database connection
mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/happypuppy')
  .then(() => {
    console.log('Connected to MongoDB');
    const PORT = process.env.PORT || 5000;
    server.listen(PORT, () => {
      console.log(`Server running on port ${PORT}`);
    });
  })
  .catch((error) => {
    console.error('MongoDB connection error:', error);
    process.exit(1);
  });

module.exports = { app, server, io };
