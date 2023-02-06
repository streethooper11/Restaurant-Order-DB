<?php

session_start();

require_once("config.php");

function selectMenu($sel_menu) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $sel_menu_E = mysqli_real_escape_string($con, $sel_menu);
    $man_result = mysqli_query($con, "SELECT * FROM `Menu` WHERE `Menu_ID`='$sel_menu_E'");
    if (mysqli_num_rows($man_result) == 0) {
        echo "Something weird happened, as this should not happen normally.";
    }
    else {
        // update dishes depending on allergies
        $_SESSION["dish_av_quan"] = array();

        $find_result = mysqli_query($con, "SELECT * FROM (`Dish` NATURAL JOIN `Has`) WHERE `Menu_ID`='$sel_menu_E'");

        $i = 0;
        while ($find_row = mysqli_fetch_array($find_result)) {
            $cur_dishID = mysqli_real_escape_string($con, $find_row["Dish_ID"]);
            $cur_dishID_E = mysqli_real_escape_string($con, $cur_dishID);

            $find_dish_ing = mysqli_query($con, "SELECT * FROM `Needs` WHERE `Dish_ID`='$cur_dishID_E'");
            // only add dishes with at least one ingredient registered
            if (mysqli_num_rows($find_dish_ing) > 0) {
                $email_E = mysqli_real_escape_string($con, $_SESSION["Email"]);
                $cur_prof_E = mysqli_real_escape_string($con, $_SESSION["curProfName"]);

                // see if the current profile has an allergy that exists in the dish
                $find_allergy = mysqli_query($con, "SELECT * FROM `Needs` AS `N` WHERE `N`.`Dish_ID`='$cur_dishID_E'
                                                    AND EXISTS
                                                    (SELECT * FROM `Allergy` AS `A`, `Ingredient` AS `I`
                                                        WHERE `I`.`IngredientID`=`N`.`IngredientID`
                                                        AND `A`.`Email_ID`='$email_E' AND `A`.`Name`='$cur_prof_E'
                                                        AND (`A`.`Allergy_Name`=`I`.`IngredientName` OR `A`.`Allergy_Name`=`I`.`Type`))");

                if (mysqli_num_rows($find_allergy) == 0) {
                    // only add the dish if the dish does not have an allergic ingredient to the profile
                    $_SESSION["dish_val"][] = $i;
                    $i++;
                    $_SESSION["dish_ID"][] = $cur_dishID;
                    $_SESSION["dish_name"][] = $find_row["Dish_Name"];
                    $_SESSION["dish_price"][] = $find_row["Price"];
                    $_SESSION["dish_cat"][] = $find_row["Category"];
                    $string = "";
                    $inserted_dish_E = mysqli_real_escape_string($con, end($_SESSION["dish_ID"]));
        
                    $ing_quan = -1;
                    $find_ings = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Restaurant_has_Ingredient` NATURAL JOIN `Needs`) WHERE `Dish_ID`='$inserted_dish_E'");
        
                    while ($find_ing_row = mysqli_fetch_array($find_ings)) {
                        $string .= $find_ing_row["IngredientName"] . ",";
        
                        // find the maximum number of dishes that can be made, depending on ingredients left
                        if ($ing_quan == -1) { // first update
                            $ing_quan = intdiv($find_ing_row["Quantity"], $find_ing_row["Amount_Needed"]);
                        }
                        else {
                            $ing_quan2 = intdiv($find_ing_row["Quantity"], $find_ing_row["Amount_Needed"]);
                            if ($ing_quan2 < $ing_quan) {
                                $ing_quan = $ing_quan2; // choose a smaller quantity
                            }
                        }
                    }
        
                    $_SESSION["dish_ings"][] = $string;
                    $_SESSION["dish_av_quan"][] = $ing_quan;
                }
    
            }
        }
    }

    mysqli_close($con);
}

function addDishToOrder($sel_dish, $order_quan) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $sel_dish_E = mysqli_real_escape_string($con, $sel_dish);
    $man_result = mysqli_query($con, "SELECT * FROM `Dish` WHERE `Dish_ID`='$sel_dish_E'");
    if (mysqli_num_rows($man_result) == 0) {
        echo "Something weird happened, as this should not happen normally.";
    }
    else {
        // add to order summary
        while ($find_row = mysqli_fetch_array($man_result)) {
            
            $_SESSION["order_val"][] = count($_SESSION["order_val"]);
            $_SESSION["order_dishID"][] = $sel_dish;
            $_SESSION["order_name"][] = $find_row["Dish_Name"];
            $_SESSION["order_cat"][] = $find_row["Category"];
            $_SESSION["order_price"][] = $find_row["Price"];
            $_SESSION["order_price_dish"][] = number_format($find_row["Price"] * $order_quan, 2);
            $_SESSION["order_quan_array"][] = $order_quan;
        }
    }

    mysqli_close($con);
}

function makeOrder($order_res_ID, $order_res_name, &$orderErr) {
    $success = false;

    $email = $_SESSION["Email"];
    $order_prof_name = $_SESSION["curProfName"];
    $order_dishID = $_SESSION["order_dishID"];
    $order_quantities = $_SESSION["order_quan_array"];
    $total_price = $_SESSION["total_price"];

    if (empty($order_dishID)) {
        $orderErr = "Error: Nothing in the order summary";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // create order
        $order_time_E = mysqli_real_escape_string($con, date("Y-m-d H:i:s"));
        $email_E = mysqli_real_escape_string($con, $email);
        $order_prof_name_E = mysqli_real_escape_string($con, $order_prof_name);
        $order_res_ID_E = mysqli_real_escape_string($con, $order_res_ID);
        $total_price_E = mysqli_real_escape_string($con, $total_price);

        $sql = "INSERT INTO `Order` (`Order_Time`, `Total_Price`, `Email_ID`, `Profile_Name`, `RestaurantID`)
                VALUES ('$order_time_E', '$total_price_E', '$email_E', '$order_prof_name_E', '$order_res_ID_E')";
        if (!mysqli_query($con,$sql)){
            die('Error: ' . mysqli_error($con));
        }

        $order_ID_E = mysqli_real_escape_string($con, mysqli_insert_id($con)); // retrieved inserted order ID

        for ($i = 0; $i < count($order_dishID); $i++) {
            // update order database
            $cur_dish_ID = $order_dishID[$i];
            $cur_dish_quan = $order_quantities[$i];

            $cur_dish_ID_E = mysqli_real_escape_string($con, $cur_dish_ID);
            $cur_dish_quan_E = mysqli_real_escape_string($con, $cur_dish_quan);
            $find_result = mysqli_query($con, "SELECT * FROM `Consists_of` WHERE `Order_ID`='$order_ID_E' AND `Dish_ID`='$cur_dish_ID_E'");

            if (mysqli_num_rows($find_result) == 0) {
                $sql = "INSERT INTO `Consists_of` (`Order_ID`, `Dish_ID`, `Quantity`) VALUES ('$order_ID_E', '$cur_dish_ID_E', '$cur_dish_quan_E')";
                if (!mysqli_query($con,$sql)) {
                    die('Error: ' . mysqli_error($con));
                }
            }
            else {
                while ($find_row = mysqli_fetch_array($find_result)) {
                    $prev_quan = $find_row["Quantity"];
                }

                $prev_quan += $cur_dish_quan;
                $prev_quan_E = mysqli_real_escape_string($con, $prev_quan);
                $sql = "UPDATE `Consists_of` SET `Quantity`='$prev_quan_E' WHERE `Dish_ID`='$cur_dish_ID_E'";
                if (!mysqli_query($con,$sql)){
                    die('Error: ' . mysqli_error($con));
                }
            }

            // update ingredient quantities
            $find_result = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Restaurant_has_Ingredient` NATURAL JOIN `Needs`)
                                                WHERE Dish_ID='$cur_dish_ID_E'");
            if (mysqli_num_rows($find_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                while ($find_row = mysqli_fetch_array($find_result)) {
                    $ing_ID = $find_row["IngredientID"];
                    $ing_used = $find_row["Amount_Needed"] * $cur_dish_quan;

                    $cur_ing_no = $find_row["Quantity"];
                    $cur_ing_new = $cur_ing_no - $ing_used;

                    $ing_ID_E = mysqli_real_escape_string($con, $ing_ID);
                    $cur_ing_new_E = mysqli_real_escape_string($con, $cur_ing_new);

                    $sql = "UPDATE `Restaurant_has_Ingredient` SET `Quantity`='$cur_ing_new_E' WHERE `IngredientID`='$ing_ID_E'";
                    if (!mysqli_query($con,$sql)){
                        die('Error: ' . mysqli_error($con));
                    }
                }
            }
        }

        $order_res_name_E = mysqli_real_escape_string($con, $order_res_name);
        // add to order history
        $sql = "INSERT INTO `History` (`Order_Place`, `Total_Price`, `UserEmail_ID`, `Order_ID`) VALUES ('$order_res_name_E', '$total_price_E', '$email_E', '$order_ID_E')";
        if (!mysqli_query($con,$sql)){
            die('Error: ' . mysqli_error($con));
        }
        
        $success = true;

        mysqli_close($con);  
    }

    return $success;
}

function updateTotalPrice() {
    $_SESSION["total_price"] = 0;

    foreach ($_SESSION["order_price_dish"] as $key => $value):
        $_SESSION["total_price"] += $value;
    endforeach;

    $_SESSION["total_price"] = number_format($_SESSION["total_price"], 2);
}

function unsetDish() {
    unset($_SESSION["dish_val"]);
    unset($_SESSION["dish_ID"]);
    unset($_SESSION["dish_name"]);
    unset($_SESSION["dish_price"]);
    unset($_SESSION["dish_cat"]);
    unset($_SESSION["dish_ings"]);
}

function unsetOrder() {
    unset($_SESSION["order_val"]);
    unset($_SESSION["order_dishID"]);
    unset($_SESSION["order_name"]);
    unset($_SESSION["order_cat"]);
    unset($_SESSION["order_price"]);
    unset($_SESSION["order_quan_array"]);
    unset($_SESSION["order_price_dish"]);
}

function unsetAll() {
    unsetDish();
    unsetOrder();
    unset($_SESSION["cur_ord_dish_quan"]);
    unset($_SESSION["total_price"]);
}

?>

