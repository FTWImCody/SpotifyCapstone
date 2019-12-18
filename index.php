<!DOCTYPE html>
<html>

<head>
    <title>Spotify Companion App</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    function ajaxNavigation(page) { //Allows you to navigate via Ajax and load the info into #dynamicContent
        var formData = {
            'page': page
        }; //grabs the page value passed in and sends it to control.php
        $.ajax({
            url: "includes/control.php",
            type: "POST",
            data: formData,
            success: function(result) {
                $("#dynamicContent").html(result);
            },
        });
        return false;
    };
    </script>
    <?php
		session_start();
		require('includes/dbconnect.php'); //connects to the database
		?>
</head>

<body>
    <div class="jumbotron jumbotron-fluid text-center text-dark">
        <div class="container">
    <h1>Spotify API Capstone Project</h1>
    <p>Welcome to my Spotify API Project!</p>
    </div>
</div>
    <?php
			require_once('includes/navMenu.php'); //will have navMenu.php on every page
		?>
    <div id="dynamicContent">
        <!--All the content of each page will go into this div-->
        <?php
			require_once('includes/Home.php');
		?>
    </div>
</body>

</html>