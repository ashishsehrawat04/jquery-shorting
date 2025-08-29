@extends('template')

@section('content')
<div class="container-fluid">
    <div class="row chat-app">
        <!-- Sidebar (User List) -->
        <div class="col-12 col-md-4 chat-sidebar p-0">
            <div class="chat-header p-3">
                <h5 class="mb-0">Chats</h5>
            </div>
            <div class="chat-users p-2">
                @foreach($users as $user)
                    <div class="chat-user d-flex align-items-center p-2 mb-2 rounded-3 SendMessage" 
                         data-id="{{ $user->id }}" 
                         data-name="{{ $user->name }}">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                             class="rounded-circle me-3" width="45" alt="User">
                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">Click to chat</small>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Chat Box -->
        <div class="col-12 col-md-8 chat-main p-0">
            <div class="chat-header p-3 d-flex align-items-center justify-content-between">
                <h5 id="chatWith" class="mb-0 text-primary">Select a user to start chat</h5>
            </div>
            <div class="chat-body p-3" id="chat-box">
                <p class="text-muted">No user selected</p>
            </div>
            <div class="chat-footer p-3 d-flex">
                <input type="text" id="message" class="form-control me-2" placeholder="Type a message...">
                <button class="btn btn-primary rounded-circle" id="sendBtn" disabled>
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Layout */
.chat-app {
    height: 90vh;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.chat-sidebar {
    background: #f8f9fa;
    border-right: 1px solid #ddd;
    height: 100%;
    overflow-y: auto;
}
.chat-main {
    background: #eef2f7;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.chat-header {
    background: #fff;
    border-bottom: 1px solid #ddd;
}
.chat-body {
    flex: 1;
    overflow-y: auto;
    background: url('https://www.transparenttextures.com/patterns/cubes.png');
}
.chat-footer {
    background: #fff;
    border-top: 1px solid #ddd;
}

/* User list */
.chat-user {
    cursor: pointer;
    transition: 0.2s;
}
.chat-user:hover, .chat-user.active {
    background: #e7f1ff;
}

/* Bubbles */
.message-row {
    display: flex;
    align-items: flex-end;
    margin-bottom: 8px;
}
.message-row.right {
    justify-content: flex-end;
}
.message-row.left {
    justify-content: flex-start;
}
.chat-bubble {
    padding: 10px 15px;
    border-radius: 20px;
    max-width: 70%;
    word-wrap: break-word;
    font-size: 14px;
    position: relative;
    display: inline-block;
}
.chat-left {
    background: #fff;
    color: #333;
    border-bottom-left-radius: 5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
.chat-right {
    background: #007bff;
    color: #fff;
    border-bottom-right-radius: 5px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.chat-time {
    font-size: 11px;
    color: #777;
    margin-left: 6px;
    white-space: nowrap;
}
</style>

<script>
let activeUserId = null;
let refreshInterval = null;
let authId = {{ Auth::id() }};

// Load messages
function loadMessages(userId) {
    $.ajax({
        url: "/messages/" + userId,
        type: "GET",
        success: function(messages){
            $("#chat-box").html("");
            if(messages.length === 0){
                $("#chat-box").html("<p class='text-muted'>Say hi 👋</p>");
            } else {
                messages.forEach(function(msg){
                    let isMine = msg.sender_id == authId;
                    let alignClass = isMine ? "right" : "left";
                    let bubble = isMine ? "chat-bubble chat-right" : "chat-bubble chat-left";

                    $("#chat-box").append(
                        `<div class="message-row ${alignClass}">
                            <span class="${bubble}">${msg.message}</span>
                            <small class="chat-time">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
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
    $(".SendMessage").removeClass("active");
    $(this).addClass("active");

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
            let alignClass = isMine ? "right" : "left";
            let bubble = isMine ? "chat-bubble chat-right" : "chat-bubble chat-left";

            $("#chat-box").append(
                `<div class="message-row ${alignClass}">
                    <span class="${bubble}">${msg.message}</span>
                    <small class="chat-time">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
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
    if(e.which === 13 && !e.shiftKey){
        e.preventDefault();
        sendMessage();
    }
});
</script>
@endsection
