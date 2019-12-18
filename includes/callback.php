<?php
require('../vendor/autoload.php'); //SpotifyAPI PHP Wrapper
@session_start();
require('dbconnect.php');

$session = new SpotifyWebAPI\Session(
    '1337210a5bb44f9f87da8eb65f9f27d3', //client_id
    '0f53b3fba4474c16beb13173d59f5d25', //client_secret
    'https://localhost/includes/callback.php' //redirect_uri
);


// Request a access token using the code from Spotify
$session->requestAccessToken($_GET['code']);

$accessToken = $session->getAccessToken();
$refreshToken = $session->getRefreshToken();

// Store the access and refresh tokens somewhere. In a database for example.
$stmt = $pdo->prepare('UPDATE users SET Token = ?, Refresh = ? WHERE Username = ?');
$stmt->execute([$accessToken, $refreshToken, $_SESSION['username']]);

// Send the user along and fetch some data!
header('Location: app.php');
die();
?>