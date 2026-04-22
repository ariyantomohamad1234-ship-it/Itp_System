<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Itp;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    private const ROLE_ABOVE = [
        'yard'  => 'os',
        'os'    => 'class',
        'class' => 'stat',
    ];

    /**
     * Notify when a role submits ITP data → send to role above
     */
    public function notifySubmit(Itp $itp, User $submitter): void
    {
        $targetRole = self::ROLE_ABOVE[$submitter->role] ?? null;
        if (!$targetRole) return;

        // Check if the target role has authority (W/RV) on this ITP
        $targetVal = $itp->getValForRole($targetRole);
        if (!in_array(strtoupper($targetVal), ['W', 'RV'])) return;

        $projectId = $this->getProjectId($itp);
        $recipients = $this->getProjectUsersWithRole($projectId, $targetRole);

        foreach ($recipients as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'submit',
                'title' => 'Data ITP Baru',
                'message' => "{$submitter->name} ({$submitter->role}) telah submit data untuk {$itp->code} - {$itp->item}",
                'link' => $this->getItpLink($itp),
                'related_itp_id' => $itp->id,
                'related_project_id' => $projectId,
                'sender_id' => $submitter->id,
            ]);
        }
    }

    /**
     * Notify when data is approved → send to role above (next in chain)
     */
    public function notifyApproved(Itp $itp, User $approver, User $dataOwner): void
    {
        // Notify the data owner
        Notification::create([
            'user_id' => $dataOwner->id,
            'type' => 'approved',
            'title' => 'Data ITP di-ACC',
            'message' => "{$approver->name} ({$approver->role}) telah meng-ACC data Anda untuk {$itp->code} - {$itp->item}",
            'link' => $this->getItpLink($itp),
            'related_itp_id' => $itp->id,
            'related_project_id' => $this->getProjectId($itp),
            'sender_id' => $approver->id,
        ]);

        // Notify next role up if applicable
        $nextRole = self::ROLE_ABOVE[$approver->role] ?? null;
        if (!$nextRole) return;

        $nextVal = $itp->getValForRole($nextRole);
        if (!in_array(strtoupper($nextVal), ['W', 'RV', '-', 'NA'])) return;
        if (in_array(strtoupper($nextVal), ['-', 'NA'])) return;

        $projectId = $this->getProjectId($itp);
        $recipients = $this->getProjectUsersWithRole($projectId, $nextRole);

        foreach ($recipients as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'approved',
                'title' => 'Data Bawahan di-ACC',
                'message' => "{$approver->name} telah ACC data {$dataOwner->name} untuk {$itp->code}. Silakan review.",
                'link' => $this->getItpLink($itp),
                'related_itp_id' => $itp->id,
                'related_project_id' => $projectId,
                'sender_id' => $approver->id,
            ]);
        }
    }

    /**
     * Notify when data is rejected → send to the rejected user
     */
    public function notifyRejected(Itp $itp, User $rejector, User $dataOwner, string $note): void
    {
        $projectId = $this->getProjectId($itp);

        Notification::create([
            'user_id' => $dataOwner->id,
            'type' => 'needs_revision',
            'title' => 'Data ITP Ditolak',
            'message' => "{$rejector->name} ({$rejector->role}) menolak data Anda untuk {$itp->code}: \"{$note}\"",
            'link' => $this->getItpLink($itp),
            'related_itp_id' => $itp->id,
            'related_project_id' => $projectId,
            'sender_id' => $rejector->id,
        ]);
    }

    private function getProjectId(Itp $itp): ?int
    {
        return DB::table('itps')
            ->join('sub_bloks', 'itps.sub_blok_id', '=', 'sub_bloks.id')
            ->join('bloks', 'sub_bloks.blok_id', '=', 'bloks.id')
            ->join('moduls', 'bloks.modul_id', '=', 'moduls.id')
            ->where('itps.id', $itp->id)
            ->value('moduls.project_id');
    }

    private function getProjectUsersWithRole(?int $projectId, string $role): \Illuminate\Support\Collection
    {
        if (!$projectId) return collect();

        return DB::table('project_user')
            ->join('users', 'project_user.user_id', '=', 'users.id')
            ->where('project_user.project_id', $projectId)
            ->where('users.role', $role)
            ->select('users.*')
            ->get();
    }

    private function getItpLink(Itp $itp): string
    {
        return "/assembly/{$itp->sub_blok_id}";
    }
}
