<?php
session_start();
function randomPassword() { //function that creates a random password using all alphanumeric characters
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
?>

<script>
function ajaxSubmit() { //when user submits the username
    var formData = {
        'username': $('input[name=username]').val(),
        'submit': true
    };

    $.ajax({
        url: "includes/Forgot.php",
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
<script>
function ajaxSubmit2() { //when user submits the answer to the forgotten password
    var formData = {
        'answer': $('input[name=answer]').val(),
        'submit2': true
    };

    $.ajax({
        url: "includes/Forgot.php",
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
require('dbconnect.php'); //connects to the PDO database
$username = $_POST['username'] ?? ""; //username = whatever the user entered into the username input
$questions = [ //Array of questions for security questions
    0 => "Who is your best friend?",
    1 => "What is your mother's maiden name?",
    2 => "What street did you grow up on?",
    3 => "What was your first car?",
    4 => "What is your first pet's name?"
];

if(isset($_POST['submit'])){
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?'); //queries the table for anything with the username = to $username
    $stmt->execute([$username]);
    $query = $stmt->fetchAll();
    if(count($query)==0){die('<script>swal("Error!","Username not found!","error");</script>');} //if the query is empty...returns an error
    if($query[0] != 0){ //if query is not empty it sets the question equal to whatever the username's question was stored in the database
        $_SESSION['question'] = $query;
        $_SESSION['forgotUser'] = $username; //creating a session variable for the username the user put into the $username field
        ?>
<div class="container h-100">
    <div class="row justify-content-center align-items-center">
        <div class="form-signIn col-xs-1 col-sm-10 col-md-8 col-lg-4">
            <h1 class="h3 mb-3 font-weight-normal">Security Question</h1>
            <input type="text" class="form-control" name="answer"
                placeholder="<?php echo $questions[$_SESSION['question'][0]['Question']];?>" required><br />
            <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit2()">Submit</button>
            <div id="output"></div>
        </div>
    </div>
</div>
<?php
    }
}
else{
?>
<div id="output">
    <div class="container h-100">
        <div class="row justify-content-center align-items-center">
            <div class="form-signIn col-xs-1 col-sm-10 col-md-8 col-lg-4"> <!-- Resizeable for each screen size -->
                <h1 class="h3 mb-3 font-weight-normal">Forgot Password?</h1>
                <input type="text" name="username" class="form-control" placeholder="Username" required><br/>
                <button class="btn btn-success btn-block" name="submit" onclick="ajaxSubmit();">Submit!</button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

<?php
if(isset($_POST['submit2'])){ //if sumbit2 is selected which is the submit answer button
    require('dbconnect.php'); //connects to the database
    $answer = $_POST['answer']; //grabs the answer the user typed in
    $username = $_SESSION['forgotUser']; //sets username = to the session stored username
    $answer = str_replace(' ', '_', $answer); //removes spaces and underscores from answer
    if($answer == $_SESSION['question'][0]['Answer']){
        $rPass = randomPassword();
        $stmt = $pdo->prepare('UPDATE users SET password = ?, forgotPass = ? WHERE username = ?');
        $stmt->execute([password_hash($rPass, PASSWORD_BCRYPT), 1, $username]);
        //TODO send user an E-Mail to reset password instead of just printing out a random password on the page
    echo "<script>swal('Success!','Your new password is: $rPass','success')</script>";
    }
    else{
        echo "<script>swal('Error!','Wrong answer!','error')</script>";
    }
}
?>