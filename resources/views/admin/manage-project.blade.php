@extends('layouts.app')
@section('title', 'Kelola Project')
@section('page-title', 'Kelola: ' . $project->nama_project)

@section('styles')
<style>
    .section-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.75rem;
        margin-bottom: 1rem;
    }
    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .section-header:hover { background: #f8fafc; }
    .section-body { padding: 1rem 1.25rem; }
    .mini-form { display: flex; gap: 8px; align-items: end; flex-wrap: wrap; }
    .mini-form .form-control, .mini-form .form-select { font-size: 0.85rem; padding: 0.4rem 0.75rem; }
    .item-list { margin-top: 0.75rem; }
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0.75rem;
        border: 1px solid #f1f5f9;
        border-radius: 0.5rem;
        margin-bottom: 0.4rem;
        font-size: 0.85rem;
        transition: background 0.2s;
    }
    .item-row:hover { background: #f8fafc; }
    .nested { margin-left: 1.25rem; padding-left: 1rem; border-left: 2px solid #e2e8f0; }
    .badge-val { font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
    .val-w { background: #fef3c7; color: #92400e; }
    .val-rv { background: #dbeafe; color: #1e40af; }
    .val-dash { background: #f1f5f9; color: #94a3b8; }
    .val-na { background: #fee2e2; color: #991b1b; }
    .assigned-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 2rem;
        padding: 4px 10px 4px 12px;
        font-size: 0.8rem;
        margin: 2px;
    }
    .assigned-user .btn-unassign {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 0.7rem;
        padding: 0 2px;
    }
    .assigned-user .btn-unassign:hover { color: #dc2626; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <a href="/admin/dashboard" class="btn-back mb-3 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali</a>

    <!-- PROJECT INFO -->
    <div class="content-card mb-4">
        <div class="content-card-header">
            <div>
                <h6 class="fw-bold mb-0">{{ $project->nama_project }}</h6>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <small class="text-muted">Kode: <strong>{{ $project->kode_project }}</strong></small>
                    @if($project->tanggal_kontrak)
                        <small class="text-muted"><i class="fas fa-file-signature me-1"></i>Kontrak: {{ $project->tanggal_kontrak->format('d M Y') }}</small>
                    @endif
                    @if($project->tanggal_mulai)
                        <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>Mulai: {{ $project->tanggal_mulai->format('d M Y') }}</small>
                    @endif
                </div>
            </div>
            <span class="badge rounded-pill {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                {{ ucfirst($project->status) }}
            </span>
        </div>
    </div>

    <!-- ASSIGN USER SECTION -->
    <div class="section-card">
        <div class="section-body">
            <h6 class="fw-bold mb-3" style="font-size:0.85rem"><i class="fas fa-user-plus text-primary me-2"></i>Assign User ke Project</h6>

            {{-- Currently assigned users --}}
            <div class="mb-3">
                @forelse($project->users as $assignedUser)
                    <span class="assigned-user">
                        <span class="role-badge role-{{ $assignedUser->role }}" style="font-size:0.55rem;padding:1px 5px">{{ strtoupper($assignedUser->role) }}</span>
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

            {{-- Assign form --}}
            @php
                $assignedIds = $project->users->pluck('id')->toArray();
                $availableUsers = $allUsers->whereNotIn('id', $assignedIds);
            @endphp
            @if($availableUsers->count() > 0)
            <form action="/admin/projects/assign-user" method="POST" class="mini-form">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <select name="user_id" class="form-select" required style="flex:1">
                    <option value="">-- Pilih User --</option>
                    @foreach($availableUsers as $au)
                        <option value="{{ $au->id }}">{{ $au->name }} ({{ strtoupper($au->role) }})</option>
                    @endforeach
                </select>
                <button class="btn btn-accent btn-sm"><i class="fas fa-plus me-1"></i>Assign</button>
            </form>
            @else
                <p class="text-muted mb-0" style="font-size:0.8rem">Semua user sudah di-assign ke project ini.</p>
            @endif
        </div>
    </div>

    <!-- ADD MODUL -->
    <div class="section-card">
        <div class="section-body">
            <h6 class="fw-bold mb-3" style="font-size:0.85rem"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Modul</h6>
            <form action="/admin/moduls" method="POST" class="mini-form">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="text" name="nama_modul" class="form-control" placeholder="Nama Modul" required style="flex:1">
                <input type="text" name="deskripsi" class="form-control" placeholder="Deskripsi (opsional)" style="flex:1">
                <button class="btn btn-accent btn-sm"><i class="fas fa-plus"></i></button>
            </form>
        </div>
    </div>

    <!-- MODULS LIST -->
    @foreach($moduls as $modul)
    <div class="section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#modul-{{ $modul->id }}">
            <div>
                <i class="fas fa-folder-open text-primary me-2"></i>
                <strong>{{ $modul->nama_modul }}</strong>
                <small class="text-muted ms-2">{{ $modul->deskripsi }}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-pill">{{ $modul->bloks->count() }} Blok</span>
                <form action="/admin/moduls/{{ $modul->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus modul ini beserta isinya?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" style="font-size:0.65rem;padding:2px 8px"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        <div class="collapse" id="modul-{{ $modul->id }}">
            <div class="section-body">
                <!-- Add Blok -->
                <form action="/admin/bloks" method="POST" class="mini-form mb-3">
                    @csrf
                    <input type="hidden" name="modul_id" value="{{ $modul->id }}">
                    <input type="text" name="nama_blok" class="form-control" placeholder="Nama Blok (contoh: BLOK 1)" required style="flex:1">
                    <button class="btn btn-accent btn-sm"><i class="fas fa-plus"></i> Blok</button>
                </form>

                @foreach($modul->bloks as $blok)
                <div class="nested mb-3">
                    <div class="item-row" data-bs-toggle="collapse" data-bs-target="#blok-{{ $blok->id }}" style="cursor:pointer">
                        <div><i class="fas fa-cubes text-warning me-2"></i><strong>{{ $blok->nama_blok }}</strong></div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size:0.6rem">{{ $blok->subBloks->count() }} Sub</span>
                            <form action="/admin/bloks/{{ $blok->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" style="font-size:0.6rem;padding:1px 6px"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                    </div>

                    <div class="collapse" id="blok-{{ $blok->id }}">
                        <div class="nested mt-2">
                            <!-- Add SubBlok -->
                            <form action="/admin/sub-bloks" method="POST" class="mini-form mb-2">
                                @csrf
                                <input type="hidden" name="blok_id" value="{{ $blok->id }}">
                                <input type="text" name="nama_sub_blok" class="form-control" placeholder="Nama Sub Blok" required style="flex:1">
                                <button class="btn btn-accent btn-sm"><i class="fas fa-plus"></i> Sub</button>
                            </form>

                            @foreach($blok->subBloks as $sub)
                            <div class="nested mb-2">
                                <div class="item-row" data-bs-toggle="collapse" data-bs-target="#sub-{{ $sub->id }}" style="cursor:pointer">
                                    <div><i class="fas fa-layer-group text-success me-2"></i><strong>{{ $sub->nama_sub_blok }}</strong></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success rounded-pill" style="font-size:0.6rem">{{ $sub->itps->count() }} ITP</span>
                                        <form action="/admin/sub-bloks/{{ $sub->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" style="font-size:0.6rem;padding:1px 6px"><i class="fas fa-times"></i></button>
                                        </form>
                                    </div>
                                </div>

                                <div class="collapse" id="sub-{{ $sub->id }}">
                                    <div class="nested mt-2">
                                        <!-- Add ITP -->
                                        <form action="/admin/itps" method="POST" class="mini-form mb-2" style="flex-wrap:wrap">
                                            @csrf
                                            <input type="hidden" name="sub_blok_id" value="{{ $sub->id }}">
                                            <div class="d-flex gap-2 w-100 mb-1">
                                                <input type="text" name="assembly_code" class="form-control" placeholder="Assembly Code" required style="flex:1">
                                                <input type="text" name="assembly_description" class="form-control" placeholder="Deskripsi Assembly (opsional)" style="flex:1">
                                            </div>
                                            <input type="text" name="code" class="form-control" placeholder="Kode" required style="width:80px">
                                            <input type="text" name="item" class="form-control" placeholder="Deskripsi Item" required style="flex:1">
                                            <select name="yard_val" class="form-select" style="width:70px" required title="Yard">
                                                <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                            </select>
                                            <select name="class_val" class="form-select" style="width:70px" required title="Class">
                                                <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                            </select>
                                            <select name="os_val" class="form-select" style="width:70px" required title="OS">
                                                <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                            </select>
                                            <select name="stat_val" class="form-select" style="width:70px" required title="Stat">
                                                <option value="W">W</option><option value="RV">RV</option><option value="-" selected>-</option><option value="NA">NA</option>
                                            </select>
                                            <button class="btn btn-accent btn-sm"><i class="fas fa-plus"></i></button>
                                        </form>

                                        @foreach($sub->itps as $itp)
                                        <div class="item-row">
                                            <div style="flex:1">
                                                <strong class="text-primary">{{ $itp->assembly_code }}</strong>
                                                @if($itp->assembly_description)
                                                    <small class="text-muted ms-1" title="{{ $itp->assembly_description }}"><i class="fas fa-info-circle"></i></small>
                                                @endif
                                                <span class="text-muted mx-1">›</span>
                                                <code>{{ $itp->code }}</code>
                                                <span class="text-muted mx-1">—</span>
                                                <span>{{ $itp->item }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="badge-val val-{{ strtolower($itp->yard_val) == 'w' ? 'w' : (strtolower($itp->yard_val) == 'rv' ? 'rv' : (strtolower($itp->yard_val) == 'na' ? 'na' : 'dash')) }}" title="Yard">Y:{{ $itp->yard_val }}</span>
                                                <span class="badge-val val-{{ strtolower($itp->class_val) == 'w' ? 'w' : (strtolower($itp->class_val) == 'rv' ? 'rv' : (strtolower($itp->class_val) == 'na' ? 'na' : 'dash')) }}" title="Class">C:{{ $itp->class_val }}</span>
                                                <span class="badge-val val-{{ strtolower($itp->os_val) == 'w' ? 'w' : (strtolower($itp->os_val) == 'rv' ? 'rv' : (strtolower($itp->os_val) == 'na' ? 'na' : 'dash')) }}" title="OS">O:{{ $itp->os_val }}</span>
                                                <span class="badge-val val-{{ strtolower($itp->stat_val) == 'w' ? 'w' : (strtolower($itp->stat_val) == 'rv' ? 'rv' : (strtolower($itp->stat_val) == 'na' ? 'na' : 'dash')) }}" title="Stat">S:{{ $itp->stat_val }}</span>
                                                <form action="/admin/itps/{{ $itp->id }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Hapus kode inspeksi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" style="font-size:0.6rem;padding:1px 6px"><i class="fas fa-times"></i></button>
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
</div>
@endsection
