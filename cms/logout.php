<?php
session_start();
if(!isset($_SESSION['uid'])){	
	Header("Location: login.php");  
}
unset($_SESSION['uid']);
unset($_SESSION['account']);
Header("Location: login.php");