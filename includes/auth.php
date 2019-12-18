<?php
require '../vendor/autoload.php'; //SpotifyAPI PHP Wrapper

$session = new SpotifyWebAPI\Session(
    '1337210a5bb44f9f87da8eb65f9f27d3', //client_id
    '0f53b3fba4474c16beb13173d59f5d25', //client_secret
    'https://localhost/includes/callback.php' //redirect_uri
);

$options = [
    'scope' => [ //What my APP is allowed to access on the Spotify Account
        'playlist-read-private',
        'user-read-private',
        'playlist-read-collaborative',
        'playlist-modify-private',
        'user-read-recently-played',
        'streaming',
        'user-read-currently-playing',
        'user-read-playback-state',
        'user-library-read',
        'user-follow-read',
        'user-read-email',
        'user-top-read'
    ],
];

header('Location: ' . $session->getAuthorizeUrl($options)); //Navigation to the spotify oAuth
die();
?>