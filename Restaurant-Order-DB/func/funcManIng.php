<?php
require_once("config.php");

function updateIng($ing_ID, $up_quan, &$manageErr) {
    $success = false;
    // update ingredient quantity
    if (empty($up_quan) || $up_quan < 1) {
        $manageErr = "Error: Enter a valid quantity for the ingredient";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $ing_ID_E = mysqli_real_escape_string($con, $ing_ID);
        $up_quan_E = mysqli_real_escape_string($con, $up_quan);
        $sql = "UPDATE `Needs` SET `Amount_Needed`='$up_quan_E' WHERE `IngredientID`='$ing_ID_E'";
        if (!mysqli_query($con,$sql)) {
            die('Error: ' . mysqli_error($con));
        }

        $success= true;

        mysqli_close($con);
    }

    return $success;
}

// remove ingredient from the dish
function removeIng($dish_ID, $ing_ID) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
    $ing_ID_E = mysqli_real_escape_string($con, $ing_ID);
    $sql = "DELETE FROM `Needs` WHERE `Dish_ID`='$dish_ID_E' AND `IngredientID`='$ing_ID_E'";
    if (!mysqli_query($con,$sql)) {
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function addIng($dish_ID, $ing_ID, $add_quan, &$addErr) {
    $success = false;
    // update ingredient quantity
    if (empty($add_quan) || $add_quan < 1) {
        $addErr = "Error: Enter a valid quantity for the ingredient";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $dish_ID_E = mysqli_real_escape_string($con, $dish_ID);
        $ing_ID_E = mysqli_real_escape_string($con, $ing_ID);
        $add_quan_E = mysqli_real_escape_string($con, $add_quan);
        $sql = "INSERT INTO `Needs` (`Dish_ID`, `IngredientID`, `Amount_Needed`) VALUES ('$dish_ID_E', '$ing_ID_E', '$add_quan_E')";
        if (!mysqli_query($con,$sql)) {
            die('Error: ' . mysqli_error($con));
        }

        $success= true;

        mysqli_close($con);
    }

    return $success;
}

?>
