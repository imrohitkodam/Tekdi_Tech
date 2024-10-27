<?php
$server = "localhost";
$username = "root";
$password = "";
$dbname = "employee_man_sys";

$conn = mysqli_connect($server, $username, $password, $dbname);

if (!$conn) {
    die("Connetion Failed : " . mysqli_connect_error());
}
