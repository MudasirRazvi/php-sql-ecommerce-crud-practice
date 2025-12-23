<?php
$conn = mysqli_connect("localhost", "root", "1234", "e_comm");

if(!$conn) {
    die ("Connection Failed: " . mysqli_connect_error());
}