import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import axios from 'axios';

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

const API_BASE_URL = 'http://127.0.0.1:8000/api';

let connectedUsers = [];



io.on('connection', (socket) => {
  console.log('A user connected:', socket.id);

  socket.on('userConnected', async (user_id) => {
    try {
          const response = await axios.post(`${API_BASE_URL}/connected-users`, {

            user_id: user_id,
            socket_id: socket.id,
        });

        // Add the user to the connectedUsers list
        userConnected = userConnected.filter(user => user.userId !== user_id);
        userConnected.push({ userId: user_id, socketId: socket.id });

        // Emit the updated user list to all clients
        io.emit('update users', userConnected);

        console.log('User saved to the database:', response.data);
    } catch (error) {
        console.error('Error saving user:', error);
    }
});


  socket.on("send_message", (data) => {
    const { message, receiver_id } = data;

    // Find the receiver's socket ID
    const receiver = connectedUsers.find(user => user.userId == receiver_id);
    if (receiver) {
        io.to(receiver.socketId).emit('message send', message);
    }

    // Optionally: send the message back to the sender's chatbox
    socket.emit('message send', message);
});

  socket.on('disconnect', () => {
    console.log('User disconnected:', socket.id);
  });


});

httpServer.listen(3000, () => {
  console.log('Server is running on port 3000');
});
