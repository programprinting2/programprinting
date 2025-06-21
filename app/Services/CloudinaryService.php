<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Asset\Media;

class CloudinaryService
{
    protected $cloudinary;
    protected $cloudName;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $this->cloudName,
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
                'url' => config('services.cloudinary.url'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Upload an image to Cloudinary
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Folder name in Cloudinary
     * @return string|null Public ID of uploaded image
     */
    public function upload($file, $folder = null)
    {
        $folder = $folder ?? config('services.cloudinary.folder');
        
        $options = [
            'folder' => $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ];
        
        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $options
            );
            
            return $result['public_id'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function delete($publicId)
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => 'image'
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate optimized image URL from public_id with optional transformations
     *
     * @param string $publicId Public ID of the image
     * @param string|null $size Predefined size: thumbnail, medium, large, or null for original
     * @return string|null
     */
    public function getImageUrl($publicId, $size = null)
    {
        if (empty($publicId)) {
            return null;
        }

        // Base URL structure for optimized performance
        $baseUrl = "https://res.cloudinary.com/{$this->cloudName}/image/upload/";
        
        // Optimized transformations based on size
        switch ($size) {
            case 'thumbnail':
                $transform = 'c_thumb,w_100,h_100,q_auto,f_auto/';
                break;
            case 'small':
                $transform = 'c_scale,w_300,q_auto,f_auto/';
                break;
            case 'medium':
                $transform = 'c_scale,w_500,q_auto,f_auto/';
                break;
            case 'large': 
                $transform = 'c_scale,w_800,q_auto,f_auto/';
                break;
            default:
                $transform = 'q_auto,f_auto/';
                break;
        }
        
        return $baseUrl . $transform . $publicId;
    }
} 