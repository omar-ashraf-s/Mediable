<?php

namespace Mabrouk\Mediable\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Mabrouk\Mediable\Models\Media;
use Mabrouk\Mediable\Models\MediaMeta;
use ReflectionClass;

Trait Mediable
{
    ## Relations

	public function media()
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('priority', 'asc');
    }

	public function singleMedia()
    {
        return $this->morphOne(Media::class, 'mediable');
    }

	public function nonMainMedia()
    {
        return $this->media()->where('is_main', false);
    }
    
    ## Getters & Setters

    public function getMediaDirectoryAttribute($value)
    {
        $className = new ReflectionClass($this);
        return Str::plural(strtolower($className->getShortName()));
    }

    public function getPhotosDirectoryAttribute($value)
    {
        return config('mediable.base_path') . config('mediable.photos.path') . "{$this->mediaDirectory}";
    }

    public function getFilesDirectoryAttribute($value)
    {
        return config('mediable.base_path') . config('mediable.files.path') . "{$this->mediaDirectory}";
    }

    public function getVideosDirectoryAttribute($value)
    {
        return config('mediable.base_path') . config('mediable.videos.path') . "{$this->mediaDirectory}";
    }

    public function getMainMediaAttribute()
    {
        $main = $this->media()->where('is_main', true)->first();
        return $main ? $main : $this->media()->first();
    }

    public function getIsMainMediaAttribute()
    {
        return (bool) $this->singleMedia->is_main;
    }

    public function getPhotosAttribute()
    {
        return $this->media('photo')->get();
    }

    public function getPhotoAttribute()
    {
        return $this->media('photo')->first();
    }

    public function getFilesAttribute()
    {
        return $this->media('file')->get();
    }

    public function getFileAttribute()
    {
        return $this->media('file')->first();
    }

    public function getVoicesAttribute()
    {
        return $this->media('voice')->get();
    }

    public function getVoiceAttribute()
    {
        return $this->media('voice')->first();
    }

    public function getVideosAttribute()
    {
        return $this->media('video')->get();
    }

    public function getVideoAttribute()
    {
        return $this->media('video')->first();
    }

    public function getUrlsAttribute()
    {
        return $this->media('url')->get();
    }

    public function getUrlAttribute()
    {
        return $this->media('url')->first();
    }

    protected function getVideoIdAttribute($value)
    {
        return getYoutubeVideoId($this->path);
    }

    ## Query Scope Methods

    ## Other Methods

    public function addMedia(
        UploadedFile $requestFile,
        string $type = 'photo',
        ?string $disk = null,
        bool $isMain = false,
        ?string $title = null,
        ?string $description = null,
        int $priority = 9999,
    ) {

        $handledFile = $this->storeRequestFile($requestFile, $disk);

        if ($isMain) {
            $this->normalizePreviousMainMedia();
        }
        
        $this->media()->create([
            'path' => $handledFile['path'],
            'type' => $type,
            'extension' => $handledFile['extension'],
            'title' => $title,
            'description' => $description,
            'is_main' => $isMain,
            'priority' => $priority,
            'size' => $handledFile['size'],
        ]);

        return $this;
    }
    
    public function editMedia(
        UploadedFile $requestFile,
        ?Media $singleMedia,
        string $type = 'photo',
        ?string $disk = null,
        bool $isMain = false,        
        ?string $title = null,
        ?string $description = null,
        int $priority = 9999,
    ): void {

        if (!$singleMedia) {
            $this->addMedia(
                requestFile: $requestFile,
                type: $type,
                disk: $disk,
                isMain: $isMain,
                title: $title,
                description: $description,
                priority: $priority,
            );

            return;
        }

        $handledFile = $this->storeRequestFile($requestFile, $disk);
        $singleMedia->remove(removeFileWithoutObject: true);

        $singleMedia->update([
            'path' => $handledFile['path'],
            'extension' => $handledFile['extension'],
            'title' => $title ?? $singleMedia->title,
            'description' => $description ?? $singleMedia->description,
            'priority' => $priority ?? $singleMedia->priority,
            'size' => $handledFile['size'],
        ]);
    }

    public function deleteMedia(Media $singleMedia)
    {
        $singleMedia->remove();
        $this->touch;
    }

    public function deleteAllMedia()
    {
        $this->media->each(function ($singleMedia) {
            $this->deleteMedia($singleMedia);
        });
    }

    public function updateOrCreateMediaMeta(Media $media)
    {
        MediaMeta::updateOrCreate(['media_id' => $media->id]);
    }

    private function normalizePreviousMainMedia(): void
    {
        if ((bool) optional($this->mainMedia)->is_main) {
            $this->mainMedia->update([
                'is_main' => false
            ]);
        }
    }
    
    private function storeRequestFile(UploadedFile $requestFile, ?string $disk = null): array
    {
        $extension = $requestFile->getClientOriginalExtension();
        $name = now()->timestamp . '-' . random_int(100000, 999999);

        $disk = $disk ?? config('filesystems.default');

        $path = $requestFile->storeAs($this->photosDirectory, "{$name}.{$extension}", $disk);
        
        if (!$path) {
            throw new \RuntimeException('Failed to store media file');
        }
        
        return [
            'path' => $path,
            'size' => $requestFile->getSize(),
            'extension' => $extension,
        ];
    }    
}
