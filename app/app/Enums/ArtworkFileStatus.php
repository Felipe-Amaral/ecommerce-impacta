<?php

namespace App\Enums;

use App\Support\UiStatus;

enum ArtworkFileStatus: string
{
    case Uploaded = 'uploaded';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case NeedsAdjustment = 'needs_adjustment';
    case Rejected = 'rejected';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return UiStatus::labelValue($this->value);
    }

    public function icon(): string
    {
        return UiStatus::iconValue($this->value);
    }

    public function tone(): string
    {
        return UiStatus::toneValue($this->value);
    }
}
