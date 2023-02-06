<?php
session_start();

if (isset($_SESSION["curProfName"])) {
    unset($_SESSION["curProfName"]);
}

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 0)) {
    session_destroy();
    header("Location: index.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>User Page</title>
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
        
        <div class="title">
            <h1> User Page </h1>
        </div>

        <div class="restaurant-suggestions">
            <h3> Featured Restaurants </h3>
            <div class="row row-cols-1 row-cols-md-3 g-4 mx-5">
                <div class="col">
                    <div class="card h-500">
                        <img src="images/bread.png" class="card-img-top mx-auto mt-3" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Bake Chef</h5>
                            <p class="card-text">Vietnamese Subs, Hotdog Buns, Cookies</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="images/cow.png" class="card-img-top mx-auto mt-3" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Korean BBQ House</h5>
                            <p class="card-text">BBQ Beef or BBQ Chicken on Rice, Noodles, or Salad.</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100">
                        <img src="images/burger.png" class="card-img-top mx-auto mt-3" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">A&W</h5>
                            <p class="card-text">Burgers, Fries and Milkshakes </p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

<?php
require_once("func/config.php");
$email = $_SESSION["Email"];
$orderErr = $addErr = "";
$pro_name = array();
$pro_all = array();
$store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email_E = mysqli_real_escape_string($con, $_SESSION["Email"]);
$find_result = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $profile_name = $find_row["Name"];
    $pro_name[] = $profile_name;
    $allergy_list = "";
    $profile_name_E = mysqli_real_escape_string($con, $profile_name);

    $find_allergies = mysqli_query($con, "SELECT * FROM `Allergy` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E'");
    while ($row_allergies = mysqli_fetch_array($find_allergies)) {
        $allergy_list .= $row_allergies["Allergy_Name"] . ",";
    }

    $pro_all[] = $allergy_list;
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Manage_Prof"]) {
        if (!isset($_POST["profRadio"])) {
            $manageErr = "Error: Profile was not selected.";
        }
        else {
            $radio = $_POST["profRadio"];
            $sel_pro = $pro_name[$radio];
            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $sel_pro_E = mysqli_real_escape_string($con, $sel_pro);
            $man_result = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E' AND `Name`='$sel_pro_E'");

            if (mysqli_num_rows($man_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                $_SESSION["editProfName"] = $sel_pro;
                header("Location: profile.php");
                die();
            }    

            mysqli_close($con);                
        }
    }
    else if ($_POST["Add_Prof"]) {
        require_once("func/funcUser.php");
        $success = addProfile($email, $_POST["addName"], $_POST["addAll"], $addErr);
        
        if ($success) {
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Log_out"]) {
        session_destroy();
        header("Location: index.php");
        die();
    }
    else if ($_POST["Del_acc"]) {
        require_once("func/funcUser.php");
        deleteUser($email);
        session_destroy();
        header("Location: index.php");
        die();
    }
}
?>

<div class="col-md-12 text-center">
    <h4 class="profile"> Your Profiles </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Profile Name</th>
                    <th>Allergies</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="profRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $pro_name[$val]; ?></th>
                        <th><?php echo $pro_all[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Manage this profile" name="Manage_Prof"><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Add a Profile </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Profile Name:<br>
            <input type="text" name="addName"><br>
            Allergies (separate with commas, no whitespace):<br>
            <input type="text" name="addAll"><br>
            <input type="submit" value="Create Profile" name="Add_Prof"><br>
        </form>
        <span class="error"><?php echo $addErr;?></span><br><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="chooseRes.php">
            <input type="submit" value="Order from a restaurant" />
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="history.php">
            <input type="submit" value="See order history" />
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
