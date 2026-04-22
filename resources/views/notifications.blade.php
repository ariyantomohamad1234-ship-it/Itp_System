@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('styles')
<style>
    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }
    .notif-list { list-style: none; padding: 0; margin: 0; }
    .notif-item {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: all 0.25s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .notif-item:hover {
        background: #f8fafc;
        box-shadow: 0 4px 12px -4px rgba(0,0,0,0.06);
        transform: translateX(4px);
    }
    .notif-item.unread {
        border-left: 3px solid var(--accent);
        background: rgba(59,130,246,0.03);
    }
    .notif-icon {
        width: 40px; height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .notif-icon.submit { background: #dbeafe; color: #1e40af; }
    .notif-icon.approved { background: #dcfce7; color: #166534; }
    .notif-icon.needs_revision, .notif-icon.rejected { background: #fee2e2; color: #991b1b; }
    .notif-body { flex: 1; min-width: 0; }
    .notif-title { font-weight: 700; font-size: 0.85rem; margin-bottom: 2px; }
    .notif-msg { font-size: 0.8rem; color: var(--text-muted); line-height: 1.4; }
    .notif-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
        font-size: 0.65rem;
        color: #94a3b8;
    }
    .notif-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--accent);
        flex-shrink: 0;
    }
    .empty-notif {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-notif i { font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem; display: block; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="notif-header">
        <h5 class="fw-bold mb-0"><i class="fas fa-bell me-2 text-primary"></i>Semua Notifikasi</h5>
        <button class="btn btn-accent btn-sm" onclick="markAllRead()" id="markAllBtn">
            <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
        </button>
    </div>

    <ul class="notif-list">
        @forelse($notifications as $n)
        @php
            $iconClass = match($n->type) {
                'submit' => 'submit',
                'approved' => 'approved',
                'needs_revision', 'rejected' => 'needs_revision',
                default => 'submit',
            };
            $iconSymbol = match($n->type) {
                'submit' => 'fa-file-upload',
                'approved' => 'fa-check-circle',
                'needs_revision', 'rejected' => 'fa-times-circle',
                default => 'fa-bell',
            };
        @endphp
        <li>
            <a href="{{ $n->link ?? '#' }}" class="notif-item {{ !$n->is_read ? 'unread' : '' }}" onclick="markRead({{ $n->id }})">
                <div class="notif-icon {{ $iconClass }}">
                    <i class="fas {{ $iconSymbol }}"></i>
                </div>
                <div class="notif-body">
                    <div class="notif-title">{{ $n->title }}</div>
                    <div class="notif-msg">{{ $n->message }}</div>
                    <div class="notif-meta">
                        @if($n->sender)
                            <span><i class="fas fa-user me-1"></i>{{ $n->sender->name }}</span>
                            <span>•</span>
                        @endif
                        <span><i class="fas fa-clock me-1"></i>{{ $n->created_at->diffForHumans() }}</span>
                        @if(!$n->is_read)
                            <span class="notif-dot" title="Belum dibaca"></span>
                        @endif
                    </div>
                </div>
            </a>
        </li>
        @empty
        <div class="empty-notif">
            <i class="fas fa-bell-slash"></i>
            <h6 class="fw-bold text-muted">Belum ada notifikasi</h6>
            <p class="text-muted" style="font-size:0.85rem">Notifikasi akan muncul saat ada aktivitas terkait inspeksi Anda.</p>
        </div>
        @endforelse
    </ul>

    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function markRead(id) {
    fetch(`/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    }).catch(() => {});
}

function markAllRead() {
    const btn = document.getElementById('markAllBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';

    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(() => {
        document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        document.querySelectorAll('.notif-dot').forEach(el => el.remove());
        btn.innerHTML = '<i class="fas fa-check-double me-1"></i>Semua Terbaca';
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca';
    });
}
</script>
@endsection
