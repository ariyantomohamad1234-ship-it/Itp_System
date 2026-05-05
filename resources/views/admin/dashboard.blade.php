@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Admin')

@section('styles')
<style>
    .admin-header { margin-bottom: 2rem; }
    .admin-header h4 { font-weight: 800; font-size: 1.75rem; color: var(--text); letter-spacing: -0.5px; }
    .admin-header p { color: var(--text-muted); font-size: 0.95rem; }

    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 1.25rem;
        padding: 1.5rem; position: relative; overflow: hidden; transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border-color: rgba(59, 130, 246, 0.3); }
    .stat-card::before {
        content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 150px;
        background: radial-gradient(circle, var(--glow-color) 0%, transparent 70%);
        opacity: 0.15; border-radius: 50%; transform: translate(30%, -30%);
    }
    .stat-icon {
        width: 54px; height: 54px; border-radius: 1rem; display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin-bottom: 1rem;
    }
    .stat-val { font-size: 2.25rem; font-weight: 900; line-height: 1; margin-bottom: 0.25rem; color: var(--text); }
    .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }

    .content-card { background: var(--card); border: 1px solid var(--border); border-radius: 1.25rem; overflow: hidden; margin-bottom: 2rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
    .content-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fafbfc; }
    .content-card-header h6 { font-size: 1.1rem; font-weight: 800; margin: 0; color: var(--text); }
    
    .table-custom th { text-transform: uppercase; font-size: 0.7rem; font-weight: 800; letter-spacing: 1px; color: var(--text-muted); padding: 1rem 1.5rem; background: #fafbfc; border-bottom: 1px solid var(--border); }
    .table-custom td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-custom tbody tr { transition: all 0.2s; }
    .table-custom tbody tr:hover { background-color: #f8fafc; }
    
    .role-badge { font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 2rem; text-transform: uppercase; letter-spacing: 1px; }
    .role-admin { background: rgba(30, 41, 59, 0.1); color: #1e293b; border: 1px solid rgba(30, 41, 59, 0.2); }
    .role-admin_galangan { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    .role-yard { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
    .role-class { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .role-os { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .role-stat { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); }
</style>
<link href="https://fonts.googleapis.com/css2?family=Bungee&family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="fade-up">
    <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h4>{{ $currentUser->isAdminGalangan() ? 'Dashboard Admin Galangan' : 'Admin Panel Monitoring' }}</h4>
            <p>{{ $currentUser->isAdminGalangan() ? 'Mengelola proyek dan pengguna di galangan Anda.' : 'Pusat kendali dan monitoring aktivitas seluruh sistem ITP.' }}</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-danger dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bolt me-2"></i>Quick Action
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2">
                    <li><a class="dropdown-item rounded-3 py-2" href="/admin/users/create"><i class="fas fa-user-plus me-2 text-danger"></i>Tambah User</a></li>
                    <li><a class="dropdown-item rounded-3 py-2" href="/admin/projects/create"><i class="fas fa-rocket me-2 text-danger"></i>Mulai Project Baru</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item rounded-3 py-2" href="/admin/logs"><i class="fas fa-list-alt me-2 text-muted"></i>Lihat System Logs</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="stat-cards">
        <div class="stat-card" style="--glow-color: #ef4444;">
            <div class="stat-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-users"></i></div>
            <div class="stat-val">{{ $totalUsers }}</div>
            <div class="stat-label">Total User</div>
        </div>
        <div class="stat-card" style="--glow-color: #dc2626;">
            <div class="stat-icon" style="background: #fecaca; color: #991b1b;"><i class="fas fa-project-diagram"></i></div>
            <div class="stat-val">{{ $totalProjects }}</div>
            <div class="stat-label">Total Project</div>
        </div>
        <div class="stat-card" style="--glow-color: #991b1b;">
            <div class="stat-icon" style="background: #fee2e2; color: #b91c1c;"><i class="fas fa-folder-open"></i></div>
            <div class="stat-val">{{ $totalModuls }}</div>
            <div class="stat-label">Total Modul</div>
        </div>
        <div class="stat-card" style="--glow-color: #f87171;">
            <div class="stat-icon" style="background: #fff1f2; color: #e11d48;"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-val">{{ $totalItps }}</div>
            <div class="stat-label">Total ITP</div>
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
                        <td>
                            @if($u->role === 'admin')
                                <span class="role-badge role-admin"><i class="fas fa-terminal me-1"></i> ADMIN SOFTWARE</span>
                            @elseif($u->role === 'admin_galangan')
                                <span class="role-badge role-admin_galangan"><i class="fas fa-hard-hat me-1"></i> ADMIN GALANGAN</span>
                            @else
                                <span class="role-badge role-{{ $u->role }}">{{ strtoupper($u->role) }}</span>
                            @endif
                        </td>
                        <td>
                            @if(!in_array($u->role, ['admin', 'admin_galangan']))
                                @php $userProjects = $u->projects; @endphp
                                @forelse($userProjects as $up)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 mb-1 d-inline-block" style="font-size:0.6rem">{{ $up->kode_project }}</span>
                                @empty
                                    <span class="text-muted fst-italic" style="font-size:0.7rem">Belum di-assign</span>
                                @endforelse
                            @else
                                <span class="text-muted fw-bold" style="font-size:0.7rem">Semua Akses</span>
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
            <h6 class="fw-bold mb-0"><i class="fas fa-rocket me-2 text-danger"></i>Daftar Project</h6>
            <a href="/admin/projects/create" class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Start Project</a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>#</th><th>Nama Project</th><th>Kode</th><th>Tgl Mulai</th><th>Deadline</th><th>Progres</th><th>Status</th><th class="text-end">Aksi</th></tr>
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
                        <td class="fw-bold">
                            {{ $p->nama_project }}
                            @if($p->template_id)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-1" style="font-size:0.5rem;vertical-align:middle" title="Dibuat dari template">📋 Template</span>
                            @endif
                        </td>
                        <td><code>{{ $p->kode_project }}</code></td>
                        <td class="text-muted" style="font-size:0.8rem">{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</td>
                        <td>{!! $deadlineBadge ?: '<span class="text-muted" style="font-size:0.65rem">—</span>' !!}</td>
                        <td>
                            <div class="progress" style="height: 6px; width: 100px; background-color: #e9ecef; border-radius: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $p->progress }}%; border-radius: 10px;"></div>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 0.65rem;">{{ $p->progress }}% Terverifikasi</div>
                        </td>
                        <td><span class="badge rounded-pill {{ $p->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($p->status) }}</span></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <form action="/admin/projects/{{ $p->id }}/toggle-status" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm rounded-pill px-3 {{ $p->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.65rem">
                                        <i class="fas {{ $p->status === 'active' ? 'fa-pause' : 'fa-play' }} me-1"></i>{{ $p->status === 'active' ? 'Nonaktif' : 'Aktif' }}
                                    </button>
                                </form>
                                <a href="/admin/projects/{{ $p->id }}/manage" class="btn btn-outline-danger btn-sm rounded-pill px-3" style="font-size:0.65rem">
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

    <!-- RECENT ACTIVITY TIMELINE -->
    <div class="content-card">
        <div class="content-card-header">
            <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-danger"></i>Aktivitas Terbaru</h6>
            <span class="text-muted" style="font-size:0.7rem">15 Aktivitas Terakhir</span>
        </div>
        <div class="p-4">
            <div class="timeline">
                @forelse($recentActivity as $act)
                <div class="d-flex gap-3 mb-4 position-relative">
                    <div class="activity-dot {{ $act->status === 'approved' ? 'bg-success' : ($act->status === 'rejected' ? 'bg-danger' : 'bg-danger') }}"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-bold" style="font-size:0.9rem">{{ $act->user_name }}</span>
                                <span class="role-badge role-{{ $act->user_role }} ms-1" style="font-size:0.5rem">{{ strtoupper($act->user_role) }}</span>
                                <span class="text-muted" style="font-size:0.85rem">
                                    {{ $act->status === 'approved' ? 'menyetujui' : ($act->status === 'rejected' ? 'menolak' : 'mengunggah data') }}
                                    ITP <strong>{{ $act->itp_code }}</strong>
                                </span>
                            </div>
                            <span class="text-muted" style="font-size:0.7rem">{{ \Carbon\Carbon::parse($act->updated_at)->diffForHumans() }}</span>
                        </div>
                        @if($act->keterangan)
                            <div class="mt-1 p-2 bg-light rounded" style="font-size:0.8rem; border-left: 3px solid #dee2e6;">
                                "{{ Str::limit($act->keterangan, 100) }}"
                            </div>
                        @endif
                        @if($act->status === 'rejected' && $act->rejection_note)
                            <div class="mt-1 p-2 bg-danger bg-opacity-10 text-danger rounded" style="font-size:0.8rem; border-left: 3px solid #dc3545;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Catatan Reject: "{{ $act->rejection_note }}"
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-3 text-muted">Belum ada aktivitas terekam.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .activity-dot {
        width: 12px; height: 12px; border-radius: 50%; margin-top: 5px; flex-shrink: 0;
        z-index: 2; position: relative;
    }
    .timeline::before {
        content: ''; position: absolute; left: 5px; top: 10px; bottom: 0;
        width: 2px; background: #e9ecef; z-index: 1;
    }
</style>
@endsection
