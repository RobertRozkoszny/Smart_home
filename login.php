<?php
	//Zawarcie pliku łączącego z MySQL.
   include("connect.php");
   //Rozpoczęcie sesji.
   session_start();
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // Login oraz hasło przekazane z formularza.
		//mysqli_real_escape dodaje znaki unikowe do łańcucha znaków.
      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']); 
      //Zapytanie do bazy danych czy istnieje użytkownik o podanym loginie z podanym hasłem.
      $sql = "SELECT ID FROM login WHERE User = '$myusername' and Pass = '$mypassword'";
      $result = mysqli_query($db,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	  //'active' jeżeli funkcja zwróciła wynik.
      $active = $row['active'];
      
      $count = mysqli_num_rows($result);
      
		//Liczba zwróconych rzędów równa się 1 jeśli podane hasło jest przypisane do wpisanej nazwy użytkownika.
      if($count == 1) {
			//Zapisanie nazwy uytkownikownika w superglobalnej tablicy zmiennych. 
			//Umożliwia podtrzymanie sesji po otwarciu kolejnych stron.
			$_SESSION['login_user'] = $myusername;
			//Przekierowanie do chronionego hasłem interfejsu użytkownika.
			header( "Location: path/to/locationpage1.php" );
		}else {
         $error = "Your Login Name or Password is invalid";
      }
   }
?>







<!DOCTYPE html>
<html lang="en">
<head>
  <title>Inteligentny budynek</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
		<!Importuje pliki Bootstrapa >
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
	
	<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Inteligentny budynek</a>
    </div>
    </nav>
  
<div class="container">
  <h1>Inteligentny budynek</h1>     
</div>


<div class="container">
  <h2>Logowanie</h2>
  
  <!Formularz logowania >
  <form method="POST" action="#" > 
    <div class="form_input">
      <label for="email">Nazwa użytkownika:</label>
      <input type="text" class="form-control" id="username" placeholder="Wprowadż nazwę użtykownika" name="username">
    </div>
    <div class="form-group">
      <label for="pwd">Hasło:</label>
      <input type="password" class="form-control" id="pwd" placeholder="Wprowadż hasło" name="password">
    </div>
    
    <button type="submit" class="btn btn-primary">Zaloguj</button>
  </form>
</div>


</body>
</html>





