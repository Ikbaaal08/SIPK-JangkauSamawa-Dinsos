<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Dashboard\DashboardStatisticsDto;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;
use App\Repositories\Contracts\ComplaintRepositoryInterface;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function getStatisticsForUser(User $user): DashboardStatisticsDto
    {
        return match ($user->role) {
            'admin' => $this->getAdminStatistics(),
            'petugas_penerima' => $this->getPenerimaStatistics(),
            'kepala_dinas' => $this->getKepalaDinasStatistics(),
            'petugas_pelaksana' => $this->getPelaksanaStatistics($user),
            default => new DashboardStatisticsDto(0, 0, 0, 0),
        };
    }

    protected function getAdminStatistics(): DashboardStatisticsDto
    {
        return new DashboardStatisticsDto(
            totalAduan: Complaint::count(),
            aduanBaru: Complaint::where('status', 'baru')->count(),
            aduanDiproses: Complaint::whereIn('status', ['diproses', 'diteruskan', 'didisposisikan', 'ditindaklanjuti'])->count(),
            aduanSelesai: Complaint::where('status', 'selesai')->count(),
            extra: [
                'aduanDitolak' => Complaint::where('status', 'ditolak')->count(),
                'aduanDiteruskan' => Complaint::where('status', 'diteruskan')->count(),
                'aduanDidisposisikan' => Complaint::where('status', 'didisposisikan')->count(),
            ],
        );
    }

    protected function getPenerimaStatistics(): DashboardStatisticsDto
    {
        return new DashboardStatisticsDto(
            totalAduan: Complaint::count(),
            aduanBaru: Complaint::where('status', 'baru')->count(),
            aduanDiproses: Complaint::whereIn('status', ['diproses', 'diteruskan'])->count(),
            aduanSelesai: Complaint::where('status', 'selesai')->count(),
            extra: [
                'perluVerifikasi' => Complaint::where('status', 'baru')->count(),
                'aduanDitolak' => Complaint::where('status', 'ditolak')->count(),
            ],
        );
    }

    protected function getKepalaDinasStatistics(): DashboardStatisticsDto
    {
        return new DashboardStatisticsDto(
            totalAduan: Complaint::count(),
            aduanBaru: Complaint::whereIn('status', ['baru', 'diteruskan'])->count(),
            aduanDiproses: Complaint::whereIn('status', ['didisposisikan', 'ditindaklanjuti', 'diproses'])->count(),
            aduanSelesai: Complaint::where('status', 'selesai')->count(),
            extra: [
                'siapDisposisi' => Complaint::where('status', 'diteruskan')->count(),
                'disposisiAktif' => Disposition::where('status', 'aktif')->count(),
            ],
        );
    }

    protected function getPelaksanaStatistics(User $user): DashboardStatisticsDto
    {
        $baseQuery = Disposition::where('assigned_to', $user->id);

        $totalAduan = (clone $baseQuery)->count();
        $aduanBaru = (clone $baseQuery)
            ->whereHas('complaint', fn ($q) => $q->where('status', 'didisposisikan'))
            ->count();
        $aduanDiproses = (clone $baseQuery)
            ->whereHas('complaint', fn ($q) => $q->where('status', 'ditindaklanjuti'))
            ->count();
        $aduanSelesai = (clone $baseQuery)
            ->where('status', 'selesai')
            ->count();
        $tugasAktif = (clone $baseQuery)
            ->where('status', 'aktif')
            ->count();

        return new DashboardStatisticsDto(
            totalAduan: $totalAduan,
            aduanBaru: $aduanBaru,
            aduanDiproses: $aduanDiproses,
            aduanSelesai: $aduanSelesai,
            extra: [
                'tugasAktif' => $tugasAktif,
            ],
        );
    }
}
