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

  $plik = fopen(date('m-d-Y')."_kontakty.ldif", "w");

  $file = date('m-d-Y')."_kontakty.ldif";

  if (file_exists($file))
  {
        for ($i = 0; $i < $liczba_wierszy; $i++)
        {

          $linia = "dn: cn=".$Imie[$i]." ".$Nazwisko[$i].",mail=".$DomEmail[$i];
          fwrite ($plik,"$linia\n");

          fwrite ($plik,"objectclass: top\nobjectclass: person\nobjectclass: organizationalPerson\nobjectclass: inetOrgPerson\nobjectclass: mozillaAbPersonAlpha\n");

          $linia = "givenName: ".$Imie[$i]."\nsn: ".$Nazwisko[$i]."\n";
          fwrite ($plik,$linia);

          $linia = "cn: ".$Imie[$i]." ".$Nazwisko[$i]."\n";
          fwrite ($plik,$linia);

          $linia = "mail: ".$DomEmail[$i]."\n";
          fwrite ($plik,$linia);
          fwrite ($plik,"modifytimestamp: 0Z\n");

          $linia = "telephoneNumber: ".$WorkPhone[$i]."\n";
          fwrite ($plik,$linia);

          $linia = "mobile: ".$DomKomorkowy[$i]."\n";
          fwrite ($plik,$linia);
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
