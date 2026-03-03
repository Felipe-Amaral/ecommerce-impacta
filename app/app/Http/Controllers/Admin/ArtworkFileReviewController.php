<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArtworkFileStatus;
use App\Enums\FulfillmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewArtworkFileRequest;
use App\Models\ArtworkFile;
use Illuminate\Http\RedirectResponse;

class ArtworkFileReviewController extends Controller
{
    public function __invoke(ReviewArtworkFileRequest $request, ArtworkFile $artworkFile): RedirectResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $artworkFile->loadMissing('orderItem.order');

        $status = $request->validated()['status'];
        $artworkFile->status = $status;
        $artworkFile->review_notes = $request->validated()['review_notes'] ?? null;
        $artworkFile->reviewed_at = now();
        $artworkFile->metadata = array_merge((array) $artworkFile->metadata, [
            'reviewed_by_user_id' => $request->user()?->id,
            'reviewed_at' => now()->toIso8601String(),
        ]);
        $artworkFile->save();

        $item = $artworkFile->orderItem;
        if ($item) {
            $item->production_status = match ($artworkFile->status) {
                ArtworkFileStatus::Approved => 'file_approved',
                ArtworkFileStatus::NeedsAdjustment, ArtworkFileStatus::Rejected => 'file_adjustment_requested',
                ArtworkFileStatus::UnderReview => 'file_under_review',
                default => 'file_received',
            };
            $item->save();
        }

        $order = $item?->order;
        if ($order) {
            $allItemsApproved = $order->items()->where(function ($query): void {
                $query->whereNull('production_status')
                    ->orWhere('production_status', '!=', 'file_approved');
            })->doesntExist();

            if ($artworkFile->status === ArtworkFileStatus::Approved && $allItemsApproved) {
                $order->fulfillment_status = FulfillmentStatus::Approved;
                $order->save();
            }

            if (in_array($artworkFile->status, [ArtworkFileStatus::NeedsAdjustment, ArtworkFileStatus::Rejected], true)
                && $order->fulfillment_status === FulfillmentStatus::Approved) {
                $order->fulfillment_status = FulfillmentStatus::Prepress;
                $order->save();
            }

            $order->statusHistory()->create([
                'from_status' => $order->status->value,
                'to_status' => $order->status->value,
                'actor_type' => 'admin',
                'actor_id' => $request->user()?->id,
                'message' => 'Revisão de arte atualizada para o item '.$item->product_name.'.',
                'metadata' => [
                    'artwork_file_id' => $artworkFile->id,
                    'artwork_status' => $artworkFile->status->value,
                ],
                'created_at' => now(),
            ]);
        }

        return back()->with('success', 'Revisão da arte atualizada.');
    }
}
