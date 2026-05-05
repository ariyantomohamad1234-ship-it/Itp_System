@extends('layouts.app')
@section('title', 'System Logs')
@section('page-title', 'Audit Trail / System Logs')

@section('content')
<div class="fade-up">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="fas fa-list-alt me-2 text-primary"></i>Riwayat Aktivitas</h4>
            <p class="text-muted small m-0">Log sistem untuk pemantauan keamanan dan audit</p>
        </div>
        <span class="badge bg-primary px-3 py-2" style="border-radius: 1rem;">Total: {{ $logs->total() }} log</span>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 1.25rem; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.7rem; letter-spacing: 1px;">Waktu</th>
                        <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.7rem; letter-spacing: 1px;">User</th>
                        <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.7rem; letter-spacing: 1px;">Aksi</th>
                        <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.7rem; letter-spacing: 1px;">Deskripsi</th>
                        <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.7rem; letter-spacing: 1px;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr style="transition: all 0.2s;">
                        <td class="text-muted px-4 py-3" style="white-space:nowrap">
                            <i class="far fa-clock me-1"></i> {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-bold text-dark">{{ $log->user->name ?? 'System' }}</div>
                            <span class="badge bg-light text-secondary border mt-1" style="font-size:0.6rem">{{ strtoupper($log->user->role ?? 'System') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1" style="font-size:0.65rem">
                                <i class="fas fa-bolt me-1"></i> {{ strtoupper($log->activity_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $log->description }}</td>
                        <td class="text-muted small px-4 py-3" style="font-family: monospace;"><i class="fas fa-network-wired me-1"></i>{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="m-0">Belum ada log aktivitas tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
