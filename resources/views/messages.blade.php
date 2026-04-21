@extends('layouts.app')
@section('title', 'Pesan')
@section('page-title', 'Pesan Project')

@section('styles')
<style>
    .chat-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 0;
        height: calc(100vh - 130px);
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        overflow: hidden;
    }

    /* === LEFT: Channel List === */
    .channel-list {
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        background: #fafbfc;
    }
    .channel-header {
        padding: 1.25rem 1.25rem 1rem;
        border-bottom: 1px solid var(--border);
    }
    .channel-header h6 { font-weight: 800; margin: 0; font-size: 0.95rem; }
    .channel-header p { font-size: 0.7rem; color: var(--text-muted); margin: 4px 0 0; }

    .channel-items {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .channel-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.75rem;
        border-radius: 0.75rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        margin-bottom: 2px;
    }
    .channel-item:hover { background: #f0f4f8; color: inherit; }
    .channel-item.active {
        background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(99,102,241,0.08));
        border: 1px solid rgba(59,130,246,0.15);
    }

    .channel-avatar {
        width: 40px; height: 40px;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        flex-shrink: 0;
    }

    .channel-info { flex: 1; min-width: 0; }
    .channel-name { font-weight: 700; font-size: 0.8rem; margin-bottom: 2px; }
    .channel-preview {
        font-size: 0.68rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .channel-meta { text-align: right; flex-shrink: 0; }
    .channel-time { font-size: 0.6rem; color: var(--text-muted); }
    .channel-badge {
        background: var(--accent);
        color: #fff;
        font-size: 0.55rem;
        font-weight: 700;
        width: 18px; height: 18px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 4px;
    }

    /* === RIGHT: Chat Area === */
    .chat-area {
        display: flex;
        flex-direction: column;
        background: #fff;
        height: 100%;
        min-height: 0;
        overflow: hidden;
    }

    .chat-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
    }
    .chat-header-info h6 { font-weight: 800; margin: 0; font-size: 0.95rem; }
    .chat-header-info p { font-size: 0.7rem; color: var(--text-muted); margin: 2px 0 0; }

    .member-avatars { display: flex; gap: 0; }
    .member-dot {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
        font-weight: 700;
        color: #fff;
        border: 2px solid #fff;
        margin-left: -6px;
        cursor: default;
    }
    .member-dot:first-child { margin-left: 0; }

    /* Messages */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
    }

    .msg-group { display: flex; gap: 10px; max-width: 75%; animation: msgIn 0.3s ease-out; }
    .msg-group.me { align-self: flex-end; flex-direction: row-reverse; }

    @keyframes msgIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .msg-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .msg-content { flex: 1; }
    .msg-sender {
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 3px;
    }
    .msg-group.me .msg-sender { justify-content: flex-end; }

    .msg-sender .role-tag {
        font-size: 0.5rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 1px 5px;
        border-radius: 3px;
        letter-spacing: 0.5px;
    }

    .msg-bubble {
        background: #f1f5f9;
        border-radius: 0 1rem 1rem 1rem;
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        line-height: 1.5;
        word-wrap: break-word;
        position: relative;
    }
    .msg-group.me .msg-bubble {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: #fff;
        border-radius: 1rem 0 1rem 1rem;
    }

    .msg-time {
        font-size: 0.58rem;
        color: #94a3b8;
        margin-top: 3px;
    }
    .msg-group.me .msg-time { text-align: right; color: rgba(255,255,255,0.6); }

    .date-divider {
        text-align: center;
        margin: 0.75rem 0;
    }
    .date-divider span {
        background: #e2e8f0;
        color: #64748b;
        font-size: 0.6rem;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 1rem;
    }

    /* Input */
    .chat-input {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        background: #fff;
    }
    .input-row {
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }
    .msg-input {
        flex: 1;
        border: 1.5px solid var(--border);
        border-radius: 1rem;
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        font-family: 'Inter', sans-serif;
        resize: none;
        outline: none;
        transition: border-color 0.2s;
        max-height: 120px;
    }
    .msg-input:focus { border-color: var(--accent); }
    .msg-input::placeholder { color: #94a3b8; }

    .btn-send {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .btn-send:hover { transform: scale(1.08); box-shadow: 0 4px 15px rgba(59,130,246,0.35); }
    .btn-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .empty-chat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }
    .empty-chat i { font-size: 3.5rem; color: #e2e8f0; margin-bottom: 1rem; }

    /* Responsive */
    @media (max-width: 768px) {
        .chat-container { grid-template-columns: 1fr; height: calc(100vh - 140px); }
        .channel-list { display: none; }
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="chat-container">
        <!-- LEFT: Project Channels -->
        <div class="channel-list">
            <div class="channel-header">
                <h6><i class="fas fa-comments me-2 text-primary"></i>Project Chat</h6>
                <p>Komunikasi tim inspeksi</p>
            </div>
            <div class="channel-items">
                @forelse($projects as $i => $p)
                @php
                    $colors = ['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444','#06b6d4'];
                    $bg = $colors[$i % count($colors)];
                @endphp
                <a href="/messages?project={{ $p->id }}" class="channel-item {{ $activeProject && $activeProject->id === $p->id ? 'active' : '' }}">
                    <div class="channel-avatar" style="background: {{ $bg }}">
                        <i class="fas fa-ship"></i>
                    </div>
                    <div class="channel-info">
                        <div class="channel-name">{{ $p->kode_project }}</div>
                        <div class="channel-preview">
                            @if($p->latest_message)
                                {{ $p->latest_message->user->name }}: {{ Str::limit($p->latest_message->message, 30) }}
                            @else
                                Belum ada pesan
                            @endif
                        </div>
                    </div>
                    <div class="channel-meta">
                        @if($p->latest_message)
                            <div class="channel-time">{{ $p->latest_message->created_at->format('H:i') }}</div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="text-center py-4 text-muted" style="font-size:0.8rem">
                    <i class="fas fa-inbox fa-2x mb-2 d-block" style="color:#e2e8f0"></i>
                    Tidak ada project
                </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Chat -->
        <div class="chat-area">
            @if($activeProject)
            <div class="chat-header">
                <div class="chat-header-info">
                    <h6><i class="fas fa-ship me-2 text-primary"></i>{{ $activeProject->nama_project }}</h6>
                    <p>{{ $activeProject->kode_project }} &bull; {{ $members->count() }} anggota</p>
                </div>
                <div class="member-avatars">
                    @php
                        $memberColors = ['#3b82f6','#8b5cf6','#10b981','#f59e0b','#ef4444'];
                    @endphp
                    @foreach($members->take(5) as $mi => $m)
                        <div class="member-dot" style="background:{{ $memberColors[$mi % 5] }}" title="{{ $m->name }} ({{ strtoupper($m->role) }})">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                    @endforeach
                    @if($members->count() > 5)
                        <div class="member-dot" style="background:#64748b">+{{ $members->count() - 5 }}</div>
                    @endif
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                @if($messages->isEmpty())
                <div class="empty-chat">
                    <i class="far fa-comments"></i>
                    <p class="fw-bold">Belum ada pesan</p>
                    <p style="font-size:0.8rem">Mulai percakapan dengan tim Anda</p>
                </div>
                @else
                    @php $lastDate = null; @endphp
                    @foreach($messages as $msg)
                        @php
                            $msgDate = $msg->created_at->format('Y-m-d');
                            $isMe = $msg->user_id === session('user')->id;
                            $roleColors = ['admin'=>'#f59e0b','yard'=>'#3b82f6','class'=>'#10b981','os'=>'#8b5cf6','stat'=>'#ef4444'];
                            $rc = $roleColors[$msg->user->role] ?? '#64748b';
                        @endphp

                        @if($lastDate !== $msgDate)
                            <div class="date-divider">
                                <span>{{ $msg->created_at->format('d M Y') }}</span>
                            </div>
                            @php $lastDate = $msgDate; @endphp
                        @endif

                        <div class="msg-group {{ $isMe ? 'me' : '' }}" data-msg-id="{{ $msg->id }}">
                            @if(!$isMe)
                            <div class="msg-avatar" style="background:{{ $rc }}">{{ strtoupper(substr($msg->user->name,0,1)) }}</div>
                            @endif
                            <div class="msg-content">
                                @if(!$isMe)
                                <div class="msg-sender">
                                    {{ $msg->user->name }}
                                    <span class="role-tag" style="background:{{ $rc }}20;color:{{ $rc }}">{{ strtoupper($msg->user->role) }}</span>
                                </div>
                                @endif
                                <div class="msg-bubble">{{ $msg->message }}</div>
                                <div class="msg-time">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="chat-input">
                <div class="input-row">
                    <textarea class="msg-input" id="msgInput" rows="1" placeholder="Tulis pesan..." onkeydown="handleKey(event)"></textarea>
                    <button class="btn-send" id="btnSend" onclick="sendMessage()">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            @else
            <div class="empty-chat">
                <i class="far fa-comments"></i>
                <p class="fw-bold">Pilih project untuk mulai chat</p>
                <p style="font-size:0.8rem">Pilih channel project di sebelah kiri</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const userId = {{ session('user')->id }};
const projectId = {{ $activeProject ? $activeProject->id : 'null' }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Auto-scroll to bottom
function scrollToBottom() {
    const el = document.getElementById('chatMessages');
    if (el) el.scrollTop = el.scrollHeight;
}
scrollToBottom();

// Auto-resize textarea
const msgInput = document.getElementById('msgInput');
if (msgInput) {
    msgInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function sendMessage() {
    const input = document.getElementById('msgInput');
    const msg = input.value.trim();
    if (!msg || !projectId) return;

    const btn = document.getElementById('btnSend');
    btn.disabled = true;

    fetch('/messages/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ project_id: projectId, message: msg }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            appendMessage(res.message, true);
            input.value = '';
            input.style.height = 'auto';
        }
        btn.disabled = false;
    })
    .catch(() => { btn.disabled = false; });
}

const roleColors = { admin:'#f59e0b', yard:'#3b82f6', class:'#10b981', os:'#8b5cf6', stat:'#ef4444' };

function appendMessage(m, isMe) {
    const container = document.getElementById('chatMessages');
    // Remove empty state
    const empty = container.querySelector('.empty-chat');
    if (empty) empty.remove();

    const rc = roleColors[m.user_role] || '#64748b';
    let html = `<div class="msg-group ${isMe ? 'me' : ''}" data-msg-id="${m.id}">`;
    if (!isMe) {
        html += `<div class="msg-avatar" style="background:${rc}">${m.user_name.charAt(0).toUpperCase()}</div>`;
    }
    html += '<div class="msg-content">';
    if (!isMe) {
        html += `<div class="msg-sender">${m.user_name} <span class="role-tag" style="background:${rc}20;color:${rc}">${m.user_role.toUpperCase()}</span></div>`;
    }
    html += `<div class="msg-bubble">${escapeHtml(m.message)}</div>`;
    html += `<div class="msg-time">${m.created_at}</div>`;
    html += '</div></div>';

    container.insertAdjacentHTML('beforeend', html);
    scrollToBottom();
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Polling for new messages every 3 seconds
let lastMsgId = 0;
document.querySelectorAll('[data-msg-id]').forEach(el => {
    const id = parseInt(el.dataset.msgId);
    if (id > lastMsgId) lastMsgId = id;
});

if (projectId) {
    setInterval(() => {
        fetch(`/messages/fetch?project_id=${projectId}&after_id=${lastMsgId}`)
            .then(r => r.json())
            .then(res => {
                if (res.messages && res.messages.length > 0) {
                    res.messages.forEach(m => {
                        if (m.user_id !== userId) {
                            appendMessage(m, false);
                        }
                        if (m.id > lastMsgId) lastMsgId = m.id;
                    });
                }
            })
            .catch(() => {});
    }, 3000);
}
</script>
@endsection
