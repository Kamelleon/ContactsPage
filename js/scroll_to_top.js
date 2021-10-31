przyciskDoGory = document.getElementById("goUpBtn"); // Pobranie przycisku powrotu do góry z HTML'a
window.onscroll = function() {scrollFunction()}; // Wyświetlenie przycisk powrotu do góry gdy strona jest przewinięta o 40px

function scrollFunction(){
  if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40){
    przyciskDoGory.style.display = "block"; //Wświetlenie przycisku powrotu do góry gdy strona jest przewinięta o 40px
  }
  else{
    przyciskDoGory.style.display = "none"; // Ukrycie przycisku powrotu do góry strony w innym wypadku
  }
}

// Funkcja przenosząca na górę strony po kliknięciu przycisku
function idzDoGoryStrony(){
  document.body.scrollTop = 0; // Dla Safari
  document.documentElement.scrollTop = 0; // Dla Chrome, Firefox, IE, Opera
}
