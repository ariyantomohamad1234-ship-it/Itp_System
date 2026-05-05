@extends('layouts.app')
@section('title', 'Buat Akun Baru')
@section('page-title', 'Buat Akun Baru')

@section('styles')
<style>
    .premium-form-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1.5rem;
        padding: 2.5rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    .premium-form-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px;
        background: linear-gradient(90deg, #3b82f6, #10b981);
    }
    .form-label-premium {
        font-weight: 800; font-size: 0.85rem; color: var(--text);
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;
    }
    .form-control-premium {
        border: 2px solid #e2e8f0; border-radius: 1rem; padding: 0.8rem 1.2rem;
        font-size: 0.95rem; transition: all 0.3s; background: #f8fafc;
    }
    .form-control-premium:focus {
        border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .btn-submit-premium {
        background: linear-gradient(135deg, #3b82f6, #2563eb); border: none;
        border-radius: 1rem; padding: 1rem; font-weight: 800; font-size: 1rem;
        color: #fff; transition: all 0.3s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .btn-submit-premium:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4); }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <a href="/admin/dashboard" class="btn-back mb-4 d-inline-flex"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>

            <div class="premium-form-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: #eff6ff; color: #3b82f6; border-radius: 1rem; font-size: 1.5rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h4 class="fw-bold m-0">Registrasi Akun Baru</h4>
                    <p class="text-muted small mt-1">Tambahkan pengguna baru ke dalam ITP System</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 py-2 px-3 mb-4" style="font-size:0.85rem">
                        @foreach($errors->all() as $err)
                            <div class="mb-1"><i class="fas fa-exclamation-circle me-2"></i>{{ $err }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="/admin/users" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label-premium">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control form-control-premium" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap">
                    </div>
                    <div class="mb-4">
                        <label class="form-label-premium">Username</label>
                        <input type="text" name="username" class="form-control form-control-premium" value="{{ old('username') }}" required placeholder="Masukkan username unik">
                    </div>
                    <div class="mb-4">
                        <label class="form-label-premium">Password</label>
                        <input type="password" name="password" class="form-control form-control-premium" required placeholder="Minimal 4 karakter">
                    </div>
                    <div class="mb-5">
                        <label class="form-label-premium">Peran (Role)</label>
                        <select name="role" class="form-select form-control-premium" required>
                            <option value="">-- Pilih Akses Role --</option>
                            <option value="yard" {{ old('role') == 'yard' ? 'selected' : '' }}>Yard (Pelaksana)</option>
                            <option value="class" {{ old('role') == 'class' ? 'selected' : '' }}>Class (Klasifikasi)</option>
                            <option value="os" {{ old('role') == 'os' ? 'selected' : '' }}>Owner Surveyor (OS)</option>
                            <option value="stat" {{ old('role') == 'stat' ? 'selected' : '' }}>Statutory (Stat)</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Pusat</option>
                            <option value="admin_galangan" {{ old('role') == 'admin_galangan' ? 'selected' : '' }}>Admin Galangan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-submit-premium w-100">
                        <i class="fas fa-user-check me-2"></i> Buat Akun Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
