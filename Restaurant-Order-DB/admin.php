<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 1)) {
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        td, th {
            border: 1px solid #000000;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>

<?php
require_once("func/config.php");
$manageErr = "";
$email = $_SESSION["Email"];
$res_ID = array();
$res_name = array();
$res_loc = array();
$store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email_E = mysqli_real_escape_string($con, $email);
$find_result = mysqli_query($con, "SELECT * FROM `Restaurant` WHERE `AdminEmail_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $res_ID[] = $find_row["R_ID"];
    $res_loc[] = $find_row["Location"];
    $res_name[] = $find_row["Name"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Manage"]) {
        if (!isset($_POST["resRadio"])) {
            $manageErr = "Error: Restaurant was not selected.";
        }
        else {
            $radio = $_POST["resRadio"];
            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $find_RID_E = mysqli_real_escape_string($con, $res_ID[$radio]);
            $man_result = mysqli_query($con, "SELECT * FROM `Restaurant` WHERE `R_ID`='$find_RID_E'");
            if (mysqli_num_rows($man_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                $_SESSION["RID"] = $res_ID[$radio];
                $_SESSION["RName"] = $res_name[$radio];
                $_SESSION["RLoc"] = $res_loc[$radio];
                header("Location: restaurant.php");
                die();
            }    

            mysqli_close($con);                
        }
    }
    else if ($_POST["Add"]) {
        require_once("func/funcAd.php");
        addRestaurant($email, $_POST["addName"], $_POST["addLoc"]);

        header('Location: '.$_SERVER['PHP_SELF']);
        die();
    }
    else if ($_POST["Log_out"]) {
        session_destroy();
        header("Location: index.php");
        die();
    }
    else if ($_POST["Del_acc"]) {
        require_once("func/funcAd.php");
        deleteAdmin($email);
        session_destroy();
        header("Location: index.php");
        die();
    }
}
?>

<div class="title">
    <h1> Admin Page </h1>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Your Restaurants </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <table>
                        <tr>
                            <th>Select</th>
                            <th>Name</th>
                            <th>Location</th>
                        </tr>
                        <?php foreach ($store_val as $val): ?>
                            <tr>
                                <th><input type="radio" name="resRadio" value=<?php echo $val; ?>></th>
                                <th><?php echo $res_name[$val]; ?></th>
                                <th><?php echo $res_loc[$val]; ?></th>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <input type="submit" value="Manage" name="Manage"><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Add a Restaurant </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <fieldset>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Restaurant Name:<br>
                <input type="text" name="addName"><br>
                Location:<br>
                <input type="text" name="addLoc"><br>
                <input type="submit" value="Add" name="Add"><br>
            </form>
        </fieldset>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="adminSeeReview.php">
            <input type="submit" value="See order reviews" />
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Log out" name="Log_out">
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Delete your account" name="Del_acc">
        </form>
    </div>
</div>

</body>
</html>
