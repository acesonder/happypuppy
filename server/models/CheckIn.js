const mongoose = require('mongoose');

const checkInSchema = new mongoose.Schema({
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  status: {
    type: String,
    enum: ['ok', 'needs_help', 'emergency'],
    required: true
  },
  botMessage: {
    type: String,
    required: true
  },
  userResponse: {
    type: String,
    default: ''
  },
  sessionId: {
    type: String,
    required: true
  },
  responded: {
    type: Boolean,
    default: false
  }
}, {
  timestamps: true
});

module.exports = mongoose.model('CheckIn', checkInSchema);
