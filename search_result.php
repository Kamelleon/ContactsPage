<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>

    <meta charset="utf-8">

    <!-- Autor strony -->
    <meta name="author" content="Kamil Bugla">

    <!-- Dostosuj stronę do szerokości i wysokości urządzenia -->
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">

    <title>Kontakty</title>

    <!-- Odnośnik do pliku z CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Czcionka dla nawigacji i nagłówka tabeli -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@100&display=swap" rel="stylesheet">

    <!-- Czcionka zawartości tabeli -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&family=Work+Sans:wght@300&display=swap" rel="stylesheet">

  </head>
  <body>

    <!-- Przycisk powrotu do góry strony -->
    <button onclick="idzDoGoryStrony()" id="goUpBtn" title="Idź do góry strony"><img id="up-arrow" src="images/up-arrow.png" alt="Przycisk powrotu do góry strony"></button>

    <!-- Skrypt dla przycisku powracania do góry -->
    <script src="js/scroll_to_top.js"></script>

    <!-- NAWIGACJA -->
    <nav>

      <!-- Logo PREVAC z odnośnikiem do strony głównej -->
      <a href="https://www.prevac.pl/"><img id="prevac-logo" src="images/logo.jpg" alt="Logo PREVAC"></a>

      <!-- Przycisk "Powrót" -->
      <a href="index.php"><button class="dropbtn" style="margin-right:6vw;">Powrót</button></a>

      <form action="search_result.php" method="post">

        <div class="search-input">
          <!-- Pole wyszukiwania -->
          <input type="search" name="s" placeholder="Szukaj w tabeli..." required>
        </div>

        <!-- Przycisk szukania -->
        <button type="submit" title="Wyszukaj" name="submit" border="0"><img id="search-ic" src="images/search-icon.png" alt="Przycisk wyszukiwania"></button>

      </form>

      <!-- Przycisk menu dropdown - "Pobierz" -->
      <div class="dropdown-pobierz">

        <button class="dropbtn">Pobierz</button>
        <!-- Zawartość dropdown'u "Pobierz" -->
          <div class="dropdown-content-pobierz">
            <a href="ldif_export.php">Pobierz książkę adresową dla Thunderbird (.LDIF)</a>
            <a href="csv_export.php">Pobierz książkę adresową dla MS Outlook (.CSV)</a>
            <a href="vcf_export.php">Pobierz książkę adresową dla vCard (.VCF)</a>
          </div>
      </div>

    </nav>

    <!-- ODDZIELENIE NAWIGACJI OD TABELI -->
    <br><br>

    <!-- NAPIS: "Wyniki wyszukiwania dla: 'fraza'" -->
    <p style="font-size:32px;">Wyniki wyszukiwania dla:<br> "<?php echo $_POST["s"]; ?>"</p>

        <?php

          $tablica_pionów = []; // Tablica do której będą umieszczane wyświetlone już piony na stronie wyszukiwania

          $wyszukiwana_fraza = $_POST["s"]; // Pobranie danych wejściowych z otrzymanego formularza - wyszukiwarki

          $sql = "SELECT `Kod organizacji`, `Nazwa org.` FROM kontakty

                  WHERE    (`Nazwisko` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Imię` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nazwa stanowiska` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nr pracownika` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Stanowisko` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Kod organizacji` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nazwa org.` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Używane wewn.` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Dom. e-mail` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Work  Phone` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Dom. Komórkowy` COLLATE utf8_polish_ci LIKE '%".$wyszukiwana_fraza."%')

                  GROUP BY `Kod organizacji` ORDER BY `Kod organizacji` ASC";

          include "db_credentials.php";

          if ($wynik_sql = $mysqli -> query($sql))
          {

            $liczba_wierszy_wyniku_sql=$wynik_sql-> num_rows;

            for($i = 0; $i < $liczba_wierszy_wyniku_sql; $i++)
            {

              $wiersz = mysqli_fetch_array($wynik_sql);

              $kod_organizacji = $wiersz['Kod organizacji'];
              $zespol = $wiersz['Nazwa org.'];

              utworzTabele($kod_organizacji, $zespol);

            }

          }
          if($liczba_wierszy_wyniku_sql==0)
          {

            echo "<br><br><p>Brak wyników wyszukiwania</p>";

          }

          $tablica_pionów = [];

          function utworzTabele($kod_organizacji, $zespol)
          {

            global $tablica_pionów;

            global $wyszukiwana_fraza;

            $kod_pionu = substr($kod_organizacji,0,1); // Wybierz pierwszą literę z nazwy organizacji

            include "db_credentials.php";

            // NAGŁÓWKI DZIAŁÓW, ZESPOŁÓW, PIONÓW

            if(strlen($kod_organizacji) == 1 && !in_array($kod_pionu,$tablica_pionów)) // Kod dla pionów - Jeśli długość nazwy organizacji = 1 oraz pion NIE znajduje się jeszcze na stronie
            {

              $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '" . $kod_organizacji . "' GROUP BY `Kod organizacji`";
              $wynik_sql = $mysqli-> query($sql);

              while($wiersz = $wynik_sql-> fetch_assoc())
              {

                  $pion = $wiersz['Nazwa org.'];

                  // Nagłówek z nazwą pionu
                  echo "</tbody></table>";
                  echo("<p>".$pion."</p>");
                  echo "<hr>";

                  include "table_header.php";

              }
              array_push($tablica_pionów,$kod_pionu); // Dodaj pion do tablicy pionów aby się nie powtórzył na stronie

            }
            else // Jeśli dłguość organizacji dłuższa niż 1
            {
              // Jeśli pion NIE znajduje się na stronie (jeśli się znajduje - pomiń ten blok kodu)
              if(!in_array($kod_pionu,$tablica_pionów))
              {

                $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '".$kod_pionu."' GROUP BY `Kod organizacji`";
                $wynik_sql = $mysqli-> query($sql);

                while($wiersz = $wynik_sql-> fetch_assoc())
                {

                  $pion = $wiersz['Nazwa org.'];

                  // Nagłówek z nazwą pionu
                  echo "</tbody></table>";
                  echo("<p>".$pion."</p>");
                  echo "<hr>";

                  include "table_header.php";

                }

                array_push($tablica_pionów,$kod_pionu); // Dodaj pion do tablicy pionów aby się nie powtórzył na stronie

              }


              if(strlen($kod_organizacji) == 3) // Kod dla działów - jeśli długość kodu organizacji = 3
              {

                $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '" . $kod_organizacji . "' GROUP BY `Kod organizacji`";
                $wynik_sql = $mysqli-> query($sql);

                while($wiersz = $wynik_sql-> fetch_assoc())
                {

                  $dzial = $wiersz['Nazwa org.'];

                  // Nagłówek z nazwą dzialu i kodem organizacji
                  echo "<tr>";
                  echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>" . $kod_organizacji . "</div> " . $dzial . "</td>");
                  echo "</tr>";

                }

              }
              else
              {

                $kod_dzialu = substr($kod_organizacji,0,3); // Wybierz pierwsze 3 litery z nazwy organizacji
                $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '".$kod_dzialu."' GROUP BY `Kod organizacji`";
                $wynik_sql = $mysqli-> query($sql);

                if($wynik_sql->num_rows == 0) // Jeśli organizacjia nie posiada działu - istnieje tylko pion i zespół
                {
                  // Nagłówek z nazwą zespołu i kodem organizacji
                  echo "<tr>";
                  echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>". $kod_organizacji ."</div> ". $zespol ."  "."</td>");
                  echo("</tr>");

                }
                else // Jeśli organizacja posiada dział i zespół
                {

                  while($wiersz = $wynik_sql-> fetch_assoc())
                  {

                    $dzial = $wiersz['Nazwa org.'];

                    // Nagłówek z nazwą dzialu, kodem organizacji i nazwą zespołu
                    echo "<tr>";
                    echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>". $kod_organizacji ."</div> ". $dzial ."&nbsp  / &nbsp". $zespol ."</td>");
                    echo("</tr>");

                  }

                }

              }

            }

            // WSTAW ZAWARTOŚĆ DO TABELI I PRZYPORZĄDKUJ ODPOWIEDNIM NAGŁÓWKOM

            $sql = "SELECT *
                    FROM kontakty
                    WHERE
                            `Kod organizacji` = '".$kod_organizacji."'
                        AND ((`Nazwisko` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Imię` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Używane wewn.` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nr pracownika` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nazwa stanowiska` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Stanowisko` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Nazwa org.` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Kod organizacji` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Dom. e-mail` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Work  Phone` LIKE '%".$wyszukiwana_fraza."%')
                        OR (`Dom. komórkowy` LIKE '%".$wyszukiwana_fraza."%'))

                    ORDER BY `Nazwisko` ASC"; // Szukaj frazy w podanych kolumnach

            $wynik_sql = $mysqli-> query($sql); // Wykonaj zapytanie

            $licznik = 0; // Licznik ilości zwróconych wyników z bazy danych

            if(!empty($wynik_sql)) // Sprawdź czy wynik zapytania jest pusty
            {

              if($wynik_sql-> num_rows > 0)
              {

                while($wiersz = $wynik_sql-> fetch_assoc()) // Pobierz wiersz wyników z tabeli asocjacyjnej - "klucz:wartość"
                {

                  if(($licznik%2)==0) // Jaśniejszy wiersz tabeli HTML dla parzystego wyniku działania
                  {

                    echo("<tr class='light'><td>" .
                      $wiersz["Nazwisko"] ."</td><td>".
                      $wiersz["Imię"] . "</td><td>" .
                      $wiersz["Nazwa stanowiska"] . "</td><td>" .
                      $wiersz["Kod organizacji"] . "</td><td><a class='.no-link-highlight' href='mailto:".
                      $wiersz["Dom. e-mail"]."'>".$wiersz["Dom. e-mail"]."</a>" . "</td><td>" .
                      $wiersz["Work  Phone"] . "</td><td>" .
                      $wiersz["Dom. komórkowy"] . "</td></tr>");
                    $licznik++; // Zwiększ licznik wyników o 1

                  }
                  else // Ciemniejszy wiersz tabeli HTML dla nieparzystego wyniku działania
                  {

                    echo("<tr class='dark'><td>" .
                      $wiersz["Nazwisko"] ."</td><td>".
                      $wiersz["Imię"] . "</td><td>" .
                      $wiersz["Nazwa stanowiska"] . "</td><td>" .
                      $wiersz["Kod organizacji"] . "</td><td><a class='.no-link-highlight' href='mailto:".
                      $wiersz["Dom. e-mail"]."'>".$wiersz["Dom. e-mail"]."</a>" . "</td><td>" .
                      $wiersz["Work  Phone"] . "</td><td>" .
                      $wiersz["Dom. komórkowy"] . "</td></tr>");
                    $licznik++; // Zwiększ licznik wyników o 1

                  }

                }

              }

            }

          }

        ?>

  </body>
</html>
