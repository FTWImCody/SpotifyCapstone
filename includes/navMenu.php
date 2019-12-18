<?php
  $_SESSION['username'] = $_SESSION['username'] ?? 'Guest'; //sets username = to Guest if no session username has been set
  $_SESSION['accessLevel'] = $_SESSION['accessLevel'] ?? 0;
?>
<nav class="navbar navbar-expand-md navbar-dark">
    <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar"> <!--Collapsible Navbar -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a href="" onclick="ajaxNavigation('Home'); return false;">Home</a>
            </li>
            <?php
                if($_SESSION['username'] != 'Guest'){ //Will return a logout button if the user has actually logged into an account
            ?>
            <li class="nav-item">
                <a href="" onclick="ajaxNavigation('RandomGen'); return false;">Random Song</a>
            </li>
        </ul>
        <ul class="navbar-nav">
        <li class="nav-item">
            <!-- Dropdown -->
            <li class="nav-item dropdown">
            <a class="dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                <?php echo $_SESSION['username'];?>
            </a>
            <div class="nav-item dropdown-menu">
                <a class="dropdown-item" href="" onclick="ajaxNavigation('myAccount'); return false;">My Account</a>
                <a class="dropdown-item" href="" onclick="ajaxNavigation('Logout'); return false;">Logout</a>
            </div>
        </li>
        </ul>
        <?php
    }
    else{
    ?>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="" onclick="ajaxNavigation('signIn'); return false;">Login</a>
            </li>
            <li class="nav-item">
                <a href="" onclick="ajaxNavigation('signUp'); return false;">Register</a>
            </li>
        </ul>
        <?php
    }
    ?>
    </div>
</nav>