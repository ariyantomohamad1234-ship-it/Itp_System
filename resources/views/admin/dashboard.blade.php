@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Admin')

@section('styles')
<style>
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-card .stat-val { font-size: 1.75rem; font-weight: 900; margin-top: 0.5rem; }
    .stat-card .stat-label { font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:#1e40af"><i class="fas fa-users"></i></div>
            <div class="stat-label">Total User</div>
            <div class="stat-val">{{ $totalUsers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8ff;color:#7c3aed"><i class="fas fa-project-diagram"></i></div>
            <div class="stat-label">Total Project</div>
            <div class="stat-val">{{ $totalProjects }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;color:#166534"><i class="fas fa-folder-open"></i></div>
            <div class="stat-label">Total Modul</div>
            <div class="stat-val">{{ $totalModuls }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ffedd5;color:#c2410c"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-label">Total ITP</div>
            <div class="stat-val">{{ $totalItps }}</div>
        </div>
    </div>

    <!-- USER TABLE -->
    <div class="content-card mb-4">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Daftar User</h6>
            <a href="/admin/users/create" class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Tambah User</a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th>Project</th><th>Dibuat</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $u->name }}</td>
                        <td><code>{{ $u->username }}</code></td>
                        <td><span class="role-badge role-{{ $u->role }}">{{ strtoupper($u->role) }}</span></td>
                        <td>
                            @if($u->role !== 'admin')
                                @php $userProjects = $u->projects; @endphp
                                @forelse($userProjects as $up)
                                    <span class="badge bg-primary rounded-pill" style="font-size:0.55rem">{{ $up->kode_project }}</span>
                                @empty
                                    <span class="text-muted" style="font-size:0.65rem">Belum di-assign</span>
                                @endforelse
                            @else
                                <span class="text-muted" style="font-size:0.65rem">—</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:0.8rem">{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('d M Y') : '-' }}</td>
                        <td class="text-end">
                            @if($u->role !== 'admin')
                            <form action="/admin/users/{{ $u->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size:0.65rem"><i class="fas fa-trash me-1"></i>Hapus</button>
                            </form>
                            @else
                            <span class="text-muted" style="font-size:0.65rem">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PROJECT TABLE -->
    <div class="content-card">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0"><i class="fas fa-rocket me-2 text-primary"></i>Daftar Project</h6>
            <a href="/admin/projects/create" class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Start Project</a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>#</th><th>Nama Project</th><th>Kode</th><th>Tgl Mulai</th><th>Deadline</th><th>User</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    @php
                        $deadlineBadge = '';
                        if ($p->deadline) {
                            $dl = \Carbon\Carbon::parse($p->deadline);
                            $diff = now()->diffInDays($dl, false);
                            if ($diff < 0) $deadlineBadge = '<span class="deadline-badge deadline-over">' . abs($diff) . 'd overdue</span>';
                            elseif ($diff <= 7) $deadlineBadge = '<span class="deadline-badge deadline-danger">' . $diff . 'd left</span>';
                            elseif ($diff <= 30) $deadlineBadge = '<span class="deadline-badge deadline-warn">' . $diff . 'd left</span>';
                            else $deadlineBadge = '<span class="deadline-badge deadline-ok">' . $dl->format('d M Y') . '</span>';
                        }
                    @endphp
                    <tr>
                        <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $p->nama_project }}</td>
                        <td><code>{{ $p->kode_project }}</code></td>
                        <td class="text-muted" style="font-size:0.8rem">{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</td>
                        <td>{!! $deadlineBadge ?: '<span class="text-muted" style="font-size:0.65rem">—</span>' !!}</td>
                        <td><span class="badge bg-info rounded-pill" style="font-size:0.6rem">{{ $p->users->count() }} user</span></td>
                        <td><span class="badge rounded-pill {{ $p->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($p->status) }}</span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <form action="/admin/projects/{{ $p->id }}/toggle-status" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm rounded-pill px-3 {{ $p->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.65rem">
                                        <i class="fas {{ $p->status === 'active' ? 'fa-pause' : 'fa-play' }} me-1"></i>{{ $p->status === 'active' ? 'Nonaktif' : 'Aktif' }}
                                    </button>
                                </form>
                                <a href="/admin/projects/{{ $p->id }}/manage" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size:0.65rem">
                                    <i class="fas fa-cog me-1"></i>Kelola
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada project.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
