<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Socket.io simple chat</title>
    {{-- <link rel="stylesheet" href="./style.css" /> --}}
  </head>
  <body>

    <div class="content">
        <h1>Laravel and Socket.IO</h1>
        <p id="message">Waiting for message...</p>
        <h1 id="m"></h1>
        <p id="n"></p>
        <p id="x"></p>
        <p id="y"></p>

        <!-- Include the Socket.IO client library -->
        <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
        <script>
            // Connect to the Socket.IO server
            const socket = io('http://192.168.10.14:3000'); // Replace with your server's address

            // Listen for messages from the server
            socket.on('message', function(data) {
                document.getElementById('message').innerText = data.text;
            });

            socket.on("message2",(data)=>{
                console.log(data)
                document.getElementById('m').innerText = data.text;
            })

            socket.on("socket",(data)=>{
                document.getElementById("n").innerHTML = data.text
            })

            socket.on("message2", (data)=>{
                document.getElementById("x").innerHTML = data.text
            })
            socket.on("message3", (data)=>{
                document.getElementById("y").innerHTML = data.text
            })

            // Example: Send a message to the server
            socket.emit('message', { text: 'Hello from Laravel!' });
            socket.emit('frontenddata', { text: 'Hello from Laravel2!' });

            socket.emit('frontenddata',{ text: 'hello socket ..' })

            socket.emit('message2',
                {
                    text: "How are you?"
                }
            )
            socket.emit('message3',
                {
                    text: "I am fine. Thank You."
                }
            )
        </script>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>

  </body>
</html>
