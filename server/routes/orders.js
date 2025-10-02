const express = require('express');
const Order = require('../models/Order');
const Product = require('../models/Product');
const { auth, adminAuth } = require('../middleware/auth');
const { isValidSchedule } = require('../utils/scheduleValidator');

const router = express.Router();

// Create order
router.post('/', auth, async (req, res) => {
  try {
    const { items, deliveryType, scheduledDate, scheduledTime, address, notes } = req.body;

    // Validate schedule
    const scheduleValidation = isValidSchedule(scheduledDate, scheduledTime);
    if (!scheduleValidation.valid) {
      return res.status(400).json({ error: scheduleValidation.message });
    }

    // Validate inventory
    for (const item of items) {
      const product = await Product.findById(item.product);
      if (!product) {
        return res.status(404).json({ error: `Product ${item.product} not found` });
      }
      if (product.quantity < item.quantity) {
        return res.status(400).json({ 
          error: `Insufficient inventory for ${product.name}` 
        });
      }
    }

    const order = new Order({
      user: req.userId,
      items,
      deliveryType,
      scheduledDate,
      scheduledTime,
      address: deliveryType === 'delivery' ? address : undefined,
      notes
    });

    await order.save();

    // Update inventory
    for (const item of items) {
      await Product.findByIdAndUpdate(item.product, {
        $inc: { quantity: -item.quantity }
      });
    }

    await order.populate('items.product');

    res.status(201).json(order);
  } catch (error) {
    res.status(500).json({ error: 'Failed to create order' });
  }
});

// Get user orders
router.get('/my-orders', auth, async (req, res) => {
  try {
    const orders = await Order.find({ user: req.userId })
      .populate('items.product')
      .sort({ createdAt: -1 });
    res.json(orders);
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch orders' });
  }
});

// Get all orders (admin only)
router.get('/', auth, adminAuth, async (req, res) => {
  try {
    const orders = await Order.find()
      .populate('user', 'name email phone')
      .populate('items.product')
      .sort({ createdAt: -1 });
    res.json(orders);
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch orders' });
  }
});

// Get order by ID
router.get('/:id', auth, async (req, res) => {
  try {
    const order = await Order.findById(req.params.id)
      .populate('items.product')
      .populate('user', 'name email phone');
    
    if (!order) {
      return res.status(404).json({ error: 'Order not found' });
    }

    // Only allow user to view their own orders or admin
    if (order.user._id.toString() !== req.userId && req.userRole !== 'admin') {
      return res.status(403).json({ error: 'Access denied' });
    }

    res.json(order);
  } catch (error) {
    res.status(500).json({ error: 'Failed to fetch order' });
  }
});

// Update order status (admin only)
router.patch('/:id/status', auth, adminAuth, async (req, res) => {
  try {
    const { status } = req.body;
    const order = await Order.findByIdAndUpdate(
      req.params.id,
      { status },
      { new: true }
    ).populate('items.product');

    if (!order) {
      return res.status(404).json({ error: 'Order not found' });
    }

    res.json(order);
  } catch (error) {
    res.status(500).json({ error: 'Failed to update order status' });
  }
});

module.exports = router;
