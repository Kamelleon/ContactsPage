<?php

  include "../db_credentials.php";

  $sql = "SELECT * FROM kontakty";

  $wynik_zapytania = mysqli_query($mysqli, $sql);
  $liczba_wierszy = mysqli_num_rows($wynik_zapytania);

  for ($k = 0; $k < $liczba_wierszy; $k++)
  {

    $wiersz = mysqli_fetch_array($wynik_zapytania);
    $Imie[$k] = $wiersz['Imię'];
    $Nazwisko[$k] = $wiersz['Nazwisko'];
    $NazwaStanowiska[$k] = $wiersz['Nazwa stanowiska'];
    $WorkPhone[$k] = $wiersz['Work  Phone'];
    $DomKomorkowy[$k] = $wiersz['Dom. komórkowy'];
    $DomEmail[$k] = $wiersz['Dom. e-mail'];

  }

  $plik = fopen(date('m-d-Y')."_kontakty.csv", "w");

  $file = date('m-d-Y')."_kontakty.csv";

  if (file_exists($file))
  {
    fwrite($plik, "Imię,Nazwisko,Stanowisko,workPhone,mobilePhone,email\n");

    for ($i = 0; $i < $liczba_wierszy; $i++)
    {

      $linia = "$Imie[$i],$Nazwisko[$i],$NazwaStanowiska[$i],$WorkPhone[$i],$DomKomorkowy[$i],$DomEmail[$i]\n";
      fwrite($plik, $linia);

    }
    fclose($plik);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
  }
  unlink($file);
  mysqli_close($mysqli);
?>
