<?php
$connect = mysqli_connect("localhost", "root", "", "newfolder");

// Check connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure the `uploads` directory exists and is writable:
if (!is_dir('../uploads')) {
    mkdir('../uploads', 0777, true);
}
chmod('../uploads', 0777);
?>