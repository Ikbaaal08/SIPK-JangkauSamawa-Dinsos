<?php

namespace App\Forms\Public;

use App\DTOs\Public\TrackComplaintData;
use Livewire\Form;

class TrackComplaintForm extends Form
{
    public string $ticket_code = '';

    /**
     * Get the validation rules.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'ticket_code' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation error messages in Indonesian.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ticket_code.required' => 'Kode tiket wajib diisi.',
        ];
    }

    /**
     * Validate and convert the form to a readonly DTO.
     */
    public function toDTO(): TrackComplaintData
    {
        $this->validate();

        return new TrackComplaintData(
            ticket_code: trim($this->ticket_code)
        );
    }
}
