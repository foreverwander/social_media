<?php
  function load_friend_list($user_accountID,$conn){
      $query_friend_list = "SELECT Account.* FROM Account WHERE (Account.accountID in (SELECT Friendship.friend1ID FROM Friendship WHERE Friendship.friend2ID = '$user_accountID')) OR (Account.accountID in (SELECT Friendship.friend2ID FROM Friendship WHERE Friendship.friend1ID = '$user_accountID'))";
      $result = mysqli_query($conn,$query_friend_list);
      return $result;
    }

    function search_by_name($name,$conn){
      $search_by_name = "SELECT * FROM Account WHERE name LIKE '%$name%'";
      $result = mysqli_query($conn,$search_by_name);
      return $result;
    }

    function search_by_age($age,$conn){
      $search_by_age = "SELECT * FROM Account WHERE age = '$age'";
      $result = mysqli_query($conn,$search_by_age);
      return $result;
    }

    function search_by_city($city,$conn){
      $search_by_city = "SELECT * FROM Account WHERE city LIKE '%$city%'";
      $result = mysqli_query($conn,$search_by_city);
      return $result;
    }

    function search_by_country($country,$conn){
      $search_by_country = "SELECT * FROM Account WHERE country LIKE '%$country%'";
      $result = mysqli_query($conn,$search_by_country);
      return $result;
    }

    function search_by_email($email,$conn){
      $search_by_email = "SELECT * FROM Account WHERE email_address = '$email'";
      $result = mysqli_query($conn,$search_by_email);
      return $result;
    }

    function search_by_friend($name,$conn){
      $friend_ID = mysqli_fetch_assoc(search_by_name($name,$conn))['accountID'];
      $query_search_by_friend = "SELECT * FROM Account WHERE accountID in (SELECT friend2ID FROM Friendship WHERE friend1ID = '$friend_ID' UNION SELECT friend1ID FROM Friendship WHERE friend2ID = '$friend_ID')";
      $result = mysqli_query($conn,$query_search_by_friend);
      return $result;
    }

    function delete_friend($user_accountID,$friend_accountID,$conn){
      $query_remove_friend = "DELETE FROM Friendship WHERE (friend2ID = '$user_accountID' AND friend1ID = '$friend_accountID') OR (friend2ID = '$friend_accountID' AND friend1ID = '$user_accountID')";
      mysqli_query($conn,$query_remove_friend);
    }

    function add_friend($user_accountID,$friend_accountID,$conn){
      #$query_add_friend = 
    }

    function send_invitation($user_accountID,$friend_ID,$conn){
      $query_send_invitation = "INSERT INTO Invitation (accountID, inviteeID) VALUES ($user_accountID,$friend_ID)";
      $result = mysqli_query($conn,$query_send_invitation);
      return $result;
    }

    function load_friend_invitation($user_accountID,$conn){
      $query_friend_invitation = "SELECT Account.* FROM Account WHERE (Account.accountID in (SELECT Invitation.accountID FROM Invitation WHERE Invitation.inviteeID = '$user_accountID')) ";
      $result = mysqli_query($conn,$query_friend_invitation);
      return $result;
    }

    function load_sent_friend_invitation($user_accountID,$conn){
      $query_sent_friend_invitation = "SELECT Account.* FROM Account WHERE (Account.accountID in (SELECT Invitation.inviteeID FROM Invitation WHERE Invitation.accountID = '$user_accountID')) ";
      $result = mysqli_query($conn,$query_sent_friend_invitation);
      return $result;
    }

    function accept_invitation($user_accountID,$friend_ID,$conn){
      $query_accept_invitation = "INSERT INTO Friendship (friend1ID, friend2ID) VALUES ('$user_accountID','$friend_ID')";
      mysqli_query($conn,$query_accept_invitation);
      delete_invitation($friend_ID,$user_accountID,$conn);
    } 

    function delete_invitation($user_accountID,$friend_ID,$conn){
      $query_delete_invitation = "DELETE FROM Invitation WHERE accountID = '$user_accountID' AND inviteeID = '$friend_ID'";
      mysqli_query($conn,$query_delete_invitation);
    }

    function reject_invitation($user_accountID,$friend_ID,$conn){
      $query_reject_invitation = "UPDATE Invitation SET isRejected = 1 WHERE accountID = '$friend_ID' AND inviteeID = '$user_accountID'";
      mysqli_query($conn,$query_reject_invitation);
    }

    function check_inv_status($user_accountID,$friend_ID,$conn){
      $query_check_inv_status = "SELECT * FROM Invitation WHERE accountID = '$friend_ID' AND inviteeID = '$user_accountID'";
      $result = mysqli_fetch_assoc(mysqli_query($conn,$query_check_inv_status))['isRejected'];
      return $result;
    }

    function check_privacy_status($friend_ID,$conn){
      $query_check_privacy_status = "SELECT * FROM Account WHERE accountID = '$friend_ID'";
      $result = mysqli_fetch_assoc(mysqli_query($conn,$query_check_privacy_status))['privacy_setting'];
      return $result;
    }

    function search_by_ID($friend_ID,$conn){
      $query_get_name = "SELECT * FROM Account WHERE accountID = '$friend_ID'";
      $result = mysqli_query($conn,$query_get_name);
      return $result;
    }
?>