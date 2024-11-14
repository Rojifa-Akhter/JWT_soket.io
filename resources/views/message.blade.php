
@section('content')
    <style>
        /* Basic Styling for Search Input */
        .form-control {
            border: 1px solid #ced4da;
            /* Light border */
            border-radius: 0.25rem;
            /* Slightly rounded corners */
            padding: 0.375rem 0.75rem;
            /* Padding inside the input */
            font-size: 1rem;
            /* Font size */
            line-height: 1.5;
            /* Line height */
        }

        /* Modal Content */
        .modal-content {
            border-radius: 0.375rem;
            /* Slightly rounded corners for the modal */
        }

        /* Modal Header */
        .modal-header {
            border-bottom: 1px solid #dee2e6;
            /* Light border for separation */
        }

        /* Modal Footer */
        .modal-footer {
            border-top: 1px solid #dee2e6;
            /* Light border for separation */
        }
    </style>


    <div class="container">
        <div class="d-flex justify-content-between">
            <h5 class="chat-with-all">Chat with all</h5>
            <div class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#creategroupModal">Create group</div>
        </div>
        <ul class="nav nav-underline message-tap">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">All messages</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Unread</a>
            </li>
        </ul>
        <div class="chat-box-area">
            <div class="row">
                <div class="col-md-4">
                    <div class="chat-list" id="group-list">
                        <div class="search-container">
                            <input type="search" id="user-search" class="form-control search-input" placeholder="Search users">
                            <i class="bi bi-search search-icon"></i>
                        </div>
                        <input id="userId" type="hidden" value="{{ auth()->user()->id }}">

                        <!-- Container for dynamic user items -->
                        <div id="user-list-container">
                            {{-- @forelse ($connectedUsers as $user)
                                <a href="{{ route('messages.show', $user->user->id) }}">
                                    <input type="hidden" id="receiver_id" name="receiver_id" value="{{ $user->user->id }}">
                                    <div class="user-list">
                                        <div class="chat-item">
                                            <img src="{{ asset('/avatars/man.png') }}" alt="{{ $user->user->name }}" class="avatar">
                                            <h6>{{ $user->user->name }} </h6>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="text-white">No users found</p>
                            @endforelse --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="chat-box">
                        <div id="chat-messages">
                            @foreach ($messages as $message)
                                <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
                                    @if (!empty($message->content) && empty($message->image))
                                        {{ $message->content }}
                                        <span class="timestamp">{{ $message->created_at->format('h:i A') }}</span>
                                    @elseif (!empty($message->image))
                                        <img height="200px" src="{{ asset('uploads/chat_images/' . $message->image) }}"
                                            alt="Image">
                                        <span class="timestamp">{{ $message->created_at->format('h:i A') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <form id="chat-input-form" class="chat-input-form" action="{{ route('messages.send') }}"
                            method="POST">
                            @csrf
                            <input type="text" id="chat-input" name="message" class="form-control"
                                placeholder="Type your message">
                            <div class="common-class d-flex align-items-center">
                                <i class="bi bi-camera image-icon" data-bs-target="#image-upload-modal"
                                    data-bs-toggle="modal"></i>
                                <button type="submit" class="send-button btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Image Upload Modal -->
    <div class="modal fade" id="image-upload-modal" tabindex="-1" aria-labelledby="image-upload-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="image-upload-modalLabel">Upload Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="image-upload-form" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input class="dropify" data-height="100" type="file" id="image" name="image"
                                accept="image/*">
                        </div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- form Modal -->
    <div class="modal fade" id="creategroupModal" tabindex="-1" aria-labelledby="creategroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creategroupModalLabel">Create group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="create-group-form">
                        @csrf
                        <div class="mb-3">
                            <input type="text" id="group-name" name="group_name" class="form-control"
                                placeholder="Enter group Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="search" id="search-users" class="form-control" placeholder="Search users">
                        </div>
                        <ul class="list-group" id="user-list">
                            @forelse ($connectedUsers as $user)
                                <li class="list-group-item d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" value="{{ $user->user->id }}"
                                        id="user_{{ $user->user->id }}">
                                    <img src="{{ asset('/avatars/man.png') }}" alt="{{ $user->user->name }}"
                                        class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <label class="form-check-label" for="user_{{ $user->user->id }}">
                                        <h6 class="mb-0">{{ $user->user->name }}</h6>
                                    </label>
                                </li>
                            @empty
                                <p class="text-muted">No users found</p>
                            @endforelse
                        </ul>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submit-group-button" class="btn btn-primary">Create group</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const socket = io('http://192.168.10.14:3000'); // Ensure this is the correct address
            const form = document.getElementById('chat-input-form');
            const input = document.getElementById('chat-input');
            const messages = document.getElementById('chat-messages');
            const userId = document.getElementById('userId').value;
            const receiverInput = document.getElementById('receiver_id');
            const notification = document.getElementById('notification');

            $('.dropify').dropify();


            socket.emit('user connected', userId);



    // Handle incoming updates on connected users
    socket.on('update users', (connectedUsers) => {
        const userListContainer = document.getElementById('user-list-container');
        userListContainer.innerHTML = ''; // Clear existing users

        // Iterate over connected users and append to the container
        connectedUsers.forEach(user => {
            const userElement = document.createElement('a');
            userElement.href = `/messages/${user.userId}`; // Adjust the route as needed

            const receiverIdInput = document.createElement('input');
            receiverIdInput.type = 'hidden';
            receiverIdInput.id = 'receiver_id';
            receiverIdInput.name = 'receiver_id';
            receiverIdInput.value = user.userId;

            const userListDiv = document.createElement('div');
            userListDiv.classList.add('user-list');

            const chatItemDiv = document.createElement('div');
            chatItemDiv.classList.add('chat-item');

            const avatarImg = document.createElement('img');
            avatarImg.src = '/avatars/man.png'; // Adjust the path as needed
            avatarImg.alt = user.name; // Use userName here
            avatarImg.classList.add('avatar');

            const userNameH6 = document.createElement('h6');
            userNameH6.textContent = user.name; // Use userName here

            chatItemDiv.appendChild(avatarImg);
            chatItemDiv.appendChild(userNameH6);
            userListDiv.appendChild(chatItemDiv);
            userElement.appendChild(receiverIdInput);
            userElement.appendChild(userListDiv);

            userListContainer.appendChild(userElement);
        });

        if (connectedUsers.length === 0) {
            const noUsersParagraph = document.createElement('p');
            noUsersParagraph.classList.add('text-white');
            noUsersParagraph.textContent = 'No users found';
            userListContainer.appendChild(noUsersParagraph);
        }
    });


           document.getElementById('submit-group-button').addEventListener('click', function() {
    const groupName = document.getElementById('group-name').value.trim();
    const selectedUsers = Array.from(document.querySelectorAll('input[type="checkbox"]:checked'))
        .map(cb => cb.value);

    if (!groupName) {
        alert('Please enter a group name.');
        return;
    }

    if (selectedUsers.length === 0) {
        alert('Please select at least one user.');
        return;
    }

    // Send AJAX request to create the group
    fetch('{{ route('groups.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                groupName: groupName,
                users: selectedUsers,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                // Emit socket event with group creation details
                socket.emit('newgroup', {
                    groupMembers: data.groupMembers,
                    groupName: data.groupName
                });

                // Close the modal and show success message
                $('#creategroupModal').modal('hide');
                alert(data.status);

                // Update the UI with new group information
                updateGroupList(data.groupName, data.groupMembers);
            } else {
                alert('Failed to create group. Please try again.');
            }
        })
        .catch(error => alert('An error occurred. Please try again.'));
});

// Function to update group list in the UI
function updateGroupList(groupName, groupMembers) {
    const groupList = document.getElementById('group-list');

    // Create a new list item for the group
    const groupItem = document.createElement('div');
    groupItem.classList.add('user-list');

    groupItem.innerHTML = `
        <h5>${groupName}</h5>
        <ul>
            ${groupMembers.map(member => `
                <li>
                    <a href="{{ route('messages.show', '') }}/${member.id}">
                        <input type="hidden" id="receiver_id" name="receiver_id" value="${member.id}">
                        <div class="chat-item d-flex align-items-center">
                            <img src="{{ asset('/avatars/man.png') }}" alt="${member.name}" class="avatar">
                            <h6>${member.name}</h6>
                        </div>
                    </a>
                </li>`).join('')}
        </ul>
    `;

    // Append the new group to the group list
    groupList.appendChild(groupItem);
}

// Socket.IO event for new group
socket.on('newgroup', function(data) {
    console.log('New group created:', data);
    // Update the UI with the new group information
    updateGroupList(data.groupName, data.groupMembers);
});




            document.getElementById('image-upload-form').addEventListener('submit', function(event) {
                event.preventDefault();
                let formData = new FormData(this);
                formData.append('receiver_id', receiverInput.value);

                fetch('{{ route('upload.image') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            socket.emit('imageUploaded', {
                                imagePath: data.imagePath,
                                receiver_id: receiverInput.value
                            });
                            document.getElementById('image').value = '';
                            $('#image-upload-modal').modal('hide');
                        } else {
                            alert('Image upload failed. Please try again.');
                        }
                    })
                    .catch(error => alert('An error occurred while uploading the image.'));
            });

            socket.on('newImage', function(data) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'received');
                const img = document.createElement('img');
                img.src = data.imagePath;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '200px';
                messageElement.appendChild(img);
                messages.appendChild(messageElement);
                messages.scrollTop = messages.scrollHeight;
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const message = input.value.trim();
                const receiver_id = receiverInput.value;
                if (message !== '') {
                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: {
                            _token: $('input[name=_token]').val(),
                            receiver_id: receiver_id,
                            message: message,
                        },
                        success: () => {
                            socket.emit('send message', {
                                message,
                                receiver_id
                            });
                            input.value = '';
                        },
                        error: () => {
                            alert('Message could not be sent. Please try again.');
                        }
                    });
                }
            });

            socket.on('chat message', (message) => {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'received');
                messageElement.innerHTML =
                    `${message}<span class="timestamp">${new Date().toLocaleTimeString()}</span>`;
                messages.appendChild(messageElement);
                messages.scrollTop = messages.scrollHeight;
            });

            $('.user-link').on('click', function(e) {
                e.preventDefault();
                const receiverId = $(this).data('receiver-id');
                receiverInput.value = receiverId;
            });
        });
    </script>
@endsection