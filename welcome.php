<?php
   include('session.php');
?>
<html>

   <head>
      <title>Welcome </title>
   </head>

   <body>
      <h1>Welcome <?php echo $login_session; ?></h1>
      <h2><a href = "FriendList.php">Friend list</a></h2>
      <h2><a href="select_friends.php">Create circle of friends</a></h2>
      <h2><a href="chat_room.php">Chat in circle of friends</a></h2>
      <h2><a href = "logout.php">Sign Out</a></h2>

   </body>

</html>
