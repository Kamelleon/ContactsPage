<?php

$mysqli = new mysqli("localhost","root","","kontakty"); // Połączenie z bazą danych i wybranie tabeli "kontaky"

if($mysqli-> connect_error)
{

  die("Problem z połączeniem z bazą danych: " . $conn->connect_error); // Wyświetlenie komunikatu błędu w razie problemu z połączeniem z bazą danych

}

?>