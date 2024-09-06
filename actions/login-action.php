<?php 

include "../classes/User.php";

$user = new User;

$user->login($_POST);
// in form tag, we set "action" so that we can get values that users inputted

?>