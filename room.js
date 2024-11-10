import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer);

let users = [];

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

  // room 
  socket.on('join_room', (room) => {
    console.log(`${socket.username} joined room: ${room}`);
    socket.join(room);
    socket.emit('roomJoined', `You joined room: ${room}`);
  });

  // Send message 
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
    });
  });

httpServer.listen(3000, () => {
  console.log('Listening to port 3000');
});

let count = 0;
setInterval(() => {
  io.emit('msg_to_client', 'server', 'test msg ' + count);
  count++;
}, 1000);
