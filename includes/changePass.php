<script>
function ajaxSubmit() {
    var formData = {
        'oldPass': $('input[name=oldPass]').val(),
        'newPass': $('input[name=newPass]').val(),
        'newPass1': $('input[name=newPass1]').val(),
        'submit': true
    };

    $.ajax({
        url: "includes/changePass.php",
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
$username = $_SESSION['username'];

function passwordValid($password, $errors){
    $uppercase = preg_match('/[A-Z]/', $password);
    $lowercase = preg_match('/[a-z]/', $password);
    $number    = preg_match('/[0-9]/', $password);
    $space     = preg_match('/[ ]/', $password);
    $specChar  = preg_match('/[!"#$%&\'()*+,-.\/:;<=>?@[\]^_`{|}~]/', $password);

    if($space){
        $errors[] = "Password can not contain spaces.";
    }
    if(!$uppercase){
        $errors[] = "Password must contain at least one Uppercase letter(A-Z).";
    }
    if(!$lowercase){
        $errors[] = "Password must contain at least one lowercase letter(a-z).";
    }
    if(!$number){
        $errors[] = "Password must contain at least one number(0-9).";
    }
    if(strlen($password) < 8 || strlen($password) > 16){
        $errors[] = "Password length must be between 8-16 characters in length.";
    }
    if(!$specChar){
        $errors[] = "Password must contain at least one special character.";
    }
    //(!\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~)
    return $errors;
}

if(isset($_POST['submit'])){
    $oldPass = $_POST['oldPass'];
    $newPass = $_POST['newPass'];
    $newPass1 = $_POST['newPass1'];
    $errors = array();
    $errors = passwordValid($newPass, $errors);
    if(count($errors) == 0){
        if($newPass == $newPass1 and $newPass != $oldPass){
            $stmt = $pdo->prepare('SELECT * FROM users WHERE Username = ?');
            $stmt->execute([$username]);
            $query = $stmt->fetchAll();

            if(count($query)==0){die('<script>swal("Error!","Invalid Username!","error");</script>');}
            $stmt = $pdo->prepare('UPDATE users SET password = ?, forgotPass = ? WHERE username = ?');
            $stmt->execute([password_hash($newPass, PASSWORD_BCRYPT), 0, $username]);
            $_SESSION['username'] = $username;
            ?>
            <script>swal({
                title: "Success!",
                text: "Successfully changed password!",
                icon: "success"
                }).then(function() {
                    location="index.php";
                });</script>
            <?php
        }
        else if($oldPass == $newPass){
            echo '<script>swal("Error!","Your new password can not match old password!","error");</script>';
        }
        else{
            echo '<script>swal("Error!","Your passwords do not match!","error");</script>';
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
            <h1 class="h3 mb-3 font-weight-normal">Change Password</h1>
            <input type="password" id="inputPassword" class="form-control" name="oldPass" placeholder="Current Password" required autofocus><br />
            <input type="password" id="inputPassword" class="form-control" name="newPass" placeholder="New Password" required><br />
            <input type="password" id="inputPassword" class="form-control" name="newPass1" placeholder="Confirm Password" required><br />
            <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit();">Submit</button>
            <br />
        </div>
    </div>
</div>
<div id="output"></div>
<?php
}
?>