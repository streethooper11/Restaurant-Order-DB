<?php
session_start();

if (!isset($_SESSION["accLevel"]) || ($_SESSION["accLevel"] !== 0)) {
    header("Location: index.php");
    die();
}
?>

<?php
    require_once("func/funcOrder.php");
    unsetAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Complete</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>

<div class="title">
    <h1> Order Page </h1>
</div>

<div class="col-md-12 text-center">
    <h4 class="group-restaurant-name"> Thank you for your order! </h4>
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
