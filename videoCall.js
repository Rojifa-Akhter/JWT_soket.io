import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import path from 'path';
import { fileURLToPath } from 'url';

// Get the current directory path using import.meta.url
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer);

let users = [];

// Serve static files (like your HTML, CSS, JS) from the 'public' directory
app.use(express.static(path.join(__dirname, 'public')));

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'call.html'));
});

io.on('connection', (socket) => {
  console.log('User connected');

  // Set user name
  socket.on('setUser', (user) => {
    console.log(`setUser received: ${user}`);

    if (users.includes(user)) {
      console.log(`Username ${user} is already in use.`); 
      socket.emit('userExist', `${user} is already in use. Please choose another one.`);
    } else {
      users.push(user);
      socket.username = user;
      socket.emit('userSet', { user });
      console.log(`${user} has connected.`);
    }
  });

  // Room Join
  socket.on('join_room', (room) => {
    console.log(`${socket.username} joined room: ${room}`);
    socket.join(room);
    socket.emit('roomJoined', `You joined room: ${room}`);
  });

  // Audio and Video Call Signaling
  socket.on('newOffer', (offer, room) => {
    console.log(`${socket.username} sent offer to room: ${room}`);
    socket.to(room).emit('newOffer', { offer, from: socket.username });
  });

  // Answer to the offer
  socket.on('newAnswer', (answer, room) => {
    console.log(`${socket.username} sent answer to room: ${room}`);
    socket.to(room).emit('newAnswer', { answer, from: socket.username });
  });

  // ICE Candidate
  socket.on('sendIceCandidateToSignalingServer', (candidate, room) => {
    console.log(`ICE candidate received from ${socket.username}`);
    socket.to(room).emit('sendIceCandidateToSignalingServer', { candidate, from: socket.username });
  });

  // Messaging
  socket.on('msg_from_client', (room, msg) => {
    if (socket.username) {
      console.log(`${socket.username} sent a message ${room}, ${msg}`);
      io.to(room).emit('msg_to_room', { user: socket.username, message: msg });
    } else {
      socket.emit('userNotSet', 'Please set your username first.');
    }
  });

  socket.on('disconnect', () => {
    console.log('User disconnected');
    users = users.filter((user) => user !== socket.username);  // Remove user on disconnect
  });
});

httpServer.listen(3000, () => {
  console.log('Listening to port 3000');
});
