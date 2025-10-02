const express = require('express');
const Message = require('../models/Message');
const { auth } = require('../middleware/auth');

const router = express.Router();

// Get messages (conversation between two users)
router.get('/:userId', auth, async (req, res) => {
  try {
    const otherUserId = req.params.userId;
    const messages = await Message.find({
      $or: [
        { from: req.userId, to: otherUserId },
        { from: otherUserId, to: req.userId }
      ]
    })
      .populate('from', 'name email')
      .populate('to', 'name email')
      .sort({ createdAt: 1 });

    res.json(messages);
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch messages' });
  }
});

// Send message
router.post('/', auth, async (req, res) => {
  try {
    const { to, content, orderId } = req.body;
    const message = new Message({
      from: req.userId,
      to,
      content,
      orderId
    });

    await message.save();
    await message.populate('from', 'name email');
    await message.populate('to', 'name email');

    res.status(201).json(message);
  } catch (error) {
    res.status(500).json({ error: 'Failed to send message' });
  }
});

// Mark messages as read
router.patch('/read/:userId', auth, async (req, res) => {
  try {
    await Message.updateMany(
      { from: req.params.userId, to: req.userId, read: false },
      { read: true }
    );
    res.json({ message: 'Messages marked as read' });
  } catch (error) {
    res.status(500).json({ error: 'Failed to mark messages as read' });
  }
});

// Get unread message count
router.get('/unread/count', auth, async (req, res) => {
  try {
    const count = await Message.countDocuments({
      to: req.userId,
      read: false
    });
    res.json({ count });
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch unread count' });
  }
});

module.exports = router;
