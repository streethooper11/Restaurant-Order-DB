<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 0)) {
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="css/styles.css">
        <style>
        td, th {
            border: 1px solid #000000;
            text-align: left;
            padding: 8px;
        }
    </style>
    </head>
    <body>

<?php
require_once("func/config.php");

$addErr = $upErr = "";

$hist_order_ID = array();
$hist_val = array();
$hist_Prof = array();
$hist_Place = array();
$hist_Time = array();
$hist_Price = array();

$rev_val = array();
$rev_order_ID = array();
$rev_Place = array();
$rev_Time = array();
$rev_Rate = array();
$rev_Comment = array();
$rev_Reply = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email_E = mysqli_real_escape_string($con, $_SESSION["Email"]);
$find_result = mysqli_query($con, "SELECT * FROM `History` NATURAL JOIN `Order` WHERE `UserEmail_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $hist_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $hist_order_ID[] = $find_row["Order_ID"];
    $hist_Prof[] = $find_row["Profile_Name"];
    $hist_Place[] = $find_row["Order_Place"];
    $hist_Time[] = $find_row["Order_Time"];
    $hist_Price[] = $find_row["Total_Price"];
}

$find_result = mysqli_query($con, "SELECT * FROM (`History` NATURAL JOIN `Review`) NATURAL JOIN `Order` WHERE `UserEmail_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $rev_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $rev_order_ID[] = $find_row["Order_ID"];
    $rev_Place[] = $find_row["Order_Place"];
    $rev_Time[] = $find_row["Date_Time"];
    $rev_Rate[] = $find_row["Rating"];
    $rev_Comment[] = $find_row["Comment"];
    $rev_Reply[] = $find_row["Reply"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Add_Review"]) {
        if (!isset($_POST["histRadio"])) {
            $addErr = "Error: Order was not selected";
        }
        else if (empty($_POST["add_rate"]) && empty($_POST["add_rev"])) {
            $addErr = "Error: Both rating and review are empty";
        }
        else {
            $radio = $_POST["histRadio"];

            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $get_order_ID_E = mysqli_real_escape_string($con, $hist_order_ID[$radio]);
            $find_result = mysqli_query($con, "SELECT * FROM `Review` WHERE `Order_ID`='$get_order_ID_E' AND `UserEmail_ID`='$email_E'");
            if (mysqli_num_rows($find_result) > 0) {
                $addErr = "Error: This review already exists. Please edit instead of adding a review";
            }
            else {
                $find_result = mysqli_query($con, "SELECT * FROM `History` NATURAL JOIN `Order` WHERE `Order_ID`='$get_order_ID_E'");
                if (mysqli_num_rows($find_result) == 0) {
                    echo "Something weird happened, as this should not happen normally.";
                }
                else {
                    while ($find_row = mysqli_fetch_array($find_result)) {
                        $review_time_E = mysqli_real_escape_string($con, date("Y-m-d H:i:s"));
                        $addErr = "Error while adding rating";
    
                        if (!empty($_POST["add_rate"])) {
                            $new_rate_E = mysqli_real_escape_string($con, $_POST["add_rate"]);
                            $sql = "INSERT INTO `Review` (`Rating`, `Comment`, `UserEmail_ID`, `Date_Time`, `Order_ID`) VALUES ('$new_rate_E', NULL, '$email_E', '$review_time_E', '$get_order_ID_E')";
                            if (!mysqli_query($con,$sql)) {
                                die('Error: ' . mysqli_error($con));
                            }
    
                            if (!empty($_POST["add_rev"])) {
                                $new_rev_E = mysqli_real_escape_string($con, $_POST["add_rev"]);
                                $sql = "UPDATE `Review` SET `Comment`='$new_rev_E' WHERE `Order_ID`='$get_order_ID_E' AND `UserEmail_ID`='$email_E'";
                                if (!mysqli_query($con,$sql)) {
                                    die('Error: ' . mysqli_error($con));
                                }
                            }
                        }
                        else if (!empty($_POST["add_rev"])) {
                            $new_rev_E = mysqli_real_escape_string($con, $_POST["add_rev"]);
    
                            $sql = "INSERT INTO `Review` (`Rating`, `Comment`, `UserEmail_ID`, `Date_Time`, `Order_ID`) VALUES (NULL, '$new_rev_E', '$email_E', '$review_time_E', '$get_order_ID_E')";
                            if (!mysqli_query($con,$sql)) {
                                die('Error: ' . mysqli_error($con));
                            }
                        }    
                    }
                }
            
                header('Location: '.$_SERVER['PHP_SELF']);
                die();    
            }

            mysqli_close($con);    
        }
    }
    else if ($_POST["Up_Review"]) {
        if (!isset($_POST["revRadio"])) {
            $upErr = "Error: Order was not selected";
        }
        else if (empty($_POST["up_rate"]) && empty($_POST["up_rev"])) {
            $upErr = "Error: Both rating and review are empty";
        }
        else {
            $radio = $_POST["revRadio"];

            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $get_order_ID_E = mysqli_real_escape_string($con, $rev_order_ID[$radio]);
            $find_result = mysqli_query($con, "SELECT * FROM `History` NATURAL JOIN `Order` WHERE `Order_ID`='$get_order_ID_E'");
            if (mysqli_num_rows($find_result) == 0) {
                echo "Something weird happened, as this should not happen normally.";
            }
            else {
                while ($find_row = mysqli_fetch_array($find_result)) {
                    if (!empty($_POST["up_rate"])) {
                        $new_rate_E = mysqli_real_escape_string($con, $_POST["up_rate"]);
                        $sql = "UPDATE `Review` SET `Rating`='$new_rate_E' WHERE `Order_ID`='$get_order_ID_E' AND `UserEmail_ID`='$email_E'";
                        if (!mysqli_query($con,$sql)) {
                            die('Error: ' . mysqli_error($con));
                        }
                    }
                    
                    if (!empty($_POST["up_rev"])) {
                        $new_rev_E = mysqli_real_escape_string($con, $_POST["up_rev"]);

                        $sql = "UPDATE `Review` SET `Comment`='$new_rev_E' WHERE `Order_ID`='$get_order_ID_E' AND `UserEmail_ID`='$email_E'";
                        if (!mysqli_query($con,$sql)) {
                            die('Error: ' . mysqli_error($con));
                        }
                    }    
                }
            }

            header('Location: '.$_SERVER['PHP_SELF']);
            die();    

            mysqli_close($con);
        }
    }
}

?>

<div class="title">
    <h1> Order History </h1>
</div>

<div class="col-md-12 text-center">
    <h4 class="order"> Your Order History </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Order ID</th>
                    <th>Ordered Profile</th>
                    <th>Ordered From</th>
                    <th>Ordered Date</th>
                    <th>Total price</th>
                </tr>
                <?php foreach ($hist_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="histRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $hist_order_ID[$val]; ?></th>
                        <th><?php echo $hist_Prof[$val]; ?></th>
                        <th><?php echo $hist_Place[$val]; ?></th>
                        <th><?php echo $hist_Time[$val]; ?></th>
                        <th><?php echo $hist_Price[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Give a rating:<input type="number" name="add_rate" min="0" max="5"><br>
            Give a review:<br>
            <textarea name="add_rev" rows="5" cols="50"></textarea><br>
            <input type="submit" value="Add review" name="Add_Review"><br>
            <span class="error"><?php echo $addErr;?></span><br>
        </form>
    </div>
</div>

<div class="col-md-12 text-center">
    <h4 class="order"> Your Reviews </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Order ID</th>
                    <th>Restaurant</th>
                    <th>Reviewed Date</th>
                    <th>Your Rating</th>
                    <th>Your Comment</th>
                    <th>Owner's Reply</th>
                </tr>
                <?php foreach ($rev_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="revRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $rev_order_ID[$val]; ?></th>
                        <th><?php echo $rev_Place[$val]; ?></th>
                        <th><?php echo $rev_Time[$val]; ?></th>
                        <th><?php echo $rev_Rate[$val]; ?></th>
                        <th style="width:150px"><?php echo $rev_Comment[$val]; ?></th>
                        <th style="width:150px"><?php echo $rev_Reply[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Edit a rating:<input type="number" name="up_rate" min="0" max="5"><br>
            Edit a review:<br>
            <textarea name="up_rev" rows="5" cols="50"></textarea><br>
            <input type="submit" value="Update review" name="Up_Review"><br>
            <span class="error"><?php echo $upErr;?></span><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="user.php">
            <input type="submit" value="Go back to user page" />
        </form>
    </div>
</div>

</body>
</html>
