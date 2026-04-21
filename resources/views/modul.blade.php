@extends('layouts.app')
@section('title', 'Modul - ' . $project->nama_project)
@section('page-title', 'Daftar Modul')

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
        background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 60%);
        border-radius: 50%;
    }
    .page-header-card h4 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .page-header-card p { color: #94a3b8; font-size: 0.85rem; margin: 0; position: relative; }

    .card-modul {
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
    .card-modul:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px -8px rgba(0,0,0,0.1);
        border-color: var(--accent);
    }
    .card-modul-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .modul-icon {
        width: 48px; height: 48px;
        border-radius: 0.75rem;
        background: var(--accent-glow);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .modul-icon i { font-size: 1.15rem; color: var(--accent); }
    .card-modul:hover .modul-icon {
        background: var(--accent);
        transform: scale(1.1);
    }
    .card-modul:hover .modul-icon i { color: #fff; }
    .modul-number {
        font-size: 0.55rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .modul-name {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .card-modul-footer {
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
                <h4><i class="fas fa-ship me-2"></i>{{ $project->nama_project }}</h4>
                <p>Kode: <strong>{{ $project->kode_project }}</strong>
                @if($project->deadline)
                    @php
                        $dl = \Carbon\Carbon::parse($project->deadline);
                        $diff = now()->diffInDays($dl, false);
                    @endphp
                    &nbsp;&bull;&nbsp;
                    <i class="fas fa-flag-checkered me-1"></i>Deadline: {{ $dl->format('d M Y') }}
                    @if($diff < 0)
                        <span class="deadline-badge deadline-over ms-2"><i class="fas fa-exclamation-circle"></i> Overdue {{ abs($diff) }} hari</span>
                    @elseif($diff <= 7)
                        <span class="deadline-badge deadline-danger ms-2"><i class="fas fa-clock"></i> {{ $diff }} hari lagi!</span>
                    @elseif($diff <= 30)
                        <span class="deadline-badge deadline-warn ms-2"><i class="fas fa-clock"></i> {{ $diff }} hari lagi</span>
                    @endif
                @endif
                </p>
            </div>
            <a href="/dashboard" class="btn-back" style="color:#cbd5e1;border-color:rgba(255,255,255,0.15)"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="row g-4" id="modul-grid">
        @forelse($modul as $m)
        @php $mp = $modulProgress[$m->id] ?? ['total' => 0, 'done' => 0, 'percent' => 0]; @endphp
        <div class="col-6 col-md-4 col-lg-3 modul-item">
            <a href="/blok/{{ $m->id }}" class="card-modul">
                <div class="card-modul-body">
                    <div class="modul-icon"><i class="fas fa-box-archive"></i></div>
                    <div class="modul-number">Modul {{ $loop->iteration }}</div>
                    <div class="modul-name">{{ $m->nama_modul }}</div>
                    @if($m->deskripsi)
                        <p class="text-muted mb-0" style="font-size:0.75rem; flex:1">{{ Str::limit($m->deskripsi, 60) }}</p>
                    @endif
                </div>
                <div class="card-modul-footer">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted" style="font-size:0.65rem">{{ $mp['done'] }}/{{ $mp['total'] }} item</span>
                        <span class="fw-bold {{ $mp['percent'] == 100 ? 'text-success' : 'text-primary' }}" style="font-size:0.7rem">{{ $mp['percent'] }}%</span>
                    </div>
                    <div class="progress-mini">
                        <div class="fill {{ $mp['percent'] == 100 ? 'bg-success' : 'bg-primary' }}" style="width:{{ $mp['percent'] }}%"></div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-folder-open fa-3x mb-3" style="color:#e2e8f0"></i>
            <p class="text-muted">Belum ada modul di project ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection