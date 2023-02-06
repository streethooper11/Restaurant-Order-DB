<?php
session_start();

if (isset($_SESSION["LoggedIn"])) {
    session_destroy();
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CPSC 471 Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php
require_once("func/config.php");
$logErr = "";
$email = $_POST["email"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    //$con=mysqli_connect("localhost","root","12345678","471db");
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $email_E = mysqli_real_escape_string($con, $email);
    $pass_E = mysqli_real_escape_string($con, $_POST["password"]);
    $result = mysqli_query($con, "SELECT * FROM `Account` WHERE `Email_ID`='$email_E' AND `Password`='$pass_E'");
    
    if (mysqli_num_rows($result) == 0) {
        $logErr = "Error: Username/Password combination does not match";
    }
    else {
        while($row = mysqli_fetch_array($result)) {
            $_SESSION["LoggedIn"] = true;
            $_SESSION["Email"] = $email; // save email address in session

            if ($row["Type"] == "User") {
                // user account
                $_SESSION["accLevel"] = 0;
                header("Location: user.php");
                die();
            }
            else {
                // owner account
                $_SESSION["accLevel"] = 1;
                header("Location: admin.php");
                die();
            }
        }
    } 
    
    mysqli_close($con);    
}
?>

<div class="title">
    <h1> CPSC 471 Project </h1>
    <h3> Created By: Mohamed Mansour, Sehwa Kim, William Ho </h3>
</div>

<div class ="container-sm">
    <div class="row justify-content-center">
        <div class="col-sm-4 col-sm-offset-4">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="email">Email Address:</label><br>
        <input type="email" name="email"><br>
        <label for="password">Password:</label><br>
        <input type="password" name="password"><br>
        <input type="submit" value="Log in"><br>
    </form>
    <span class="error"><?php echo $logErr;?></span>

        </div>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="register.php">
            <input type="submit" value="Sign up" />
        </form>
    </div>
</div>

</body>
</html>
