@extends('layouts.app')
@section('title', 'Start Project')
@section('page-title', 'Start Project Baru')

@section('content')
<div class="fade-up">
    <div class="row justify-content-center">
        <div class="col-lg-6">
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

                    <form action="/admin/projects" method="POST">
                        @csrf
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
                                <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold" style="font-size:0.85rem">Deadline <span class="text-danger">*</span></label>
                                <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Deskripsi <span class="text-muted fw-normal">(opsional)</span></label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi project...">{{ old('deskripsi') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">
                            <i class="fas fa-rocket me-2"></i>Start Project
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
