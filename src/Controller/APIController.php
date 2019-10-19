<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostFormType;
use App\Service\imageUploaderService;

use App\Service\PostsService;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Psr7\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;
use Google\Auth\OAuth2;
use GuzzleHttp\Client;
use Google_Client;
use Cloudinary\Uploader;


class APIController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function test(Request $request)
    {
        \Cloudinary::config(array(
            "cloud_name" => "przemke",
            "api_key" => "884987643496832",
            "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
            "secure" => true
        ));

        $upload = \Cloudinary\Uploader::upload('/home/przemke/Obrazy/A4 B7 Sedan Blue.jpg', ['folder' => '2019-10']);

        dd($upload['public_id']);

        return new Response("Ok");
    }


    /**
     * @Route("/auth/google/callback", name="google_photo")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function callback(Request $request)
    {
        $localFilePath = '/home/przemke/Obrazy/A4 B7 Sedan Blue.jpg';
        $fileName = "Audi A4 B7 Sedan Blue";
        $itemDescription = "Hello World!";

        //Create album
        $photosLibraryClient = new PhotosLibraryClient(['credentials' => $_SESSION['credentials']]);
        $newAlbum = PhotosLibraryResourceFactory::album("My Album");
        $createdAlbum = $photosLibraryClient->createAlbum($newAlbum);
        $albumId = $createdAlbum->getId();

        //Upload
        $uploadToken = $photosLibraryClient->upload(file_get_contents($localFilePath), $fileName);
        $newMediaItems[] = PhotosLibraryResourceFactory::newMediaItemWithDescription($uploadToken, $itemDescription);
        //dd($newMediaItems);
        $response = $photosLibraryClient->batchCreateMediaItems($newMediaItems, ['albumId' => $albumId]);

        foreach ($response->getNewMediaItemResults() as $itemResult) {
            $itemIds[] = $itemResult->getMediaItem()->getId();

            $photosLibraryClient->batchRemoveMediaItemsFromAlbum($albumId, $itemIds);
        }

        return new Response("Ok");
    }

    /**
     * @Route("/google", name="google")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function add_photo(Request $request)
    {
        //$client = new Google_Client();

        $redirectURI = "http://localhost:8000/google";
        $scopes = ["https://www.googleapis.com/auth/photoslibrary.sharing", "https://www.googleapis.com/auth/photoslibrary"];

        $clientSecretJson = json_decode(
            file_get_contents($this->getParameter("secret_client_json")),
            true
        )['web'];

        /*
        $client->setAuthConfig($this->getParameter("secret_client_json"));
        $client->addScope($scopes);
        $client->setRedirectUri($redirectURI);
        $client->setAccessType('offline');
        $client->setApprovalPrompt("force");
        $client->setIncludeGrantedScopes(true);
        $auth_url = $client->createAuthUrl();
        header("Location: " . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit();
        */

        $clientId = $clientSecretJson['client_id'];
        $clientSecret = $clientSecretJson['client_secret'];

        $oauth2 = new OAuth2([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
            // Where to return the user to if they accept your request to access their account.
            // You must authorize this URI in the Google API Console.
            'redirectUri' => $redirectURI,
            'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
            'scope' => $scopes,
        ]);

        // The authorization URI will, upon redirecting, return a parameter called code.
        if (!isset($_GET['code'])) {
            $authenticationUrl = $oauth2->buildFullAuthorizationUri(['access_type' => 'offline']);
            header("Location: " . $authenticationUrl);
            exit();
        } else {
            // With the code returned by the OAuth flow, we can retrieve the refresh token.
            $oauth2->setCode($_GET['code']);
            $authToken = $oauth2->fetchAuthToken();
            $refreshToken = $authToken['access_token'];
            // The UserRefreshCredentials will use the refresh token to 'refresh' the credentials when
            // they expire.
            $_SESSION['credentials'] = new UserRefreshCredentials(
                $scopes,
                [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $refreshToken
                ]
            );
            // Return to the add photo route.
            return $this->redirectToRoute("google_photo");
        }


        return $this->redirectToRoute("main_page");
    }
}
