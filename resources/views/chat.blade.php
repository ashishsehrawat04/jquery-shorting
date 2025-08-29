@extends('template')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Chat</h3>
    <div class="row">
        <!-- User List -->
        <div class="col-12 col-md-4 mb-3">
            @foreach($users as $user)
                <div class="card shadow-sm p-3 rounded-3 mb-2 SendMessage" 
                     data-id="{{ $user->id }}" 
                     data-name="{{ $user->name }}">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                             class="rounded-circle me-3" width="50" alt="User">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Chat Box -->
        <div class="col-12 col-md-8">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-primary text-white">
                    <h5 id="chatWith" class="mb-0">Select a user to start chat</h5>
                </div>
                <div class="card-body bg-light" style="height:400px; overflow-y:auto;" id="chat-box">
                    <p class="text-muted">No user selected</p>
                </div>
                <div class="card-footer d-flex">
                    <input type="text" id="message" class="form-control me-2" placeholder="Type your message...">
                    <button class="btn btn-primary" id="sendBtn" disabled>Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Chat bubble style */
.chat-bubble {
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 70%;
    display: inline-block;
    word-wrap: break-word;
}
.chat-left {
    background: #f1f1f1;
    color: #333;
    border-bottom-left-radius: 0;
}
.chat-right {
    background: #007bff;
    color: #fff;
    border-bottom-right-radius: 0;
}
</style>

<script>
let activeUserId = null;
let refreshInterval = null;
let authId = {{ Auth::id() }}; // Always know current logged-in user

// Load messages
function loadMessages(userId) {
    $.ajax({
        url: "/messages/" + userId,
        type: "GET",
        success: function(messages){
            $("#chat-box").html("");
            if(messages.length === 0){
                $("#chat-box").html("<p class='text-muted'>Say hi</p>");
            } else {
                messages.forEach(function(msg){
                    let isMine = msg.sender_id == authId;
                    let align  = isMine ? "d-flex justify-content-end" : "d-flex justify-content-start";
                    let bubble = isMine ? "chat-bubble chat-right" : "chat-bubble chat-left";

                    $("#chat-box").append(
                        `<div class="mb-2 ${align}">
                            <span class="${bubble}">${msg.message}</span>
                        </div>`
                    );
                });
                $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
            }
        }
    });
}

// Click on user
$(document).on('click', '.SendMessage', function(){
    let userName = $(this).data("name");
    let userId   = $(this).data("id");

    $("#chatWith").text("Chat with " + userName);
    $("#chat-box").html("<p class='text-muted'>Loading messages...</p>");
    $("#sendBtn").prop("disabled", false).data("user-id", userId);
    activeUserId = userId;

    // First load
    loadMessages(userId);

    // Clear old interval & start new polling every 2s
    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(function(){
        if(activeUserId) loadMessages(activeUserId);
    }, 2000);
});

// Send new message
function sendMessage() {
    let message = $("#message").val().trim();
    let userId  = $("#sendBtn").data("user-id");

    if(message === "") return;

    $.ajax({
        url: "/messages/send",
        type: "POST",
        data: {
            user_id: userId,
            text: message,
            _token: "{{ csrf_token() }}"
        },
        success: function(msg){
            let isMine = msg.sender_id == authId;
            let align  = isMine ? "d-flex justify-content-end" : "d-flex justify-content-start";
            let bubble = isMine ? "chat-bubble chat-right" : "chat-bubble chat-left";

            $("#chat-box").append(
                `<div class="mb-2 ${align}">
                    <span class="${bubble}">${msg.message}</span>
                </div>`
            );
            $("#message").val(""); 
            $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
        }
    });
}

$("#sendBtn").on("click", sendMessage);

// Send on Enter key
$("#message").keypress(function(e){
    if(e.which === 13 && !e.shiftKey){ // Enter key
        e.preventDefault();
        sendMessage();
    }
});
</script>
@endsection
