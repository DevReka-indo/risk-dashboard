// Deklarasikan elemen global saat DOM siap
let chatWindow, chatMessages, chatInput, chatWidget;

document.addEventListener("DOMContentLoaded", function () {
    chatWindow = document.getElementById('ai-chat-window');
    chatMessages = document.getElementById('ai-chat-messages');
    chatInput = document.getElementById('ai-chat-input');
    chatWidget = document.getElementById('ai-chat-widget');
});

function toggleAiChat() {
    if (!chatWindow || !chatInput) return;
    if (chatWindow.classList.contains('hidden')) {
        chatWindow.classList.remove('hidden');
        setTimeout(() => chatInput.focus(), 100);
    } else {
        chatWindow.classList.add('hidden');
    }
}

function handleAiChatEnter(e) {
    if (e.key === 'Enter') {
        sendAiMessage();
    }
}

async function sendAiMessage() {
    if (!chatInput || !chatMessages || !chatWidget) return;
    const message = chatInput.value.trim();
    if (!message) return;

    // 1. Kosongkan input & tampilkan pesan user
    chatInput.value = '';
    appendAiMessage('user', message);

    // 2. Tampilkan indikator AI sedang mengetik
    const typingId = 'typing-' + Date.now();
    appendAiMessage('ai', '<span class="flex items-center gap-1">Berpikir <span class="animate-bounce">.</span><span class="animate-bounce delay-100">.</span><span class="animate-bounce delay-200">.</span></span>', typingId);

    // Dapatkan CSRF Token dari data-attribute widget atau meta tag halaman
    const csrfToken = chatWidget.getAttribute('data-csrf') ||
                      document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        // 3. Request ke API
        const response = await fetch('/chat/ask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();

        // 4. Hapus indikator mengetik & tampilkan jawaban AI
        const typingIndicator = document.getElementById(typingId);
        if (typingIndicator) typingIndicator.remove();

        const formattedReply = data.reply ? data.reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') : 'Tidak ada respon.';
        appendAiMessage('ai', formattedReply);

    } catch (error) {
        const typingIndicator = document.getElementById(typingId);
        if (typingIndicator) typingIndicator.remove();
        appendAiMessage('ai', '<span class="text-rose-600">Maaf, terjadi kesalahan koneksi ke server AI. Pastikan route /chat/ask sudah dibuat.</span>');
    }
}

function appendAiMessage(sender, text, id = null) {
    if (!chatMessages) return;
    const msgDiv = document.createElement('div');
    msgDiv.className = 'flex items-start gap-2 ' + (sender === 'user' ? 'flex-row-reverse' : '');
    if (id) msgDiv.id = id;

    const bubbleClass = sender === 'user'
        ? 'bg-slate-800 text-white p-3 rounded-2xl rounded-tr-sm max-w-[85%] shadow-sm'
        : 'bg-indigo-100 text-indigo-900 p-3 rounded-2xl rounded-tl-sm max-w-[85%] shadow-sm';

    msgDiv.innerHTML = `<div class="${bubbleClass}">${text}</div>`;
    chatMessages.appendChild(msgDiv);

    // Auto scroll ke bawah
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Ekspos fungsi ke scope global (window) agar tag onclick di HTML tetap berfungsi
window.toggleAiChat = toggleAiChat;
window.handleAiChatEnter = handleAiChatEnter;
window.sendAiMessage = sendAiMessage;
