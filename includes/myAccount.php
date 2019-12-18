<div class="container">
    <div class="row justify-content-center">
    <?php
        @session_start();
        echo "<p id=\"account\">".$_SESSION['username']."</p>";
    ?>
    </div>
    <div class="row justify-content-center">
        <a href="includes/auth.php" target="_blank"><button class="btn btn-success">Link My Spotiy Account</button></a>
        <button class="btn btn-success" onclick="ajaxNavigation('ChangePass')">Change Password</button>
    </div>
</div>