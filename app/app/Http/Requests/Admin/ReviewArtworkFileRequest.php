<?php

namespace App\Http\Requests\Admin;

use App\Enums\ArtworkFileStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewArtworkFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                ArtworkFileStatus::UnderReview->value,
                ArtworkFileStatus::Approved->value,
                ArtworkFileStatus::NeedsAdjustment->value,
                ArtworkFileStatus::Rejected->value,
            ])],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
