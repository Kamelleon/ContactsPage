<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>

    <meta charset="utf-8">

    <!-- Autor strony -->
    <meta name="author" content="Kamil Bugla Praktykant">

    <!-- Dostosuj stronę do szerokości i wysokości urządzenia -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kontakty</title>

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
      <a href="https://www.prevac.pl/"><img id="prevac-logo" src="images/logo.jpg" title="Przejdź do strony głównej PREVAC" alt="Logo PREVAC"></a>

      <!-- Przycisk "Przejdź do pionu" -->
      <div class="dropdown-organizacja">
        <button class="dropbtn" title="Wybierz ogranizację z listy">Przejdź do pionu</button>

          <!-- Zawartość menu dropdown "Przejdź do pionu" -->
          <div class="dropdown-content-organizacja">

            <?php

              include "db_credentials.php";

              $sql = "SELECT `Nazwa org.`, `Kod organizacji` FROM kontakty WHERE `Kod organizacji` LIKE '_' GROUP BY `Nazwa org.` ORDER BY `Kod organizacji` ASC"; // Zapytanie sql dla tabeli "kontakty"

              if ($wynik_sql = $mysqli -> query($sql))
              {

                $liczba_wierszy=$wynik_sql->num_rows; // Zlicz ilość wierszy

                for($i = 0; $i < $liczba_wierszy; $i++)
                {

                  $wiersz_sql = mysqli_fetch_array($wynik_sql);
                  echo "<a href='#".$wiersz_sql['Nazwa org.']."'>".$wiersz_sql["Kod organizacji"]." - ".$wiersz_sql['Nazwa org.']."</a>"; // Dodawaj kolejne załączniki do menu dropdown

                }
              }
              else
              {

                echo "<a href='#'>Brak danych</a>";

              }
            ?>
          </div>
      </div>
      <!-- Wyszukiwarka -->
      <form action="search_result.php" method="post">

        <div class="search-input">
          <!-- Pole wyszukiwania -->
          <input type="search" name="s" placeholder="Szukaj w tabeli..." required>
        </div>

        <!-- Przycisk szukania -->
        <button type="submit" title="Wyszukaj" name="submit" border="0"><img id="search-ic" src="images/search-icon.png" alt="Przycisk wyszukiwania"></button>

      </form>

      <!-- Menu dropdown - "Pobierz" -->
      <div class="dropdown-pobierz">
        <button class="dropbtn">Pobierz</button>
          <!-- Zawartość menu dropdown - "Pobierz" -->
          <div class="dropdown-content-pobierz">
            <a href="exports/ldif_export.php">Pobierz kontakty dla Thunderbird (.LDIF)</a>
            <a href="exports/csv_export.php">Pobierz kontakty w formacie (.CSV)</a>
            <a href="exports/vcf_export.php">Pobierz kontakty dla vCard (.VCF)</a>
          </div>
      </div>

    </nav>
    <h2 style="float:left;">Ostatnia aktualizacja:
      <?php
         include "db_credentials.php";
         $zapytanie = "SELECT data FROM aktualizacje ORDER BY data DESC LIMIT 1;";
         if($wynik_sql = mysqli_query($mysqli, $zapytanie))
         {
           while($wiersz_sql = $wynik_sql-> fetch_assoc())
           {
             $data = $wiersz_sql['data'];
             echo $data;
           }
         }
         else
         {
           echo "Brak daty";
         }
      ?>
    </h2>
    <div style="clear:both;">
    </div>

    <!-- PRZERWA MIĘDZY NAWIGACJĄ A TABELĄ -->


    <!-- PĘTLA DO TWORZENIA TABELI NA PODSTAWIE KODU ORGANIZACJI -->
    <?php

      include "db_credentials.php";

      // Zapytanie wybierające kod organizacji i nazwę organizacji dla każdej organizacji z bazy danych
      $sql = "SELECT `Kod organizacji`, `Nazwa org.` FROM kontakty GROUP BY `Kod organizacji` ORDER BY `Kod organizacji` ASC";

      if ($wynik_sql = $mysqli -> query($sql))
      {

        $liczba_wierszy=$wynik_sql->num_rows; // Zlicz wiersze wyniku zapytania

        for($i = 0; $i < $liczba_wierszy; $i++) // Pętla do tworzenia tabeli dla każdej organizacji z osobna
        {

          $wiersz_sql = mysqli_fetch_array($wynik_sql); // Utworzenie tabeli asocjacyjnej dla każdego wyniku zapytania - [klucz:wartość]

          $kod_organizacji = $wiersz_sql['Kod organizacji'];
          $zespol = $wiersz_sql['Nazwa org.'];

          utworzTabele($kod_organizacji, $zespol); // Utwórz tabelę dla każdej organizacji występującej w bazie danych na podstawie nazwy organizacji i kodu z bazy danych

        }

      }
      else
      {

        echo "<p>Brak danych</p><br><p>Sprawdź czy tabela istnieje w bazie</p><br>"; // Komunikat w razie braku wyników w bazie danych

      }

    ?>

    <!-- UTWORZENIE TABELI -->
    <?php
      // Funkcja do tworzenia tabeli na podstawie argumentów - kodu organizacji i zespołu

      function utworzTabele($kod_organizacji, $zespol){

        include "db_credentials.php";

        // UTWÓRZ NAGŁÓWKI DZIAŁÓW, ZESPOŁÓW, PIONÓW

        if(strlen($kod_organizacji) == 1) // Kod dla pionów - jeśli długość kodu organizacji = 1
        {

          $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '".$kod_organizacji."' GROUP BY `Kod organizacji`";
          $wynik_sql = $mysqli-> query($sql);

          while($wiersz_sql = $wynik_sql-> fetch_assoc())
          {

            $pion = $wiersz_sql['Nazwa org.'];

            echo "</tbody></table>"; // Zamknij poprzednią tabelę jeśli istnieje
            echo "<a name='".$zespol."'></a>"; // Utwórz odnośnik dla przycisku "Przejdź do pionu"
            echo("<p>".$pion."</p>"); // Nazwa pionu
            echo "<hr>"; // Linia oddzielająca nazwę pionu od tabeli

            include "table_header.php"; // Załącz plik z nagłówkiem tabeli - nazwami kolumn

          }

        }
        elseif(strlen($kod_organizacji) == 3) // Kod dla działów - jeśli długość kodu organizacji = 3
        {

          $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '" . $kod_organizacji . "' GROUP BY `Kod organizacji`";
          $wynik_sql = $mysqli-> query($sql);

          while($wiersz_sql = $wynik_sql-> fetch_assoc())
          {

            $dzial = $wiersz_sql['Nazwa org.'];

            //Utwórz nagłówek z nazwą działu i kodem organizacji
            echo "<tr>";
            echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>" . $kod_organizacji . "</div> " . $dzial . "</td>");
            echo "</tr>";

          }

        }
        else // Kod dla zespołów - jeśli długość kodu organizacji > 3
        {

          $kod_dzialu = substr($kod_organizacji,0,3); // Wybierz pierwsze 3 litery z nazwy organizacji
          $sql = "SELECT * FROM kontakty WHERE `Kod organizacji` = '".$kod_dzialu."' GROUP BY `Kod organizacji`";
          $wynik_sql = $mysqli-> query($sql);

          if($wynik_sql->num_rows == 0) // Jeśli organizacjia NIE POSIADA działu - istnieje tylko pion i zespół
          {
            // Utwórz nagłówek z nazwą zespołu i kodem organizacji
            echo "<tr>";
            echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>". $kod_organizacji ."</div> ". $zespol ."  "."</td>");
            echo("</tr>");

          }
          else // Jeśli organizacja POSIADA pion dział i zespół
          {

            while($wiersz_sql = $wynik_sql-> fetch_assoc())
            {

              $dzial = $wiersz_sql['Nazwa org.'];

              //Utwórz nagłówek z nazwą zespołu, kodem organizacji i nazwą działu
              echo "<tr>";
              echo("<td colspan='7' class='dzial-i-zespol-header'><div id='kodOrg'>". $kod_organizacji ."</div> ". $dzial ."&nbsp  / &nbsp". $zespol ."</td>");
              echo("</tr>");

            }

          }

        }

        // WSTAW ZAWARTOŚĆ DO TABELI I PRZYPORZĄDKUJ ODPOWIEDNIM NAGŁÓWKOM

        $sql = "SELECT `Nazwisko`,
                       `Imię`,
                       `Nazwa stanowiska`,
                       `Kod organizacji`,
                       `Dom. e-mail`,
                       `Work  Phone`,
                       `Dom. Komórkowy`
                FROM kontakty
                WHERE `Kod organizacji` = '".$kod_organizacji."'
                ORDER BY `Nazwisko` ASC";

        $wynik_sql = $mysqli-> query($sql); // Wynik zapytania
        $licznik = 0; // Licznik ilości zwróconych wyników z bazy danych

        if(!empty($wynik_sql)) // Sprawdź czy wynik zapytania jest pusty
        {

          if($wynik_sql-> num_rows > 0)
          {

            while($wiersz_sql = $wynik_sql-> fetch_assoc()) // Pobierz wiersz wyników z tabeli asocjacyjnej - "klucz:wartość"
            {

              if(($licznik%2)==0) // Jaśniejszy wiersz tabeli HTML dla parzystego wyniku działania
              {
                // Wstaw zawartość wierszy do tabeli
                echo("<tr class='light'><td>" .
                  $wiersz_sql["Nazwisko"] ."</td><td>".
                  $wiersz_sql["Imię"] . "</td><td>" .
                  $wiersz_sql["Nazwa stanowiska"] . "</td><td>" .
                  $wiersz_sql["Kod organizacji"] . "</td><td><a class='.no-link-highlight' href='mailto:".
                  $wiersz_sql["Dom. e-mail"]."'>".$wiersz_sql["Dom. e-mail"]."</a>" . "</td><td>" .
                  $wiersz_sql["Work  Phone"] . "</td><td>" .
                  $wiersz_sql["Dom. Komórkowy"] . "</td></tr>");
                $licznik++; // Zwiększ licznik wyników o 1

              }
              else // Ciemniejszy wiersz tabeli HTML dla nieparzystego wyniku działania
              {
                // Wstaw zawartość wierszy do tabeli
                echo("<tr class='dark'><td>" .
                  $wiersz_sql["Nazwisko"] ."</td><td>".
                  $wiersz_sql["Imię"] . "</td><td>" .
                  $wiersz_sql["Nazwa stanowiska"] . "</td><td>" .
                  $wiersz_sql["Kod organizacji"] . "</td><td><a class='.no-link-highlight' href='mailto:".
                  $wiersz_sql["Dom. e-mail"]."'>".$wiersz_sql["Dom. e-mail"]."</a>" . "</td><td>" .
                  $wiersz_sql["Work  Phone"] . "</td><td>" .
                  $wiersz_sql["Dom. Komórkowy"] . "</td></tr>");
                $licznik++; // Zwiększ licznik wyników o 1

              }

            }

          }

        }

      }

    ?>

  </body>
</html>
