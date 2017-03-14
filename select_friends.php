<?php
  include('session.php');
  if (!isset($_SESSION['login_user'])){
    echo '<script type="text/javascript">
       window.location = "index.php"
  </script>';
  }
  $selfIDQuery = mysqli_query($conn, "SELECT accountID FROM account WHERE email_address = '$user_check'");
  $row = mysqli_fetch_array($selfIDQuery);
  if (isset($_SESSION['login_user'])){
    $selfID = $row['accountID'];
  } else {
    $selfID = "error";
  }

  $selfFriendsQuery = mysqli_query($conn, "SELECT friend2ID FROM Friendship WHERE friend1ID = ('$selfID') ");
  $selfFriendsQuery2 = mysqli_query($conn, "SELECT friend1ID FROM Friendship WHERE friend2ID = ('$selfID') ");
  $XcircleQuery = mysqli_query($conn, "SELECT circleID FROM CircleMembership WHERE accountID = ('$selfID')");
  $selfFriendsQuery_2 = mysqli_query($conn, "SELECT friend2ID FROM Friendship WHERE friend1ID = ('$selfID') ");
  $selfFriendsQuery2_2= mysqli_query($conn, "SELECT friend1ID FROM Friendship WHERE friend2ID = ('$selfID') ");
  $XcircleQuery_2 = mysqli_query($conn, "SELECT circleID FROM CircleMembership WHERE accountID = ('$selfID')");


