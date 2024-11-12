import express from 'express';
import { createServer } from 'http';
import { Server as socketIo } from 'socket.io';
import jwt from 'jwt-simple';

const app = express();
const server = createServer(app);

const JWT_SECRET = 'your_jwt_secret'; // Same as in Laravel

const io = new socketIo(server, {
  cors: {
    origin: "*"
  }
});

let connectedUsers = [];

io.use((socket, next) => {
  const token = socket.handshake.query.token;
  if (!token) return next(new Error('Authentication error'));

  try {
    const decoded = jwt.decode(token, JWT_SECRET);
    socket.user = decoded;
    next();
  } catch (error) {
    next(new Error('Authentication error'));
  }
});

io.on('connection', (socket) => {
  console.log('A user connected:', socket.user.email);

  // Add the connected user to the array
  connectedUsers.push({ userId: socket.user.id, socketId: socket.id });
  console.log('Connected Users:', connectedUsers);

  socket.on('send_message', (data) => {
    const { message, receiverId } = data;
    console.log('Receiver ID:', receiverId);

    // Find the receiver from connected users
    const receiver = connectedUsers.find(user => user.userId == receiverId);

    if (receiver) {
      // message to the receiver
      io.to(receiver.socketId).emit('receive message', {
        message,
        senderId: socket.user.id,
        receiverId
      });
      console.log('Message sent to receiver:', receiverId, { message });
    } else {
      console.log('Receiver not found.');
    }

  });

  socket.on('disconnect', () => {
    // Remove user from the connectedUsers list when they disconnect
    connectedUsers = connectedUsers.filter(user => user.socketId !== socket.id);
    io.emit('update users', connectedUsers);
  });
});

server.listen(3000, () => console.log('Socket.io server running on port 3000'));
