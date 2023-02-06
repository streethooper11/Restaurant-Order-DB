<?php
require_once("config.php");

function createDish($res_ID, $add_name, $add_price, $add_cat, &$new_id) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $res_ID_E = mysqli_real_escape_string($con, $res_ID);
    $add_name_E = mysqli_real_escape_string($con, $add_name);
    $add_price_E = mysqli_real_escape_string($con, $add_price);
    $add_cat_E = mysqli_real_escape_string($con, $add_cat);
    $sql = "INSERT INTO `Dish` (`Dish_Name`, `Price`, `Category`, `RestaurantID`) VALUES ('$add_name_E', '$add_price_E', '$add_cat_E', '$res_ID_E')";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    $new_id = mysqli_insert_id($con);

    mysqli_close($con);
}

?>
