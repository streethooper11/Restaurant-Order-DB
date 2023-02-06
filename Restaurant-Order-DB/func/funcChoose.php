<?php

session_start();

require_once("config.php");

function searchDish($searchDish) {
    unsetAll();

    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $search_D = mysqli_real_escape_string($con, $searchDish);
    $find_result = mysqli_query($con, "SELECT * FROM (`Dish` JOIN `Restaurant` ON `RestaurantID`=`R_ID`) WHERE `Dish_Name` LIKE '%$search_D%'");
    if (mysqli_num_rows($find_result) > 0) {
        // get all dishes found
        $_SESSION["searchRes_val"] = array();
        $_SESSION["searchRes_ID"] = array();
        $_SESSION["searchRes_N"] = array();
        $_SESSION["searchRes_L"] = array();
        $_SESSION["searchRes_D"] = array();
        $_SESSION["searchRes_I"] = array();

        $i = 0;
        while ($find_row = mysqli_fetch_array($find_result)) {
            $inserted_dish_E = mysqli_real_escape_string($con, $find_row["Dish_ID"]);
            $find_dish_ing = mysqli_query($con, "SELECT * FROM `Needs` WHERE `Dish_ID`='$inserted_dish_E'");
            // only add dishes with at least one ingredient registered
            if (mysqli_num_rows($find_dish_ing) > 0) {
                $_SESSION["searchRes_val"][] = $i;
                $i++;
                $_SESSION["searchRes_ID"][] = $find_row["R_ID"];
                $_SESSION["searchRes_N"][] = $find_row["Name"];
                $_SESSION["searchRes_L"][] = $find_row["Location"];
                $_SESSION["searchRes_D"][] = $find_row["Dish_Name"];
                $string = "";

                $find_ings = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Needs`) WHERE `Dish_ID`='$inserted_dish_E'");

                while ($find_ing_row = mysqli_fetch_array($find_ings)) {
                    $string .= $find_ing_row["IngredientName"] . ",";
                }

                $_SESSION["searchRes_I"][] = $string;
            }
        }
    }

    mysqli_close($con);
}

function searchIng($searchIng) {
    unsetAll();

    // Create connection
    $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $search_I = mysqli_real_escape_string($con, $searchIng);
    $find_result = mysqli_query($con, "SELECT *
                                        FROM (`Ingredient` NATURAL JOIN `Needs` NATURAL JOIN `Dish`)
                                        JOIN `Restaurant` ON `RestaurantID`=`R_ID`
                                        WHERE `IngredientName`='$search_I'");
    if (mysqli_num_rows($find_result) > 0) {
        // get all dishes found
        $_SESSION["searchRes_val"] = array();
        $_SESSION["searchRes_ID"] = array();
        $_SESSION["searchRes_N"] = array();
        $_SESSION["searchRes_L"] = array();
        $_SESSION["searchRes_D"] = array();
        $_SESSION["searchRes_I"] = array();

        $i = 0;
        while ($find_row = mysqli_fetch_array($find_result)) {
            $_SESSION["searchRes_val"][] = $i;
            $i++;
            $_SESSION["searchRes_ID"][] = $find_row["R_ID"];
            $_SESSION["searchRes_N"][] = $find_row["Name"];
            $_SESSION["searchRes_L"][] = $find_row["Location"];
            $_SESSION["searchRes_D"][] = $find_row["Dish_Name"];
            $string = "";
            $inserted_dish = mysqli_real_escape_string($con, $find_row["Dish_ID"]);

            $find_ings = mysqli_query($con, "SELECT * FROM (`Ingredient` NATURAL JOIN `Needs`) WHERE `Dish_ID`='$inserted_dish'");

            while ($find_ing_row = mysqli_fetch_array($find_ings)) {
                $string .= $find_ing_row["IngredientName"] . ",";
            }

            $_SESSION["searchRes_I"][] = $string;
        }
    }

    mysqli_close($con);
}

function unsetAll() {
    unset($_SESSION["searchRes_val"]);
    unset($_SESSION["searchRes_ID"]);
    unset($_SESSION["searchRes_N"]);
    unset($_SESSION["searchRes_L"]);
    unset($_SESSION["searchRes_D"]);
    unset($_SESSION["searchRes_I"]);
}

?>
