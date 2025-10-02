import React, { useState, useEffect, useRef } from 'react';
import { messagesAPI } from '../services/api';
import socketService from '../services/socket';
import { Message } from '../types';
import { useAuth } from '../contexts/AuthContext';
import '../styles/Messaging.css';

interface MessagingModalProps {
  recipientId: string;
  recipientName: string;
  onClose: () => void;
}

const MessagingModal: React.FC<MessagingModalProps> = ({
  recipientId,
  recipientName,
  onClose,
}) => {
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const { user } = useAuth();

  useEffect(() => {
    fetchMessages();
    
    socketService.onReceiveMessage((message: Message) => {
      if (
        (message.from.id === recipientId && message.to.id === user?.id) ||
        (message.from.id === user?.id && message.to.id === recipientId)
      ) {
        setMessages((prev) => [...prev, message]);
      }
    });

    socketService.onMessageSent((message: Message) => {
      setMessages((prev) => [...prev, message]);
    });

    return () => {
      messagesAPI.markAsRead(recipientId);
    };
  }, [recipientId, user]);

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const fetchMessages = async () => {
    try {
      const response = await messagesAPI.getConversation(recipientId);
      setMessages(response.data);
    } catch (error) {
      console.error('Failed to fetch messages:', error);
    }
  };

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const handleSend = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newMessage.trim() || !user) return;

    socketService.sendMessage({
      from: user.id,
      to: recipientId,
      content: newMessage,
    });

    setNewMessage('');
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="messaging-modal" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>Message with {recipientName}</h3>
          <button className="close-button" onClick={onClose}>
            ×
          </button>
        </div>
        
        <div className="messages-container">
          {messages.map((message) => (
            <div
              key={message._id}
              className={`message ${message.from.id === user?.id ? 'sent' : 'received'}`}
            >
              <div className="message-content">
                <p>{message.content}</p>
                <span className="message-time">
                  {new Date(message.createdAt).toLocaleTimeString()}
                </span>
              </div>
            </div>
          ))}
          <div ref={messagesEndRef} />
        </div>

        <form className="message-input-form" onSubmit={handleSend}>
          <input
            type="text"
            value={newMessage}
            onChange={(e) => setNewMessage(e.target.value)}
            placeholder="Type a message..."
            className="message-input"
          />
          <button type="submit" className="btn-send">
            Send
          </button>
        </form>
      </div>
    </div>
  );
};

export default MessagingModal;
