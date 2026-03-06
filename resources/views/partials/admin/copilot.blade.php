<style>
    /* Copilot AI Chat Widget Styles */
    #copilot-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }
    #copilot-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    #copilot-btn:hover {
        transform: scale(1.05);
    }
    #copilot-btn svg {
        width: 30px;
        height: 30px;
    }
    #copilot-chat {
        display: none;
        position: absolute;
        bottom: 75px;
        right: 0;
        width: 350px;
        height: 500px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        visibility: hidden;
        opacity: 0;
        transition: visibility 0s, opacity 0.3s ease;
    }
    #copilot-chat.active {
        visibility: visible;
        opacity: 1;
        display: flex;
    }
    #copilot-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #copilot-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: white;
    }
    #copilot-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
    }
    #copilot-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .c-msg {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.4;
    }
    .c-msg.bot {
        background: #e9ecef;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }
    .c-msg.user {
        background: #667eea;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    #copilot-input-area {
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }
    #copilot-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 15px;
        outline: none;
        transition: border-color 0.3s;
    }
    #copilot-input:focus {
        border-color: #667eea;
    }
    #copilot-send {
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
</style>

<div id="copilot-widget">
    <div id="copilot-chat">
        <div id="copilot-header">
            <h5><i data-feather="cpu" style="width:18px; height:18px; margin-right:5px;"></i> ERP Copilot</h5>
            <button id="copilot-close"><i data-feather="x"></i></button>
        </div>
        <div id="copilot-messages">
            <div class="c-msg bot">Bonjour ! Je suis l'assistant IA de votre ERP. Comment puis-je vous aider aujourd'hui ?</div>
        </div>
        <div id="copilot-input-area">
            <input type="text" id="copilot-input" placeholder="Posez une question..." />
            <button id="copilot-send"><i data-feather="send" style="width:16px;"></i></button>
        </div>
    </div>
    <div id="copilot-btn">
        <i data-feather="message-circle" style="color:white; width: 30px; height: 30px;"></i>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof feather !== 'undefined') {
            feather.replace();
        }
        const btn = document.getElementById('copilot-btn');
        const chat = document.getElementById('copilot-chat');
        const closeBtn = document.getElementById('copilot-close');
        const sendBtn = document.getElementById('copilot-send');
        const input = document.getElementById('copilot-input');
        const messages = document.getElementById('copilot-messages');

        function toggleChat() {
            chat.classList.toggle('active');
            if(chat.classList.contains('active')){
                chat.style.display = 'flex';
                setTimeout(() => chat.style.opacity = '1', 10);
                input.focus();
            } else {
                chat.style.opacity = '0';
                setTimeout(() => chat.style.display = 'none', 300);
            }
        }

        btn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);

        function addMessage(text, sender) {
            const msg = document.createElement('div');
            msg.className = `c-msg ${sender}`;
            msg.innerText = text;
            messages.appendChild(msg);
            messages.scrollTop = messages.scrollHeight;
        }

        function sendMessage() {
            const text = input.value.trim();
            if(!text) return;
            addMessage(text, 'user');
            input.value = '';
            
            // Show loading
            const loading = document.createElement('div');
            loading.className = 'c-msg bot';
            loading.id = 'copilot-loading';
            loading.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Réflexion...';
            messages.appendChild(loading);
            messages.scrollTop = messages.scrollHeight;

            fetch('{{ route("copilot.ask") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('copilot-loading').remove();
                addMessage(data.reply, 'bot');
            })
            .catch(err => {
                document.getElementById('copilot-loading').remove();
                addMessage('Erreur de connexion à l\'assistant.', 'bot');
            });
        }

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') sendMessage();
        });
    });
</script>
