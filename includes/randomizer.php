<script>
function ajaxSubmit() { //when user submits genres
    var formData = {
        'genres': $('input[name=genres]').val(),
        'limit' : $('input[name=limit]').val(),
        'submit': true
    };

    $.ajax({
        url: "includes/randomizer.php",
        type: "POST",
        data: formData,
        success: function(result) {
            $("#output").html(result);
        },
        error: function(result) {
            $("#output").html("Error!");
        }
    });
    return false;
};
</script>

<?php
require '../vendor/autoload.php'; //for the SpotifyAPI PHP wrapper
@session_start();
require('dbconnect.php');

$stmt = $pdo->prepare('SELECT * FROM users WHERE Username = ?'); //query the database for accessToken/refreshToken
$stmt->execute([$_SESSION['username']]);
$query = $stmt->fetchAll();
$accessToken = $query[0]['Token'];
$refreshToken = $query[0]['Refresh'];

if($accessToken != NULL){ //if the user has an accessToken...then continue otherwise print give the user the ability to link Spotify Account
    try{
        if(isset($_POST['submit'])){
            $session = new SpotifyWebAPI\Session(
                '1337210a5bb44f9f87da8eb65f9f27d3', //client_id
                '0f53b3fba4474c16beb13173d59f5d25' //client_secret
            );

            $api = new SpotifyWebAPI\SpotifyWebAPI(); //setting a new instance of SpotifyWebAPI
            // print_r($api);

            function genreValid($api, $genres, $errors){
                $seedGenres = $api->getGenreSeeds(); //gets list of searchable genres
                if(empty($genres)){
                    $errors[] = "Please enter a genre!";
                }
                else{
                    $genres = explode(", ", $genres); //returns an array of the values entered
                    if(count($genres)>5){
                        $errors[]="Please only enter up to 5 genres.";
                    }
                    for($i=0; $i < count($genres); $i++){
                        $genres[$i] = str_replace(' ', '-', $genres[$i]);
                    }
                    $genreDiff = array_diff($genres, $seedGenres->genres);
                    if(!empty($genreDiff)){
                        foreach($genreDiff as $genre){
                            $errors[]=$genre." is not a valid genre.";
                        }
                    }
                }
                return array($errors, $genres);
            }

            function limitValid($limit, $errors){
                if($limit != NULL){
                    if($limit > 10 or $limit < 1){
                        $errors[] = "Please enter a number between 1 and 10.";
                    }
                    if(!is_numeric($limit)){
                        $errors[] = "Please enter a number.";
                    }
                    if(!ctype_digit($limit)){
                        $errors[] = "Please enter an integer.";
                    }
                }
                else{
                    $limit = 1;
                }
                return array($errors, $limit);
            }

            // When setting a complete Session instance, it's also not necessary to set the access token. It'll be automatically fetched from the Session instance
            $api->setSession($session);
            $api->setOptions([
                'auto_refresh' => true,
            ]);

            // Use previously requested tokens fetched from somewhere. A database for example.
            if ($accessToken) {
                $session->setAccessToken($accessToken);
                $session->setRefreshToken($refreshToken);
            } else {
                // Or request a new access token
                $session->refreshAccessToken($refreshToken);
            }

            // print_r($seedGenres);
            $errors = [];
            $genres = strtolower($_POST['genres']); //grabs the values entered into the database
            $limit = $_POST['limit']; //number of songs to request
            list($errors, $genres) = genreValid($api, $genres, $errors);
            list($errors, $limit) = limitValid($limit, $errors);

            if(count($errors) == 0){
                $recommendations = $api->getRecommendations([ //gets recommednations within the genres the user submitted
                    'seed_genres' => $genres, //sets the genres to get recommendations from
                    'limit' => $limit, //limit of how many outputs
                    'min_popularity' => 1,
                    'max_popularity' => 100,
                ]);
                echo '<div class="row justify-content-center">';
                //loop through the whole array of tracks and plug the values into a BS4 Card
                foreach($recommendations->tracks as $track){
                    $id = $track->id; //grabs the id from the first track in the object
                    $song = $api->getTracks($id); //grabs the info about the track pulled from the $recommendations object above

                    ?>
                        <div class="card bg-dark col-xs-12 col-sm-12 col-md-6 col-lg-4 col-xl-3" style="width:400px">
                            <img class="card-img-top" src="<?php echo $song->tracks[0]->album->images[0]->url;?>" alt="Card image">
                            <div class="card-body">
                                <h4 class="card-title"><a href="<?php echo $song->tracks[0]->artists[0]->external_urls->spotify; ?>" target="_blank\"><?php echo $song->tracks[0]->artists[0]->name?></a><br/></h4>
                                <p class="card-text">
                                    <?php echo "<a href=\"".$song->tracks[0]->external_urls->spotify."\" target=\"_blank\">".$song->tracks[0]->name."</a><br/>";
                                    echo "<a href=\"".$song->tracks[0]->album->external_urls->spotify."\" target=\"_blank\">".$song->tracks[0]->album->name."</a><br/>"; ?></p>
                                <?php
                                if(($song->tracks[0]->preview_url) != NULL){ //First Audio Tag for Intermediate Web Final
                                    echo "<audio controls>
                                <source src=\"".$song->tracks[0]->preview_url."type=\"audio/mpeg\"\"/>
                                </audio><br/><br/>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                }
            }
            else{
                $er = implode("\\n\\n", $errors);
                echo "<script>swal('Error!','$er','error');</script>";
            }
            echo '</div>';

            // Remember to fetch the tokens afterwards, they might have been updated
            $newAccessToken = $session->getAccessToken();
            $newRefreshToken = $session->getRefreshToken(); // Sometimes, a new refresh token will be returned
            $stmt = $pdo->prepare('UPDATE users SET Token = ?, Refresh = ? WHERE Username = ?');
            $stmt->execute([$newAccessToken, $newRefreshToken, $_SESSION['username']]);
        }
        else{
            ?>
        <div class="container">
        <div class="row justify-content-center align-items-center">
        <button class="btn btn-success" data-toggle="collapse" data-target="#demo">Genres</button>
        <div id="demo" class="collapse">
        acoustic, afrobeat, alt-rock, alternative, ambient, anime, black-metal, bluegrass, blues, bossanova, brazil, breakbeat, british, cantopop, chicago-house, children, chill, classical, club, comedy, country, dance, dancehall, death-metal, deep-house, detroit-techno, disco, disney, drum-and-bass, dub, dubstep, edm, electro, electronic, emo, folk, forro, french, funk, garage, german, gospel, goth, grindcore, groove, grunge, guitar, happy, hard-rock, hardcore, hardstyle, heavy-metal, hip-hop, holidays, honky-tonk, house, idm, indian, indie, indie-pop, industrial, iranian, j-dance, j-idol, j-pop, j-rock, jazz, k-pop, kids, latin, latino, malay, mandopop, metal, metal-misc, metalcore, minimal-techno, movies, mpb, new-age, new-release, opera, pagode, party, philippines-opm, piano, pop, pop-film, post-dubstep, power-pop, progressive-house, psych-rock, punk, punk-rock, r-n-b, rainy-day, reggae, reggaeton, road-trip, rock, rock-n-roll, rockabilly, romance, sad, salsa, samba, sertanejo, show-tunes, singer-songwriter, ska, sleep, songwriter, soul, soundtracks, spanish, study, summer, swedish, synth-pop, tango, techno, trance, trip-hop, turkish, work-out, world-music
        </div>
        </div>
        <div class="row justify-content-center align-items-center">
            <div class="form-signIn col-xs-1 col-sm-10 col-md-8 col-lg-4"> <!-- Resizeable for each screen size -->
                <h1 class="h3 mb-3 font-weight-normal">Random Song</h1>
                <input type="text" id="genres" class="form-control" name="genres" placeholder="Genres? ex: rock, k-pop, edm" required autofocus><br />
                <input type="text" id="limit" class="form-control" name="limit" placeholder="How many results? 1-10" required ><br />
                <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit();">Randomize!</button>
                <br />
            </div>
        </div>
        </div>
        <div id="output"></div>

    <?php
        }
    }
        catch (exception $e){
            echo "<script>swal('Error!','Unexpected error occurred! Try again later or try relinking your Spotify Account!','error')</script>";
    }
}
else{
    echo "No spotify account linked!<br/>";
    echo '<a href="includes/auth.php" target="_blank"><button class="btn btn-success">Link My Spotiy Account</button></a>';
}
?>