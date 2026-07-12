<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CampaignImageService
{
    public function getCampaignImage(Campaign $campaign)
    {
        return $campaign->images;
    }

    public function storeCampaignImage(Campaign $campaign, Request $request)
    {
        return DB::transaction(function () use ($campaign, $request) {

            $images = collect();

            $hasPrimary = $campaign->images()
                ->where('is_primary', true)
                ->exists();

            foreach ($request->file('images') as $index => $file) {

                $path = $file->store('campaign-images', 'public');

                $images->push(
                    $campaign->images()->create([
                        'url' => $path,
                        'is_primary' => !$hasPrimary && $index === 0,
                    ])
                );
            }

            return $images;
        });
    }

    public function updateCampaignImage(CampaignImage $image, Request $request)
    {
        if ($request->hasFile('image')) {
        
            // Hapus gambar lama
            if ($image->url && Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }
        
            // Upload gambar baru
            $path = $request->file('image')->store('campaign-images', 'public');
        
            // Update database
            $image->update([
                'url' => $path,
            ]);
        }
    
        return $image->fresh();
    }

    public function deleteCampaignImage(CampaignImage $image): bool
    {
        if ($image->url && Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        return $image->delete();
    }

    public function setPrimary(Campaign $campaign, CampaignImage $image)
    {
        CampaignImage::where('campaign_id', $campaign->id)->update(['is_primary' => false]);
        
        $image->update(['is_primary' => true]);
        
        return $image->fresh();
    }
}