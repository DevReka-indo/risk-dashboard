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

    // 1. Kosongkan input & tampilkan pesan user (RATA KANAN)
    chatInput.value = '';
    appendAiMessage('user', message);

    // 2. Tampilkan indikator AI sedang mengetik (RATA KIRI)
    const typingId = 'typing-' + Date.now();
    appendAiMessage('ai', '<span style="display:flex;align-items:center;gap:4px;">Berpikir <span class="animate-bounce">.</span><span class="animate-bounce delay-100">.</span><span class="animate-bounce delay-200">.</span></span>', typingId);

    // Dapatkan CSRF Token
    const csrfToken = chatWidget.getAttribute('data-csrf') ||
                      document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        // 3. Request ke API Laravel
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
        appendAiMessage('ai', '<span style="color:#e11d48;font-weight:600;">Maaf, terjadi kesalahan koneksi ke server AI. Pastikan route /chat/ask sudah dibuat.</span>');
    }
}

function appendAiMessage(sender, text, id = null) {
    if (!chatMessages) return;

    const isUser = sender === 'user';

    // Pembungkus Alignment — pakai class statis "flex" (aman dari purge)
    // + inline style untuk justify-content (dipaksa, tidak bergantung Tailwind)
    const msgDiv = document.createElement('div');
    msgDiv.className = 'flex items-start gap-2 w-full';
    msgDiv.style.cssText = 'display:flex; width:100%; justify-content:' + (isUser ? 'flex-end' : 'flex-start') + ';';
    if (id) msgDiv.id = id;

    // Bubble Chat — class statis untuk shape/shadow/ukuran (aman, tertulis utuh)
    const bubble = document.createElement('div');
    bubble.className = 'p-3 rounded-2xl max-w-[85%] shadow-sm text-sm break-words';

    if (isUser) {
        // Styling Pesan USER: Rata Kanan, Background Indigo Pekat, Teks Putih
        bubble.style.cssText = 'background-color:#4f46e5; color:#ffffff; border-top-right-radius:0.25rem;';
    } else {
        // Styling Pesan AI: Rata Kiri, Background Indigo Muda, Teks Indigo Gelap
        bubble.style.cssText = 'background-color:#e0e7ff; color:#312e81; border-top-left-radius:0.25rem;';
    }

    bubble.innerHTML = text;

    msgDiv.appendChild(bubble);
    chatMessages.appendChild(msgDiv);

    // Auto scroll ke paling bawah
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Ekspos fungsi ke scope global (window)
window.toggleAiChat = toggleAiChat;
window.handleAiChatEnter = handleAiChatEnter;
window.sendAiMessage = sendAiMessage;