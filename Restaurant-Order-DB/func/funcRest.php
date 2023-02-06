<?php
require_once("config.php");

function addMenu($res_ID, $add_name) {
    $success = false;
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $res_ID_E = mysqli_real_escape_string($con, $res_ID);
    $add_name_E = mysqli_real_escape_string($con, $add_name);
    $sql = "INSERT INTO `Menu` (`Menu_Name`, `RestaurantID`) VALUES ('$add_name_E', '$res_ID_E')";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function updateRestaurant($res_ID, $up_name, $up_loc) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $res_ID_E = mysqli_real_escape_string($con, $res_ID);
    $up_name_E = mysqli_real_escape_string($con, $up_name);
    $up_loc_E = mysqli_real_escape_string($con, $up_loc);
    $sql = "UPDATE `Restaurant` SET `Name`='$up_name_E', `Location`='$up_loc_E' WHERE `R_ID`='$res_ID_E'";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function deleteRestaurant($res_ID) {
    // delete restaurant
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $res_ID_E = mysqli_real_escape_string($con, $res_ID);
    $list = array();
    $find_result = mysqli_query($con, "SELECT * FROM `Restaurant_has_Ingredient` NATURAL JOIN `Ingredient` WHERE `RestaurantID`='$res_ID_E'");
    while ($find_row = mysqli_fetch_array($find_result)) {
        $list[] = $find_row["IngredientID"];
    }

    require_once("funcIng.php");
    foreach ($list as $element):
        deleteIng($element);
    endforeach;

    $sql = "DELETE FROM `Restaurant` WHERE `R_ID`='$res_ID_E'";

    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
