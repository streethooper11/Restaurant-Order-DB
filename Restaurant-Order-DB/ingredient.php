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
    <title>Ingredient Page</title>
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

$manageErr = "";
$ing_ID = array();
$ing_name = array();
$ing_quan = array();
$store_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$res_ID_E = mysqli_real_escape_string($con, $res_ID);
$find_result = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Restaurant_has_Ingredient`) WHERE `RestaurantID`='$res_ID_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $ing_ID[] = $find_row["IngredientID"];
    $ing_name[] = $find_row["IngredientName"];
    $ing_quan[] = $find_row["Quantity"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["Add_Ing"]) {
        require_once("func/funcIng.php");
        $success = addIng($res_ID, $_POST["add_name"], $_POST["add_type"], $_POST["add_quan"], $addErr);

        if ($success) {
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else {
        if (!isset($_POST["ingRadio"])) {
            $manageErr = "Error: Ingredient was not selected.";
        }
        else {
            $radio = $_POST["ingRadio"];
            $find_IID = $ing_ID[$radio];

            if ($_POST["Update_Ing"]) {
                $up_quan = $_POST["up_quan"];    
                require_once("func/funcIng.php");
                $success = updateIng($res_ID, $find_IID, $up_quan, $manageErr);
        
                if ($success) {
                    header('Location: '.$_SERVER['PHP_SELF']);
                    die();
                }
            }
            else if ($_POST["Delete_Ing"]) {
                require_once("func/funcIng.php");
                $manageErr = deleteIng($find_IID);

                header('Location: '.$_SERVER['PHP_SELF']);
                die();
            }    
        }
    }
}
?>

<div class="title">
    <h1> Ingredient Page </h1>
</div>


<div class="col-md-12 text-center">
    <h4 class="ingredient-name"> <?php echo "Ingredients used in $res_name";?>  </h4>
</div>

<div class="col-md-12 text-center">
    <h4 class="ingredients"> Ingredient stocks </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Ingredient Name</th>
                    <th>Quantity left</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="ingRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $ing_name[$val]; ?></th>
                        <th><?php echo $ing_quan[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Update Quantity left:<br>
            <input type="number" name="up_quan"><br>
            <input type="submit" value="Update Quantity" name="Update_Ing">
            <input type="submit" value="Delete Ingredient" name="Delete_Ing"><br><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Add an ingredient </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Ingredient Name:<br>
            <input type="text" name="add_name"><br>
            Ingredient Type:<br>
            <input type="text" name="add_type"><br>
            Quantity:<br>
            <input type="number" name="add_quan" min="0"><br>
            <input type="submit" value="Add Ingredient" name="Add_Ing"><br>
        </form>
        <span class="error"><?php echo $addErr;?></span>
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
