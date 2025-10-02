import React, { useState, useEffect } from 'react';
import socketService from '../services/socket';
import { useAuth } from '../contexts/AuthContext';
import { CheckInRequest } from '../types';
import '../styles/CheckIn.css';

const CheckInBot: React.FC = () => {
  const [isActive, setIsActive] = useState(false);
  const [currentCheckIn, setCurrentCheckIn] = useState<CheckInRequest | null>(null);
  const [showModal, setShowModal] = useState(false);
  const [response, setResponse] = useState('');
  const { user } = useAuth();

  useEffect(() => {
    socketService.onCheckInRequest((data: CheckInRequest) => {
      setCurrentCheckIn(data);
      setShowModal(true);
    });

    socketService.onCheckInAlert((data: any) => {
      alert(data.message);
    });

    return () => {
      if (isActive && user) {
        socketService.stopCheckInSession(user.id);
      }
    };
  }, [isActive, user]);

  const handleStartSession = () => {
    if (user) {
      socketService.startCheckInSession(user.id);
      setIsActive(true);
    }
  };

  const handleStopSession = () => {
    if (user) {
      socketService.stopCheckInSession(user.id);
      setIsActive(false);
      setShowModal(false);
    }
  };

  const handleRespond = (status: 'ok' | 'needs_help' | 'emergency') => {
    if (currentCheckIn) {
      socketService.respondToCheckIn({
        checkInId: currentCheckIn.checkInId,
        response: response || status,
        status,
      });
      setResponse('');
      setShowModal(false);
    }
  };

  return (
    <div className="checkin-bot">
      <div className="checkin-toggle">
        <label className="toggle-label">
          <input
            type="checkbox"
            checked={isActive}
            onChange={(e) => {
              if (e.target.checked) {
                handleStartSession();
              } else {
                handleStopSession();
              }
            }}
          />
          <span>AI Safety Check-ins {isActive ? 'Active' : 'Inactive'}</span>
        </label>
        {isActive && (
          <div className="active-indicator">
            <span className="pulse"></span>
            Active - Checking every 30 seconds
          </div>
        )}
      </div>

      {showModal && currentCheckIn && (
        <div className="modal-overlay">
          <div className="checkin-modal">
            <div className="modal-header">
              <h3>🤖 Safety Check-in</h3>
            </div>
            <div className="modal-body">
              <p className="bot-message">{currentCheckIn.message}</p>
              <textarea
                value={response}
                onChange={(e) => setResponse(e.target.value)}
                placeholder="Optional: Add a message..."
                rows={3}
              />
            </div>
            <div className="modal-actions">
              <button
                onClick={() => handleRespond('ok')}
                className="btn-ok"
              >
                ✓ I'm OK
              </button>
              <button
                onClick={() => handleRespond('needs_help')}
                className="btn-help"
              >
                ⚠ Need Help
              </button>
              <button
                onClick={() => handleRespond('emergency')}
                className="btn-emergency"
              >
                🚨 Emergency
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default CheckInBot;
