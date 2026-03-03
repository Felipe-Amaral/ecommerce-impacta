<?php

namespace App\Http\Controllers;

use App\Models\ArtworkFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArtworkFileDownloadController extends Controller
{
    public function __invoke(ArtworkFile $artworkFile): StreamedResponse
    {
        $artworkFile->loadMissing('orderItem.order');

        $order = $artworkFile->orderItem?->order;
        abort_unless($order, 404);

        $user = auth()->user();
        $authorized = $user && ($user->is_admin || ($order->user_id && $order->user_id === $user->id));
        abort_unless($authorized, 403);

        return Storage::disk($artworkFile->storage_disk)->download(
            $artworkFile->path,
            $artworkFile->original_name,
        );
    }
}
