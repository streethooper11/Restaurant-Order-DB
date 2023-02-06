<?php
require_once("config.php");

function addProfile($email, $add_name, $add_all, &$addErr) {
    $success = false;

    if (empty($add_name)) {
        $addErr = "Error: Profile Name cannot be empty";
    }
    else {
        // Create connection
        $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        
        // Check connection
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $email_E = mysqli_real_escape_string($con, $email);
        $add_name_E = mysqli_real_escape_string($con, $add_name);
        $addSearch = mysqli_query($con, "SELECT * FROM `Profile` WHERE `Email_ID`='$email_E' AND `Name`='$add_name_E'");
        if (mysqli_num_rows($addSearch) > 0) {
            $addErr = "Error: This profile name already exists";
        }
        else {
            $sql = "INSERT INTO `Profile` (`Email_ID`, `Name`) VALUES ('$email_E', '$add_name_E')";
            if (!mysqli_query($con,$sql)){
                die('Error: ' . mysqli_error($con));
            }

            // add allergies here
            $alls = array();
            $alls = explode(',', $add_all);
            foreach ($alls as $allerg):
                if (!empty($allerg)) {
                    $allerg_E = mysqli_real_escape_string($con, $allerg);
                    $sql = "INSERT INTO `Allergy` (`Name`, `Email_ID`, `Allergy_Name`) Values ('$add_name_E', '$email_E', '$allerg_E')";
                    if (!mysqli_query($con,$sql)){
                        die('Error: ' . mysqli_error($con));
                    }
                }
            endforeach;

            $success = true;
        }

        mysqli_close($con);
    }
    
    return $success;
}

function deleteUser($email) {
    // delete user account
    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $email_E = mysqli_real_escape_string($con, $email);
    $sql = "DELETE FROM `Account` WHERE `Email_ID`='$email_E'";
    if (!mysqli_query($con,$sql)){
        die('Error: ' . mysqli_error($con));
    }

    mysqli_close($con);
}

?>
