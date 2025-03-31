<?php
const DB_SERVER = 'localhost';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';
const DB_DATABASE = 'elsevier';

// Create a database connection
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Use the $db variable or explicitly unset it if not needed
if (!$db) {
	die("Connection failed: " . mysqli_connect_error());
}