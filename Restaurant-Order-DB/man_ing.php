<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 1)) {
    session_destroy();
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ingredient management Page</title>
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
$dish_ID = $_SESSION["DID"];
$dish_name = $_SESSION["Dname"];

$manageErr = $addErr = $delErr = "";
$ing_ID = array();
$ing_name = array();
$ing_type = array();
$ing_need = array();
$store_val = array();

$canI_ID = array();
$canI_name = array();
$canI_type = array();
$canI_store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
$find_result = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Needs`) WHERE `Dish_ID`='$dish_ID_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $ing_ID[] = $find_row["IngredientID"];
    $ing_name[] = $find_row["IngredientName"];
    $ing_type[] = $find_row["Type"];
    $ing_need[] = $find_row["Amount_Needed"];
}

// find ingredients that exist in the restaurant but not already included in the dish
$res_ID_E = mysqli_real_escape_string($con, $res_ID);
$find_result = mysqli_query($con, "SELECT * FROM `Restaurant_has_Ingredient` AS `A`, `Ingredient` AS `I`
                                    WHERE `A`.`RestaurantID`='$res_ID_E'
                                    AND `A`.`IngredientID`=`I`.`IngredientID`
                                    AND NOT EXISTS
                                    (SELECT * FROM `Needs` AS `N`
                                     WHERE `N`.`Dish_ID`='$dish_ID_E'
                                     AND `A`.`IngredientID`=`N`.`IngredientID`)");

$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $canI_store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $canI_ID[] = $find_row["IngredientID"];
    $canI_name[] = $find_row["IngredientName"];
    $canI_type[] = $find_row["Type"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Update_Ing"]) {
        if (!isset($_POST["ingRadio"])) {
            $manageErr = "Error: Ingredient was not selected.";
        }
        else {
            require_once("func/funcManIng.php");
            $up_quan = $_POST["up_ing_quan"];
            $success = updateIng($ing_ID[$_POST["ingRadio"]], $up_quan, $manageErr);
    
            if ($success) {
                header('Location: '.$_SERVER['PHP_SELF']);
                die();
            }
        }
    }
    else if ($_POST["Remove_Ing"]) {
        if (!isset($_POST["ingRadio"])) {
            $manageErr = "Error: Ingredient was not selected.";
        }
        else {
            require_once("func/funcManIng.php");
            removeIng($dish_ID, $ing_ID[$_POST["ingRadio"]]);

            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Add_Ing"]) {
        if (!isset($_POST["addIngRadio"])) {
            $addErr = "Error: Ingredient was not selected.";
        }
        else {
            require_once("func/funcManIng.php");
            $success = addIng($dish_ID, $canI_ID[$_POST["addIngRadio"]], $_POST["add_ing_quan"], $addErr);

            if ($success) {
                header('Location: '.$_SERVER['PHP_SELF']);
                die();
            }
        }
    }
}
?>

<div class="title">
    <h1> Ingredient Management Page </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="menus"> Ingredient information for <?php echo $dish_name; ?> </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Ingredient name</th>
                    <th>Ingredient type</th>
                    <th>Amount needed</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="ingRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $ing_name[$val]; ?></th>
                        <th><?php echo $ing_type[$val]; ?></th>
                        <th><?php echo $ing_need[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Amount required:<br>
            <input type="number" name="up_ing_quan" min="1"><br>
            <input type="submit" value="Update Ingredient amount" name="Update_Ing"><br><br>
            <input type="submit" value="Remove Ingredient from this Dish" name="Remove_Ing"><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="menus"> Add an ingredient </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                <th>Select</th>
                    <th>Ingredient name</th>
                    <th>Ingredient type</th>
                </tr>
                <?php foreach ($canI_store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="addIngRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $canI_name[$val]; ?></th>
                        <th><?php echo $canI_type[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Amount required:<br>
            <input type="number" name="add_ing_quan" min="1"><br>
            <input type="submit" value="Add Ingredient to this Dish" name="Add_Ing"><br>
        </form>
        <span class="error"><?php echo $addErr;?></span><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="dish.php">
            <input type="submit" value="Go back to dish page" />
        </form>
    </div>
</div>

</body>
</html>
