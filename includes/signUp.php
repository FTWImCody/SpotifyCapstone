<script>
function ajaxSubmit() {
    var formData = {
        'username': $('input[name=username]').val(),
        'pass': $('input[name=pass]').val(),
        'pass1': $('input[name=pass1]').val(),
        'email': $('input[name=email]').val(),
        'securityQ': $('select[name=securityQ]').val(),
        'answer': $('input[name=answer]').val(),
        'submit': true
    };

    $.ajax({
        url: "includes/SignUp.php",
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
require('dbconnect.php');
$username = $_POST['username'] ?? "Username";

function usernameValid($username, $errors){
    $validUser = '/^[\w]{6,12}$/';
    if (!preg_match($validUser, $username)){
        $errors[] = "Username must be between 6-12 characters in length and can only contain alphanumeric characters(A-Z, a-z, 0-9 or _).";
    }

    return $errors;
}

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

function emailValid($email, $errors){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please make sure your email is valid.";
    }
    return $errors;
}

function secureQValid($question, $errors){
    if($question == null){
        $errors[] = "Please select a security question.";
    }
    return $errors;
}

if(isset($_POST['submit'])){
    $password = $_POST['pass'];
    $verifyPass = $_POST['pass1'];
    $email = $_POST['email'];
    $question = $_POST['securityQ'];
    $answer = $_POST['answer'];
    $answer = str_replace(' ', '_', $answer);
    $errors = array();
    $errors = usernameValid($username, $errors);
    $errors = passwordValid($password, $errors);
    $errors = emailValid($email, $errors);
    $errors = secureQValid($question, $errors);
    if($password != $verifyPass){
        $errors[] = "Passwords do not match.";
    }
    if(count($errors) == 0){
        $stmt = $pdo->prepare('SELECT count(*) as Username FROM users WHERE Username = ?');
        $stmt->execute([$username]);
        $query = $stmt->fetchAll();
        if($query[0]["Username"] == 0){
            //send info to database
            $stmt = $pdo->prepare('INSERT INTO users(Username, Password, AccessLevel, Email, Question, Answer, Token, Refresh, forgotPass) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_BCRYPT), 0, $email ,$question, $answer, "", "", 0]);
            echo '<script>swal({
                title: "Success!",
                text: "Successfully created account!",
                icon: "success"
                }).then(function() {
                ajaxNavigation("signIn");
                });</script>';
        }
        else{
            echo '<script>swal("Error!","Username already taken!","error");</script>';
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
            <h1 class="h3 mb-3 font-weight-normal">Register</h1>
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus><br />
            <input type="password" name="pass" class="form-control" placeholder="Password" required><br />
            <input type="password" name="pass1" class="form-control" placeholder="Confirm Password" required><br />
            <input type="text" name="email" class="form-control" placeholder="Email" required><br />
            <select name="securityQ" class="form-control" required>
                <option value="" hidden>Security Question</option>
                <option value=0>Who is your best friend?</option>
                <option value=1>What is your mother's maiden name?</option>
                <option value=2>What street did you grow up on?</option>
                <option value=3>What was your first car?</option>
                <option value=4>What is your first pet's name?</option>
            </select><br />
            <input type="text" class="form-control" name="answer" placeholder="Answer" required><br />
            <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit();">Submit</button>
            <br />
            Already have an account?
            <a href="" onclick="ajaxNavigation('signIn'); return false;">Sign In</a>
        </div>
    </div>
</div>
<div id="output"></div>
<?php
}
?>