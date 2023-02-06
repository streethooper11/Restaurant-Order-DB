<?php
session_start();

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
        <title>Choose a restaurant to order</title>
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
            <h1> Choose a restaurant to order </h1>
        </div>

<?php
require_once("func/config.php");
$orderErr = $addErr = "";
$pro_name = array();
$pro_all = array();
$store_val = array();

$res_ID = array();
$res_name = array();
$res_loc = array();
$res_val = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$find_result = mysqli_query($con, "SELECT * FROM `Restaurant`");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $res_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $res_ID[] = $find_row["R_ID"];
    $res_name[] = $find_row["Name"];
    $res_loc[] = $find_row["Location"];
}

$email_E = mysqli_real_escape_string($con, $_SESSION["Email"]);
$find_result = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $pro_name[] = $find_row["Name"];
    $profile_name_E = mysqli_real_escape_string($con, $find_row["Name"]);
    $allergy_list = "";

    $find_allergies = mysqli_query($con, "SELECT * FROM `Allergy` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E'");
    while ($row_allergies = mysqli_fetch_array($find_allergies)) {
        $allergy_list .= $row_allergies["Allergy_Name"] . ",";
    }

    $pro_all[] = $allergy_list;
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Sel_Prof"]) {
        if (!isset($_POST["profRadio"])) {
            $manageErr = "Error: Profile was not selected.";
        }
        else {
            $radio = $_POST["profRadio"];
            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $sel_pro_E = mysqli_real_escape_string($con, $pro_name[$radio]);
            $man_result = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E' AND `Name`='$sel_pro_E'");
            if (mysqli_num_rows($man_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                $_SESSION["curProfName"] = $pro_name[$radio];

                header('Location: '.$_SERVER['PHP_SELF']);
                die();
            }

            mysqli_close($con);                
        }
    }
    else if ($_POST["Order"]) {
        if (!isset($_POST["resRadio"])) {
            $orderErr = "Error: Restaurant was not selected.";
        }
        else if (!isset($_SESSION["curProfName"])) {
            $orderErr = "Error: No profile being used.";
        }
        else {
            $radio = $_POST["resRadio"];
            $_SESSION["orderResID"] = $res_ID[$radio];
            $_SESSION["orderResName"] = $res_name[$radio];
            header("Location: order.php");
            die();
        }
    }
    else if ($_POST["Search_Dish"]) {
        $searchDish = $_POST["searchDish"];
        if (empty($searchDish)) {
            $s_dishErr = "Error: Dish search field must not be empty.";
        }
        else {
            require_once("func/funcChoose.php");
            searchDish($searchDish);
            header('Location: '.$_SERVER['PHP_SELF']);
            die();        
        }
    }
    else if ($_POST["Search_Ing"]) {
        $searchIng = $_POST["searchIng"];
        if (empty($searchIng)) {
            $s_ingErr = "Error: Ingredient search field must not be empty.";
        }
        else {
            require_once("func/funcChoose.php");
            searchIng($searchIng);
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Order_Search"]) {
        if (!isset($_POST["searchRadio"])) {
            $s_orderErr = "Error: Restaurant was not selected.";
        }
        else if (!isset($_SESSION["curProfName"])) {
            $s_orderErr = "Error: No profile being used.";
        }
        else {
            $radio = $_POST["searchRadio"];
            $_SESSION["orderResID"] = $_SESSION["searchRes_ID"][$radio];
            $_SESSION["orderResName"] = $_SESSION["searchRes_N"][$radio];
            header("Location: order.php");
            die();
        }
    }
    else if ($_POST["Go_Back"]) {
        // unset search variables and go back to user page
        require_once("func/funcChoose.php");
        unsetAll();

        header("Location: user.php");
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
            <input type="submit" value="Use this profile" name="Sel_Prof"><br>
        </form>
        <span class="error"><?php echo $manageErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Select a restaurant to order using profile: <?php if (isset($_SESSION["curProfName"])) {echo $_SESSION["curProfName"];} else {echo "None";} ?>
</h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Restaurant Name</th>
                    <th>Location</th>
                </tr>
                <?php foreach ($res_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="resRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $res_name[$val]; ?></th>
                        <th><?php echo $res_loc[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Make an order" name="Order"><br>
        </form>
        <span class="error"><?php echo $orderErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Search for a dish name or an ingredient
</h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="searchDish"><br>
            <input type="submit" value="Search for a dish name" name="Search_Dish"><br>
        </form>
        <span class="error"><?php echo $s_dishErr;?></span></br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="searchIng"><br>
            <input type="submit" value="Search for an ingredient" name="Search_Ing"><br>
        </form>
        <span class="error"><?php echo $s_ingErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="restaurants"> Search results
</h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Restaurant Name</th>
                    <th>Location</th>
                    <th>Dish name</th>
                    <th>Ingredients</th>
                </tr>
                <?php foreach ($_SESSION["searchRes_val"] as $val): ?>
                    <tr>
                        <th><input type="radio" name="searchRadio" value=<?php echo $val; ?>></th>
                        <th><?php if (isset($_SESSION["searchRes_N"])) {echo $_SESSION["searchRes_N"][$val];} ?></th>
                        <th><?php if (isset($_SESSION["searchRes_L"])) {echo $_SESSION["searchRes_L"][$val];} ?></th>
                        <th><?php if (isset($_SESSION["searchRes_D"])) {echo $_SESSION["searchRes_D"][$val];} ?></th>
                        <th><?php if (isset($_SESSION["searchRes_I"])) {echo $_SESSION["searchRes_I"][$val];} ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Make an order from this restaurant" name="Order_Search"><br>
        </form>
        <span class="error"><?php echo $s_orderErr;?></span></br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Go back to user page" name="Go_Back"><br>
        </form>
    </div>
</div>

</body>
</html>
