@extends('layouts.app')
@section('title', 'Buat Akun Baru')
@section('page-title', 'Buat Akun Baru')

@section('content')
<div class="fade-up">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <a href="/admin/dashboard" class="btn-back mb-3 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali</a>

            <div class="content-card">
                <div class="content-card-header">
                    <h6 class="fw-bold mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Form Buat Akun</h6>
                </div>
                <div class="content-card-body">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 py-2">
                            @foreach($errors->all() as $err)
                                <div><i class="fas fa-exclamation-circle me-1"></i>{{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="/admin/users" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Masukkan username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 4 karakter">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size:0.85rem">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="yard" {{ old('role') == 'yard' ? 'selected' : '' }}>Yard</option>
                                <option value="class" {{ old('role') == 'class' ? 'selected' : '' }}>Class</option>
                                <option value="os" {{ old('role') == 'os' ? 'selected' : '' }}>Owner Surveyor (OS)</option>
                                <option value="stat" {{ old('role') == 'stat' ? 'selected' : '' }}>Statutory (Stat)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-accent w-100">
                            <i class="fas fa-save me-2"></i>Simpan Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
