<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php
require_once("func/config.php");
$regErr = "";
$email = $_POST["email"];
$password = $_POST["password"];
$type = $_POST["type"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    //$con=mysqli_connect("localhost","root","12345678","471db");
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    if ($type !== "User" && $type !=="Admin") {
        $regErr = "Invalid type of account";
    }
    else if (empty($email)) {
        $regErr = "E-mail address is empty";
    }
    else if (empty($password)) {
        $regErr = "Password is empty";
    }
    else {
        $email_E = mysqli_real_escape_string($con, $email);
        $pass_E = mysqli_real_escape_string($con, $password);
        $type_E = mysqli_real_escape_string($con, $type);
        
        $result = "SELECT * FROM `Account` WHERE `Email_ID`='$email_E'";
        if (mysqli_num_rows($result) > 0) {
            $regErr = "Account exists";
        }
        else {
            $sql = "INSERT INTO `Account` (`Email_ID`, `Password`, `Type`) VALUES ('$email_E', '$pass_E', '$type_E')";
            if (!mysqli_query($con,$sql)){
                die('Error: ' . mysqli_error($con));
            }
            else {
                $_SESSION["Email"] = $email;
                if ($type == "User") {
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
    }
      
    mysqli_close($con);
}
?>

<div class="title">
    <h1>Register</h1>
</div>

<div class="container-sm">
    <div class="row justify-content-center">
        <div class="col-sm-7 col-sm-offset-7">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Email Address:<br>
                <input type="email" name="email"><br>
                Password:<br>
                <input type="password" name="password"><br>
                Account type:<br>
                <select name="type" size="2">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
                <input type="submit" value="Register"><br>
            </form>
            <span class="error"><?php echo $regErr;?></span>
        </div>
    </div>
</div>


</body>
</html>

