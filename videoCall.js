import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import path from 'path';
import { fileURLToPath } from 'url';
import { v4 as uuidv4 } from 'uuid';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const httpServer = createServer(app);
const io = new Server(httpServer);

app.use(express.static(path.join(__dirname, 'public')));

// unique room ID
app.get('/', (req, res) => {
  res.redirect(`/${uuidv4()}`);
});

app.get('/:room', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'room.html'));
});

app.post('/start-call', (req, res) => {
  const { roomId, userId } = req.body;

  if (!roomId || !userId) {
    return res.status(400).send('Room ID and User ID are required');
  }

  io.to(roomId).emit('user-connected', userId);
  res.status(200).send('User connected');
});

io.on('connection', (socket) => {
  console.log('User connected:', socket.id);

  socket.on('join-room', (roomId, userId) => {
    if (!roomId || !userId) {
      console.log('Room ID or User ID is missing');
      return;
    }

    // Join the room
    socket.join(roomId);
    console.log(`User ${userId} joined room ${roomId}`);

    setTimeout(() => {
      if (io.sockets.adapter.rooms.get(roomId)) {
        socket.to(roomId).broadcast.emit('user-connected', userId);
      } else {
        console.log(`Room ${roomId} does not exist`);
      }
    }, 1000);

    socket.on('disconnect', () => {
      console.log('User disconnected:', userId);
      socket.to(roomId).broadcast.emit('user-disconnected', userId);
    });
  });
});

httpServer.listen(3000, () => {
  console.log('Server is listening on port 3000');
});
