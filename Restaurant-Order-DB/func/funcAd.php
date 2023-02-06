<?php
require_once("config.php");

function addRestaurant($email, $add_name, $add_loc) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $email_E = mysqli_real_escape_string($con, $email);
    $add_name_E = mysqli_real_escape_string($con, $add_name);
    $add_loc_E = mysqli_real_escape_string($con, $add_loc);
    $sql = "INSERT INTO `Restaurant` (`AdminEmail_ID`, `Location`, `Name`) VALUES ('$email_E', '$add_loc_E', '$add_name_E')";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function deleteAdmin($email) {
    // delete admin account
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    // delete restaurants then delete account
    $email_E = mysqli_real_escape_string($con, $email);
    $list = array();
    $find_result = mysqli_query($con, "SELECT * FROM `Restaurant` WHERE `AdminEmail_ID`='$email_E'");
    while ($find_row = mysqli_fetch_array($find_result)) {
        $list[] = $find_row["R_ID"];
    }

    require_once("funcRest.php");
    foreach ($list as $element):
        deleteRestaurant($element);
    endforeach;

    $sql = "DELETE FROM `Account` WHERE `Email_ID`='$email_E'";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
