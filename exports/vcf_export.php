<?php

  include "../db_credentials.php";
  mysqli_set_charset ($mysqli , 'utf-8');

  $sql = "SELECT * FROM kontakty";

  $wynik_zapytania = mysqli_query($mysqli, $sql);
  $liczba_wierszy = mysqli_num_rows($wynik_zapytania);

  for ($k = 0; $k < $liczba_wierszy; $k++)
  {

    $wiersz = mysqli_fetch_array($wynik_zapytania);

    $Imie[$k] = $wiersz['Imię'];
    $Nazwisko[$k] = $wiersz['Nazwisko'];
    $WorkPhone[$k] = $wiersz['Work  Phone'];
    $DomKomorkowy[$k] = $wiersz['Dom. komórkowy'];
    $DomEmail[$k] = $wiersz['Dom. e-mail'];

  }

  $plik = fopen(date('m-d-Y')."_kontakty.vcf", "w");

  $file = date('m-d-Y')."_kontakty.vcf";

  if(file_exists($file))
  {
    for ($i = 0; $i < $liczba_wierszy; $i++)
    {
      fwrite ($plik,"BEGIN:VCARD\n");

      fwrite ($plik,"VERSION:3.0\n");

      $linia = "N;CHARSET=UTF-8:".$Imie[$i].";".$Nazwisko[$i]."\n";
      fwrite ($plik,$linia);

      $linia = "FN;CHARSET=UTF-8:".$Imie[$i]." ".$Nazwisko[$i]."\n";
      fwrite ($plik,$linia);

      $linia = "TEL;TYPE=WORK,VOICE:".$WorkPhone[$i]."\n";
      fwrite ($plik,$linia);

      $linia = "TEL;TYPE=CELL,VOICE:".$DomKomorkowy[$i]."\n";
      fwrite ($plik,$linia);

      $linia = "EMAIL;TYPE=PREF,INTERNET:".$DomEmail[$i]."\n";
      fwrite ($plik,$linia);

      fwrite ($plik,"END:VCARD\n");

      fwrite ($plik,"\n");
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
