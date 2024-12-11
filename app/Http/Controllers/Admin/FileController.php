<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileType;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('throttle:' . config('files.upload_rate_limit'))->only('upload');
    }

    public function upload(FileUploadRequest $request)
    {
        try {
            $result = $this->fileService->upload(
                $request->file('file'),
                FileType::from($request->type)
            );

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'file' => $request->file('file')->getClientOriginalName()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string', 'max:' . config('files.max_filename_length')]
        ]);

        try {
            $this->fileService->delete($request->path);

            return response()->json([
                'status' => 'success',
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'path' => $request->path
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $e->getMessage() === 'File not found' ? 404 : 500);
        }
    }
}
