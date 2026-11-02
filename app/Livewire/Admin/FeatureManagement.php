<?php

namespace App\Livewire\Admin;

use App\Actions\Feature\ToggleFeatureAction;
use App\Models\FeatureFlag;
use App\Services\FeatureService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kelola Fitur - Jangkau Samawa')]
class FeatureManagement extends Component
{
    public bool $showConfirmModal = false;

    public string $confirmingKey = '';

    public string $confirmingTitle = '';

    public string $confirmingMessage = '';

    public function mount(): void
    {
        if (! auth()->user()?->isSuperAdmin()) {
            abort(403, 'Hanya Super Administrator yang berhak mengakses halaman Kelola Fitur.');
        }
    }

    public function toggle(string $key, ToggleFeatureAction $action, FeatureService $featureService): void
    {
        // Konfirmasi untuk fitur berdampak besar saat dinonaktifkan
        if ($key === 'email_verification' && $featureService->isEnabled('email_verification')) {
            $this->confirmingKey = $key;
            $this->confirmingTitle = 'Nonaktifkan Verifikasi Email?';
            $this->confirmingMessage = 'Menonaktifkan fitur ini akan mengizinkan seluruh pengguna masuk ke dashboard tanpa proses verifikasi email. Apakah Anda yakin ingin melanjutkan?';
            $this->showConfirmModal = true;

            return;
        }

        $newState = $action->handle($key, auth()->user());
        $label = FeatureFlag::where('key', $key)->value('label') ?: $key;

        session()->flash('success', "Fitur '{$label}' berhasil ".($newState ? 'diaktifkan' : 'dinonaktifkan').'.');
    }

    public function confirmToggle(ToggleFeatureAction $action): void
    {
        if ($this->confirmingKey === '') {
            return;
        }

        $newState = $action->handle($this->confirmingKey, auth()->user());
        $label = FeatureFlag::where('key', $this->confirmingKey)->value('label') ?: $this->confirmingKey;

        session()->flash('success', "Fitur '{$label}' berhasil ".($newState ? 'diaktifkan' : 'dinonaktifkan').'.');
        $this->cancelConfirm();
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->confirmingKey = '';
        $this->confirmingTitle = '';
        $this->confirmingMessage = '';
    }

    public function render(): View
    {
        $features = FeatureFlag::with('updatedBy')
            ->orderBy('id')
            ->get();

        return view('livewire.admin.feature-management', [
            'features' => $features,
        ]);
    }
}
