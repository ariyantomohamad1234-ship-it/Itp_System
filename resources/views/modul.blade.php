@extends('layouts.app')
@section('title', 'Modul - ' . $project->nama_project)
@section('page-title', 'Daftar Modul')

@section('styles')
<style>
    .page-header-card {
        background: linear-gradient(135deg, #dc2626, #991b1b);
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
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
        border-radius: 50%;
    }
    .page-header-card h4 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .page-header-card p { color: #94a3b8; font-size: 0.85rem; margin: 0; position: relative; }
    .day-counter {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 4px 12px;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
    }

    /* === PHASE LABEL === */
    .phase-separator {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 1.75rem 0 1rem;
        padding: 0 4px;
    }
    .phase-separator .phase-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        color: #64748b;
        white-space: nowrap;
        padding: 5px 14px;
        background: #f1f5f9;
        border-radius: 2rem;
        border: 1px solid #e2e8f0;
    }
    .phase-separator .phase-label.active-phase {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.2);
    }
    .phase-separator .phase-label.completed-phase {
        background: rgba(16,185,129,0.08);
        color: #10b981;
        border-color: rgba(16,185,129,0.2);
    }
    .phase-separator::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, #e2e8f0, transparent);
    }

    /* === MODULE CARD === */
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
        position: relative;
    }

    /* Active state */
    .card-modul.state-active {
        border-color: rgba(220, 38, 38, 0.3);
        box-shadow: 0 0 0 1px rgba(220, 38, 38, 0.05), 0 4px 12px rgba(220, 38, 38, 0.08);
    }
    .card-modul.state-active:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px -8px rgba(220,38,38,0.15);
        border-color: var(--accent);
    }
    .card-modul.state-active::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #dc2626, #ef4444);
        z-index: 1;
    }

    /* Completed state */
    .card-modul.state-completed {
        opacity: 0.7;
        border-color: rgba(16,185,129,0.2);
        background: #fafffe;
    }
    .card-modul.state-completed:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px -6px rgba(16,185,129,0.1);
        opacity: 0.85;
    }
    .card-modul.state-completed::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, #10b981, #059669);
        z-index: 1;
    }

    /* Locked state */
    .card-modul.state-locked {
        opacity: 0.5;
        pointer-events: none;
        filter: grayscale(0.4);
        background: #fafbfc;
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
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: all 0.3s;
    }
    .modul-icon i { font-size: 1.15rem; }

    /* Icon colors by state */
    .modul-icon.icon-active { background: rgba(220,38,38,0.08); }
    .modul-icon.icon-active i { color: #dc2626; }
    .card-modul.state-active:hover .modul-icon.icon-active {
        background: #dc2626;
        transform: scale(1.1);
    }
    .card-modul.state-active:hover .modul-icon.icon-active i { color: #fff; }

    .modul-icon.icon-completed { background: rgba(16,185,129,0.1); }
    .modul-icon.icon-completed i { color: #10b981; }

    .modul-icon.icon-locked { background: #f3f4f6; }
    .modul-icon.icon-locked i { color: #9ca3af; }

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

    /* State badges */
    .state-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.62rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 0.5rem;
        margin-top: auto;
    }
    .badge-active {
        background: rgba(220, 38, 38, 0.08);
        color: #dc2626;
        border: 1px solid rgba(220, 38, 38, 0.15);
    }
    .badge-completed {
        background: rgba(16,185,129,0.08);
        color: #059669;
        border: 1px solid rgba(16,185,129,0.15);
    }
    .badge-locked {
        background: #f9fafb;
        color: #9ca3af;
        border: 1px solid #e5e7eb;
    }

    /* Time progress bar */
    .time-progress { margin-top: 0.75rem; }
    .time-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.6rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .time-bar {
        height: 4px;
        background: #f1f5f9;
        border-radius: 100px;
        overflow: hidden;
    }
    .time-bar .fill {
        height: 100%;
        border-radius: 100px;
        background: linear-gradient(90deg, #dc2626, #ef4444);
        transition: width 0.6s ease;
    }

    .card-modul-footer {
        padding: 0.875rem 1.5rem;
        border-top: 1px solid var(--border);
        background: #fafbfc;
    }

    .schedule-badge {
        font-size: 0.6rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-card { padding: 1.25rem; border-radius: 1rem; }
        .page-header-card h4 { font-size: 1.1rem; }
        .card-modul-body { padding: 1.25rem; }
        .card-modul-footer { padding: 0.75rem 1.25rem; }
        .modul-name { font-size: 0.9rem; }
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="page-header-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
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
                @if($dayN)
                    <div class="mt-2">
                        <span class="day-counter"><i class="fas fa-calendar-day"></i> Hari ke-{{ $dayN }}</span>
                    </div>
                @endif
            </div>
            <a href="/dashboard" class="btn-back" style="color:#cbd5e1;border-color:rgba(255,255,255,0.15)"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    @php
        // Group modules by phase for display
        $lastPhase = null;
        $phaseMap = [];
        $phaseCounter = 0;
        $sortedModuls = collect($modul)->values();

        foreach ($sortedModuls as $m) {
            $lock = $modulLock[$m->id] ?? ['state' => 'active', 'start_day' => null];
            $startDay = $lock['start_day'] ?? 0;
            if ($startDay !== $lastPhase) {
                $phaseCounter++;
                $lastPhase = $startDay;
            }
            $phaseMap[$m->id] = $phaseCounter;
        }
    @endphp

    @php $currentPhase = 0; @endphp
    @forelse($sortedModuls as $m)
    @php
        $mp = $modulProgress[$m->id] ?? ['total' => 0, 'done' => 0, 'percent' => 0];
        $lock = $modulLock[$m->id] ?? ['state' => 'active'];
        $state = $lock['state'];
        $phase = $phaseMap[$m->id] ?? 0;
    @endphp

    @if($phase !== $currentPhase)
        @php $currentPhase = $phase; @endphp
        @if($loop->index > 0)
            </div>
        @endif

        <div class="phase-separator">
            <span class="phase-label {{ $state === 'active' ? 'active-phase' : ($state === 'completed' ? 'completed-phase' : '') }}">
                @if($state === 'active')
                    <i class="fas fa-bolt"></i>
                @elseif($state === 'completed')
                    <i class="fas fa-check-circle"></i>
                @else
                    <i class="fas fa-lock"></i>
                @endif
                Fase {{ $currentPhase }}
                @if($lock['start_day'] ?? null)
                    — Hari {{ $lock['start_day'] }}{{ isset($lock['end_day']) ? '-'.$lock['end_day'] : '' }}
                @endif
            </span>
        </div>
        <div class="row g-4">
    @endif

    <div class="col-6 col-md-4 col-lg-3">
        @if($state === 'locked')
        <div class="card-modul state-locked">
        @elseif($state === 'completed')
        <a href="/blok/{{ $m->id }}" class="card-modul state-completed">
        @else
        <a href="/blok/{{ $m->id }}" class="card-modul state-active">
        @endif
            <div class="card-modul-body">
                <div class="modul-icon icon-{{ $state }}">
                    @if($state === 'completed')
                        <i class="fas fa-check-double"></i>
                    @elseif($state === 'locked')
                        <i class="fas fa-lock"></i>
                    @else
                        <i class="fas fa-box-archive"></i>
                    @endif
                </div>
                <div class="modul-number">
                    @php
                        preg_match('/Modul\s*(\d+)/i', $m->nama_modul, $matches);
                        $modulNum = $matches[1] ?? $loop->iteration;
                    @endphp
                    Modul {{ $modulNum }}
                </div>
                <div class="modul-name">{{ $m->nama_modul }}</div>

                @if($state === 'active')
                    <div class="state-badge badge-active">
                        <i class="fas fa-bolt"></i> Sedang Berjalan
                        @if(isset($lock['days_remaining']))
                            — {{ $lock['days_remaining'] }} hari tersisa
                        @endif
                    </div>
                    @if(isset($lock['time_percent']))
                    <div class="time-progress">
                        <div class="time-progress-label">
                            <span>Hari {{ $lock['days_elapsed'] ?? '-' }} dari {{ $lock['duration_days'] }}</span>
                            <span>{{ $lock['time_percent'] }}%</span>
                        </div>
                        <div class="time-bar">
                            <div class="fill" style="width:{{ $lock['time_percent'] }}%"></div>
                        </div>
                    </div>
                    @endif
                @elseif($state === 'completed')
                    <div class="state-badge badge-completed">
                        <i class="fas fa-check-circle"></i> Selesai
                        @if(isset($lock['days_since_completed']))
                            — {{ $lock['days_since_completed'] }} hari lalu
                        @endif
                    </div>
                @elseif($state === 'locked')
                    <div class="state-badge badge-locked">
                        <i class="fas fa-lock"></i> Terkunci
                    </div>
                    @if(isset($lock['unlock_date']))
                        <div class="schedule-badge">
                            <i class="fas fa-calendar-alt"></i>
                            Tersedia: {{ $lock['unlock_date'] }}
                            @if(isset($lock['days_until_unlock']))
                                ({{ $lock['days_until_unlock'] }} hari lagi)
                            @endif
                        </div>
                    @endif
                @endif

                @if($lock['start_day'] ?? null)
                    <div class="schedule-badge">
                        <i class="fas fa-clock"></i>
                        Hari {{ $lock['start_day'] }}–{{ ($lock['end_day'] ?? ($lock['start_day'] + ($lock['duration_days'] ?? 0) - 1)) }}
                        ({{ $lock['duration_days'] ?? '?' }} hari)
                    </div>
                @endif
            </div>

            <div class="card-modul-footer">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted" style="font-size:0.65rem">{{ $mp['done'] }}/{{ $mp['total'] }} item</span>
                    <span class="fw-bold {{ $mp['percent'] == 100 ? 'text-success' : ($state === 'active' ? 'text-primary' : 'text-muted') }}" style="font-size:0.7rem">{{ $mp['percent'] }}%</span>
                </div>
                <div class="progress-mini">
                    <div class="fill {{ $mp['percent'] == 100 ? 'bg-success' : ($state === 'active' ? 'bg-primary' : 'bg-secondary') }}" style="width:{{ $mp['percent'] }}%"></div>
                </div>
            </div>
        @if($state === 'locked')
        </div>
        @else
        </a>
        @endif
    </div>

    @if($loop->last)
        </div>
    @endif

    @empty
    <div class="row">
        <div class="col-12 text-center py-5">
            <i class="fas fa-folder-open fa-3x mb-3" style="color:#e2e8f0"></i>
            <p class="text-muted">Belum ada modul di project ini.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection