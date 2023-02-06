<?php
require_once("config.php");

function addAllergy($email, $profile_name, $allerg, &$addErr) {
    $success = false;
    if (empty($allerg)) {
        $addErr = "Error: Allergy name is blank";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $email_E = mysqli_real_escape_string($con, $email);
        $profile_name_E = mysqli_real_escape_string($con, $profile_name);
        $allerg_E = mysqli_real_escape_string($con, $allerg);
        $addSearch = mysqli_query($con, "SELECT * FROM `Allergy` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E' AND `Allergy_Name`='$allerg_E'");
        if (mysqli_num_rows($addSearch) > 0) {
            $addErr = "Error: This allergy already exists in the profile";
        }
        else {
            $sql = "INSERT INTO `Allergy` (`Name`, `Email_ID`, `Allergy_Name`) Values ('$profile_name_E', '$email_E', '$allerg_E')";
            if (!mysqli_query($con,$sql)){
                die('Error: ' . mysqli_error($con));
            }
    
            $success = true;                
        }

        mysqli_close($con);
    }

    return $success;
}

function deleteAllergy($email, $profile_name, $find_all) {
    // delete admin account
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $email_E = mysqli_real_escape_string($con, $email);
    $profile_name_E = mysqli_real_escape_string($con, $profile_name);
    $find_all_E = mysqli_real_escape_string($con, $find_all);

    $sql = "DELETE FROM `Allergy` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E' AND `Allergy_Name`='$find_all_E'";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

function updateProfile($email, $profile_name, $up_name, &$upErr) {
    $success = false;

    if (empty($up_name)) {
        $upErr = "Error: Profile Name cannot be empty";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $email_E = mysqli_real_escape_string($con, $email);
        $up_name_E = mysqli_real_escape_string($con, $up_name);
        $upSearch = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E' AND `Name`='$up_name_E'");
        if (mysqli_num_rows($upSearch) > 0) {
            $upErr = "Error: This profile name already exists";
        }
        else {
            $profile_name_E = mysqli_real_escape_string($con, $profile_name);
            $sql = "UPDATE `Profile` SET `Name`='$up_name_E' WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E'";
            if (!mysqli_query($con,$sql)){
                die('Error: ' . mysqli_error($con));
            }

            $success = true;
        }

        mysqli_close($con);
    }
    return $success;
}

function deleteProfile($email, $profile_name) {
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $email_E = mysqli_real_escape_string($con, $email);
    $profile_name_E = mysqli_real_escape_string($con, $profile_name);
    $sql = "DELETE FROM `Profile` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E'";

    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
