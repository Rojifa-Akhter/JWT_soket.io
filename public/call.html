<!-- resources/views/audio_video_call.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audio/Video Call</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div id="user-info" class="my-3">
            <input type="text" id="userNameInput" placeholder="Enter your username" class="form-control">
            <button id="setUserBtn" class="btn btn-primary mt-2">Set Username</button>
        </div>
        <div id="call-controls" class="my-3" style="display: none;">
            <button id="callBtn" class="btn btn-success">Call</button>
            <button id="hangupBtn" class="btn btn-danger" style="display: none;">Hang Up</button>
        </div>
        <div id="videos" class="my-3">
            <div id="local-video-container">
                <video id="local-video" autoplay playsinline></video>
            </div>
            <div id="remote-video-container">
                <video id="remote-video" autoplay playsinline></video>
            </div>
        </div>
    </div>

    <script src="/socket.io/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/peerjs@1.4.1/dist/peerjs.min.js"></script>
    <script>
        const socket = io.connect('http://localhost:3000');
        let userName = '';
        let peerConnection = null;
        let localStream = null;
        let remoteStream = new MediaStream();
        let room = 'room1';  // Example room name for call

        // Get HTML elements
        const localVideo = document.getElementById('local-video');
        const remoteVideo = document.getElementById('remote-video');
        const setUserBtn = document.getElementById('setUserBtn');
        const callBtn = document.getElementById('callBtn');
        const hangupBtn = document.getElementById('hangupBtn');

        // Set username event
        setUserBtn.addEventListener('click', () => {
            userName = document.getElementById('userNameInput').value;
            if (userName) {
                socket.emit('setUser', userName);
                document.getElementById('user-info').style.display = 'none';
                document.getElementById('call-controls').style.display = 'block';
            }
        });

        // Join room on call button click
        callBtn.addEventListener('click', () => {
            socket.emit('join_room', room);
            startCall();
        });

        // Hang up button
        hangupBtn.addEventListener('click', () => {
            hangUpCall();
        });

        // Fetch user media (audio and video)
        async function fetchUserMedia() {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
            localStream = stream;
            localVideo.srcObject = localStream;
        }

        // Start a call
        async function startCall() {
            await fetchUserMedia();
            peerConnection = new RTCPeerConnection({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' }
                ]
            });

            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
            peerConnection.ontrack = (event) => {
                remoteStream.addTrack(event.track);
                remoteVideo.srcObject = remoteStream;
            };

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    socket.emit('sendIceCandidateToSignalingServer', event.candidate, room);
                }
            };

            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            socket.emit('newOffer', offer, room);
        }

        // Handle incoming offer and create an answer
        socket.on('newOffer', async (data) => {
            await fetchUserMedia();
            peerConnection = new RTCPeerConnection({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' }
                ]
            });

            localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
            peerConnection.ontrack = (event) => {
                remoteStream.addTrack(event.track);
                remoteVideo.srcObject = remoteStream;
            };

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    socket.emit('sendIceCandidateToSignalingServer', event.candidate, room);
                }
            };

            await peerConnection.setRemoteDescription(data.offer);
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            socket.emit('newAnswer', answer, room);
        });

        // Handle incoming answer
        socket.on('newAnswer', (data) => {
            peerConnection.setRemoteDescription(data.answer);
        });

        // Handle incoming ICE candidate
        socket.on('sendIceCandidateToSignalingServer', (data) => {
            const candidate = new RTCIceCandidate(data.candidate);
            peerConnection.addIceCandidate(candidate);
        });

        // Hang up the call
        function hangUpCall() {
            peerConnection.close();
            peerConnection = null;
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
            remoteStream = new MediaStream();
            remoteVideo.srcObject = remoteStream;
        }
    </script>
</body>
</html>
