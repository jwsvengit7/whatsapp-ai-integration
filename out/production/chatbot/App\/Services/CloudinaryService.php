<?php
namespace  App\Services;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    /**
     * @throws ApiError
     */
    public function uploadToCloudinary(Request $request): string
    {
        $publicId=md5(uniqid(true));

        $uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath())->getSecurePath();
        $uploadedFileUrl = cloudinary()->upload($request->file('file')->getRealPath(), [
            'folder' => 'uploads',
            'transformation' => [
                'width' => 400,
                'height' => 400,
                'crop' => 'fill'
            ]
        ])->getSecurePath();
        if(Storage::disk('cloudinary')->fileExists($publicId)){
            return "File already Exist";
        }
       return cloudinary()->getUrl($publicId);

    }
}

