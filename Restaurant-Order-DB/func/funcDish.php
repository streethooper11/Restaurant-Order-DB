<?php
require_once("config.php");

function updateDish($dish_ID, $up_name, $up_price, $up_cat, &$upErr) {
    $success = false;
    // update dish info
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    if (empty($up_name)) {
        $upErr = "Dish name cannot be empty";
    }
    else {
        $dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
        $up_name_E = mysqli_real_escape_string($con, $up_name);
        $up_price_E = mysqli_real_escape_string($con, $up_price);
        $up_cat_E = mysqli_real_escape_string($con, $up_cat);
    
        $sql = "UPDATE `Dish` SET `Dish_Name`='$up_name_E', `Price`='$up_price_E', `Category`='$up_cat_E'
                WHERE `Dish_ID`='$dish_ID_E'";
        if (!mysqli_query($con,$sql)) {
            die('Error: ' . mysqli_error($con));
        }

        $success = true;
    }

    mysqli_close($con);

    return $success;
}

function deleteDish($dish_ID) {
    // delete dish
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
    $sql = "DELETE FROM `Dish` WHERE `Dish_ID`='$dish_ID_E'";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
