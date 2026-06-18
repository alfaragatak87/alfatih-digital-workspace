<!-- 02_dasbor_utama/ai_widget.php -->
<style>
/* AI Widget Styles - Premium Edition */
.ai-widget-btn {
    position: fixed;
    bottom: 96px; 
    right: 24px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6, #d946ef);
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite, floatAnim 3s ease-in-out infinite;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.5), inset 0 2px 4px rgba(255,255,255,0.4);
    cursor: pointer;
    z-index: 9999;
    border: 2px solid rgba(255,255,255,0.2);
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.ai-widget-btn:hover {
    transform: scale(1.15) translateY(-5px);
    box-shadow: 0 12px 40px rgba(139, 92, 246, 0.6), inset 0 2px 4px rgba(255,255,255,0.5);
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
@keyframes floatAnim {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

.ai-chat-box {
    position: fixed;
    bottom: 170px;
    right: 24px;
    width: 380px;
    height: 550px;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255,255,255,0.05);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(40px) scale(0.9) rotate(2deg);
    opacity: 0;
    visibility: hidden;
    transform-origin: bottom right;
    transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.ai-chat-box.open {
    transform: translateY(0) scale(1) rotate(0deg);
    opacity: 1;
    visibility: visible;
}
.ai-chat-box::before {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: radial-gradient(circle at top right, rgba(139,92,246,0.15), transparent 50%);
    pointer-events: none;
    z-index: 0;
}
.ai-chat-header {
    padding: 20px 24px;
    background: linear-gradient(90deg, rgba(15,23,42,0.8), rgba(30,41,59,0.8));
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}
.ai-chat-header-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 12px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}
.ai-chat-header-title i {
    color: #a855f7;
    font-size: 1.4rem;
    filter: drop-shadow(0 0 8px rgba(168,85,247,0.6));
}
.ai-close-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(0,0,0,0.1);
    color: #cbd5e1;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    border-radius: 50%;
}
.ai-close-btn:hover {
    color: #fff;
    background: rgba(239, 68, 68, 0.8);
    border-color: rgba(239, 68, 68, 1);
    transform: rotate(90deg);
}
.ai-chat-body {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.2) transparent;
    position: relative;
    z-index: 1;
}
.ai-chat-body::-webkit-scrollbar { width: 6px; }
.ai-chat-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
.ai-msg {
    max-width: 88%;
    padding: 14px 18px;
    border-radius: 20px;
    font-size: 0.92rem;
    line-height: 1.6;
    word-wrap: break-word;
    position: relative;
    animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes popIn {
    0% { opacity: 0; transform: translateY(10px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.ai-msg.bot {
    align-self: flex-start;
    background: rgba(30, 41, 59, 0.9);
    border: 1px solid rgba(0,0,0,0.1);
    color: #f1f5f9;
    border-bottom-left-radius: 4px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.ai-msg.user {
    align-self: flex-end;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 8px 20px rgba(99,102,241,0.3);
}
.ai-chat-input-area {
    padding: 20px;
    background: rgba(15, 23, 42, 0.6);
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    gap: 12px;
    position: relative;
    z-index: 1;
}
.ai-chat-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 30px;
    padding: 12px 20px;
    color: #fff;
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
    transition: all 0.3s;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
}
.ai-chat-input:focus {
    border-color: #a855f7;
    background: rgba(255, 255, 255, 0.1);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.2), 0 0 0 4px rgba(168,85,247,0.2);
}
.ai-attach-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    padding: 0 4px;
}
.ai-attach-btn:hover {
    color: #a855f7;
    transform: scale(1.1);
}
.ai-image-preview-container {
    padding: 10px 24px 0 24px;
    background: rgba(15, 23, 42, 0.6);
    display: none;
    position: relative;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.ai-image-preview {
    position: relative;
    display: inline-block;
}
.ai-image-preview img {
    height: 50px;
    border-radius: 8px;
    border: 2px solid #a855f7;
    object-fit: cover;
}
.ai-image-remove {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.5);
}
.ai-send-btn {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a855f7, #ec4899);
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(236,72,153,0.3);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.ai-send-btn:hover {
    transform: scale(1.1) rotate(-10deg);
    box-shadow: 0 6px 18px rgba(236,72,153,0.5);
}
.ai-send-btn:disabled {
    background: #334155;
    color: #94a3b8;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}
.ai-typing {
    display: flex;
    gap: 5px;
    align-items: center;
    padding: 6px 4px;
}
.ai-dot {
    width: 7px;
    height: 7px;
    background: #a855f7;
    border-radius: 50%;
    animation: ai-bounce 1.4s infinite ease-in-out both;
}
.ai-dot:nth-child(1) { animation-delay: -0.32s; }
.ai-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes ai-bounce {
    0%, 80%, 100% { transform: scale(0); opacity: 0.3; }
    40% { transform: scale(1); opacity: 1; }
}

@media(max-width: 480px) {
    .ai-widget-btn {
        bottom: 84px; 
        right: 16px;
        width: 56px;
        height: 56px;
        font-size: 1.6rem;
    }
    .ai-chat-box {
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
        z-index: 10000;
        transform: translateY(100%);
    }
    .ai-chat-box.open {
        transform: translateY(0);
    }
    .ai-chat-body {
        padding: 16px;
    }
}
</style>

<div class="ai-widget-btn" id="ai-toggle-btn" onclick="toggleAIChat()" title="Tanya Alfatih AI">
    <!-- Menggunakan SVG Icon Robot Modern -->
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 8V4H8"></path>
        <rect width="16" height="12" x="4" y="8" rx="2"></rect>
        <path d="M2 14h2"></path>
        <path d="M20 14h2"></path>
        <path d="M15 13v2"></path>
        <path d="M9 13v2"></path>
    </svg>
</div>

<div class="ai-chat-box" id="ai-chat-box">
    <div class="ai-chat-header">
        <div class="ai-chat-header-title">
            <i class="fa-solid fa-robot"></i> Alfatih AI <span style="font-size: 0.65rem; background: rgba(168,85,247,0.2); padding: 3px 8px; border-radius: 12px; color: #e9d5ff; border: 1px solid rgba(168,85,247,0.3); font-weight: 600;">Beta</span>
        </div>
        <button class="ai-close-btn" onclick="toggleAIChat()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="ai-chat-body" id="ai-chat-body">
        <div class="ai-msg bot">
            Halo! 👋 Saya <b>Alfatih AI</b>. Asisten virtual pintar Anda di workspace ini.<br><br>Ada yang bisa saya bantu hari ini? 😊
        </div>
    </div>
    <div class="ai-image-preview-container" id="ai-image-preview-container">
        <div class="ai-image-preview">
            <img id="ai-preview-img" src="" alt="preview">
            <div id="ai-preview-doc" style="display:none; background:rgba(255,255,255,0.1); padding:12px 16px; border-radius:8px; border:2px solid #a855f7; align-items:center; gap:8px; color: var(--text-main); font-size:0.9rem;">
                <i class="fa-solid fa-file"></i> <span id="ai-preview-doc-name"></span>
            </div>
            <button class="ai-image-remove" onclick="removeAIImage()" title="Hapus Lampiran"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
    <div class="ai-chat-input-area">
        <button class="ai-attach-btn" onclick="document.getElementById('ai-file-input').click()" title="Lampirkan File">
            <i class="fa-solid fa-paperclip"></i>
        </button>
        <input type="file" id="ai-file-input" accept="*/*" style="display:none;" onchange="handleAIImageSelect(event)">
        <input type="text" class="ai-chat-input" id="ai-chat-input" placeholder="Tanya AI..." onkeypress="if(event.key === 'Enter') sendAIMessage()">
        <button class="ai-send-btn" id="ai-send-btn" onclick="sendAIMessage()">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
function toggleAIChat() {
    const box = document.getElementById('ai-chat-box');
    box.classList.toggle('open');
    if(box.classList.contains('open')) {
        setTimeout(() => { document.getElementById('ai-chat-input').focus(); }, 300);
    }
}

function appendAIMessage(text, sender, isRawHtml = false) {
    const body = document.getElementById('ai-chat-body');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-msg ' + sender;
    
    if (!isRawHtml) {
        // Escape HTML to prevent text cut-offs if AI uses < or >
        text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        // Simple formatter (Bold & Italic)
        text = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
        text = text.replace(/\*(.*?)\*/g, '<i>$1</i>');
        text = text.replace(/\n/g, '<br>');
    }

    msgDiv.innerHTML = text;
    body.appendChild(msgDiv);
    body.scrollTop = body.scrollHeight;
}

function showAITyping() {
    const body = document.getElementById('ai-chat-body');
    const msgDiv = document.createElement('div');
    msgDiv.className = 'ai-msg bot ai-typing-indicator';
    msgDiv.innerHTML = '<div class="ai-typing"><div class="ai-dot"></div><div class="ai-dot"></div><div class="ai-dot"></div></div>';
    msgDiv.id = 'ai-typing';
    body.appendChild(msgDiv);
    body.scrollTop = body.scrollHeight;
}

function hideAITyping() {
    const typing = document.getElementById('ai-typing');
    if(typing) typing.remove();
}

let aiAttachedFile = null;

function handleAIImageSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 20 * 1024 * 1024) {
        alert('Ukuran file maksimal 20MB!');
        return;
    }

    aiAttachedFile = file;

    document.getElementById('ai-image-preview-container').style.display = 'block';
    
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('ai-preview-img').src = e.target.result;
            document.getElementById('ai-preview-img').style.display = 'inline-block';
            document.getElementById('ai-preview-doc').style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('ai-preview-img').style.display = 'none';
        document.getElementById('ai-preview-doc').style.display = 'inline-flex';
        document.getElementById('ai-preview-doc-name').innerText = file.name;
    }
}

function removeAIImage() {
    aiAttachedFile = null;
    document.getElementById('ai-file-input').value = '';
    document.getElementById('ai-preview-img').src = '';
    document.getElementById('ai-image-preview-container').style.display = 'none';
}

async function sendAIMessage() {
    const input = document.getElementById('ai-chat-input');
    const btn = document.getElementById('ai-send-btn');
    const text = input.value.trim();
    if(!text && !aiAttachedFile) return;

    let escapedText = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
    let displayMsg = escapedText;
    if (aiAttachedFile) {
        if (aiAttachedFile.type.startsWith('image/')) {
            displayMsg += '<br><img src="'+document.getElementById('ai-preview-img').src+'" style="max-width:100%; border-radius:8px; margin-top:8px;">';
        } else {
            displayMsg += '<br><div style="background:rgba(255,255,255,0.1); padding:8px 12px; border-radius:8px; margin-top:8px; font-size:0.8rem; display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-file"></i> '+aiAttachedFile.name+'</div>';
        }
    }
    appendAIMessage(displayMsg || '<i>[Mengirim Lampiran]</i>', 'user', true);
    
    const formData = new FormData();
    formData.append('message', text);
    if (aiAttachedFile) {
        formData.append('file', aiAttachedFile);
    }

    input.value = '';
    input.disabled = true;
    btn.disabled = true;
    removeAIImage();
    showAITyping();

    try {
        const response = await fetch('api/ai_chat.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        hideAITyping();
        
        if(data.status === 'success') {
            appendAIMessage(data.reply, 'bot');
        } else {
            appendAIMessage("Maaf, " + (data.message || "Gagal menghubungi server AI."), 'bot');
        }
    } catch (e) {
        hideAITyping();
        appendAIMessage("⚠️ Koneksi terputus. Silakan periksa jaringan Anda.", 'bot');
    }

    input.disabled = false;
    btn.disabled = false;
    input.focus();
}

// Fitur Draggable untuk tombol AI
const aiBtn = document.getElementById('ai-toggle-btn');
let isDragging = false;
let startX, startY, initialX, initialY;

aiBtn.addEventListener('mousedown', dragStart);
aiBtn.addEventListener('touchstart', dragStart, {passive: true});

function dragStart(e) {
    if (e.type === "touchstart") {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    } else {
        startX = e.clientX;
        startY = e.clientY;
    }

    const rect = aiBtn.getBoundingClientRect();
    initialX = rect.left;
    initialY = rect.top;

    isDragging = false;

    document.addEventListener('mousemove', drag);
    document.addEventListener('touchmove', drag, {passive: false});
    document.addEventListener('mouseup', dragEnd);
    document.addEventListener('touchend', dragEnd);
}

function drag(e) {
    let currentX, currentY;
    if (e.type === "touchmove") {
        currentX = e.touches[0].clientX;
        currentY = e.touches[0].clientY;
    } else {
        currentX = e.clientX;
        currentY = e.clientY;
    }

    const diffX = currentX - startX;
    const diffY = currentY - startY;

    if (Math.abs(diffX) > 5 || Math.abs(diffY) > 5) {
        isDragging = true;
    }

    if (isDragging) {
        if(e.type === "touchmove") e.preventDefault();
        aiBtn.style.bottom = 'auto';
        aiBtn.style.right = 'auto';
        
        let newX = initialX + diffX;
        let newY = initialY + diffY;
        
        // Batasi agar tidak keluar layar
        const maxX = window.innerWidth - aiBtn.offsetWidth;
        const maxY = window.innerHeight - aiBtn.offsetHeight;
        
        if(newX < 0) newX = 0;
        if(newX > maxX) newX = maxX;
        if(newY < 0) newY = 0;
        if(newY > maxY) newY = maxY;
        
        aiBtn.style.left = newX + 'px';
        aiBtn.style.top = newY + 'px';
    }
}

function dragEnd(e) {
    document.removeEventListener('mousemove', drag);
    document.removeEventListener('touchmove', drag);
    document.removeEventListener('mouseup', dragEnd);
    document.removeEventListener('touchend', dragEnd);
}

// Timpa fungsi toggleAIChat untuk mencegah klik saat sedang drag
const originalToggle = toggleAIChat;
toggleAIChat = function() {
    if (isDragging) {
        setTimeout(() => isDragging = false, 100);
        return;
    }
    originalToggle();
}

// Logika Pop-up Tengah Malam
document.addEventListener("DOMContentLoaded", function() {
    const hour = new Date().getHours();
    if (hour >= 0 && hour < 4) {
        const greeted = localStorage.getItem('midnight_greeted_date');
        const today = new Date().toDateString();
        
        if (greeted !== today) {
            localStorage.setItem('midnight_greeted_date', today);
            
            // Buka widget secara otomatis setelah 1.5 detik
            setTimeout(() => {
                const box = document.getElementById('ai-chat-box');
                if(!box.classList.contains('open')) toggleAIChat();
                
                // Bersihkan pesan default
                const body = document.getElementById('ai-chat-body');
                body.innerHTML = '';
                
                // Kirim prompt tersembunyi untuk memancing AI merespons tengah malam
                sendHiddenPrompt('[SYSTEM_MIDNIGHT_GREETING]');
            }, 1500);
        }
    }
});

async function sendHiddenPrompt(text) {
    showAITyping();
    try {
        const response = await fetch('api/ai_chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        });
        const data = await response.json();
        hideAITyping();
        if(data.status === 'success') {
            appendAIMessage(data.reply, 'bot');
        } else {
            appendAIMessage("Halo! Udah tengah malem nih, kok belum tidur? 😆", 'bot');
        }
    } catch(e) {
        hideAITyping();
        appendAIMessage("Halo! Udah tengah malem nih, kok belum tidur? 😆", 'bot');
    }
}
</script>
