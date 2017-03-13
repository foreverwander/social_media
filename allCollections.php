<!DOCTYPE html>
<?php
include("dbconfig.php");
session_start();

if (isset($_SESSION['login_user'])) {
    $user_email = $_SESSION['login_user'];
} else {
    $user_email = "error";
}

$load_accountID = "SELECT accountID, isAdmin FROM Account WHERE email_address = '$user_email'";
$user_accountID = mysqli_fetch_assoc(mysqli_query($conn, $load_accountID))['accountID'];
$isAdmin = mysqli_fetch_assoc(mysqli_query($conn, $load_accountID))['isAdmin'];

if (!isset($_GET['accountID'])) {
    $accountID = $user_accountID;
} else {
    $accountID = $_GET['accountID'];
}

$load_name = "SELECT name FROM Account WHERE accountID = '$accountID'";
$name = mysqli_fetch_assoc(mysqli_query($conn, $load_name))['name'];

$collections = array();

function saveToDatabase($collection, $user_accountID, $conn) {
    $new_collection_query = "INSERT INTO Collection (accountID, name, description) " .
            "VALUES ( '$user_accountID', '${collection['name']}', '${collection['description']}')";
    $result = mysqli_query($conn, $new_collection_query)
            or die('Error making new collection query' . mysql_error());
}

//create new collection
if (isset($_POST['title']) && isset($_POST['description'])) {
    $collection = array();
    $collection['name'] =  str_replace("'", "\'\'", $_POST['title']);
    $collection['description'] =  str_replace("'", "\'\'", $_POST['description']);
    saveToDatabase($collection, $user_accountID, $conn);
}

if (isset($_REQUEST["delete"])) {
    $delete = $_REQUEST["delete"];

    $delete_collection_query = "DELETE FROM Collection WHERE collectionID = $delete";

    $delete_result = mysqli_query($conn, $delete_collection_query)
            or die('Error making saveToDatabase query' . mysql_error());
}

// get all collections of the account
$select_collection_query = "SELECT * FROM Collection WHERE accountID = $accountID";
$result = mysqli_query($conn, $select_collection_query)
        or die('Error making select collections query' . mysql_error());
$k = 0;
while ($row = mysqli_fetch_array($result)) {
    $collections[$k] = $row;
    $k = $k + 1;
}

// get all collections that current user has access right to
$grantedCollections = array();
$grantedQuery = "SELECT collectionID FROM Collection WHERE"
        . " collectionID IN (SELECT collectionID FROM friendaccessright WHERE accountID = $user_accountID "
        . "UNION "
        . "SELECT collectionID FROM circleaccessright INNER JOIN circlemembership ON circleaccessright.circleID = circlemembership.circleID WHERE accountID = $user_accountID)";
$result = mysqli_query($conn, $grantedQuery)
        or die('Error making get granted collections query' . mysql_error());
$k = 0;
while ($row = mysqli_fetch_array($result)) {
    $grantedCollections[$k] = $row[0];
    $k = $k + 1;
}

function hasPermission($Collection, $user_accountID, $grantedCollections, $isAdmin) {
    if ($isAdmin == 1){
        return true;
    }
    if ($Collection[1] == $user_accountID) {
        return true;
    }
    if (in_array($Collection[0], $grantedCollections)) {
        return true;
    }
    return false;
}

function displayCollections($collections, $user_accountID, $grantedCollections, $isAdmin) {

    echo "  <div class=\"container\">
                    <div class=\"row\">
                    <div class=\"col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1\">";

    for ($x = 0; $x < count($collections); $x++) {
        $Collection = $collections[$x];
        $title = str_replace("''", "'", $Collection[2]);
        $description = str_replace("''", "'", $Collection[3]);
        if (hasPermission($Collection, $user_accountID, $grantedCollections, $isAdmin)) {
            echo "               
                    <div class=\"post-preview\">
                        <a href=\"collection.php?collectionID=$Collection[0]\">
                            <h2 class=\"post-title\">
                                $title
                            </h2>
                            <h3 class=\"post-subtitle\">
                                $description
                            </h3>
                        </a>
                    </div>
                    <hr>
                    ";
        }
    }

    echo "
        </div>
        </div>
        </div>
        ";
}

function displayNewCollectionButton() {
    echo "
            <div class = \"container\">
            <div class = \"row\">
                <div class = \"col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1\">
                    <ul class = \"pager\">
                        <li class = \"next\">
                            <a href = \"newCollection.php\">New Collection</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>";
}
?>

<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Photo Collections</title>

        <!-- Bootstrap Core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Theme CSS -->
        <link href="css/clean-blog.min.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
        <?php require_once('head.php'); ?>
    </head>

    <body>
        <?php require_once('common_navbar.html'); ?>
        <!-- Page Header -->
        <!-- Set your background image for this header on the line below. -->
        <header class="intro-header" style="background-image: url('img/home-bg.jpg')">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <div class="site-heading">
                            <h1><?php echo $name ?>'s Photo Collections</h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <?php displayCollections($collections, $user_accountID, $grantedCollections, $isAdmin) ?>

        <?php
        if ($accountID == $user_accountID) {
            displayNewCollectionButton();
        }
        ?>

        <?php require_once('common_footer.html'); ?>

        <!--jQuery -->
        <script src = "vendor/jquery/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

        <!-- Contact Form JavaScript -->
        <script src="js/jqBootstrapValidation.js"></script>
        <script src="js/contact_me.js"></script>

        <!-- Theme JavaScript -->
        <script src="js/clean-blog.min.js"></script>

    </body>

</html>
