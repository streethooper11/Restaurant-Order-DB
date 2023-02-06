<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 1)) {
    header("Location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin's review Page</title>
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

$upErr = "";

$rev_val = array();
$rev_order_ID = array();
$rev_Place = array();
$rev_Time = array();
$rev_CusE = array();
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
$find_result = mysqli_query($con, "SELECT * FROM (`History` NATURAL JOIN `Review` NATURAL JOIN `Order`),
                                    `Restaurant` AS `R` WHERE `RestaurantID`=`R`.`R_ID` AND `R`.`AdminEmail_ID`='$email_E'");
$num_rows = mysqli_num_rows($find_result);

for ($i = 0; $i < $num_rows; $i++) {
    $rev_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_result)) {
    $rev_order_ID[] = $find_row["Order_ID"];
    $rev_Place[] = $find_row["Order_Place"];
    $rev_Time[] = $find_row["Date_Time"];
    $rev_CusE[] = $find_row["UserEmail_ID"];
    $rev_Rate[] = $find_row["Rating"];
    $rev_Comment[] = $find_row["Comment"];
    $rev_Reply[] = $find_row["Reply"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Up_Reply"]) {
        if (!isset($_POST["revRadio"])) {
            $upErr = "Error: Order was not selected";
        }
        else {
            $radio = $_POST["revRadio"];

            // Create connection
            $con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    
            // Check connection
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            }

            $new_rev_E = mysqli_real_escape_string($con, $_POST["up_rev"]);
            $get_order_ID_E = mysqli_real_escape_string($con, $rev_order_ID[$radio]);
            $get_customer_email_E = mysqli_real_escape_string($con, $rev_CusE[$radio]);

            $sql = "UPDATE `Review` SET `Reply`='$new_rev_E', `AdminEmail_ID`='$email_E' WHERE `Order_ID`='$get_order_ID_E' AND `UserEmail_ID`='$get_customer_email_E'";
            if (!mysqli_query($con,$sql)) {
                die('Error: ' . mysqli_error($con));
            }

            header('Location: '.$_SERVER['PHP_SELF']);
            die();    

            mysqli_close($con);
        }
    }
}

?>

    <div class="title">
        <h1> Admin's review Page </h1>
    </div>

<div class="col-md-12 text-center">
    <h4 class="order"> Reviews from your restaurants </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Restaurant</th>
                    <th>Reviewed Date</th>
                    <th>Customer's Email</th>
                    <th>Customer's Rating</th>
                    <th>Customer's Comment</th>
                    <th>Your Reply</th>
                </tr>
                <?php foreach ($rev_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="revRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $rev_Place[$val]; ?></th>
                        <th><?php echo $rev_Time[$val]; ?></th>
                        <th><?php echo $rev_CusE[$val]; ?></th>
                        <th><?php echo $rev_Rate[$val]; ?></th>
                        <th style="width:150px"><?php echo $rev_Comment[$val]; ?></th>
                        <th style="width:150px"><?php echo $rev_Reply[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
            </table>
            Leave/Edit a reply:<br>
            <textarea name="up_rev" rows="5" cols="50"></textarea><br>
            <input type="submit" value="Reply" name="Up_Reply"><br>
            <span class="error"><?php echo $upErr;?></span><br>
        </form>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form action="admin.php">
            <input type="submit" value="Go back to admin page" />
        </form>
    </div>
</div>

</body>
</html>
