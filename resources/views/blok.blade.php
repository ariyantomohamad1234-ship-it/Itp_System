@extends('layouts.app')
@section('title', 'Blok - ' . $modul->nama_modul)
@section('page-title', 'Daftar Blok')

@section('styles')
<style>
    .page-header-card {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 1.25rem;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .page-header-card::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 60%);
        border-radius: 50%;
    }
    .page-header-card h4 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .page-header-card p { color: #94a3b8; font-size: 0.85rem; margin: 0; position: relative; }

    .card-blok {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 0;
        text-decoration: none !important;
        color: inherit;
        display: flex;
        flex-direction: column;
        transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
        height: 100%;
    }
    .card-blok:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px -8px rgba(0,0,0,0.1);
        border-color: var(--accent);
    }
    .card-blok-body {
        padding: 2rem 1.5rem 1rem;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .blok-icon {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: var(--accent-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .blok-icon i { font-size: 1.35rem; color: var(--accent); }
    .card-blok:hover .blok-icon {
        background: var(--accent);
        transform: scale(1.1) rotate(5deg);
    }
    .card-blok:hover .blok-icon i { color: #fff; }
    .blok-name { font-weight: 800; font-size: 1.1rem; letter-spacing: 0.5px; }
    .card-blok-footer {
        padding: 0.875rem 1.5rem;
        border-top: 1px solid var(--border);
        background: #fafbfc;
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="page-header-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-box-archive me-2"></i>{{ $modul->nama_modul }}</h4>
                <p>Pilih blok untuk melihat sub blok
                @if($project->deadline)
                    @php
                        $dl = \Carbon\Carbon::parse($project->deadline);
                        $diff = now()->diffInDays($dl, false);
                    @endphp
                    &nbsp;&bull;&nbsp;
                    <i class="fas fa-flag-checkered me-1"></i>Deadline: {{ $dl->format('d M Y') }}
                    @if($diff < 0)
                        <span class="deadline-badge deadline-over ms-2"><i class="fas fa-exclamation-circle"></i> Overdue</span>
                    @elseif($diff <= 14)
                        <span class="deadline-badge deadline-warn ms-2"><i class="fas fa-clock"></i> {{ $diff }} hari lagi</span>
                    @endif
                @endif
                </p>
            </div>
            <a href="/modul/{{ $modul->project_id }}" class="btn-back" style="color:#cbd5e1;border-color:rgba(255,255,255,0.15)"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="row g-4">
        @forelse($bloks as $b)
        @php $bp = $blokProgress[$b->id] ?? ['total' => 0, 'done' => 0, 'percent' => 0]; @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <a href="/subblok/{{ $b->id }}" class="card-blok">
                <div class="card-blok-body">
                    <div class="blok-icon"><i class="fas fa-th-large"></i></div>
                    <div class="blok-name">{{ $b->nama_blok }}</div>
                </div>
                <div class="card-blok-footer">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted" style="font-size:0.65rem">{{ $bp['done'] }}/{{ $bp['total'] }} item</span>
                        <span class="fw-bold {{ $bp['percent'] == 100 ? 'text-success' : 'text-primary' }}" style="font-size:0.7rem">{{ $bp['percent'] }}%</span>
                    </div>
                    <div class="progress-mini">
                        <div class="fill {{ $bp['percent'] == 100 ? 'bg-success' : 'bg-primary' }}" style="width:{{ $bp['percent'] }}%"></div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-cube fa-3x mb-3" style="color:#e2e8f0"></i>
            <p class="text-muted">Belum ada blok di modul ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection