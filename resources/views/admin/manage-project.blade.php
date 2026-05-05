@extends('layouts.app')
@section('title', 'Kelola Project')
@section('page-title', 'Kelola: ' . $project->nama_project)

@section('styles')
<style>
    :root {
        --modul-bg: #eff6ff; --modul-border: #bfdbfe; --modul-text: #1e40af;
        --blok-bg: #fff7ed; --blok-border: #fed7aa; --blok-text: #9a3412;
        --sub-bg: #f0fdf4; --sub-border: #bbf7d0; --sub-text: #166534;
    }

    .manage-project-container { max-width: 1200px; margin: 0 auto; }
    
    /* Project Header */
    .project-header-premium {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 1.5rem; padding: 2rem; color: #fff; margin-bottom: 2rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        position: relative; overflow: hidden;
    }
    .project-header-premium::after {
        content: ''; position: absolute; top: -50%; right: -10%; width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Cards & Containers */
    .glass-card {
        background: var(--card); border: 1px solid var(--border); border-radius: 1rem;
        margin-bottom: 1.25rem; transition: all 0.3s ease; box-shadow: var(--shadow-sm);
    }
    .glass-card:hover { border-color: #cbd5e1; box-shadow: var(--shadow); }

    /* Hierarchy Levels */
    .modul-node { border-left: 4px solid var(--primary); }
    .blok-node { border-left: 4px solid #f59e0b; margin: 1rem; background: #fffcf9; }
    .sub-node { border-left: 4px solid #10b981; margin: 1rem; background: #f0fdf4; border-radius: 0.75rem; }

    .node-header {
        padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;
        cursor: pointer; user-select: none; border-bottom: 1px solid transparent;
    }
    .node-header:hover { background: rgba(0,0,0,0.015); }
    .node-title { display: flex; align-items: center; gap: 0.75rem; font-weight: 700; font-size: 1.05rem; }
    .node-title i { font-size: 1.2rem; }

    .node-content { padding: 1.5rem; border-top: 1px solid var(--border); background: #f8fafc; }

    /* Forms Premium */
    .form-premium-row {
        background: #fff; padding: 1.5rem; border-radius: 1rem;
        border: 1px solid #e2e8f0; margin-bottom: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .form-premium-row .form-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem; }
    .form-premium-row .form-control, .form-premium-row .form-select { border-radius: 0.5rem; border: 1px solid #cbd5e1; font-size: 0.85rem; padding: 0.5rem 0.75rem; }

    /* ITP Items */
    .itp-list-premium { display: flex; flex-direction: column; gap: 0.5rem; }
    .itp-item-premium {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem;
        padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: space-between;
        transition: all 0.2s;
    }
    .itp-item-premium:hover { transform: translateX(4px); border-color: var(--primary); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08); }
    
    .itp-badge-group { display: flex; gap: 4px; }
    .itp-badge-mini {
        font-size: 0.65rem; font-weight: 800; padding: 4px 8px; border-radius: 6px;
        min-width: 42px; text-align: center; border: 1px solid transparent;
    }
    .val-w { background: #fef3c7; color: #92400e; border-color: #fde68a; }
    .val-rv { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
    .val-na { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
    .val-dash { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }

    .btn-action-circle {
        width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        border: none; background: #fee2e2; color: #ef4444; transition: all 0.2s;
    }
    .btn-action-circle:hover { background: #ef4444; color: #fff; transform: scale(1.1); }
    .btn-action-mini { width: 26px; height: 26px; font-size: 0.7rem; }

    .badge-count { font-size: 0.7rem; background: #e2e8f0; color: #475569; padding: 2px 10px; border-radius: 1rem; font-weight: 700; margin-left: 8px; }

    /* Assigned User Badge */
    .assigned-user {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        border: 1px solid #cbd5e1;
        padding: 0.25rem 0.5rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-unassign {
        background: none;
        border: none;
        padding: 0;
        color: #ef4444;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.2s;
    }
    .btn-unassign:hover {
        color: #b91c1c;
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .blok-node, .sub-node { margin: 0.5rem; }
        .node-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        .node-header .d-flex { width: 100%; justify-content: flex-end; }
    }
</style>
@endsection

@section('content')
<div class="manage-project-container">
    <div class="fade-up">
        <a href="/admin/dashboard" class="btn-back mb-3 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>

    <!-- PROJECT HEADER PREMIUM -->
    <div class="project-header-premium">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2 class="fw-900 mb-1"><i class="fas fa-ship me-3 text-primary"></i>{{ $project->nama_project }}</h2>
                <div class="d-flex flex-wrap gap-3 mt-3 opacity-75">
                    <span><i class="fas fa-barcode me-2"></i>{{ $project->kode_project }}</span>
                    @if($project->tanggal_mulai)
                        <span><i class="fas fa-calendar-alt me-2"></i>Mulai: {{ $project->tanggal_mulai->format('d M Y') }}</span>
                    @endif
                    @if($project->deadline)
                        <span><i class="fas fa-flag-checkered me-2"></i>Deadline: {{ $project->deadline->format('d M Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="text-end">
                <span class="badge rounded-pill {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }} px-4 py-2" style="font-size:0.8rem">
                    {{ strtoupper($project->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA: TABBED NAVIGATION -->
    <div class="card shadow-sm border-0 mb-4 bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-0 px-md-4">
            <ul class="nav nav-tabs border-bottom-0 gap-2" id="projectMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold pb-3 px-3 px-md-4 text-dark border-0 border-bottom border-3" data-bs-toggle="tab" data-bs-target="#tab-structure" style="font-size: 1rem; border-color: transparent;">
                        <i class="fas fa-sitemap text-primary me-2"></i>Struktur Project
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold pb-3 px-3 px-md-4 text-muted border-0 border-bottom border-3" data-bs-toggle="tab" data-bs-target="#tab-team" style="font-size: 1rem; border-color: transparent;">
                        <i class="fas fa-users text-info me-2"></i>Tim & Akses <span class="badge bg-secondary ms-1">{{ $project->users->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold pb-3 px-3 px-md-4 text-muted border-0 border-bottom border-3" data-bs-toggle="tab" data-bs-target="#tab-schedule" style="font-size: 1rem; border-color: transparent;">
                        <i class="fas fa-calendar-alt text-warning me-2"></i>Jadwal Modul
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-4 bg-light" style="min-height: 50vh;">
            <div class="tab-content">
                
                <!-- TAB 1: STRUKTUR PROJECT -->
                <div class="tab-pane fade show active" id="tab-structure">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0"><i class="fas fa-sitemap text-primary me-2"></i>Project Structure</h5>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addModulModal">
                    <i class="fas fa-plus me-1"></i> Modul Baru
                </button>
            </div>

            @if($moduls->isEmpty())
                <div class="glass-card text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                    <h6 class="text-muted">Belum ada Modul.</h6>
                    <p class="small text-muted mb-0">Klik tombol "Modul Baru" untuk memulai struktur project.</p>
                </div>
            @else
                <!-- MODUL TABS -->
                <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="modulTabs" role="tablist">
                    @foreach($moduls as $index => $modul)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }} fw-bold rounded-pill" 
                                id="tab-modul-{{ $modul->id }}" 
                                data-bs-toggle="pill" 
                                data-bs-target="#content-modul-{{ $modul->id }}" 
                                type="button" role="tab"
                                style="font-size: 0.85rem; padding: 0.5rem 1.2rem;">
                            <i class="fas fa-folder me-1"></i> {{ $modul->nama_modul }}
                        </button>
                    </li>
                    @endforeach
                </ul>

                <!-- MODUL TAB CONTENT -->
                <div class="tab-content" id="modulTabsContent">
                    @foreach($moduls as $index => $modul)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="content-modul-{{ $modul->id }}" role="tabpanel">
                        
                        <!-- Modul Header Actions -->
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-white rounded-3 border shadow-sm">
                            <div class="fw-bold text-primary"><i class="fas fa-info-circle me-1"></i> {{ $modul->nama_modul }} ({{ $modul->bloks->count() }} Blok)</div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm rounded-pill shadow-sm" onclick="openAddBlok({{ $modul->id }}, '{{ $modul->nama_modul }}')">
                                    <i class="fas fa-plus me-1"></i> Tambah Blok
                                </button>
                                <form action="/admin/moduls/{{ $modul->id }}" method="POST" onsubmit="return confirm('Hapus Modul ini beserta isinya?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm rounded-pill"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <!-- FLAT TREE VIEW (List Group) -->
                        @if($modul->bloks->isEmpty())
                            <div class="text-center p-4 text-muted small border rounded-3 border-dashed bg-white">
                                Belum ada Blok di modul ini.
                            </div>
                        @else
                            <div class="card shadow-sm border-0 overflow-hidden">
                                <ul class="list-group list-group-flush">
                                    @foreach($modul->bloks as $blok)
                                    <!-- BLOK ROW -->
                                    <li class="list-group-item bg-light border-bottom border-top-0 p-3 d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-dark">
                                            <i class="fas fa-cube text-warning me-2"></i>{{ $blok->nama_blok }}
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-warning btn-sm text-dark rounded-pill fw-bold" style="padding: 0.2rem 0.6rem; font-size: 0.75rem;" onclick="openAddSubBlok({{ $blok->id }}, '{{ $blok->nama_blok }}')">
                                                <i class="fas fa-plus me-1"></i> Sub-Blok
                                            </button>
                                            <form action="/admin/bloks/{{ $blok->id }}" method="POST" class="m-0">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 26px; height: 26px; padding: 0; line-height: 24px;"><i class="fas fa-trash" style="font-size: 0.7rem;"></i></button>
                                            </form>
                                        </div>
                                    </li>

                                    @if($blok->subBloks->isEmpty())
                                        <li class="list-group-item bg-white text-muted small py-2 px-4 text-center border-bottom-0" style="font-style: italic;">Belum ada Sub-Blok</li>
                                    @else
                                        @foreach($blok->subBloks as $sub)
                                        <!-- SUB-BLOK ROW -->
                                        <li class="list-group-item bg-white p-3 border-bottom-0" style="padding-left: 2.5rem !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                                                <div class="fw-bold text-success" style="font-size: 0.95rem;">
                                                    <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-2 opacity-50"></i>
                                                    <i class="fas fa-layer-group me-1"></i> {{ $sub->nama_sub_blok }}
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-success btn-sm rounded-pill fw-bold" style="padding: 0.2rem 0.6rem; font-size: 0.75rem;" onclick="openAddItp({{ $sub->id }}, '{{ $sub->nama_sub_blok }}')">
                                                        <i class="fas fa-plus me-1"></i> ITP
                                                    </button>
                                                    <form action="/admin/sub-bloks/{{ $sub->id }}" method="POST" class="m-0">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-outline-danger btn-sm rounded-circle" style="width: 26px; height: 26px; padding: 0; line-height: 24px;"><i class="fas fa-trash" style="font-size: 0.7rem;"></i></button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- ITP TABLE FOR THIS SUB-BLOK -->
                                            <div class="ps-4">
                                                @if($sub->itps->isEmpty())
                                                    <div class="text-muted small py-1" style="font-style: italic;">Belum ada ITP</div>
                                                @else
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover align-middle mb-0 border" style="font-size: 0.85rem;">
                                                            <thead class="table-light text-muted">
                                                                <tr>
                                                                    <th style="width: 15%">Kode</th>
                                                                    <th style="width: 45%">Deskripsi</th>
                                                                    <th style="width: 35%" class="text-center">Standar (Y/C/O/S)</th>
                                                                    <th style="width: 5%"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($sub->itps as $itp)
                                                                @php $valC = fn($v) => match(strtoupper($v)) { 'W' => 'text-primary fw-bold', 'RV' => 'text-warning fw-bold', 'NA' => 'text-secondary', default => 'text-muted' }; @endphp
                                                                <tr>
                                                                    <td class="fw-bold" style="font-family: monospace;">{{ $itp->assembly_code }}.{{ $itp->code }}</td>
                                                                    <td class="text-truncate" style="max-width: 200px;" title="{{ $itp->item }}">{{ $itp->item }}</td>
                                                                    <td class="text-center">
                                                                        <div class="d-flex justify-content-center gap-2">
                                                                            <span class="{{ $valC($itp->yard_val) }}" title="YARD">Y:{{ $itp->yard_val }}</span>
                                                                            <span class="text-muted border-end"></span>
                                                                            <span class="{{ $valC($itp->class_val) }}" title="CLASS">C:{{ $itp->class_val }}</span>
                                                                            <span class="text-muted border-end"></span>
                                                                            <span class="{{ $valC($itp->os_val) }}" title="OS">O:{{ $itp->os_val }}</span>
                                                                            <span class="text-muted border-end"></span>
                                                                            <span class="{{ $valC($itp->stat_val) }}" title="STAT">S:{{ $itp->stat_val }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <form action="/admin/itps/{{ $itp->id }}" method="POST" class="m-0">
                                                                            @csrf @method('DELETE')
                                                                            <button class="btn btn-link text-danger p-0 m-0 border-0" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                        @endforeach
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
                </div>

                <!-- TAB 2: TEAM ASSIGNMENT -->
                <div class="tab-pane fade" id="tab-team">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-bottom p-4">
                                    <h5 class="fw-bold m-0"><i class="fas fa-user-plus text-info me-2"></i>Kelola Akses Tim</h5>
                                </div>
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3 text-muted">Tim yang Ditugaskan:</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        @forelse($project->users as $assignedUser)
                                            <div class="bg-light border rounded-pill d-flex align-items-center px-3 py-2 shadow-sm">
                                                <span class="badge bg-dark rounded-pill me-2" style="font-size:0.6rem">{{ strtoupper($assignedUser->role) }}</span>
                                                <span class="fw-bold text-dark me-3">{{ $assignedUser->name }}</span>
                                                <form action="/admin/projects/{{ $project->id }}/users/{{ $assignedUser->id }}" method="POST" class="m-0">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-link text-danger p-0 m-0 border-0 text-decoration-none"><i class="fas fa-times-circle fs-5"></i></button>
                                                </form>
                                            </div>
                                        @empty
                                            <div class="text-muted w-100 text-center py-3 bg-light rounded-3 border-dashed">Belum ada anggota tim yang ditugaskan.</div>
                                        @endforelse
                                    </div>

                                    @php
                                        $assignedIds = $project->users->pluck('id')->toArray();
                                        $availableUsers = $allUsers->whereNotIn('id', $assignedIds);
                                    @endphp
                                    @if($availableUsers->count() > 0)
                                    <hr class="text-muted opacity-25 my-4">
                                    <h6 class="fw-bold mb-3 text-muted">Tambahkan Anggota Baru:</h6>
                                    <form action="/admin/projects/assign-user" method="POST">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                        <div class="input-group input-group-lg shadow-sm">
                                            <select name="user_id" class="form-select bg-white border-end-0" required>
                                                <option value="">Pilih anggota tim...</option>
                                                @foreach($availableUsers as $au)
                                                    <option value="{{ $au->id }}">{{ $au->name }} ({{ strtoupper($au->role) }})</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-info text-white fw-bold px-4 border"><i class="fas fa-plus me-2"></i> Tambahkan</button>
                                        </div>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: MODULE SCHEDULE -->
                <div class="tab-pane fade" id="tab-schedule">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-bottom p-4">
                                    <h5 class="fw-bold m-0"><i class="fas fa-calendar-alt text-warning me-2"></i>Atur Jadwal Modul</h5>
                                </div>
                                <div class="card-body p-4">
                                    @if($moduls->isEmpty())
                                        <div class="text-center py-4 text-muted">Tambahkan Modul terlebih dahulu untuk mengatur jadwal.</div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama Modul</th>
                                                        <th>Hari Dimulai (Start)</th>
                                                        <th>Durasi (Hari)</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($moduls as $modul)
                                                    <tr>
                                                        <td class="fw-bold">{{ $modul->nama_modul }}</td>
                                                        <form action="/admin/moduls/{{ $modul->id }}/schedule" method="POST">
                                                            @csrf
                                                            <td><input type="number" name="start_day" class="form-control form-control-sm w-75" value="{{ $modul->start_day }}" required></td>
                                                            <td><input type="number" name="duration_days" class="form-control form-control-sm w-75" value="{{ $modul->duration_days }}" required></td>
                                                            <td><button class="btn btn-sm btn-warning fw-bold text-dark rounded-pill px-3 shadow-sm"><i class="fas fa-save me-1"></i> Simpan</button></td>
                                                        </form>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>
</div>
@endsection

<!-- Modal Add Modul -->
<div class="modal fade" id="addModulModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/moduls" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Modul Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Modul</label>
                    <input type="text" name="nama_modul" class="form-control" placeholder="Contoh: Modul Hull Construction" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat modul..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Modul</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Blok -->
<div class="modal fade" id="addBlokModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/bloks" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="modul_id" id="modal_modul_id">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-cube me-2"></i>Tambah Blok Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary bg-opacity-10 border-primary border-opacity-25" style="font-size:0.85rem">
                    Menambahkan blok baru ke Modul: <strong id="modal_modul_name"></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Blok</label>
                    <input type="text" name="nama_blok" class="form-control" placeholder="Contoh: BLOK 01" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Blok</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Sub-Blok -->
<div class="modal fade" id="addSubBlokModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="/admin/sub-bloks" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="blok_id" id="modal_blok_id">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-layer-group me-2"></i>Tambah Sub-Blok Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning bg-opacity-10 border-warning border-opacity-50 text-dark" style="font-size:0.85rem">
                    Menambahkan sub-blok ke Blok: <strong id="modal_blok_name"></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Sub-Blok</label>
                    <input type="text" name="nama_sub_blok" class="form-control" placeholder="Contoh: Assembly B1-A" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning fw-bold text-dark">Simpan Sub-Blok</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add ITP -->
<div class="modal fade" id="addItpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="/admin/itps" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="sub_blok_id" id="modal_sub_blok_id">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-list-check me-2"></i>Tambah Item ITP Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success bg-opacity-10 border-success border-opacity-25 text-dark" style="font-size:0.85rem">
                    Menambahkan ITP ke Sub-Blok: <strong id="modal_sub_blok_name"></strong>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Kode Assembly</label>
                        <select name="assembly_code" class="form-select" required>
                            <option value="">Pilih...</option>
                            @foreach($assemblyCodes as $ac)
                                <option value="{{ $ac->code }}">{{ $ac->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Nomor Urut</label>
                        <input type="text" name="code" class="form-control" placeholder="01" required>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label fw-bold">Deskripsi Pekerjaan</label>
                        <input type="text" name="item" class="form-control" placeholder="Deskripsi tahapan inspeksi..." required>
                    </div>
                </div>
                
                <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Referensi & Panduan Inspeksi (Opsional)</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Metode Inspeksi</label>
                        <input type="text" name="metode_inspeksi" class="form-control" placeholder="Contoh: Visual, Dimension check...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Alat/Peralatan</label>
                        <input type="text" name="alat_peralatan" class="form-control" placeholder="Contoh: Meteran, Welding Gauge...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Referensi Rules</label>
                        <input type="text" name="referensi_rules" class="form-control" placeholder="Contoh: BKI Vol II 2023...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Syarat Lulus (Acceptance Criteria)</label>
                        <input type="text" name="syarat_pemenuhan" class="form-control" placeholder="Contoh: Sesuai drawing, Toleransi ±2mm...">
                    </div>
                </div>

                <h6 class="fw-bold mb-3 border-bottom pb-2">Standar Inspeksi (W/RV/-)</h6>
                <div class="row g-3">
                    @foreach(['yard'=>'YARD', 'class'=>'CLASS', 'os'=>'OWNER (OS)', 'stat'=>'STATUTORY'] as $roleKey => $roleLabel)
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold">{{ $roleLabel }}</label>
                        <select name="{{ $roleKey }}_val" class="form-select" required>
                            <option value="W">W (Witness)</option>
                            <option value="RV">RV (Review)</option>
                            <option value="-" selected>- (Bypass)</option>
                            <option value="NA">NA (Not Applicable)</option>
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success fw-bold">Simpan ITP</button>
            </div>
        </form>
    </div>
</div>
@section('scripts')
<script>
    function openAddBlok(id, name) {
        document.getElementById('modal_modul_id').value = id;
        document.getElementById('modal_modul_name').innerText = name;
        new bootstrap.Modal(document.getElementById('addBlokModal')).show();
    }
    function openAddSubBlok(id, name) {
        document.getElementById('modal_blok_id').value = id;
        document.getElementById('modal_blok_name').innerText = name;
        new bootstrap.Modal(document.getElementById('addSubBlokModal')).show();
    }
    function openAddItp(id, name) {
        document.getElementById('modal_sub_blok_id').value = id;
        document.getElementById('modal_sub_blok_name').innerText = name;
        new bootstrap.Modal(document.getElementById('addItpModal')).show();
    }

    // Auto-fill assembly description when code selected
    document.querySelectorAll('select[name="assembly_code"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const form = this.closest('form');
            const descField = form ? form.querySelector('input[name="assembly_description"]') : null;
            if (descField && option.dataset.desc) {
                descField.value = option.dataset.desc;
            }
        });
    });
</script>
@endsection
