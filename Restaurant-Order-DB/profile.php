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
    <title>Profile Page</title>
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
$email = $_SESSION["Email"];
$profile_name = $_SESSION["editProfName"];
$manageErr = $addErr = "";
$store_val = array();
$ing = array();

//$con=mysqli_connect("localhost","root","12345678","471db");
$con=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email_E = mysqli_real_escape_string($con, $email);
$profile_name_E = mysqli_real_escape_string($con, $profile_name);
$find_allergies = mysqli_query($con, "SELECT * FROM `Allergy` WHERE `Email_ID`='$email_E' AND `Name`='$profile_name_E'");
$num_rows = mysqli_num_rows($find_allergies);

for ($i = 0; $i < $num_rows; $i++) {
    $store_val[] = $i;
}

while ($find_row = mysqli_fetch_array($find_allergies)) {
    $ing[] = $find_row["Allergy_Name"];
}

mysqli_close($con);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["Add_All"]) {
        require_once("func/funcProf.php");
        $success = addAllergy($email, $profile_name, $_POST["add_this"], $addErr);

        if ($success) {
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Update_prof"]) {
        require_once("func/funcProf.php");
        $success = updateProfile($email, $profile_name, $_POST["up_prof"], $upErr);

        if ($success) {
            $_SESSION["editProfName"] = $_POST["up_prof"];
            header('Location: '.$_SERVER['PHP_SELF']);
            die();
        }
    }
    else if ($_POST["Del_prof"]) {
        require_once("func/funcProf.php");
        deleteProfile($email, $profile_name);

        header("Location: user.php");
        die();
    }
    else {
        if (!isset($_POST["allRadio"])) {
            $manageErr = "Error: Allergy item was not selected.";
        }
        else {
            $radio = $_POST["allRadio"];
            $find_all = $ing[$radio];
            if ($_POST["Update_All"]) {
                $up_all = $_POST["up_all"];
    
                require_once("func/funcProf.php");
                // delete with old name/type and add with new name/type
                deleteAllergy($email, $profile_name, $find_all);
                $success = addAllergy($email, $profile_name, $up_all, $manageErr);
    
                if ($success) {
                    header('Location: '.$_SERVER['PHP_SELF']);
                    die();
                }
            }
            else if ($_POST["Delete_All"]) {
                require_once("func/funcProf.php");
                deleteAllergy($email, $profile_name, $find_all);
    
                header('Location: '.$_SERVER['PHP_SELF']);
                die();
            }
        }    
    }
}
?>

<div class="title">
    <h1> Profile Page </h1>
</div>

<div class="col-md-12 text-center">
    <h4 class="profile"> Current Profile Name: <?php echo $profile_name; ?> </h4>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <table>
                <tr>
                    <th>Select</th>
                    <th>Allergies for profile</th>
                </tr>
                <?php foreach ($store_val as $val): ?>
                    <tr>
                        <th><input type="radio" name="allRadio" value=<?php echo $val; ?>></th>
                        <th><?php echo $ing[$val]; ?></th>
                    </tr>
                <?php endforeach; ?>
                </tr>
            </table>
            Update Allergy name:<br>
            <input type="text" name="up_all"><br>
            <input type="submit" value="Update Allergy" name="Update_All">
            <input type="submit" value="Delete Allergy" name="Delete_All">
        </form>
        <span class="error"><?php echo $manageErr;?></span><br><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            Add Allergy:<br>
            <input type="text" name="add_this"><br>
            <input type="submit" value="Add Allergy" name="Add_All">
        </form>
        <span class="error"><?php echo $addErr;?></span><br><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            New Profile Name:<br>
            <input type="text" name="up_prof"><br>
            <input type="submit" value="Update this profile" name="Update_prof">
        </form>
        <span class="error"><?php echo $upErr;?></span><br><br>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-auto">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="submit" value="Delete this profile" name="Del_prof">
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
