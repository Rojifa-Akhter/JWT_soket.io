import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer);

io.on('connection', (socket) => {
  console.log('Connected');

  socket.on('msg_from_client', (from, msg) => {
    console.log('Message is ' + from, msg);
  });
  socket.on('disconnect', () => {
    console.log('Disconnected');
  });
});

httpServer.listen(3000, () => {
  console.log('Listening to port 3000');
});

let count = 0;
setInterval(() => {
  io.emit('msg_to_client', 'client', 'test msg ' + count);
  count++;
}, 1000);
