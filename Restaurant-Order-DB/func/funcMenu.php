<?php
require_once("config.php");

function removeDish($menu_ID, $find_DID) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $menu_ID_E = mysqli_real_escape_string($con, $menu_ID);
    $find_DID_E = mysqli_real_escape_string($con, $find_DID);
    $sql = "DELETE FROM `Has` WHERE `Menu_ID`='$menu_ID_E' AND `Dish_ID`='$find_DID_E'";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);    
}

function addDish($menu_ID, $add_DID) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $menu_ID_E = mysqli_real_escape_string($con, $menu_ID);
    $add_DID_E = mysqli_real_escape_string($con, $add_DID);
    $sql = "INSERT INTO `Has` (`Menu_ID`, `Dish_ID`) VALUES ('$menu_ID_E', '$add_DID_E')";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function updateMenu($menu_ID, $up_name) {
    $success = false;
    // update menu name
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $menu_ID_E = mysqli_real_escape_string($con, $menu_ID);
    $up_name_E = mysqli_real_escape_string($con, $up_name);
    $sql = "UPDATE `Menu` SET `Menu_Name`='$up_name_E' WHERE `Menu_ID`='$menu_ID_E'";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    $success = true;

    mysqli_close($con);

    return $success;
}

function deleteMenu($menu_ID) {
    // delete this menu; do not delete dishes under the menu; delete just the connections
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $menu_ID_E = mysqli_real_escape_string($con, $menu_ID);
    $sql = "DELETE FROM `Menu` WHERE `Menu_ID`='$menu_ID_E'";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
