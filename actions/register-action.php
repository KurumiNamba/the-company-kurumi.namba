<?php 

include "../classes/User.php";

#Create an object
$user = new User;

# Call the method
$user->store($_POST); 
//$_POST --> holds the data first_name, last_name, username and password(as an array)


?>