<div id="ai-chat-widget" data-csrf="{{ csrf_token() }}" class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
    <div id="ai-chat-window" class="hidden mb-4 w-80 sm:w-96 rounded-lg bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col h-[28rem] transition-all duration-300 transform origin-bottom-right">
        <div class="p-4 text-white flex justify-between items-center" style="background: linear-gradient(to right, #4f46e5, #4338ca) !important;">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-white">Risk AI Assistant</h3>
                    <p class="text-[10px] text-indigo-200">Online</p>
                </div>
            </div>
            <button onclick="toggleAiChat()" class="text-indigo-200 hover:text-white transition rounded-full hover:bg-white/10 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 text-sm scroll-smooth">
            <div class="flex items-start gap-2">
                <div class="bg-indigo-100 text-indigo-900 p-3 rounded-lg rounded-tl-sm max-w-[85%] shadow-sm">
                    Halo! Saya asisten AI untuk Manajemen Risiko. Coba tanyakan: <strong>"Bulan ini adakah risiko yang paling tinggi?"</strong>
                </div>
            </div>
        </div>

        <div class="p-3 bg-white border-t border-slate-100 flex gap-2 items-center">
            <input type="text" id="ai-chat-input" onkeypress="handleAiChatEnter(event)" placeholder="Ketik pertanyaan Anda..." class="flex-1 rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 outline-none transition shadow-inner">
            <button onclick="sendAiMessage()" class="p-2.5 rounded-lg hover:opacity-90 transition flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5" style="background-color: #4f46e5 !important; color: #ffffff !important;">
                <svg class="w-4 h-4 transform rotate-45 ml-[-2px] stroke-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>

    <button onclick="toggleAiChat()" class="group relative flex h-14 w-14 items-center justify-center rounded-full shadow-2xl transition-all duration-300 hover:scale-110 active:scale-95" style="background: linear-gradient(135deg, #4f46e5, #4338ca) !important; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.5) !important;">
        <svg class="h-7 w-7 text-white transition-transform group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="stroke: #ffffff !important;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <span class="absolute top-0 right-0 flex h-4 w-4">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 border-2 border-white"></span>
        </span>
    </button>
</div>
