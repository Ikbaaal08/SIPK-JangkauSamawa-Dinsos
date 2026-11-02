<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Dashboard\DashboardStatisticsDto;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Disposition;
use App\Models\User;
use App\Repositories\Contracts\ComplaintRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    /**
     * Get base scoped query based on the authenticated user's role and bidang.
     */
    protected function getScopedQuery(?User $user = null): Builder
    {
        $query = Complaint::query();

        if (! $user) {
            return $query;
        }

        // Super Admin, Admin, and Kadis can see all complaints
        if (in_array($user->role, ['super_admin', 'admin', 'kadis', 'kepala_dinas'], true)) {
            return $query;
        }

        // Kabag only sees complaints in their bidang
        if (in_array($user->role, ['kabag', 'petugas_penerima'], true)) {
            $bidang = $user->bidang;

            if (! empty($bidang)) {
                $query->whereHas('category', function ($q) use ($bidang) {
                    $q->where(function ($sub) use ($bidang) {
                        $sub->where('bidang', $bidang);

                        if ($bidang === 'perlindungan_jaminan_sosial') {
                            $sub->orWhereIn('code', ['BANSOS']);
                        } elseif ($bidang === 'rehabilitasi_sosial') {
                            $sub->orWhereIn('code', ['DIFABEL', 'LANSIA', 'ANPER']);
                        } elseif ($bidang === 'pengembangan_kesejahteraan') {
                            $sub->orWhereIn('code', ['KEMSOS']);
                        }
                    });
                });
            }

            return $query;
        }

        // Petugas only sees complaints assigned directly to them via dispositions
        if (in_array($user->role, ['petugas', 'petugas_pelaksana'], true)) {
            $query->whereHas('dispositions', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });

            return $query;
        }

        return $query;
    }

    /**
     * Get complaint counts grouped by status for a given user scope.
     *
     * @return array<string, int>
     */
    public function countByStatus(?User $user = null): array
    {
        $scoped = $this->getScopedQuery($user);

        return [
            'total' => (clone $scoped)->count(),
            'baru' => (clone $scoped)->where('status', 'baru')->count(),
            'diproses' => (clone $scoped)->where('status', 'diproses')->count(),
            'diteruskan' => (clone $scoped)->where('status', 'diteruskan')->count(),
            'didisposisikan' => (clone $scoped)->where('status', 'didisposisikan')->count(),
            'ditindaklanjuti' => (clone $scoped)->where('status', 'ditindaklanjuti')->count(),
            'selesai' => (clone $scoped)->where('status', 'selesai')->count(),
            'ditolak' => (clone $scoped)->where('status', 'ditolak')->count(),
        ];
    }

    /**
     * Get recent complaints for a given user scope.
     */
    public function getRecent(?User $user = null, int $limit = 5): Collection
    {
        return $this->getScopedQuery($user)
            ->with(['category', 'dispositions'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get complaint distribution grouped by category for a given user scope.
     *
     * @return array<int, array{id: int, name: string, code: string, count: int, percentage: float}>
     */
    public function countByCategory(?User $user = null): array
    {
        $total = $this->getScopedQuery($user)->count();
        $categories = Category::where('is_active', true)->get();
        $result = [];

        foreach ($categories as $cat) {
            $count = (clone $this->getScopedQuery($user))
                ->where('category_id', $cat->id)
                ->count();

            if ($count > 0 || $total === 0) {
                $result[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'code' => $cat->code,
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
                ];
            }
        }

        usort($result, fn ($a, $b) => $b['count'] <=> $a['count']);

        return $result;
    }

    /**
     * Get complaint distribution grouped by bidang for a given user scope.
     *
     * @return array<string, array{label: string, count: int, percentage: float}>
     */
    public function countByBidang(?User $user = null): array
    {
        $total = $this->getScopedQuery($user)->count();

        $bidangDefinitions = [
            'perlindungan_jaminan_sosial' => [
                'label' => 'Perlindungan & Jaminan Sosial',
                'codes' => ['BANSOS'],
            ],
            'rehabilitasi_sosial' => [
                'label' => 'Rehabilitasi Sosial',
                'codes' => ['DIFABEL', 'LANSIA', 'ANPER'],
            ],
            'pengembangan_kesejahteraan' => [
                'label' => 'Pengembangan Kesejahteraan',
                'codes' => ['KEMSOS'],
            ],
        ];

        $result = [];

        foreach ($bidangDefinitions as $key => $def) {
            $count = (clone $this->getScopedQuery($user))
                ->whereHas('category', function ($q) use ($key, $def) {
                    $q->where(function ($sub) use ($key, $def) {
                        $sub->where('bidang', $key)
                            ->orWhereIn('code', $def['codes']);
                    });
                })
                ->count();

            $result[$key] = [
                'label' => $def['label'],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            ];
        }

        return $result;
    }

    /**
     * Get overdue complaints for a given user scope.
     */
    public function getOverdue(?User $user = null, int $limit = 5): Collection
    {
        return $this->getScopedQuery($user)
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->whereHas('dispositions', function ($q) {
                $q->whereNotNull('due_date')
                    ->where('due_date', '<', now()->toDateString());
            })
            ->with(['category', 'dispositions'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get complaints that currently need action depending on user role.
     */
    public function getActionNeeded(User $user, int $limit = 5): Collection
    {
        $scoped = $this->getScopedQuery($user);

        if (in_array($user->role, ['super_admin', 'admin'], true)) {
            $scoped->whereIn('status', ['baru', 'diproses']);
        } elseif (in_array($user->role, ['kadis', 'kepala_dinas'], true)) {
            $scoped->whereIn('status', ['diteruskan', 'didisposisikan']);
        } elseif (in_array($user->role, ['kabag', 'petugas_penerima'], true)) {
            $scoped->where('status', 'diteruskan');
        } elseif (in_array($user->role, ['petugas', 'petugas_pelaksana'], true)) {
            $scoped->whereIn('status', ['didisposisikan', 'ditindaklanjuti']);
        }

        return $scoped
            ->with(['category', 'dispositions'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Legacy statistics calculation method for backward compatibility.
     */
    public function getStatisticsForUser(User $user): DashboardStatisticsDto
    {
        return match ($user->role) {
            'super_admin', 'admin' => $this->getAdminStatistics(),
            'kabag', 'petugas_penerima' => $this->getPenerimaStatistics(),
            'kadis', 'kepala_dinas' => $this->getKepalaDinasStatistics(),
            'petugas', 'petugas_pelaksana' => $this->getPelaksanaStatistics($user),
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
