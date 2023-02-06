<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 0)) {
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Page</title>
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
$order_res_ID = $_SESSION["orderResID"];
$order_res_name = $_SESSION["orderResName"];
$menuErr = $dishErr = $orderErr = "";

$menu_val = array();
$menu_ID = array();
$menu_name = array();

require_once("func/funcOrder.php");
updateTotalPrice();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$order_res_ID_E = mysqli_real_escape_string($con, $order_res_ID);
$find_result = mysqli_query($con, "SELECT * FROM `Menu` WHERE `RestaurantID`='$order_res_ID_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $menu_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $menu_ID[] = $find_row["Menu_ID"];
    $menu_name[] = $find_row["Menu_Name"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Sel_Menu"]) {
        if (!isset($_POST["menuRadio"])) {
            $menuErr = "Error: Menu was not selected.";
        }
        else {
            $radio = $_POST["menuRadio"];
            $sel_menu = $menu_ID[$radio];

            // selecting a new menu will wipe the order history as you can only order from one menu
            require_once("func/funcOrder.php");
            unsetAll();
            selectMenu($sel_menu);
            
            // get max number of the dish the restaurant is able to make; update it with each item added
            foreach ($_SESSION["dish_av_quan"] as $each_quan) {
                $_SESSION["cur_ord_dish_quan"][] = $each_quan;
            }

            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Sel_Dish"]) {
        if (!isset($_POST["dishRadio"])) {
            $dishErr = "Error: Dish was not selected.";
        }
        else {
            $order_quan = $_POST["dish_quan"];
            if (empty($order_quan) || $order_quan < 0) {
                $dishErr = "Error: Please enter a non-negative number.";
            }
            else {
                $radio = $_POST["dishRadio"];
                $sel_dish = $_SESSION["dish_ID"][$radio];

                if ($order_quan > $_SESSION["cur_ord_dish_quan"][$radio]) {
                    $dishErr = "Error: Not enough quantity left.";
                }
                else {
                    $_SESSION["cur_ord_dish_quan"][$radio] = $_SESSION["cur_ord_dish_quan"][$radio] - $order_quan;

                    require_once("func/funcOrder.php");
                    addDishToOrder($sel_dish, $order_quan);
                    updateTotalPrice();    

                    header('Location: '.$_SERVER['PHP_SELF']);
                    die();    
                }
            }
        }
    }
    else if ($_POST["Up_Order"]) {
        if (!isset($_POST["orderRadio"])) {
            $orderErr = "Error: Order was not selected.";
        }
        else {
            $order_quan = $_POST["up_quan"];
            $radio = $_POST["orderRadio"];
            $_SESSION["order_price_dish"][$radio] = number_format($_SESSION["order_price"][$radio] * $order_quan, 2);

            for ($i = 0; $i < count($_SESSION["dish_ID"]); $i++) {
                if ($_SESSION["order_dishID"][$radio] == $_SESSION["dish_ID"][$i]) {
                    if ($_SESSION["cur_ord_dish_quan"][$i] + $_SESSION["order_quan_array"][$radio] - $order_quan < 0) {
                        $orderErr = "Error: Not enough quantity left.";
                        break;
                    }
                    else {
                        $_SESSION["cur_ord_dish_quan"][$i] = $_SESSION["cur_ord_dish_quan"][$i] + $_SESSION["order_quan_array"][$radio] - $order_quan;
                        $_SESSION["order_quan_array"][$radio] = $order_quan;
                        header('Location: '.$_SERVER['PHP_SELF']);
                        die();
                    }
                }
            }
        }
    }
    else if ($_POST["Rem_Order"]) {
        // remove selected order from the order summary
        if (!isset($_POST["orderRadio"])) {
            $orderErr = "Error: Order was not selected.";
        }
        else {
            $radio = $_POST["orderRadio"];

            for ($i = 0; $i < count($_SESSION["dish_ID"]); $i++) {
                if ($_SESSION["order_dishID"][$radio] == $_SESSION["dish_ID"][$i]) {
                    $_SESSION["cur_ord_dish_quan"][$i] = $_SESSION["cur_ord_dish_quan"][$i] + $_SESSION["order_quan_array"][$radio];
                    break;
                }
            }

            unset($_SESSION["order_val"][$radio]);
            unset($_SESSION["order_dishID"][$radio]);
            unset($_SESSION["order_name"][$radio]);
            unset($_SESSION["order_cat"][$radio]);
            unset($_SESSION["order_price"][$radio]);
            unset($_SESSION["order_quan_array"][$radio]);
            unset($_SESSION["order_price_dish"][$radio]);

            // reindex all arrays affected
            $_SESSION["order_dishID"] = array_values($_SESSION["order_dishID"]);
            $_SESSION["order_name"] = array_values($_SESSION["order_name"]);
            $_SESSION["order_cat"] = array_values($_SESSION["order_cat"]);
            $_SESSION["order_price"] = array_values($_SESSION["order_price"]);
            $_SESSION["order_quan_array"] = array_values($_SESSION["order_quan_array"]);
            $_SESSION["order_price_dish"] = array_values($_SESSION["order_price_dish"]);
            $_SESSION["order_val"] = array_values($_SESSION["order_val"]);
            // order_val values need to change to store the right indices
            for ($i = 0; $i < count($_SESSION["order_val"]); $i++) {
                $_SESSION["order_val"][$i] = $i;
            }

            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Sel_Order"]) {
        // make an order with the given order summary
        $success = makeOrder($order_res_ID, $order_res_name, $orderErr);

        if ($success) {
            header("Location: orderComplete.php");
            die();    
        }
    }
    else if ($_POST["Clear_Order"]) {
        // remove all orders from the order summary
        require_once("func/funcOrder.php");
        unsetOrder();

        $_SESSION["cur_ord_dish_quan"] = array();
        // get max number of the dish the restaurant is able to make; update it with each item added
        foreach ($_SESSION["dish_av_quan"] as $each_quan) {
            $_SESSION["cur_ord_dish_quan"][] = $each_quan;
        }

        header('Location: '.$_SERVER['PHP_SELF']);
        die();
    }
    else if ($_POST["Go_Back"]) {
        // unset everything and go back to choose page
        require_once("func/funcOrder.php");
        unsetAll();

        header("Location: chooseRes.php");
        die();
    }
}

?>

<div class="title">
    <h1> Order Page </h1>
</div>

<div class="col-md-12 text-center">
    <h4 class="order"> Ordering from: <?php echo $order_res_name; ?> </h4>
</div>

<div class="col-md-12 text-center">
    <h4 class="menu"> Select a Menu </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Menu Name</th>
                </tr>
                <?php foreach ($menu_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="menuRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $menu_name[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            <input type="submit" value="Select this Menu" name="Sel_Menu"><br>
        </form>
        <span class="error"><?php echo $menuErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="dish"> Available Dish </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Dish Name</th>
                    <th>Dish Price</th>
                    <th>Dish Category</th>
                    <th>Ingredients used</th>
                    <th>Quantities available</th>
                </tr>
                <?php foreach ($_SESSION["dish_val"] as $val): ?>
                    <tr>
                        <th><input type="radio" name="dishRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $_SESSION["dish_name"][$val]; ?></th>
                        <th><?php echo $_SESSION["dish_price"][$val]; ?></th>
                        <th><?php echo $_SESSION["dish_cat"][$val]; ?></th>
                        <th><?php echo $_SESSION["dish_ings"][$val]; ?></th>
                        <th><?php if (isset($_SESSION["cur_ord_dish_quan"])) echo $_SESSION["cur_ord_dish_quan"][$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Quantity to order:<br>
            <input type="number" name="dish_quan" min="1"><br>
            <input type="submit" value="Add Dish to Order" name="Sel_Dish"><br>
        </form>
        <span class="error"><?php echo $dishErr;?></span></br>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="dish"> Order Summary </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Dish Name</th>
                    <th>Dish Category</th>
                    <th>Unit Price</th>
                    <th>Quantity to Order</th>
                    <th>Total Price</th>
                </tr>
                <?php foreach ($_SESSION["order_val"] as $val): ?>
                    <tr>
                        <th><input type="radio" name="orderRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $_SESSION["order_name"][$val]; ?></th>
                        <th><?php echo $_SESSION["order_cat"][$val]; ?></th>
                        <th><?php echo $_SESSION["order_price"][$val]; ?></th>
                        <th><?php echo $_SESSION["order_quan_array"][$val]; ?></th>
                        <th><?php echo $_SESSION["order_price_dish"][$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Total Price: <?php echo "$" . $_SESSION["total_price"] ?><br>
            Update quantity:<br>
            <input type="number" name="up_quan" min="1"><br>
            <input type="submit" value="Change quantity of selected dish" name="Up_Order"><br><br>
            <input type="submit" value="Remove the Selected item from Order" name="Rem_Order"><br><br>
            <input type="submit" value="Reset Order" name="Clear_Order"><br><br>
            <input type="submit" value="Confirm Order" name="Sel_Order"><br><br>
        </form>
        <span class="error"><?php echo $orderErr;?></span></br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Choose another restaurant/profile" name="Go_Back"><br>
        </form>
    </div>
</div>

</body>
</html>
