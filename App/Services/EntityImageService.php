<?php

namespace App\Services;

class EntityImageService
{
    private const MAX_IMAGES_PER_ENTITY = 100;

    private \App\Models\Image $imageModel;
    private \App\Domain\Services\CloudStorageService $cloudStorage;

    public function __construct()
    {
        $this->imageModel = new \App\Models\Image();
        $this->cloudStorage = new \App\Domain\Services\CloudStorageService();
    }

    public function getImages(string $entityType, int $entityId): array
    {
        return $this->imageModel->getByEntity($entityType, $entityId);
    }

    public function countImages(string $entityType, int $entityId): int
    {
        return $this->imageModel->countByEntity($entityType, $entityId);
    }

    public function uploadMany(string $entityType, int $entityId, array $files): array
    {
        $existingCount = $this->countImages($entityType, $entityId);
        $pendingFiles = 0;

        if (!empty($files['tmp_name']) && is_array($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $tmpName) {
                if (!empty($tmpName)) {
                    $pendingFiles++;
                }
            }
        }

        if ($existingCount + $pendingFiles > self::MAX_IMAGES_PER_ENTITY) {
            return [
                'success' => false,
                'error' => 'max_images_exceeded',
                'message' => 'Mỗi mục chỉ được tối đa 100 ảnh.',
            ];
        }

        $uploaded = 0;
        $errors = [];
        $folder = $this->folderFor($entityType);

        foreach (($files['tmp_name'] ?? []) as $index => $tmpName) {
            if (empty($tmpName)) {
                continue;
            }

            $upload = $this->cloudStorage->uploadDetails($tmpName, $folder);
            if (!$upload) {
                $errors[] = 'Không thể upload ảnh ' . (($files['name'][$index] ?? ('#' . $index)));
                continue;
            }

            $created = $this->imageModel->create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'image_url' => $upload['secure_url'],
                'public_id' => $upload['public_id'],
            ]);

            if ($created) {
                $uploaded++;
                continue;
            }

            $this->cloudStorage->delete($upload['public_id']);
            $errors[] = 'Không thể lưu metadata ảnh ' . (($files['name'][$index] ?? ('#' . $index)));
        }

        return [
            'success' => $uploaded > 0 && empty($errors),
            'uploaded' => $uploaded,
            'errors' => $errors,
        ];
    }

    public function deleteImage(int $imageId, ?string $entityType = null, ?int $entityId = null): bool
    {
        $image = $this->imageModel->findById($imageId);
        if (!$image) {
            return false;
        }

        if ($entityType !== null && $image['entity_type'] !== $entityType) {
            return false;
        }

        if ($entityId !== null && (int) $image['entity_id'] !== $entityId) {
            return false;
        }

        if (!empty($image['public_id'])) {
            $this->cloudStorage->delete((string) $image['public_id']);
        }

        return $this->imageModel->delete($imageId);
    }

    public function deleteAllForEntity(string $entityType, int $entityId): void
    {
        foreach ($this->getImages($entityType, $entityId) as $image) {
            $this->deleteImage((int) ($image['id'] ?? 0), $entityType, $entityId);
        }
    }

    public function getYoutubeVideo(string $entityType, int $entityId): ?string
    {
        $row = $this->imageModel->getFirstByEntity($entityType . '_video', $entityId);
        return $row['image_url'] ?? null;
    }

    public function syncYoutubeVideo(string $entityType, int $entityId, ?string $url): void
    {
        $videoEntityType = $entityType . '_video';
        $normalized = $this->normalizeYoutubeUrl((string) $url);

        foreach ($this->imageModel->getByEntity($videoEntityType, $entityId) as $row) {
            $this->imageModel->delete((int) ($row['id'] ?? 0));
        }

        if ($normalized === null) {
            return;
        }

        $this->imageModel->create([
            'entity_type' => $videoEntityType,
            'entity_id' => $entityId,
            'image_url' => $normalized,
            'public_id' => '',
        ]);
    }

    private function folderFor(string $entityType): string
    {
        return match ($entityType) {
            'room' => 'rooms',
            'service' => 'services',
            default => 'room_types',
        };
    }

    private function normalizeYoutubeUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $patterns = [
            '#(?:https?://)?(?:www\.)?youtube\.com/watch\?v=([^&]+)#i',
            '#(?:https?://)?(?:www\.)?youtu\.be/([^?&/]+)#i',
            '#(?:https?://)?(?:www\.)?youtube\.com/embed/([^?&/]+)#i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        return null;
    }
}

