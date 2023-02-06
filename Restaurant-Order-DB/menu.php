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
    <title>Menu Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        td, th {
            border: 1px solid #000000;
            text-align: left;
            pcrIng: 8px;
        }
    </style>
</head>
<body>

<?php
require_once("func/config.php");
$res_ID = $_SESSION["RID"];
$menu_ID = $_SESSION["MID"];
$res_name = $_SESSION["RName"];
$menu_name = $_SESSION["MName"];

$manageErr = $upErr = $addErr = $delErr = "";
$dish_name = array();
$dish_ID = array();
$dish_price = array();
$dish_cat = array();
$store_val = array();

$canD_ID = array();
$canD_name = array();
$canD_price = array();
$canD_cat = array();
$canD_store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$res_ID_E = mysqli_real_escape_string($con, $res_ID);
$menu_ID_E = mysqli_real_escape_string($con, $menu_ID);
$find_result = mysqli_query($con, "SELECT * FROM (`Dish` NATURAL JOIN `Has`) WHERE `Menu_ID`='$menu_ID_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $dish_ID[] = $find_row["Dish_ID"];
    $dish_name[] = $find_row["Dish_Name"];
    $dish_price[] = $find_row["Price"];
    $dish_cat[] = $find_row["Category"];
}

$find_result = mysqli_query($con, "SELECT * FROM `Dish` AS `D`, `Restaurant` AS `R`
                                         WHERE `R`.`R_ID`='$res_ID_E' AND `R`.`R_ID`=`D`.`RestaurantID`
                                         AND NOT EXISTS
                                         (SELECT * FROM `Has` AS `H`, `Menu` AS `M`
                                          WHERE `D`.`Dish_ID`=`H`.`Dish_ID`
                                          AND `H`.`Menu_ID`=`M`.`Menu_ID`
                                          AND `M`.`Menu_ID`='$menu_ID_E')");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $canD_store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $canD_ID[] = $find_row["Dish_ID"];
    $canD_name[] = $find_row["Dish_Name"];
    $canD_price[] = $find_row["Price"];
    $canD_cat[] = $find_row["Category"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // go to manage dish
    if ($_POST["Remove_Dish"]) {
        // take out dish from menu but do not delete dish altogether
        if (!isset($_POST["dishRadio"])) {
            $delErr = "Error: Dish was not selected.";
        }
        else {
            require_once("func/funcMenu.php");
            removeDish($menu_ID, $dish_ID[$_POST["dishRadio"]]);

            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Add_Dish"]) {
        // add existing dish; it's in Dish but not in Has
        if (!isset($_POST["addDishRadio"])) {
            $addErr = "Error: Dish was not selected.";
        }
        else {
            require_once("func/funcMenu.php");
            addDish($menu_ID, $canD_ID[$_POST["addDishRadio"]]);

            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Update"]) {
        require_once("func/funcMenu.php");
        $up_name = $_POST["updateName"];
        $success = updateMenu($menu_ID, $up_name);

        if ($success) {
            $_SESSION["MName"] = $up_name;
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Delete"]) {
        require_once("func/funcMenu.php");
        deleteMenu($menu_ID);


        header("Location: restaurant.php");
        die();
    }
}
?>

<div class="title">
    <h1> Menu Page </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="menu-name"> <?php echo "$menu_name menu in $res_name";?>  </h4>
</div>

<div class="col-md-12 text-center">
    <h4 class="menus"> Your Dishes </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Dish ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="dishRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $dish_ID[$val]; ?></th>
                        <th><?php echo $dish_name[$val]; ?></th>
                        <th><?php echo $dish_price[$val]; ?></th>
                        <th><?php echo $dish_cat[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Remove Dish from this Menu" name="Remove_Dish"><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                </tr>
                <?php foreach ($canD_store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="addDishRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $canD_name[$val]; ?></th>
                        <th><?php echo $canD_price[$val]; ?></th>
                        <th><?php echo $canD_cat[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Add Dish to this Menu" name="Add_Dish"><br>
        </form>
        <span class="error"><?php echo $addErr;?></span><br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="menus"> Update menu name </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            New Menu Name:<br>
            <input type="text" name="updateName"><br>
            <input type="submit" value="Update Menu name" name="Update"><br>
        </form>
        <span class="error"><?php echo $upErr;?></span><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Delete this Menu" name="Delete"><br>
        </form>
        <span class="error"><?php echo $delErr;?></span><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="restaurant.php">
            <input type="submit" value="Go back to restaurant page" />
        </form>
    </div>
</div>

</body>
</html>
