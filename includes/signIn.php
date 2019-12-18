<script>
function ajaxSubmit() {
    var formData = {
        'username': $('input[name=username]').val(),
        'pass': $('input[name=pass]').val(),
        'submit': true
    };

    $.ajax({
        url: "includes/signIn.php",
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
@session_start();
require('dbconnect.php');
$username = $_POST['username'] ?? "";

function usernameValid($username, $errors){
    $validUser = '/^[\w]{6,12}$/';
    if (!preg_match($validUser, $username)){
        $errors[] = "Username must be between 6-12 characters in length and can only contain alphanumeric characters(A-Z, a-z, 0-9 or _).";
    }

    return $errors;
}

if(isset($_POST['submit'])){
    date_default_timezone_set('America/New_York');
    $dt = new DateTime();
    $password = $_POST['pass'];
    $errors = array();
    $errors = usernameValid($username, $errors);
    if(count($errors) == 0){
        $stmt = $pdo->prepare('SELECT * FROM users WHERE Username = ?');
        $stmt->execute([$username]);
        $query = $stmt->fetchAll();

        if(count($query)==0){die('<script>swal("Error!","Invalid Username!","error");</script>');}
        if(password_verify($password, $query[0]['Password'])){
            $_SESSION['username'] = $query[0]['Username'];
            $_SESSION['accessLevel'] = $query[0]['AccessLevel'];
            $stmt = $pdo->prepare('INSERT INTO login_log(username, logDate) VALUES (?,?)');
            $stmt->execute([$username, $dt->format('Y-m-d H:i:s')]);
            ?>
            <script>swal({
                title: "Logged in!",
                text: "Successfully logged in!",
                icon: "success"
                }).then(function() {
                <?php
                $_SESSION['username'] = $username;
                if($query[0]['forgotPass'] == 1){
                    echo 'ajaxNavigation("ChangePass");';
                }
                else{
                    echo 'location="index.php";';
                }
                ?>
                });</script>
            <?php
        }
        else{
            echo '<script>swal("Error!","Wrong username or password!","error");</script>';
        }
    }
    else{
        $er = implode("\\n\\n", $errors);
        echo "<script>swal('Error!','$er','error');</script>";
    }
}
else{
    ?>
<div class="container h-100">
    <div class="row justify-content-center align-items-center">
        <div class="form-signIn col-xs-1 col-sm-10 col-md-8 col-lg-4"> <!-- Resizeable for each screen size -->
            <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
            <input type="text" id="inputUser" class="form-control" name="username" placeholder="Username" required
                autofocus><br />
            <input type="password" id="inputPassword" class="form-control" name="pass" placeholder="Password"
                required><br />
            <a href="" onclick="ajaxNavigation('PasswordReset'); return false;">Forgot Password</a>
            <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit();">Login</button>
            <br />
            Don't have an account? <a href="" onclick="ajaxNavigation('signUp'); return false;">Sign Up</a>
        </div>
    </div>
</div>
<div id="output"></div>
<?php
}
?>