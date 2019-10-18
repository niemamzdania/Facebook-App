<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

class imageUploaderService
{
    public function uploadImageToCloudinary($tempDirectory, $request)
    {
        \Cloudinary::config([
            "cloud_name" => getenv('CLOUD_NAME'),
            'api_key' => getenv('API_KEY'),
            "api_secret" => getenv('API_SECRET')
        ]);

        $session = $request->getSession();

        $realPath = $tempDirectory . '/' . $session->get('file')['post_form']['name']['name'];

        $rrr = new File($realPath);

        \Cloudinary\Uploader::upload($rrr, array("public"));
    }
}