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
    <title>Manage Dish Page</title>
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
$dish_ID = array();
$dish_name = array();
$dish_price = array();
$dish_cat = array();

$upErr = "";
//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$res_ID_E = mysqli_real_escape_string($con, $res_ID);
$find_result = mysqli_query($con, "SELECT * FROM `Dish` WHERE `RestaurantID`='$res_ID_E'");
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

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Manage_Dish"]) {
        if (!isset($_POST["dishRadio"])) {
            $manageErr = "Error: Dish was not selected.";
        }
        else {
            $radio = $_POST["dishRadio"];
            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }
            
            $find_DID_E = mysqli_real_escape_string($con, $dish_ID[$radio]);
            $man_result = mysqli_query($con, "SELECT * FROM `Dish` WHERE `Dish_ID`='$find_DID_E'");
            if (mysqli_num_rows($man_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                $_SESSION["DID"] = $dish_ID[$radio];
                $_SESSION["DName"] = $dish_name[$radio];
                $_SESSION["DPrice"] = $dish_price[$radio];
                $_SESSION["DCat"] = $dish_cat[$radio];
                header("Location: dish.php");
                die();
            }
            
            mysqli_close($con);    
        }
    }
    else if ($_POST["Create_Dish"]) {
            require_once("func/funcManDish.php");
            createDish($res_ID, $_POST["crName"], $_POST["crPrice"], $_POST["crCat"], $new_id);

            $_SESSION["DID"] = $new_id;
            $_SESSION["DName"] = $_POST["crName"];
            $_SESSION["DPrice"] = $_POST["crPrice"];
            $_SESSION["DCat"] = $_POST["crCat"];
            header("Location: man_ing.php"); // go to ingredient page to add an ingredient
            die();
    }
}

?>

<div class="title">
    <h1> Manage Dishes </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="dishes"> Your Dish </h4>
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
            <input type="submit" value="Manage Dish" name="Manage_Dish">
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="menus"> Create a Dish </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Dish Name:<br>
            <input type="text" name="crName"><br>
            Dish Price:<br>
            <input type="number" step=".01" name="crPrice"><br>
            Dish Category:<br>
            <input type="text" name="crCat"><br>
            <input type="submit" value="Create Dish" name="Create_Dish"><br>
        </form>
        <span class="error"><?php echo $crErr;?></span><br><br>
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