?>
<html>
  <head>
  <?php require_once('head.php');?>
  <title>Create Friend Cicle</title>
  </head>
  <body>
    <script src="js/jqBootstrapValidation.js"></script>
    <?php require_once('common_navbar.html');?>
    <div class="container">
    <script>
      $("#selectedFriends_header").addClass("active");
    </script>
    <div class="row">
    <script>
      $(function () { $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); } );
    </script>
    <h1>Form circle of friends</h1>
    <div class="col-md-6">
      <h2>Create Circle:</h2>
      <form method = "post" role = "form">
        <div class="form-group">
          <label class="control-label">Name of the circle: </label>
          <input type="text" name="circleName" class="box" placeholder="Circle Name" required/><br/>
        </div>
        <div class="form-group">
          <?php echo "You have ".(mysqli_num_rows($selfFriendsQuery)+mysqli_num_rows($selfFriendsQuery2))." friends<br/>"; ?>
          <p>Select friends to form a circle of friends<p>
          <p class="help-block">select at least one friend</p>
        </div>
        <div class="checkbox">
          <?php
            while($friendRow = mysqli_fetch_array($selfFriendsQuery)){
            $friendID = $friendRow["friend2ID"];
            $friendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$friendID') ");
            $nameRow = mysqli_fetch_array($friendNameQuery);
            echo "<input type=\"checkbox\" name=\"selectedFriends[]\" value=\"".$friendID."\" data-validation-minchecked-minchecked=\"2\" data-validation-minchecked-message=\"Choose at least one\" >".$nameRow['name']."<br>";
          }
            while($friendRow = mysqli_fetch_array($selfFriendsQuery2)){
            $friendID = $friendRow["friend1ID"];
            $friendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$friendID') ");
            $nameRow = mysqli_fetch_array($friendNameQuery);
            echo "<input type=\"checkbox\" name=\"selectedFriends[]\" value=\"".$friendID."\" data-validation-minchecked-minchecked=\"2\" data-validation-minchecked-message=\"Choose at least one\" >".$nameRow['name']."<br>";
          }?>
        </div>
        <br/>
        <input name="create-submit" type="submit" class="btn btn-default" value="Create">
        <br/>
      </form>
      <?php
      if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['create-submit'])){
        $filteredName = mysqli_real_escape_string($conn,$_POST['circleName']);
        if(!empty($_POST['selectedFriends'])) {
          $FriendCircleQuery = mysqli_query($conn,"INSERT INTO FriendCircle (accountID,nameOfCircle) VALUES ($selfID,'$filteredName')");
          $circleID = mysqli_insert_id($conn);
          $insertSelfIntoCircleQuery = mysqli_query($conn,"INSERT INTO CircleMembership (circleID,accountID) VALUES ($circleID,$selfID)");
          foreach($_POST['selectedFriends'] as $eachFriend) {
            mysqli_query($conn,"INSERT INTO CircleMembership (circleID,accountID) VALUES ($circleID,'$eachFriend')");
          }
          echo "<div class=\"alert alert-success\" role=\"alert\">Circle created! Go to Chat room and chat!</div>";
        }
      }
      ?>
    </div>
    <div class="col-md-6">
      <h2>Current friend cirle</h2>
      <?php
        while($XcircleRow = mysqli_fetch_array($XcircleQuery)){
          $XcircleID = $XcircleRow["circleID"];
          $XcircleNameQuery = mysqli_query($conn,"SELECT nameOfCircle FROM FriendCircle WHERE circleID = $XcircleID ORDER BY nameOfCircle");
          $XcircleNameRow = mysqli_fetch_array($XcircleNameQuery);
          $XnameOfCircle = $XcircleNameRow['nameOfCircle'];
          echo "<h5>".$XnameOfCircle."<h5/>";
          $XcircleFriendIDQuery = mysqli_query($conn, "SELECT accountID FROM CircleMembership WHERE circleID = ('$XcircleID')");
          echo "<p class=\"help-block\">Circle member: ";
          $selfNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$selfID') ");
          $selfNameRow = mysqli_fetch_array($selfNameQuery);
          $selfName=$selfNameRow['name'];
          echo $selfName." ";
          while ($XcircleFriendIDRow = mysqli_fetch_array($XcircleFriendIDQuery)) {
            if ($XcircleFriendIDRow['accountID']!=$selfID) {
              $XfriendID=$XcircleFriendIDRow['accountID'];
              $XfriendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$XfriendID') ");
              $XfriendNameRow = mysqli_fetch_array($XfriendNameQuery);
              $XfriendName=$XfriendNameRow['name'];
              echo $XfriendName." ";
            }
          }
          echo "</p>";
        }
      ?>
    </div>
  </div>
  <div class="row">
    <h1>Add friends into current circle</h1>
    <script>
      $(function () { $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); } );
    </script>
    <div class="col-md-6">
      <form method = "post" role = "form">
      <p class="help-block">Select the circle that you want to add friends in</p>
      <?php
        while($XcircleRow = mysqli_fetch_array($XcircleQuery_2)){
          $XcircleID = $XcircleRow["circleID"];
          $XcircleNameQuery = mysqli_query($conn,"SELECT nameOfCircle FROM FriendCircle WHERE circleID = $XcircleID ORDER BY nameOfCircle");
          $XcircleNameRow = mysqli_fetch_array($XcircleNameQuery);
          $XnameOfCircle = $XcircleNameRow['nameOfCircle'];
          echo "<input type=\"radio\" name=\"selectedCircle\" value=\"".$XcircleID."\"> ".$XnameOfCircle."<br/>";
          $XcircleFriendIDQuery = mysqli_query($conn, "SELECT accountID FROM CircleMembership WHERE circleID = ('$XcircleID')");
          echo "<p class=\"help-block\">Circle member: ";
          $selfNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$selfID') ");
          $selfNameRow = mysqli_fetch_array($selfNameQuery);
          $selfName=$selfNameRow['name'];
          echo $selfName." ";
          while ($XcircleFriendIDRow = mysqli_fetch_array($XcircleFriendIDQuery)) {
            if ($XcircleFriendIDRow['accountID']!=$selfID) {
              $XfriendID=$XcircleFriendIDRow['accountID'];
              $XfriendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$XfriendID') ");
              $XfriendNameRow = mysqli_fetch_array($XfriendNameQuery);
              $XfriendName=$XfriendNameRow['name'];
              echo $XfriendName." ";
            }
          }
          echo "</p>";
        }
      ?>
    </div>
      <div class="col-md-6">
          <div class="form-group">
            <p class="help-block">Select friends to add into current circle<p>
          </div>
          <div class="checkbox">
            <?php
              while($friendRow = mysqli_fetch_array($selfFriendsQuery_2)){
              $friendID = $friendRow["friend2ID"];
              $friendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$friendID') ");
              $nameRow = mysqli_fetch_array($friendNameQuery);
              echo "<input type=\"checkbox\" name=\"selectedFriends2[]\" value=\"".$friendID."\">".$nameRow['name']."<br>";
            }
              while($friendRow = mysqli_fetch_array($selfFriendsQuery2_2)){
              $friendID = $friendRow["friend1ID"];
              $friendNameQuery = mysqli_query($conn, "SELECT name FROM Account WHERE accountID = ('$friendID') ");
              $nameRow = mysqli_fetch_array($friendNameQuery);
              echo "<input type=\"checkbox\" name=\"selectedFriends2[]\" value=\"".$friendID."\">".$nameRow['name']."<br>";
            }?>
          </div>
          <br/>
          <input name="add-submit" type="submit" class="btn btn-default" value="Add">
          <br/>
        </form>
        <?php
        if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['add-submit'])){
          if(!empty($_POST['selectedFriends2']) && !empty($_POST['selectedCircle'])) {
            $circleID = $_POST['selectedCircle'];
            foreach($_POST['selectedFriends2'] as $eachFriend) {
              mysqli_query($conn,"INSERT INTO CircleMembership (circleID,accountID) VALUES ($circleID,'$eachFriend')");
            }
            echo "<br/><div class=\"alert alert-success\" role=\"alert\">friend added!</div>";
          }
        }
        ?>
      </div>
    </div>
  </div>
    <?php require_once('common_footer.html');?>
  </body>
</html>
