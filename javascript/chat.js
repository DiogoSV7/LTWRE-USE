async function sendMessage(event) {
    event.preventDefault();

    const csrf = document.querySelector('input[name="csrf"]').value;
    const messageText = document.querySelector('input[name="message"]').value;

    const response = await fetch('../actions/action_send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            csrf: csrf,
            message: messageText,
            otherUserId: otherUserId,
            itemId: itemId,
        })
    });

    if (!response.ok) {
        throw new Error('Failed to send message');
    }

    document.querySelector('input[name="message"]').value = '';
    fetchChatMessages();

}

function fetchChatMessages() {
    fetch(`../api/api_chat_messages.php?other_user_id=${otherUserId}&item_id=${itemId}`)
        .then(response => response.json())
        .then(data => {
            document.querySelector('.messages-container').innerHTML = '';

            data.forEach(chat => {
                const message = document.createElement('div');
                message.className = 'message_' + (chat.sender_id === userId ? 'outgoing' : 'incoming');
                message.innerHTML = `
                    <h4>${cleanHtml(chat.sender_name)}</h4>
                    <p class="message_content">${cleanHtml(chat.message)}</p>
                    <p class="hint">${chat.timestamp}</p>
                `;
                document.querySelector('.messages-container').appendChild(message);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function cleanHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

setInterval(fetchChatMessages, 5000); //5 sec

fetchChatMessages();

document.querySelector('.message-form button[type="submit"]').addEventListener('click', sendMessage);
