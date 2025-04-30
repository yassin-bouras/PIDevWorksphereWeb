<?php
namespace App\Service;

use Cloudinary\Cloudinary;
class CloudinaryUploader
{
    private const CLOUD_NAME = 'dfinw6lmg';
    private const API_KEY = '273431972826225';
    private const API_SECRET = 'KhAfNJB5T9TuSYkojn8lpqcx4hk';

    public function uploadPdfToCloudinary(string $pdfContent): ?string
    {
        try {
            $tempFile = tmpfile();
            fwrite($tempFile, $pdfContent);
            $tempPath = stream_get_meta_data($tempFile)['uri'];
    
            $cloudinary = new Cloudinary([
                'cloud_name' => self::CLOUD_NAME,
                'api_key' => self::API_KEY,
                'api_secret' => self::API_SECRET,
                'url' => [
                    'secure' => true,
                    'cdn_subdomain' => true
                ]
            ]);
    
            $result = $cloudinary->uploadApi()->upload($tempPath, [
                'resource_type' => 'raw',
                'type' => 'upload',
                'access_mode' => 'public',
                'invalidate' => true,
                'use_filename_as_display_name' => true,
                'unique_filename' => false,
                'overwrite' => true,
                'format' => 'pdf'
            ]);
    
            fclose($tempFile);
            return $result['secure_url'];
    
        } catch (\Exception $e) {
            error_log('Cloudinary error: '.$e->getMessage());
            return null;
        }
    }
}