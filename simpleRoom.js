import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer);

var roomNo=2;
io.on('connection',function(socket) {
  console.log('Connected user');

  socket.join("room"+roomNo);
  io.sockets.in("room"+roomNo).emit('connected room',"Your room number." + roomNo);

  socket.on('disconnected',function(){  
    console.log('Disconnected User');
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
