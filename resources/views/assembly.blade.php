@extends('layouts.app')
@section('title', 'Assembly - ' . $subblok->nama_sub_blok)
@section('page-title', 'Kode Inspeksi')

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
        background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 60%);
        border-radius: 50%;
    }
    .page-header-card h4 { font-weight: 800; margin-bottom: 4px; position: relative; }
    .page-header-card p { color: #94a3b8; font-size: 0.85rem; margin: 0; position: relative; }

    .assembly-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: box-shadow 0.3s;
    }
    .assembly-card:hover { box-shadow: 0 8px 25px -8px rgba(0,0,0,0.08); }
    .assembly-header {
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .assembly-header:hover { background: #f8fafc; }
    .assembly-header .asm-code { font-weight: 800; font-size: 1rem; color: var(--accent); }
    .assembly-header .asm-desc { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; }
    .assembly-header .asm-count {
        font-size: 0.65rem; font-weight: 700;
        background: var(--accent-glow); color: var(--accent);
        padding: 4px 10px; border-radius: 1rem;
    }
    .assembly-body { border-top: 1px solid var(--border); }

    .code-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
        gap: 8px;
    }
    .code-row:last-child { border-bottom: none; }
    .code-row:hover { background: #fafbfd; }
    .code-info { flex: 1; min-width: 0; }
    .code-label { font-size: 0.65rem; font-weight: 700; color: var(--accent); letter-spacing: 1px; text-transform: uppercase; }
    .code-item { font-size: 0.88rem; font-weight: 600; color: var(--text); margin-top: 2px; }
    .code-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .badge-val { font-size: 0.55rem; font-weight: 800; padding: 3px 7px; border-radius: 4px; text-transform: uppercase; }
    .val-w { background: #fef3c7; color: #92400e; }
    .val-rv { background: #dbeafe; color: #1e40af; }
    .val-dash { background: #f1f5f9; color: #94a3b8; }
    .val-na { background: #fee2e2; color: #991b1b; }

    .role-dots { display: flex; gap: 3px; }
    .role-dot {
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.5rem; font-weight: 800; transition: transform 0.2s;
    }
    .role-dot:hover { transform: scale(1.2); }
    .role-dot.done { background: #10b981; color: #fff; }
    .role-dot.approved { background: #3b82f6; color: #fff; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #3b82f6; }
    .role-dot.pending { background: #e2e8f0; color: #94a3b8; }

    .btn-itp {
        border: none; padding: 6px 14px; border-radius: 0.5rem;
        font-size: 0.72rem; font-weight: 700; cursor: pointer;
        transition: all 0.2s; display: inline-flex; align-items: center;
        gap: 6px; white-space: nowrap; color: #fff;
    }
    .btn-itp:hover { transform: translateY(-1px); }
    .btn-itp-submit { background: linear-gradient(135deg, #3b82f6, #6366f1); }
    .btn-itp-submit:hover { box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
    .btn-itp-done { background: linear-gradient(135deg, #10b981, #059669); }
    .btn-itp-approved { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .btn-itp-view { background: linear-gradient(135deg, #64748b, #475569); }

    /* MODAL */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15,23,42,0.7);
        backdrop-filter: blur(6px);
        z-index: 2000; display: none;
        align-items: center; justify-content: center;
        padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
        background: #fff; border-radius: 1.25rem;
        width: 100%; max-width: 580px;
        max-height: 90vh; overflow-y: auto;
        box-shadow: 0 25px 60px -12px rgba(0,0,0,0.3);
        animation: modalIn 0.3s ease-out;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-hdr {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-bdy { padding: 1.5rem; }
    .modal-close {
        width: 34px; height: 34px; border-radius: 50%; border: none;
        background: #f1f5f9; color: var(--text-muted); cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: #fee2e2; color: #ef4444; }

    .photo-preview {
        width: 100%; max-height: 280px;
        object-fit: contain; border-radius: 0.75rem;
        border: 1px solid var(--border); margin-bottom: 1rem;
    }
    .upload-zone {
        border: 2px dashed var(--border); border-radius: 1rem;
        padding: 1.5rem; text-align: center; cursor: pointer;
        transition: all 0.3s; position: relative;
    }
    .upload-zone:hover { border-color: var(--accent); background: var(--accent-glow); }
    .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

    .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.25rem; }
    .status-item {
        display: flex; align-items: center; gap: 8px;
        padding: 0.6rem 0.75rem; border-radius: 0.625rem;
        background: #f8fafc; border: 1px solid #f1f5f9;
    }
    .status-item.is-me { border-color: var(--accent); background: var(--accent-glow); }
    .status-item .role-name { font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }
    .status-item .role-status { font-size: 0.7rem; margin-left: auto; }

    /* ACC Buttons */
    .btn-acc {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff; border: none; border-radius: 0.625rem;
        padding: 0.6rem 1.5rem; font-weight: 700; font-size: 0.85rem;
        cursor: pointer; transition: all 0.25s; display: inline-flex;
        align-items: center; gap: 8px;
    }
    .btn-acc:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(16,185,129,0.3); }
    .btn-acc:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .acc-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0.5rem 1rem; border-radius: 0.625rem;
        font-size: 0.8rem; font-weight: 700;
    }
    .acc-approved {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1));
        color: #059669; border: 1px solid rgba(16,185,129,0.2);
    }
    .acc-rejected {
        background: rgba(239,68,68,0.08);
        color: #dc2626; border: 1px solid rgba(239,68,68,0.2);
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="page-header-card">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-microchip me-2"></i>{{ $subblok->nama_sub_blok }}</h4>
                <p>Assembly Code dan Kode Inspeksi
                    <span class="badge bg-primary rounded-pill ms-2" style="font-size:0.65rem">{{ $grouped->count() }} Assembly</span>
                </p>
            </div>
            <a href="/subblok/{{ $blok->id }}" class="btn-back" style="color:#cbd5e1;border-color:rgba(255,255,255,0.15)"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    @forelse($grouped as $assemblyCode => $items)
    <div class="assembly-card">
        <div class="assembly-header" data-bs-toggle="collapse" data-bs-target="#asm-{{ Str::slug($assemblyCode) }}">
            <div>
                <div class="asm-code"><i class="fas fa-microchip me-2"></i>{{ $assemblyCode }}</div>
                @if($items->first()->assembly_description)
                    <div class="asm-desc">{{ $items->first()->assembly_description }}</div>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="asm-count">{{ $items->count() }} Kode</span>
                <i class="fas fa-chevron-down text-muted" style="font-size:0.75rem"></i>
            </div>
        </div>
        <div class="collapse" id="asm-{{ Str::slug($assemblyCode) }}">
            <div class="assembly-body">
                @foreach($items as $itp)
                @php
                    $val = $itp->getValForRole($role);
                    $canSubmit = in_array(strtoupper($val), ['W', 'RV']);
                    $myData = $itp->itpData->where('uploaded_by', $userId)->first();
                    $myDone = $myData && $myData->status === 'done';
                    $myApproved = $myData && $myData->status === 'approved';

                    $roleDoneStatus = [];
                    foreach(['yard', 'class', 'os', 'stat'] as $r) {
                        $rVal = $itp->getValForRole($r);
                        if (in_array(strtoupper($rVal), ['W', 'RV'])) {
                            $rData = $itp->itpData->first(fn($d) => $d->uploader && $d->uploader->role === $r);
                            $roleDoneStatus[$r] = $rData ? $rData->status : 'pending';
                        }
                    }

                    $valClass = match(strtoupper($val)) {
                        'W' => 'val-w', 'RV' => 'val-rv', 'NA' => 'val-na', default => 'val-dash',
                    };
                @endphp
                <div class="code-row">
                    <div class="code-info">
                        <div class="code-label">{{ $itp->code }}</div>
                        <div class="code-item">{{ $itp->item }}</div>
                    </div>
                    <div class="code-actions">
                        <div class="role-dots">
                            @foreach(['yard' => 'Y', 'class' => 'C', 'os' => 'O', 'stat' => 'S'] as $r => $label)
                                @if(isset($roleDoneStatus[$r]))
                                    <span class="role-dot {{ $roleDoneStatus[$r] === 'approved' ? 'approved' : ($roleDoneStatus[$r] === 'done' ? 'done' : 'pending') }}"
                                          title="{{ ucfirst($r) }}: {{ ucfirst($roleDoneStatus[$r]) }}">{{ $label }}</span>
                                @endif
                            @endforeach
                        </div>

                        <span class="badge-val {{ $valClass }}">{{ strtoupper($val) }}</span>

                        @if($canSubmit)
                            @if($myApproved)
                                <button class="btn-itp btn-itp-approved" onclick="openItpModal({{ $itp->id }})">
                                    <i class="fas fa-shield-alt"></i> ACC
                                </button>
                            @elseif($myDone)
                                <button class="btn-itp btn-itp-done" onclick="openItpModal({{ $itp->id }})">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </button>
                            @else
                                <button class="btn-itp btn-itp-submit" onclick="openItpModal({{ $itp->id }})">
                                    <i class="fas fa-file-upload"></i> ITP Data
                                </button>
                            @endif
                        @else
                            <button class="btn-itp btn-itp-view" onclick="openItpModal({{ $itp->id }})">
                                <i class="fas fa-chart-bar"></i> Status
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-clipboard-check fa-3x mb-3" style="color:#e2e8f0"></i>
        <p class="text-muted fw-bold">Tidak ada kode inspeksi tersedia.</p>
    </div>
    @endforelse
</div>

<!-- ITP DATA MODAL -->
<div class="modal-overlay" id="itpModal">
    <div class="modal-box">
        <div class="modal-hdr">
            <h6 class="fw-bold mb-0" id="modalTitle"><i class="fas fa-file-alt me-2 text-primary"></i>ITP Data</h6>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-bdy" id="modalBody">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2">Memuat data...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const userRole = '{{ $role }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openItpModal(itpId) {
    const modal = document.getElementById('itpModal');
    const body = document.getElementById('modalBody');
    modal.classList.add('active');
    body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="text-muted mt-2">Memuat...</p></div>';

    fetch(`/itp-data/${itpId}`)
        .then(r => r.json())
        .then(res => {
            const itp = res.itp;
            const myData = res.my_data;
            const allData = res.all_data;
            const canSubmit = res.can_submit;
            const val = res.val;
            const allVals = res.all_vals;
            const photoRequired = res.photo_required;
            const role = res.role;
            const canAccRole = res.can_acc_role;
            const visibleRoles = res.visible_roles || [];

            document.getElementById('modalTitle').innerHTML = `<i class="fas fa-file-alt me-2 text-primary"></i>${itp.code} — ${itp.item}`;

            let html = '<div class="mb-3"><div class="fw-bold mb-2" style="font-size:0.8rem"><i class="fas fa-users me-1 text-primary"></i>Status Semua Role</div>';
            html += '<div class="status-grid">';

            const roleLabels = { yard: 'Yard', class: 'Class', os: 'OS', stat: 'Stat' };
            const roleIcons = { yard: 'fa-hard-hat', class: 'fa-user-shield', os: 'fa-user-tie', stat: 'fa-stamp' };

            for (const [r, rVal] of Object.entries(allVals)) {
                const rData = allData.find(d => d.role === r);
                const isApproved = rData && rData.status === 'approved';
                const isRejected = rData && (rData.status === 'rejected' || rData.status === 'needs_revision');
                const isDone = rData && rData.status === 'done';
                const isRequired = rVal === 'W' || rVal === 'RV';
                const isMe = r === role;
                const canView = visibleRoles.includes(r);

                let statusHtml;
                if (!isRequired) {
                    statusHtml = `<span class="text-muted" style="font-size:0.7rem">${rVal === 'NA' ? 'N/A' : '—'}</span>`;
                } else if (isApproved) {
                    statusHtml = '<span class="text-primary" style="font-size:0.7rem"><i class="fas fa-shield-alt me-1"></i>ACC</span>';
                } else if (isRejected) {
                    statusHtml = '<span class="text-danger" style="font-size:0.7rem"><i class="fas fa-times-circle me-1"></i>Revisi</span>';
                } else if (isDone) {
                    statusHtml = '<span class="text-success" style="font-size:0.7rem"><i class="fas fa-check-circle me-1"></i>Done</span>';
                } else {
                    statusHtml = '<span class="text-warning" style="font-size:0.7rem"><i class="fas fa-clock me-1"></i>Pending</span>';
                }

                const clickable = canView && isRequired && rData && !isMe;
                html += `<div class="status-item ${isMe ? 'is-me' : ''} ${clickable ? 'cursor-pointer' : ''}" ${clickable ? `onclick="toggleRoleDetail('${r}')"` : ''} style="${clickable ? 'cursor:pointer' : ''}">
                    <i class="fas ${roleIcons[r]} ${isMe ? 'text-primary' : 'text-muted'}" style="font-size:0.85rem"></i>
                    <div>
                        <div class="role-name ${isMe ? 'text-primary' : ''}">${roleLabels[r]}${isMe ? ' (Anda)' : ''}${clickable ? ' <i class="fas fa-eye" style="font-size:0.5rem"></i>' : ''}</div>
                        <span class="badge-val ${rVal === 'W' ? 'val-w' : rVal === 'RV' ? 'val-rv' : rVal === 'NA' ? 'val-na' : 'val-dash'}" style="font-size:0.5rem">${rVal}</span>
                    </div>
                    <div class="role-status">${statusHtml}</div>
                </div>`;

                // Expandable detail panel for cross-role visibility
                if (clickable && rData) {
                    html += `<div id="detail-${r}" style="display:none;grid-column:1/-1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.5rem">
                        <div class="fw-bold mb-1" style="font-size:0.75rem"><i class="fas fa-user me-1"></i>${rData.name} (${roleLabels[r]})</div>
                        ${rData.photo ? `<img src="/storage/${rData.photo}" style="width:100%;max-height:180px;object-fit:contain;border-radius:0.5rem;margin-bottom:0.5rem">` : ''}
                        <div style="font-size:0.8rem">${rData.keterangan || '<em class="text-muted">Tidak ada keterangan</em>'}</div>
                        <div class="text-muted mt-1" style="font-size:0.65rem"><i class="fas fa-clock me-1"></i>${rData.updated_at ? new Date(rData.updated_at).toLocaleString('id-ID') : '-'}</div>
                        ${rData.rejection_note ? `<div class="text-danger mt-1" style="font-size:0.7rem"><i class="fas fa-exclamation-triangle me-1"></i>Catatan reject: ${rData.rejection_note}</div>` : ''}
                    </div>`;
                }
            }
            html += '</div></div>';

            // === ACC/REJECT SUBORDINATE DATA (hierarchy-based) ===
            const subData = allData.filter(d => d.can_acc || d.can_reject);
            if (subData.length > 0) {
                html += '<hr style="border-color:#f1f5f9">';
                html += '<div class="fw-bold mb-2" style="font-size:0.8rem"><i class="fas fa-gavel me-1 text-success"></i>Review Data Bawahan</div>';
                subData.forEach(d => {
                    html += `<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:0.75rem;padding:0.75rem;margin-bottom:0.5rem">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="font-size:0.8rem"><i class="fas fa-user me-1"></i>${d.name} (${roleLabels[d.role] || d.role})</span>
                            <span class="badge bg-warning text-dark" style="font-size:0.6rem">Menunggu Review</span>
                        </div>
                        ${d.photo ? `<img src="/storage/${d.photo}" style="width:100%;max-height:150px;object-fit:contain;border-radius:0.5rem;margin-bottom:0.5rem">` : ''}
                        <div style="font-size:0.8rem;margin-bottom:0.5rem">${d.keterangan || '-'}</div>
                        <div class="d-flex gap-2">
                            <button onclick="accItpData(${d.id})" class="btn-acc flex-fill" style="padding:0.4rem;font-size:0.8rem" id="accBtn-${d.id}">
                                <i class="fas fa-check-circle"></i> ACC
                            </button>
                            <button onclick="showRejectForm(${d.id})" class="btn btn-outline-danger flex-fill" style="padding:0.4rem;font-size:0.8rem;border-radius:0.625rem;font-weight:700">
                                <i class="fas fa-times-circle"></i> Reject
                            </button>
                        </div>
                        <div id="rejectForm-${d.id}" style="display:none;margin-top:0.5rem">
                            <textarea id="rejectNote-${d.id}" class="form-control mb-2" rows="2" placeholder="Alasan reject (wajib diisi)..." style="font-size:0.8rem"></textarea>
                            <button onclick="rejectItpData(${d.id})" class="btn btn-danger w-100" style="font-size:0.8rem;font-weight:700;border-radius:0.625rem" id="rejectBtn-${d.id}">
                                <i class="fas fa-paper-plane me-1"></i>Kirim Reject
                            </button>
                        </div>
                    </div>`;
                });
            }

            // === MY SUBMIT SECTION ===
            if (canSubmit) {
                html += '<hr style="border-color:#f1f5f9">';

                if (myData && myData.status === 'approved') {
                    html += `<div class="text-center mb-3">
                        <div class="acc-badge acc-approved"><i class="fas fa-shield-alt"></i> Data Anda sudah di-ACC</div>
                        <div class="text-muted mt-1" style="font-size:0.7rem">Disetujui pada: ${myData.approved_at ? new Date(myData.approved_at).toLocaleString('id-ID') : '-'}</div>
                    </div>`;
                } else if (myData && (myData.status === 'needs_revision' || myData.status === 'rejected')) {
                    html += `<div class="text-center mb-3">
                        <div class="acc-badge acc-rejected"><i class="fas fa-redo"></i> Perlu Revisi</div>
                        ${myData.rejection_note ? `<div class="text-danger mt-1" style="font-size:0.75rem"><i class="fas fa-comment-alt me-1"></i>Catatan: ${myData.rejection_note}</div>` : ''}
                    </div>`;
                }

                const needsResubmit = myData && (myData.status === 'needs_revision' || myData.status === 'rejected');
                const showForm = !myData || myData.status === 'done' || needsResubmit;

                if (showForm) {
                    html += `<div class="fw-bold mb-2" style="font-size:0.8rem"><i class="fas fa-upload me-1 text-primary"></i>${needsResubmit ? 'Resubmit Data ITP' : 'Upload Data ITP'}</div>`;

                    if (myData && myData.photo) {
                        html += `<img src="/storage/${myData.photo}" class="photo-preview" id="previewImg">`;
                    }

                    html += `<div class="upload-zone" id="uploadZone">
                        <input type="file" accept="image/*" id="photoInput" onchange="previewPhoto(this)">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0" style="font-size:0.8rem">${myData && myData.photo ? 'Ganti foto' : 'Klik untuk upload foto'}</p>
                        ${photoRequired ? '<p class="text-danger mt-1 mb-0" style="font-size:0.65rem"><i class="fas fa-exclamation-triangle me-1"></i>Foto WAJIB (Witness)</p>' : '<p class="text-muted mt-1 mb-0" style="font-size:0.65rem">Opsional</p>'}
                    </div>`;

                    html += `<div class="mb-3 mt-3">
                        <label class="form-label fw-bold" style="font-size:0.8rem">Keterangan</label>
                        <textarea id="keteranganInput" class="form-control" rows="2" placeholder="Catatan inspeksi..." style="font-size:0.85rem">${myData ? (myData.keterangan || '') : ''}</textarea>
                    </div>`;

                    const btnLabel = needsResubmit ? '<i class="fas fa-redo me-2"></i>Resubmit Data ITP' : (myData ? '<i class="fas fa-save me-2"></i>Update Data ITP' : '<i class="fas fa-save me-2"></i>Simpan Data ITP');
                    html += `<button onclick="submitItpData(${itp.id})" class="btn ${needsResubmit ? 'btn-warning' : 'btn-accent'} w-100 mb-2" id="submitBtn">${btnLabel}</button>`;
                }

                if (myData) {
                    html += `<div class="text-center mt-2"><small class="text-muted"><i class="fas fa-clock me-1"></i>Terakhir disimpan: ${new Date(myData.updated_at).toLocaleString('id-ID')}</small></div>`;
                }
            } else {
                html += '<hr style="border-color:#f1f5f9">';
                html += `<div class="text-center py-3" style="background:#f8fafc;border-radius:0.75rem">
                    <i class="fas fa-info-circle fa-2x text-muted d-block mb-2"></i>
                    <p class="fw-bold text-muted mb-1" style="font-size:0.85rem">Status Anda: <span class="badge-val ${val === 'NA' ? 'val-na' : 'val-dash'}">${val}</span></p>
                    <p class="text-muted mb-0" style="font-size:0.8rem">Anda tidak perlu submit data untuk kode inspeksi ini.</p>
                </div>`;
            }

            body.innerHTML = html;
        })
        .catch(err => {
            body.innerHTML = '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle fa-2x mb-2"></i><p>Gagal memuat data</p></div>';
            console.error(err);
        });
}

function closeModal() { document.getElementById('itpModal').classList.remove('active'); }
document.getElementById('itpModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('previewImg');
            if (!img) {
                img = document.createElement('img');
                img.id = 'previewImg';
                img.className = 'photo-preview';
                document.getElementById('uploadZone').before(img);
            }
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function submitItpData(itpId) {
    const btn = document.getElementById('submitBtn');
    const photoInput = document.getElementById('photoInput');
    const formData = new FormData();
    formData.append('itp_id', itpId);
    formData.append('keterangan', document.getElementById('keteranganInput').value);
    formData.append('_token', csrfToken);
    if (photoInput.files[0]) formData.append('photo', photoInput.files[0]);

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

    fetch('/itp-data', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Berhasil!';
            btn.className = 'btn btn-success w-100 mb-2';
            setTimeout(() => { closeModal(); location.reload(); }, 800);
        } else {
            alert(res.message || 'Gagal menyimpan');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Data ITP';
        }
    })
    .catch(() => {
        alert('Terjadi kesalahan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Data ITP';
    });
}

function accItpData(dataId) {
    if (!confirm('Apakah Anda yakin ingin ACC (approve) data ini?')) return;

    const btn = document.getElementById('accBtn-' + dataId) || document.getElementById('accBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...'; }

    fetch(`/itp-data/${dataId}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (btn) { btn.innerHTML = '<i class="fas fa-shield-alt me-1"></i> ACC Berhasil!'; btn.style.background = 'linear-gradient(135deg, #3b82f6, #2563eb)'; }
            setTimeout(() => { closeModal(); location.reload(); }, 800);
        } else {
            alert(res.message || 'Gagal ACC');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> ACC'; }
        }
    })
    .catch(() => { alert('Terjadi kesalahan'); if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> ACC'; } });
}

function showRejectForm(dataId) {
    const form = document.getElementById('rejectForm-' + dataId);
    if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function rejectItpData(dataId) {
    const note = document.getElementById('rejectNote-' + dataId)?.value?.trim();
    if (!note || note.length < 3) { alert('Alasan reject wajib diisi (minimal 3 karakter)'); return; }

    const btn = document.getElementById('rejectBtn-' + dataId);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...'; }

    fetch(`/itp-data/${dataId}/reject`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ note: note }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (btn) btn.innerHTML = '<i class="fas fa-check me-1"></i>Ditolak!';
            setTimeout(() => { closeModal(); location.reload(); }, 800);
        } else {
            alert(res.message || 'Gagal reject');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Reject'; }
        }
    })
    .catch(() => { alert('Terjadi kesalahan'); if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Kirim Reject'; } });
}

function toggleRoleDetail(role) {
    const el = document.getElementById('detail-' + role);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection