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
    <title>Restaurant Page</title>
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
$res_ID = $_SESSION["RID"];
$res_name = $_SESSION["RName"];
$res_loc = $_SESSION["RLoc"];

$manageErr = "";
$menu_name = array();
$menu_ID = array();
$store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$res_ID_E = mysqli_real_escape_string($con, $res_ID);
$find_result = mysqli_query($con, "SELECT * FROM `Menu` WHERE `RestaurantID`='$res_ID_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $menu_ID[] = $find_row["Menu_ID"];
    $menu_name[] = $find_row["Menu_Name"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // go to manage menu
    if ($_POST["Manage_Menu"]) {
        if (!isset($_POST["menuRadio"])) {
            $manageErr = "Error: Menu was not selected.";
        }
        else {
            $radio = $_POST["menuRadio"];
            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            
            $find_MID_E = mysqli_real_escape_string($con, $menu_ID[$radio]);
            $man_result = mysqli_query($con, "SELECT * FROM `Menu` WHERE `Menu_ID`='$find_MID_E'");            
            if (mysqli_num_rows($man_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                $_SESSION["MID"] = $menu_ID[$radio];
                $_SESSION["MName"] = $menu_name[$radio];
                header("Location: menu.php");
                die();
            }
            
            mysqli_close($con);    
        }
    }
    else if ($_POST["Add_Menu"]) {
        require_once("func/funcRest.php");
        addMenu($res_ID, $_POST["addName"]);

        header('Location: '.$_SERVER['PHP_SELF']);
        die();
    }
    else if ($_POST["Update"]) {
        // update restaurant information
        require_once("func/funcRest.php");
        updateRestaurant($res_ID, $_POST["updateName"], $_POST["updateLoc"]);

        $_SESSION["RName"] = $up_name;
        $_SESSION["RLoc"] = $up_loc;
        header('Location: '.$_SERVER['PHP_SELF']);
        die();
    }
    else if ($_POST["Delete"]) {
        require_once("func/funcRest.php");
        deleteRestaurant($res_ID);
        
        header("Location: admin.php");
        die();    
    }
}
?>

<div class="title">
    <h1> Restaurant Page </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="restaurant-name"> <?php echo "Restaurant $res_name in $res_loc";?>  </h4>
</div>

<div class="col-md-12 text-center">
    <h4 class="menus"> Your Menus </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Name</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="menuRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $menu_name[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Manage Menu" name="Manage_Menu">
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Add a Menu </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Menu Name:<br>
            <input type="text" name="addName"><br>
            <input type="submit" value="Add Menu" name="Add_Menu"><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="man_dish.php">
            <input type="submit" value="Manage Dishes" />
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="ingredient.php">
            <input type="submit" value="Manage Ingredients" />
        </form>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Update restaurant information </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            New Restaurant Name:<br>
            <input type="text" name="updateName"><br>
            New Location:<br>
            <input type="text" name="updateLoc"><br>
            <input type="submit" value="Update restaurant info" name="Update"><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Delete this Restaurant" name="Delete"><br><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="admin.php">
            <input type="submit" value="Go back to account page" />
        </form>
    </div>
</div>

</body>
</html>
