<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

/*
|--------------------------------------------------------------------------
| Safe file uploads into public/
|--------------------------------------------------------------------------
| Uploads land in the public web root, so the stored name must never come
| from the client: a file called "shell.php" would otherwise be executable.
| The extension is taken from the file's actual contents and must be in the
| allow-list; the name is random.
|--------------------------------------------------------------------------
*/
class SafeUpload
{
    public const DOCUMENT_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    // Validation rule for member documents (payment proofs, KYC)
    public const DOCUMENT_RULE = 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120';

    /**
     * Move an uploaded file into public/{$dir} and return its relative path ("{$dir}/name.ext").
     */
    public static function store(UploadedFile $file, string $dir, string $prefix = '', array $allowed = self::DOCUMENT_EXTENSIONS): string
    {
        $ext = strtolower((string) $file->guessExtension());
        if ($ext === 'jpe') {
            $ext = 'jpg';
        }
        if (!in_array($ext, $allowed, true)) {
            throw new RuntimeException('Unsupported file type.');
        }

        $dir = trim($dir, '/');
        $target = public_path($dir);
        if (!is_dir($target)) {
            @mkdir($target, 0755, true);
        }

        $name = ($prefix !== '' ? $prefix . '_' : '') . time() . '_' . Str::random(16) . '.' . $ext;
        $file->move($target, $name);

        return $dir . '/' . $name;
    }
}
