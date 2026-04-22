@extends('layouts.app')
@section('title', 'Kelola Project')
@section('page-title', 'Kelola: ' . $project->nama_project)

@section('styles')
<style>
    .section-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .section-header:hover { background: #f8fafc; }
    .section-header .chevron-icon {
        transition: transform 0.3s ease;
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .section-header[aria-expanded="true"] .chevron-icon { transform: rotate(180deg); }
    .section-body { padding: 1.25rem; }

    /* Forms */
    .add-form { display: flex; gap: 8px; align-items: end; flex-wrap: wrap; }
    .add-form .form-control,
    .add-form .form-select { font-size: 0.85rem; padding: 0.45rem 0.75rem; border-radius: 0.5rem; }

    /* ITP Add Form — stacked grid layout */
    .itp-form-card {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    .itp-form-card .form-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    .itp-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .itp-form-row.single { grid-template-columns: 1fr; }
    .itp-code-row {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .itp-val-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto;
        gap: 0.5rem;
        align-items: end;
    }
    .itp-val-row .val-col { text-align: center; }
    .itp-val-row .val-col .form-select {
        font-size: 0.8rem;
        padding: 0.35rem 0.4rem;
        text-align: center;
    }
    .val-label {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    /* Hierarchy tree */
    .tree-node { margin-left: 1.5rem; padding-left: 1rem; border-left: 2px solid #e2e8f0; }
    .tree-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0.85rem;
        border: 1px solid #f1f5f9;
        border-radius: 0.625rem;
        margin-bottom: 0.35rem;
        font-size: 0.85rem;
        transition: all 0.2s;
        cursor: pointer;
        background: #fff;
    }
    .tree-item:hover { background: #f8fafc; border-color: #e2e8f0; box-shadow: 0 2px 8px -2px rgba(0,0,0,0.04); }

    /* ITP list item */
    .itp-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        border: 1px solid #f1f5f9;
        border-radius: 0.5rem;
        margin-bottom: 0.3rem;
        font-size: 0.82rem;
        background: #fff;
        transition: background 0.2s;
        gap: 8px;
    }
    .itp-item:hover { background: #fafbfd; }
    .itp-info { flex: 1; min-width: 0; }
    .itp-info .asm-code { font-weight: 700; color: var(--accent); font-size: 0.8rem; }
    .itp-info .itp-code { font-family: monospace; font-size: 0.75rem; color: #64748b; }
    .itp-info .itp-desc { font-size: 0.8rem; color: var(--text); }
    .itp-vals { display: flex; gap: 3px; flex-shrink: 0; flex-wrap: wrap; }
    .badge-val { font-size: 0.6rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; white-space: nowrap; }
    .val-w { background: #fef3c7; color: #92400e; }
    .val-rv { background: #dbeafe; color: #1e40af; }
    .val-dash { background: #f1f5f9; color: #94a3b8; }
    .val-na { background: #fee2e2; color: #991b1b; }

    /* Assigned users */
    .assigned-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 2rem;
        padding: 5px 10px 5px 12px;
        font-size: 0.8rem;
        margin: 3px;
        transition: all 0.2s;
    }
    .assigned-user:hover { background: #f1f5f9; border-color: #cbd5e1; }
    .assigned-user .btn-unassign {
        background: none; border: none; color: #ef4444;
        cursor: pointer; font-size: 0.7rem; padding: 0 2px;
    }
    .assigned-user .btn-unassign:hover { color: #dc2626; }

    /* Schedule section */
    .schedule-item {
        background: linear-gradient(135deg, #f0f4ff, #f5f3ff);
        border: 1px solid #c7d2fe;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.5rem;
    }
    .schedule-item label { font-size: 0.72rem; font-weight: 600; color: #4338ca; margin-bottom: 0.2rem; }

    /* Project header */
    .project-header {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border-radius: 1rem;
        padding: 1.5rem 1.75rem;
        color: #fff;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .project-header::before {
        content: '';
        position: absolute;
        top: -50%; right: -15%;
        width: 250px; height: 250px;
        background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 60%);
        border-radius: 50%;
    }
    .project-header h5 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .project-header .meta { color: #94a3b8; font-size: 0.8rem; position: relative; }
    .project-header .meta strong { color: #cbd5e1; }

    @media (max-width: 768px) {
        .itp-form-row { grid-template-columns: 1fr; }
        .itp-code-row { grid-template-columns: 1fr; }
        .itp-val-row { grid-template-columns: repeat(2, 1fr); }
        .itp-val-row .btn-submit-col { grid-column: 1 / -1; }
        .tree-node { margin-left: 0.75rem; padding-left: 0.75rem; }
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <a href="/admin/dashboard" class="btn-back mb-3 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali</a>

    <!-- PROJECT HEADER -->
    <div class="project-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5><i class="fas fa-ship me-2"></i>{{ $project->nama_project }}</h5>
                <div class="meta">
                    Kode: <strong>{{ $project->kode_project }}</strong>
                    @if($project->tanggal_kontrak)
                        &nbsp;•&nbsp; <i class="fas fa-file-signature me-1"></i>Kontrak: {{ $project->tanggal_kontrak->format('d M Y') }}
                    @endif
                    @if($project->tanggal_mulai)
                        &nbsp;•&nbsp; <i class="fas fa-calendar-alt me-1"></i>Mulai: {{ $project->tanggal_mulai->format('d M Y') }}
                    @endif
                    @if($project->deadline)
                        &nbsp;•&nbsp; <i class="fas fa-flag-checkered me-1"></i>Deadline: {{ $project->deadline->format('d M Y') }}
                    @endif
                </div>
            </div>
            <span class="badge rounded-pill {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="font-size:0.7rem">
                {{ ucfirst($project->status) }}
            </span>
        </div>
    </div>

    <!-- ASSIGN USER SECTION -->
    <div class="section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#assign-section">
            <div>
                <i class="fas fa-user-plus text-primary me-2"></i>
                <strong style="font-size:0.9rem">Assign User ke Project</strong>
                <span class="badge bg-info rounded-pill ms-2" style="font-size:0.6rem">{{ $project->users->count() }} user</span>
            </div>
            <i class="fas fa-chevron-down chevron-icon"></i>
        </div>
        <div class="collapse show" id="assign-section">
            <div class="section-body">
                <div class="mb-3" style="display:flex;flex-wrap:wrap;gap:4px">
                    @forelse($project->users as $assignedUser)
                        <span class="assigned-user">
                            <span class="role-badge role-{{ $assignedUser->role }}" style="font-size:0.5rem;padding:1px 5px">{{ strtoupper($assignedUser->role) }}</span>
                            <strong>{{ $assignedUser->name }}</strong>
                            <form action="/admin/projects/{{ $project->id }}/users/{{ $assignedUser->id }}" method="POST" class="d-inline" onsubmit="return confirm('Unassign user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn-unassign" title="Unassign"><i class="fas fa-times"></i></button>
                            </form>
                        </span>
                    @empty
                        <p class="text-muted mb-0" style="font-size:0.8rem"><i class="fas fa-info-circle me-1"></i>Belum ada user yang di-assign.</p>
                    @endforelse
                </div>

                @php
                    $assignedIds = $project->users->pluck('id')->toArray();
                    $availableUsers = $allUsers->whereNotIn('id', $assignedIds);
                @endphp
                @if($availableUsers->count() > 0)
                <form action="/admin/projects/assign-user" method="POST" class="add-form">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <select name="user_id" class="form-select" required style="flex:1;min-width:200px">
                        <option value="">-- Pilih User --</option>
                        @foreach($availableUsers as $au)
                            <option value="{{ $au->id }}">{{ $au->name }} ({{ strtoupper($au->role) }})</option>
                        @endforeach
                    </select>
                    <button class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Assign</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- ADD MODUL -->
    <div class="section-card">
        <div class="section-body">
            <h6 class="fw-bold mb-3" style="font-size:0.85rem"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Modul</h6>
            <form action="/admin/moduls" method="POST" class="add-form">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="text" name="nama_modul" class="form-control" placeholder="Nama Modul" required style="flex:1;min-width:180px">
                <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi (opsional)" style="flex:1;min-width:180px">
                <button class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Tambah</button>
            </form>
        </div>
    </div>

    <!-- MODULS LIST -->
    @foreach($moduls as $modul)
    <div class="section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#modul-{{ $modul->id }}">
            <div class="d-flex align-items-center gap-2" style="min-width:0">
                <i class="fas fa-folder-open text-primary"></i>
                <strong style="white-space:nowrap">{{ $modul->nama_modul }}</strong>
                @if($modul->deskripsi)
                    <small class="text-muted d-none d-md-inline" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px">{{ $modul->deskripsi }}</small>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <span class="badge bg-primary rounded-pill" style="font-size:0.6rem">{{ $modul->bloks->count() }} Blok</span>
                <form action="/admin/moduls/{{ $modul->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus modul ini beserta isinya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" style="font-size:0.6rem;padding:2px 8px"><i class="fas fa-trash"></i></button>
                </form>
                <i class="fas fa-chevron-down chevron-icon"></i>
            </div>
        </div>
        <div class="collapse" id="modul-{{ $modul->id }}">
            <div class="section-body">
                <!-- Add Blok -->
                <form action="/admin/bloks" method="POST" class="add-form mb-3">
                    @csrf
                    <input type="hidden" name="modul_id" value="{{ $modul->id }}">
                    <input type="text" name="nama_blok" class="form-control" placeholder="Nama Blok (contoh: BLOK 1)" required style="flex:1;min-width:200px">
                    <button class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Blok</button>
                </form>

                @foreach($modul->bloks as $blok)
                <div class="tree-node mb-2">
                    <div class="tree-item" data-bs-toggle="collapse" data-bs-target="#blok-{{ $blok->id }}">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-cubes text-warning"></i>
                            <strong>{{ $blok->nama_blok }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.55rem">{{ $blok->subBloks->count() }} Sub</span>
                            <form action="/admin/bloks/{{ $blok->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" style="font-size:0.55rem;padding:1px 6px"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="collapse" id="blok-{{ $blok->id }}">
                        <div class="tree-node mt-1">
                            <!-- Add SubBlok -->
                            <form action="/admin/sub-bloks" method="POST" class="add-form mb-2">
                                @csrf
                                <input type="hidden" name="blok_id" value="{{ $blok->id }}">
                                <input type="text" name="nama_sub_blok" class="form-control" placeholder="Nama Sub Blok" required style="flex:1;min-width:180px">
                                <button class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Sub</button>
                            </form>

                            @foreach($blok->subBloks as $sub)
                            <div class="mb-2">
                                <div class="tree-item" data-bs-toggle="collapse" data-bs-target="#sub-{{ $sub->id }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-layer-group text-success"></i>
                                        <strong>{{ $sub->nama_sub_blok }}</strong>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill" style="font-size:0.55rem">{{ $sub->itps->count() }} ITP</span>
                                        <form action="/admin/sub-bloks/{{ $sub->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" style="font-size:0.55rem;padding:1px 6px"><i class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                </div>

                                <div class="collapse" id="sub-{{ $sub->id }}">
                                    <div class="tree-node mt-1">
                                        <!-- ITP Add Form — Clean Grid -->
                                        <div class="itp-form-card">
                                            <form action="/admin/itps" method="POST">
                                                @csrf
                                                <input type="hidden" name="sub_blok_id" value="{{ $sub->id }}">

                                                <div class="itp-form-row">
                                                    <div>
                                                        <label class="form-label">Assembly Code</label>
                                                        <select name="assembly_code" class="form-select form-select-sm" required>
                                                            <option value="">-- Pilih --</option>
                                                            @foreach($assemblyCodes as $ac)
                                                                <option value="{{ $ac->code }}" data-desc="{{ $ac->description }}">{{ $ac->code }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="form-label">Deskripsi Assembly</label>
                                                        <input type="text" name="assembly_description" class="form-control form-control-sm" placeholder="Otomatis terisi...">
                                                    </div>
                                                </div>

                                                <div class="itp-code-row">
                                                    <div>
                                                        <label class="form-label">Kode</label>
                                                        <input type="text" name="code" class="form-control form-control-sm" placeholder="01" required>
                                                    </div>
                                                    <div>
                                                        <label class="form-label">Deskripsi Item</label>
                                                        <input type="text" name="item" class="form-control form-control-sm" placeholder="Nama item inspeksi..." required>
                                                    </div>
                                                </div>

                                                <div class="itp-val-row">
                                                    <div class="val-col">
                                                        <div class="val-label">Yard</div>
                                                        <select name="yard_val" class="form-select form-select-sm" required>
                                                            <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                                        </select>
                                                    </div>
                                                    <div class="val-col">
                                                        <div class="val-label">Class</div>
                                                        <select name="class_val" class="form-select form-select-sm" required>
                                                            <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                                        </select>
                                                    </div>
                                                    <div class="val-col">
                                                        <div class="val-label">OS</div>
                                                        <select name="os_val" class="form-select form-select-sm" required>
                                                            <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                                        </select>
                                                    </div>
                                                    <div class="val-col">
                                                        <div class="val-label">Stat</div>
                                                        <select name="stat_val" class="form-select form-select-sm" required>
                                                            <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                                        </select>
                                                    </div>
                                                    <div class="btn-submit-col">
                                                        <button class="btn btn-accent btn-sm w-100" style="white-space:nowrap"><i class="fas fa-plus me-1"></i>Tambah ITP</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- ITP List -->
                                        @foreach($sub->itps as $itp)
                                        @php
                                            $valClass = fn($v) => match(strtoupper($v)) { 'W' => 'val-w', 'RV' => 'val-rv', 'NA' => 'val-na', default => 'val-dash' };
                                        @endphp
                                        <div class="itp-item">
                                            <div class="itp-info">
                                                <span class="asm-code">{{ $itp->assembly_code }}</span>
                                                @if($itp->assembly_description)
                                                    <small class="text-muted ms-1" title="{{ $itp->assembly_description }}"><i class="fas fa-info-circle"></i></small>
                                                @endif
                                                <span class="text-muted mx-1">›</span>
                                                <span class="itp-code">{{ $itp->code }}</span>
                                                <span class="text-muted mx-1">—</span>
                                                <span class="itp-desc">{{ $itp->item }}</span>
                                            </div>
                                            <div class="itp-vals">
                                                <span class="badge-val {{ $valClass($itp->yard_val) }}" title="Yard">Y:{{ $itp->yard_val }}</span>
                                                <span class="badge-val {{ $valClass($itp->class_val) }}" title="Class">C:{{ $itp->class_val }}</span>
                                                <span class="badge-val {{ $valClass($itp->os_val) }}" title="OS">O:{{ $itp->os_val }}</span>
                                                <span class="badge-val {{ $valClass($itp->stat_val) }}" title="Stat">S:{{ $itp->stat_val }}</span>
                                                <form action="/admin/itps/{{ $itp->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kode inspeksi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" style="font-size:0.55rem;padding:1px 6px"><i class="fas fa-times"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    @if($moduls->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="fas fa-folder-open fa-3x mb-3" style="color:#e2e8f0"></i>
        <p class="fw-bold">Belum ada modul. Tambahkan modul pertama di atas.</p>
    </div>
    @endif

    {{-- FEAT-07: Module Schedule Management --}}
    @if($moduls->isNotEmpty())
    <div class="section-card mt-3">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#schedule-section">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-calendar-alt text-primary"></i>
                <strong style="font-size:0.9rem">Pengaturan Jadwal Modul</strong>
                <small class="text-muted d-none d-sm-inline">Hari mulai dan durasi tiap modul</small>
            </div>
            <i class="fas fa-chevron-down chevron-icon"></i>
        </div>
        <div class="collapse" id="schedule-section">
            <div class="section-body">
                @foreach($moduls as $modul)
                <div class="schedule-item">
                    <div class="fw-bold mb-2" style="font-size:0.85rem"><i class="fas fa-folder-open me-1 text-primary"></i>{{ $modul->nama_modul }}</div>
                    <form action="/admin/moduls/{{ $modul->id }}/schedule" method="POST" class="add-form">
                        @csrf
                        <div>
                            <label>Hari Mulai (ke-N)</label>
                            <input type="number" name="start_day" class="form-control form-control-sm" value="{{ $modul->start_day }}" min="1" placeholder="1" style="width:110px">
                        </div>
                        <div>
                            <label>Durasi (hari)</label>
                            <input type="number" name="duration_days" class="form-control form-control-sm" value="{{ $modul->duration_days }}" min="1" placeholder="35" style="width:110px">
                        </div>
                        <button class="btn btn-accent btn-sm"><i class="fas fa-save me-1"></i>Simpan</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@section('scripts')
<script>
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
