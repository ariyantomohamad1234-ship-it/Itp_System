@extends('layouts.app')
@section('title', 'Pilih Project')
@section('page-title', 'Pilih Project')

@section('styles')
<style>
    .picker-hero {
        text-align: center;
        padding: 2rem 0 1.5rem;
    }
    .picker-hero h2 {
        font-size: 1.75rem;
        font-weight: 900;
        background: linear-gradient(135deg, #1e293b, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.25rem;
    }
    .picker-hero p { color: var(--text-muted); font-size: 0.9rem; }

    .project-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        max-width: 960px;
        margin: 0 auto;
    }

    .project-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        padding: 0;
        text-decoration: none !important;
        color: inherit;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
        display: flex;
        flex-direction: column;
    }
    .project-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -10px rgba(59,130,246,0.15);
        border-color: var(--accent);
    }

    .project-card-header {
        padding: 1.5rem 1.5rem 1.25rem;
        position: relative;
    }
    .project-card-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .project-card:hover .project-card-header::before { opacity: 1; }

    .project-icon {
        width: 52px; height: 52px;
        border-radius: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 1rem;
        box-shadow: 0 6px 18px -4px rgba(0,0,0,0.15);
    }
    .project-code {
        font-size: 0.6rem;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .project-name {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.75rem;
    }

    .project-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 1rem;
    }
    .meta-item {
        font-size: 0.7rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .project-card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        background: #fafbfc;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 6px;
    }
    .progress-bar-track {
        height: 8px;
        background: #e5e7eb;
        border-radius: 100px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 0.8s ease;
        background: linear-gradient(90deg, #3b82f6, #6366f1);
    }
    .progress-bar-fill.complete { background: linear-gradient(90deg, #10b981, #059669); }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        grid-column: 1 / -1;
    }
    .empty-state i { font-size: 4rem; color: #e2e8f0; margin-bottom: 1.5rem; display: block; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="picker-hero">
        <h2>Selamat Datang, {{ session('user')->name }}</h2>
        <p>Pilih project untuk memulai inspeksi</p>
    </div>

    <div class="project-grid">
        @forelse($projects as $i => $p)
        @php
            $colors = [
                'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                'linear-gradient(135deg, #8b5cf6, #6d28d9)',
                'linear-gradient(135deg, #10b981, #059669)',
                'linear-gradient(135deg, #f59e0b, #d97706)',
                'linear-gradient(135deg, #ef4444, #dc2626)',
                'linear-gradient(135deg, #06b6d4, #0891b2)',
            ];
            $color = $colors[$i % count($colors)];
            $prog = $projectProgress[$p->id] ?? ['total' => 0, 'done' => 0, 'percent' => 0];

            $deadlineBadge = '';
            if ($p->deadline) {
                $dl = \Carbon\Carbon::parse($p->deadline);
                $now = now();
                $diff = $now->diffInDays($dl, false);
                if ($diff < 0) $deadlineBadge = '<span class="deadline-badge deadline-over"><i class="fas fa-exclamation-circle"></i> Overdue ' . abs($diff) . ' hari</span>';
                elseif ($diff <= 7) $deadlineBadge = '<span class="deadline-badge deadline-danger"><i class="fas fa-clock"></i> ' . $diff . ' hari lagi</span>';
                elseif ($diff <= 30) $deadlineBadge = '<span class="deadline-badge deadline-warn"><i class="fas fa-clock"></i> ' . $diff . ' hari lagi</span>';
                else $deadlineBadge = '<span class="deadline-badge deadline-ok"><i class="fas fa-calendar-check"></i> ' . $dl->format('d M Y') . '</span>';
            }
        @endphp
        <a href="/modul/{{ $p->id }}" class="project-card">
            <div class="project-card-header">
                <div class="project-icon" style="background: {{ $color }}">
                    <i class="fas fa-ship"></i>
                </div>
                <div class="project-code">{{ $p->kode_project }}</div>
                <div class="project-name">{{ $p->nama_project }}</div>
                <div class="project-meta">
                    @if($p->tanggal_mulai)
                        <span class="meta-item"><i class="fas fa-play-circle"></i> {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</span>
                    @endif
                    @if($p->deadline)
                        <span class="meta-item"><i class="fas fa-flag-checkered"></i> Deadline: {{ \Carbon\Carbon::parse($p->deadline)->format('d M Y') }}</span>
                    @endif
                </div>
                {!! $deadlineBadge !!}
            </div>
            <div class="project-card-footer">
                <div class="progress-label">
                    <span class="text-muted">Progress Inspeksi</span>
                    <span class="{{ $prog['percent'] == 100 ? 'text-success' : 'text-primary' }}">{{ $prog['percent'] }}%</span>
                </div>
                <div class="progress-bar-track">
                    <div class="progress-bar-fill {{ $prog['percent'] == 100 ? 'complete' : '' }}" style="width: {{ $prog['percent'] }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2" style="font-size:0.65rem; color: var(--text-muted)">
                    <span>{{ $prog['done'] }} dari {{ $prog['total'] }} item selesai</span>
                    @if($prog['percent'] == 100)
                        <span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Tuntas</span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5 class="fw-bold text-muted">Belum ada project</h5>
            <p class="text-muted" style="font-size:0.85rem">Anda belum di-assign ke project manapun.<br>Hubungi admin untuk mendapatkan akses.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection