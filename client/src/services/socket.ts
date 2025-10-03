import { io, Socket } from 'socket.io-client';

const SOCKET_URL = process.env.REACT_APP_SOCKET_URL || 'http://localhost:5000';

class SocketService {
  private socket: Socket | null = null;

  connect(userId: string) {
    if (this.socket?.connected) {
      return;
    }

    this.socket = io(SOCKET_URL);

    this.socket.on('connect', () => {
      console.log('Connected to socket server');
      this.socket?.emit('authenticate', userId);
    });

    this.socket.on('disconnect', () => {
      console.log('Disconnected from socket server');
    });

    return this.socket;
  }

  disconnect() {
    if (this.socket) {
      this.socket.disconnect();
      this.socket = null;
    }
  }

  sendMessage(data: any) {
    this.socket?.emit('send_message', data);
  }

  onReceiveMessage(callback: (message: any) => void) {
    this.socket?.on('receive_message', callback);
  }

  onMessageSent(callback: (message: any) => void) {
    this.socket?.on('message_sent', callback);
  }

  onNewNotification(callback: (notification: any) => void) {
    this.socket?.on('new_notification', callback);
  }

  startCheckInSession(userId: string) {
    this.socket?.emit('start_checkin_session', userId);
  }

  stopCheckInSession(userId: string) {
    this.socket?.emit('stop_checkin_session', userId);
  }

  onCheckInRequest(callback: (data: any) => void) {
    this.socket?.on('checkin_request', callback);
  }

  onCheckInAlert(callback: (data: any) => void) {
    this.socket?.on('checkin_alert', callback);
  }

  respondToCheckIn(data: any) {
    this.socket?.emit('checkin_response', data);
  }

  getSocket() {
    return this.socket;
  }
}

export default new SocketService();
