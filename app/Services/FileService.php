<?php

namespace App\Services;

use App\Enums\FileType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class FileService
{
    public function upload(UploadedFile $file, FileType $type): array
    {
        $this->validateFile($file);
        
        $filename = $this->generateSecureFilename($file);
        $path = $this->getSecurePath($type->value, $filename);
        
        if (config('files.scan_virus')) {
            $this->scanForVirus($file);
        }
        
        // Save original file
        Storage::disk(config('files.storage_disk'))->put(
            $path, 
            file_get_contents($file)
        );
        
        // Optimize image if needed
        if (config('files.optimize_images')) {
            $this->optimizeImage($path);
        }
        
        Log::info('File uploaded successfully', [
            'path' => $path,
            'type' => $type->value,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);
        
        return [
            'path' => $path,
            'url' => Storage::disk(config('files.storage_disk'))->url($path)
        ];
    }
    
    public function delete(string $path): bool
    {
        $this->validatePath($path);
        
        if (!Storage::disk(config('files.storage_disk'))->exists($path)) {
            throw new Exception('File not found');
        }
        
        $result = Storage::disk(config('files.storage_disk'))->delete($path);
        
        Log::info('File deleted', ['path' => $path, 'success' => $result]);
        
        return $result;
    }
    
    private function validateFile(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), config('files.allowed_mime_types'))) {
            throw new Exception('Invalid file type');
        }
        
        if ($file->getSize() > config('files.max_size') * 1024) {
            throw new Exception('File size exceeds limit');
        }
    }
    
    private function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, config('files.allowed_extensions'))) {
            throw new Exception('Invalid file extension');
        }
        
        return Str::random(32) . '.' . $extension;
    }
    
    private function getSecurePath(string $type, string $filename): string
    {
        $path = $type . '/' . $filename;
        
        // Check for path traversal attempts
        foreach (config('files.path_blacklist') as $blacklisted) {
            if (Str::contains($path, $blacklisted)) {
                throw new Exception('Invalid path detected');
            }
        }
        
        return $path;
    }
    
    private function scanForVirus(UploadedFile $file): void
    {
        // Implement virus scanning logic here
        // You can use ClamAV or other virus scanning libraries
        // For now, we'll just log that we would scan
        Log::info('Virus scan would be performed here', ['file' => $file->getClientOriginalName()]);
    }
    
    private function optimizeImage(string $path): void
    {
        // Implement image optimization logic here
        // You can use libraries like intervention/image
        // For now, we'll just log that we would optimize
        Log::info('Image optimization would be performed here', ['path' => $path]);
    }
}
