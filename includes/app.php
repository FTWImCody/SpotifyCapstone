<html>

<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
    <?php
        require '../vendor/autoload.php';
        @session_start();
        require('dbconnect.php');

        $session = new SpotifyWebAPI\Session(
            '1337210a5bb44f9f87da8eb65f9f27d3',
            '0f53b3fba4474c16beb13173d59f5d25'
        );

        $api = new SpotifyWebAPI\SpotifyWebAPI();

        // When setting a complete Session instance, it's also not necessary to set the access token. It'll be automatically fetched from the Session instance
        $api->setSession($session);
        $api->setOptions([
            'auto_refresh' => true,
        ]);

        $stmt = $pdo->prepare('SELECT * FROM users WHERE Username = ?'); //querying the database for accessToken and refreshToken
        $stmt->execute([$_SESSION['username']]);
        $query = $stmt->fetchAll();
        $accessToken = $query[0]['Token'];
        $refreshToken = $query[0]['Refresh'];

        // Use previously requested tokens fetched from somewhere. A database for example.
        if ($accessToken) {
            $session->setAccessToken($accessToken);
            $session->setRefreshToken($refreshToken);
        } else {
            // Or request a new access token
            $session->refreshAccessToken($refreshToken);
        }
        ?>
        <div class="container">
        <div class="row justify-content-center">
        <?php
        // Call the API as usual
        $me = $api->me();
        echo "Account: ".$me->display_name."<br/>";
        echo "E-Mail: ".$me->email."<br/>";
        echo "Country: ".$me->country."<br/>";

        ?>
        </div>
        <div class="row justify-content-center">
    <script>
    function closeTab() { //funtion to close the window on button click
        window.close();
    }
    </script>
    <button class="btn btn-success" onclick="closeTab()">Close Tab</button>
    <?php
        // Remember to fetch the tokens afterwards, they might have been updated
        $newAccessToken = $session->getAccessToken();
        $newRefreshToken = $session->getRefreshToken(); // Sometimes, a new refresh token will be returned
        $stmt = $pdo->prepare('UPDATE users SET Token = ?, Refresh = ? WHERE Username = ?'); //if new refreshToken/accessToken are fetched, then it will query the database to update
        $stmt->execute([$newAccessToken, $newRefreshToken, $_SESSION['username']]);
        ?>
    </div>
</div>
</body>

</html>