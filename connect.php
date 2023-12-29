<?php
try 
{
	
$dbu = new PDO('mysql:host=localhost;dbname=UnifiMigrate', 'user', 'pass');	
	
} catch(PDOException $e) {
	die($e->getMessage());
	}		


?> 