<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\Api\UserResource;
use App\Services\MediaService;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(protected MediaService $mediaService) {}

    /**
     * Get the current logged-in user profile.
     *
     * @param Request $request
     * @return UserResource
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update user profile information.
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                $oldMedia = MediaFile::where('url', $user->avatar)->first();
                if ($oldMedia) {
                    $this->mediaService->deleteFile($oldMedia);
                    $oldMedia->delete();
                } elseif (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }
            
            // Get or create Avatar folder
            $avatarFolder = MediaFolder::firstOrCreate(
                ['slug' => 'avatar'],
                [
                    'name' => 'Avatar',
                    'user_id' => $user->id,
                ]
            );

            // Upload new avatar via MediaService
            $result = $this->mediaService->handleUpload($request->file('avatar'), $avatarFolder->id);
            if (!$result['error']) {
                $validated['avatar'] = $result['data']->url;
            }
        }

        // Handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            // Delete old cover photo if exists
            if ($user->cover_photo) {
                $oldMedia = MediaFile::where('url', $user->cover_photo)->first();
                if ($oldMedia) {
                    $this->mediaService->deleteFile($oldMedia);
                    $oldMedia->delete();
                } elseif (Storage::disk('public')->exists($user->cover_photo)) {
                    Storage::disk('public')->delete($user->cover_photo);
                }
            }
            
            // Get or create Cover folder
            $coverFolder = MediaFolder::firstOrCreate(
                ['slug' => 'cover'],
                [
                    'name' => 'Cover',
                    'user_id' => $user->id,
                ]
            );

            // Upload new cover photo via MediaService
            $result = $this->mediaService->handleUpload($request->file('cover_photo'), $coverFolder->id);
            if (!$result['error']) {
                $validated['cover_photo'] = $result['data']->url;
            }
        }

        // Remove email if it was passed (we don't allow email update here)
        unset($validated['email']);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công.',
            'data' => new UserResource($user->fresh())
        ]);
    }
}
