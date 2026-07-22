<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\Quality;
use Cloudinary\Transformation\Format;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(config('services.cloudinary.url'));
    }

    public function upload(string $path, string $folder, array $options = []): array
    {
        $result = $this->cloudinary->uploadApi()->upload($path, array_merge([
            'folder' => $folder,
            'resource_type' => 'auto',
        ], $options));

        return [
            'public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
        ];
    }

    public function destroy(string $publicId): void
    {
        $this->cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'auto']);
    }

    public function url(string $publicId, ?int $width = null, ?int $height = null): string
    {
        $image = $this->cloudinary->image($publicId)
            ->delivery(Quality::auto())
            ->delivery(Format::auto());

        if ($width && $height) {
            $image->resize(Resize::fill($width, $height));
        }

        return $image->toUrl();
    }

    public function videoUrl(string $publicId, ?int $width = null, ?int $height = null): string
    {
        $video = $this->cloudinary->video($publicId)
            ->delivery(Quality::auto())
            ->delivery(Format::auto());

        if ($width && $height) {
            $video->resize(Resize::fill($width, $height));
        }

        return $video->toUrl();
    }

    public function fileUrl(string $publicId): string
    {
        return $this->cloudinary->raw($publicId)->toUrl();
    }
}