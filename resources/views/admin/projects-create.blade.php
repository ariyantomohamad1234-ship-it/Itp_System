@extends('layouts.app')
@section('title', 'Start Project')
@section('page-title', 'Start Project Baru')

@section('styles')
<style>
    .mode-selector {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .mode-card {
        flex: 1;
        padding: 1.25rem;
        border: 2px solid var(--border, #e2e8f0);
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        background: var(--card, #fff);
    }
    .mode-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 15px rgba(99,102,241,0.15);
    }
    .mode-card.active {
        border-color: #6366f1;
        background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(139,92,246,0.05));
        box-shadow: 0 4px 15px rgba(99,102,241,0.2);
    }
    .mode-card .mode-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .mode-card .mode-title {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }
    .mode-card .mode-desc {
        font-size: 0.75rem;
        color: #64748b;
    }
    .template-select-wrapper {
        transition: all 0.3s ease;
    }
    .template-select-wrapper.hidden {
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }
    .template-info {
        background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
        border: 1px solid #86efac;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
        margin-top: 0.75rem;
        color: #166534;
    }
    .template-info i { color: #22c55e; }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <a href="/admin/dashboard" class="btn-back mb-3 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali</a>

            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-0"><i class="fas fa-rocket me-2 text-primary"></i>Form Start Project</h6>
                </div>
                <div class="content-card-body">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 py-2" style="font-size:0.85rem">
                            @foreach($errors->all() as $err)
                                <div><i class="fas fa-exclamation-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- MODE SELECTOR --}}
                    <label class="form-label fw-bold" style="font-size:0.85rem">
                        <i class="fas fa-cogs me-1 text-primary"></i>Mode Pembuatan Project
                    </label>
                    <div class="mode-selector">
                        <div class="mode-card active" id="mode-template" onclick="setMode('template')">
                            <span class="mode-icon">📋</span>
                            <div class="mode-title">Dari Template</div>
                            <div class="mode-desc">Struktur modul, blok, sub-blok & ITP otomatis di-generate</div>
                        </div>
                        <div class="mode-card" id="mode-manual" onclick="setMode('manual')">
                            <span class="mode-icon">✏️</span>
                            <div class="mode-title">Custom / Manual</div>
                            <div class="mode-desc">Buat project kosong, isi data secara manual</div>
                        </div>
                    </div>

                    <form action="/admin/projects" method="POST">
                        @csrf

                        {{-- TEMPLATE SELECTOR --}}
                        <div class="template-select-wrapper mb-3" id="template-section">
                            <label class="form-label fw-bold" style="font-size:0.85rem">
                                <i class="fas fa-layer-group me-1 text-success"></i>Pilih Template
                            </label>
                            <select name="template_id" id="template_id" class="form-select">
                                <option value="">-- Pilih Template --</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="template-info" id="template-info" style="display:none">
                                <i class="fas fa-check-circle me-1"></i>
                                <span id="template-info-text"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Nama Project</label>
                            <input type="text" name="nama_project" class="form-control" value="{{ old('nama_project') }}" required placeholder="Contoh: Mini LNG Vessel Hull 001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Kode Project</label>
                            <input type="text" name="kode_project" class="form-control" value="{{ old('kode_project') }}" required placeholder="Contoh: LNG-001">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:0.85rem">Tanggal Kontrak</label>
                                <input type="date" name="tanggal_kontrak" class="form-control" value="{{ old('tanggal_kontrak') }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:0.85rem">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:0.85rem">Deadline <span class="text-danger">*</span></label>
                                <input type="date" name="deadline" id="deadline" class="form-control" value="{{ old('deadline') }}">
                            </div>
                        </div>
                        <div id="deadline-warning" class="mb-3" style="display:none">
                            <div style="background:#fef3c7;border:1px solid #fbbf24;border-radius:0.5rem;padding:0.6rem 1rem;font-size:0.8rem;color:#92400e">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <span id="deadline-warning-text"></span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Deskripsi <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi project...">{{ old('deskripsi') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-accent w-100" id="submit-btn">
                            <i class="fas fa-rocket me-2"></i>Start Project dari Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const templateDescriptions = @json($templates->pluck('description', 'id'));
    const templateDurations = @json($templates->pluck('standard_duration', 'id'));
    let currentMode = 'template';

    function setMode(mode) {
        currentMode = mode;
        const templateSection = document.getElementById('template-section');
        const templateInput = document.getElementById('template_id');
        const modeTemplate = document.getElementById('mode-template');
        const modeManual = document.getElementById('mode-manual');
        const submitBtn = document.getElementById('submit-btn');

        if (mode === 'template') {
            modeTemplate.classList.add('active');
            modeManual.classList.remove('active');
            templateSection.classList.remove('hidden');
            submitBtn.innerHTML = '<i class="fas fa-rocket me-2"></i>Start Project dari Template';
        } else {
            modeManual.classList.add('active');
            modeTemplate.classList.remove('active');
            templateSection.classList.add('hidden');
            templateInput.value = '';
            document.getElementById('template-info').style.display = 'none';
            document.getElementById('deadline-warning').style.display = 'none';
            submitBtn.innerHTML = '<i class="fas fa-rocket me-2"></i>Start Project (Manual)';
        }
    }

    document.getElementById('template_id').addEventListener('change', function() {
        const info = document.getElementById('template-info');
        const infoText = document.getElementById('template-info-text');
        if (this.value && templateDescriptions[this.value]) {
            infoText.textContent = templateDescriptions[this.value];
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
        // Auto-compute deadline if tanggal_mulai is set
        autoComputeDeadline();
    });

    // Auto-deadline computation
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    const deadlineInput = document.getElementById('deadline');

    function autoComputeDeadline() {
        const templateId = document.getElementById('template_id').value;
        const startDate = tanggalMulaiInput.value;

        if (currentMode !== 'template' || !templateId || !startDate) {
            document.getElementById('deadline-warning').style.display = 'none';
            return;
        }

        const duration = templateDurations[templateId];
        if (!duration) return;

        // Auto-fill deadline if empty
        if (!deadlineInput.value) {
            const start = new Date(startDate);
            start.setDate(start.getDate() + parseInt(duration));
            deadlineInput.value = start.toISOString().split('T')[0];
        }

        checkDeadlineWarning();
    }

    function checkDeadlineWarning() {
        const templateId = document.getElementById('template_id').value;
        const startDate = tanggalMulaiInput.value;
        const deadlineDate = deadlineInput.value;
        const warningEl = document.getElementById('deadline-warning');
        const warningText = document.getElementById('deadline-warning-text');

        if (currentMode !== 'template' || !templateId || !startDate || !deadlineDate) {
            warningEl.style.display = 'none';
            return;
        }

        const duration = templateDurations[templateId];
        if (!duration) { warningEl.style.display = 'none'; return; }

        const start = new Date(startDate);
        const deadline = new Date(deadlineDate);
        const diffDays = Math.floor((deadline - start) / (1000 * 60 * 60 * 24));

        if (diffDays < parseInt(duration)) {
            warningText.textContent = `Durasi ini (${diffDays} hari) lebih pendek dari standar (${duration} hari). Tidak disarankan.`;
            warningEl.style.display = 'block';
        } else {
            warningEl.style.display = 'none';
        }
    }

    tanggalMulaiInput.addEventListener('change', autoComputeDeadline);
    deadlineInput.addEventListener('change', checkDeadlineWarning);

    // Initialize: trigger change if template was pre-selected (old value)
    document.addEventListener('DOMContentLoaded', function() {
        const templateSelect = document.getElementById('template_id');
        if (templateSelect.value) {
            templateSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
