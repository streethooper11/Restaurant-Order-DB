<?php
require_once("config.php");

function addIng($res_ID, $new_name, $new_type, $new_quan, &$addErr) {
    $success = false;

    if (empty($new_name)) {
        $addErr = "Ingredient name cannot be empty";
    }
    else if (empty($new_quan) || $new_quan < 0) {
        $addErr = "Please enter a non-negative number for the new quantity";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $res_ID_E = mysqli_real_escape_string($con, $res_ID);
        $new_name_E = mysqli_real_escape_string($con, $new_name);
        $addSearch = "SELECT * FROM (`Restaurant_has_Ingredient` NATURAL JOIN `Ingredient`)
                        WHERE `RestaurantID`='$res_ID_E' AND `IngredientName`='$new_name_E'";
        if (mysqli_num_rows($addSearch) > 0) {
            $addErr = "Error: The ingredient exists in the restaurant";
        }
        else {
            $new_type_E = mysqli_real_escape_string($con, $new_type);
            $new_quan_E = mysqli_real_escape_string($con, $new_quan);
            $sql = "INSERT INTO `Ingredient` (`Type`, `IngredientName`) VALUES ('$new_type_E', '$new_name_E')";
            if (!mysqli_query($con,$sql)) {
                die('Error: ' . mysqli_error($con));
            }                
            $id_E = mysqli_real_escape_string($con, mysqli_insert_id($con)); // get inserted ID

            $sql = "INSERT INTO `Restaurant_has_Ingredient` (`IngredientID`, `RestaurantID`, `Quantity`) VALUES ('$id_E', '$res_ID_E', '$new_quan_E')";
            if (!mysqli_query($con,$sql)) {
                die('Error: ' . mysqli_error($con));
            }

            $success = true;
        }

        mysqli_close($con);    
    }

    return $success;
}

function updateIng($res_ID, $find_IID, $up_quan, &$manageErr) {
    $success = false;
    // update ingredient quantity
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    if (empty($up_quan) || $up_quan < 0) {
        $manageErr = "Please enter a non-negative number for the new quantity";
    }
    else {
        $res_ID_E = mysqli_real_escape_string($con, $res_ID);
        $find_IID_E = mysqli_real_escape_string($con, $find_IID);
        $up_quan_E = mysqli_real_escape_string($con, $up_quan);
        $sql = "UPDATE `Restaurant_has_Ingredient` SET `Quantity`='$up_quan_E' WHERE `RestaurantID`='$res_ID_E' AND `IngredientID`='$find_IID_E'";
        if (!mysqli_query($con,$sql)){
            die('Error: ' . mysqli_error($con));
        }

        $success = true;
    }                        

    mysqli_close($con);

    return $success;
}

function deleteIng($find_IID) {
    $message = "";
    
    // delete ingredient
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $find_IID_E = mysqli_real_escape_string($con, $find_IID);
    $find_result = mysqli_query($con, "SELECT * FROM `Needs` WHERE `IngredientID`='$find_IID_E'");

    $num_rows = mysqli_num_rows($find_result);
    if ($num_rows > 0) {
        $message = "Error: Ingredient exists in some of the dishes.<br>Please delete/modify those dishes first";
    }
    else {
        $sql = "DELETE FROM `Ingredient` WHERE `IngredientID`='$find_IID_E'";
        if (!mysqli_query($con,$sql)) {
            die('Error: ' . mysqli_error($con));
        }
    }

    mysqli_close($con);

    return $message;
}

?>
