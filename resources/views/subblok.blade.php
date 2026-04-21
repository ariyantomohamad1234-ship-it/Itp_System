@extends('layouts.app')
@section('title', 'Sub Blok - ' . $blok->nama_blok)
@section('page-title', 'Daftar Sub Blok')

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
        background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, transparent 60%);
        border-radius: 50%;
    }
    .page-header-card h4 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .page-header-card p { color: #94a3b8; font-size: 0.85rem; margin: 0; position: relative; }

    .card-sub {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 0.75rem;
        text-decoration: none !important;
        color: inherit;
        display: block;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .card-sub::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
        transition: background 0.3s;
    }
    .card-sub.status-done-card::before { background: var(--success); }
    .card-sub.status-progress-card::before { background: var(--warning); }
    .card-sub.status-pending-card::before { background: #cbd5e1; }

    .card-sub:hover {
        transform: translateX(6px);
        box-shadow: 0 10px 25px -8px rgba(0,0,0,0.08);
        border-color: var(--accent);
    }

    .progress-bar-custom {
        height: 8px;
        background: #f1f5f9;
        border-radius: 100px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 100px;
        transition: width 0.6s ease;
    }
    .badge-status {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 2rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .status-done { background: #dcfce7; color: #166534; }
    .status-progress { background: #fef3c7; color: #854d0e; }
    .status-pending { background: #f1f5f9; color: #475569; }

    .sub-icon {
        width: 36px; height: 36px;
        border-radius: 0.625rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    @php
        $modulId = \DB::table('bloks')->where('id', $blok->id)->value('modul_id');
    @endphp
    <div class="page-header-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-th-large me-2"></i>{{ $blok->nama_blok }}</h4>
                <p>Pilih sub blok untuk melihat kode inspeksi</p>
            </div>
            <a href="/blok/{{ $modulId }}" class="btn-back" style="color:#cbd5e1;border-color:rgba(255,255,255,0.15)"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    @forelse($subbloks as $s)
    @php
        $p = $progress[$s->id] ?? ['percent' => 0, 'done' => 0, 'total' => 0, 'approved' => 0];
        $statusClass = $p['percent'] == 100 ? 'status-done-card' : ($p['percent'] > 0 ? 'status-progress-card' : 'status-pending-card');
    @endphp
    <a href="/assembly/{{ $s->id }}" class="card-sub {{ $statusClass }}">
        <div class="d-flex align-items-center">
            <div class="sub-icon" style="background: {{ $p['percent'] == 100 ? '#dcfce7' : ($p['percent'] > 0 ? '#fef3c7' : '#f1f5f9') }}">
                <i class="fas {{ $p['percent'] == 100 ? 'fa-check-circle text-success' : ($p['percent'] > 0 ? 'fa-spinner text-warning' : 'fa-layer-group text-muted') }}"></i>
            </div>
            <div style="flex:1; min-width:0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold" style="font-size:1rem">{{ $s->nama_sub_blok }}</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            <i class="far fa-check-circle me-1"></i>{{ $p['done'] }} dari {{ $p['total'] }} selesai
                            @if($p['approved'] > 0)
                                &nbsp;&bull;&nbsp; <span class="text-success"><i class="fas fa-shield-alt me-1"></i>{{ $p['approved'] }} ACC</span>
                            @endif
                        </div>
                    </div>
                    @if($p['percent'] == 100)
                        <span class="badge-status status-done"><i class="fas fa-check me-1"></i>Selesai</span>
                    @elseif($p['percent'] > 0)
                        <span class="badge-status status-progress"><i class="fas fa-spinner me-1"></i>Progress</span>
                    @else
                        <span class="badge-status status-pending">Pending</span>
                    @endif
                </div>
                <div class="mt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted" style="font-size:0.7rem">{{ $p['percent'] }}% Selesai</span>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill {{ $p['percent'] == 100 ? 'bg-success' : ($p['percent'] > 0 ? 'bg-warning' : 'bg-secondary') }}"
                             style="width: {{ $p['percent'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-folder-open fa-3x mb-3" style="color:#e2e8f0"></i>
        <p class="text-muted">Belum ada sub blok di {{ $blok->nama_blok }}.</p>
    </div>
    @endforelse
</div>
@endsection