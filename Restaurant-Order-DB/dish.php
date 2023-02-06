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
    <title>Dish Page</title>
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
$dish_ID = $_SESSION["DID"];
$dish_name = $_SESSION["DName"];
$dish_price = number_format((float)$_SESSION["DPrice"], 2, '.', ''); // 2 decimals places for showing price
$dish_cat = $_SESSION["DCat"];

$upErr = "";
//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
$ing = array();
$find_result = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Needs`) WHERE `Dish_ID`='$dish_ID_E'");

while ($find_row = mysqli_fetch_array($find_result)) {
    $ing[] = $find_row["IngredientName"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Update"]) {
        require_once("func/funcDish.php");
        $up_name = $_POST["updateName"];
        $up_price = $_POST["updatePrice"];
        $up_cat = $_POST["updateCat"];
        $success = updateDish($dish_ID, $up_name, $up_price, $up_cat, $upErr);

        if ($success) {
            $_SESSION["DName"] = $up_name;
            $_SESSION["DPrice"] = $up_price;
            $_SESSION["DCat"] = $up_cat;
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Delete"]) {
        require_once("func/funcDish.php");
        deleteDish($dish_ID);

        header("Location: menu.php");
        die();
    }
}
?>

<div class="title">
    <h1> Dish Page </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="dishes"> Your Dish </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <table>
            <tr>
                <th>Dish Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Ingredients</th>
            </tr>
            <tr>
                <th><?php echo $dish_name; ?></th>
                <th><?php echo $dish_price; ?></th>
                <th><?php echo $dish_cat; ?></th>
                <th><?php foreach ($ing as $key => $value): echo $value,","; endforeach; ?></th>
            </tr>
        </table>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="dishes"> Update dish info </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            New Dish Name:<br>
            <input type="text" name="updateName"><br>
            New Dish Price:<br>
            <input type="number" step=".01" name="updatePrice"><br>
            New Dish Category:<br>
            <input type="text" name="updateCat"><br>
            <input type="submit" value="Update dish information" name="Update"><br>
        </form>
        <span class="error"><?php echo $upErr;?></span>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="man_ing.php">
            <input type="submit" value="Update ingredients" name="Delete"><br><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Delete this Dish" name="Delete"><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="man_dish.php">
            <input type="submit" value="Go back" />
        </form>
    </div>
</div>

</body>
</html>
