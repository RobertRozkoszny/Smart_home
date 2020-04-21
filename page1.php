 <?php
  //Zawarcie pliku sprawdzającego uprawnienia dostępu
 include('session.php');
  //Zawarcie pliku zapewniającego połączenie z baza danych
   include("connect.php");
 ?>
  <!DOCTYPE html>
<html lang="en">
<head>
  <title>Praca inżynierska</title>
  <meta charset="utf-8">
  <!--Importuje pliki Bootstrapa -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
   
    <ul class="nav navbar-nav navbar-right">
     
      <li><a href="destroy.php"><span class="glyphicon glyphicon-log-out"></span> Wyloguj</a></li>
    </ul>
    
  </div>
</nav>

<div class="container">
 <ul class="nav nav-pills">
       <li ><a data-toggle="pill" href="#home">Start</a></li>       						<!--usuniete class="active" z <li > --> 
    <li><a data-toggle="pill" href="#menu1">Temperatura, wilgotność powietrza i gaz</a></li>
    <li><a data-toggle="pill" href="#menu2">Natężenie światła i ruch</a></li>
   
    </ul>
  
<div class="container">
 <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
      <h3>Start</h3><form method="post"><div class="text-right">
	  <input type="submit" name="przycisk" id="przycisk10" class="btn btn-primary" value="Aktualizuj">
	  </form>
	  </div>
	  <p>				</p>
      <div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">Aktualne dane</div>
       <div class="panel-body">
		   
		  	<!--Tabela przedstawiająca aktualne dane pomiarowe.-->
		    <table class="table table-striped">
			
			<?php

$sql = "SELECT id, mdate, humidity,temperature FROM temp ORDER BY Id DESC LIMIT 1";
$result = $conn->query($sql);
//Nagłówek tabeli
 echo "<thead><tr><th>ID</th><th>Data/Godzina</th><th>Temperatura [ºC]</th><th>Wilgotność powietrza [%]</th></tr></thead>";

if ($result->num_rows > 0) {
   
	echo"<tbody>";
	//Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["mdate"]. "</td><td> " . $row["temperature"]. "</td><td> " . $row["humidity"]. "</td></tr>";
    }
    echo"</tbody>";
} else {
    echo "0 results";
}

$sql = "SELECT id, mdate, ruch,natezenie_swiatla FROM ruch ORDER BY Id DESC LIMIT 1";
$result = $conn->query($sql);
//Nagłówek tabeli
 echo "<thead><tr><th>ID</th><th>Data/Godzina</th><th>Ruch</th><th>Natężenie światła</th></tr></thead>";

if ($result->num_rows > 0) {
    
	echo"<tbody>";
	//Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["mdate"]. "</td><td> " . $row["ruch"]. "</td><td> " . $row["natezenie_swiatla"]. "</td></tr>";
    }
    echo"</tbody>";
} else {
    echo "0 results";
}
$sql = "SELECT id, mdate, gas FROM temp ORDER BY Id DESC LIMIT 1";
$result = $conn->query($sql);
//Nagłówek tabeli
 echo "<thead><tr><th>ID</th><th>Data/Godzina</th><th>Dym i łatwopalne gazy</th><th></th></tr></thead>";

if ($result->num_rows > 0) {
    
	echo"<tbody>";
	//Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["mdate"]. "</td><td> " . $row["gas"]. "</td><td> " . $row[""]. "</td></tr>";
    }
    echo"</tbody>";
} else {
    echo "0 results";
}
?>

</table>

      
      
      
      
      </div>
  </div>
</div>

<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">Panel sterowania</div>
    <div class="panel-body">
    
    
    
    
          <style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: center;
  padding: 16px;
}

tr:nth-child(even) {
  background-color: #f2f2f2
}
</style>
<?php


$sql = "SELECT symulacja, oswietlenie FROM panel_sterowania ";
	
$result = $conn->query($sql);
//Nagłówek tabeli
    echo "<table><tr><th>Oswietlenie wł/wył:</th><th>Symulacja wł/wył:</th></tr>";
  //Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["oswietlenie"]. "</td><td>" . $row["symulacja"]. "</td></tr>";
    }
    echo "</table>";


?> 
 
  <center><h2>Zmień ustawienia:</h2></center>
  <form method="post">
  <div class="btn-group btn-group-justified">
  
	   <!-- Formularz panelu sterowania -->
	  
	  
    <div class="btn-group btn-group-lg">
<input type="submit"  name="przycisk" id="przycisk1"class="btn btn-primary" value="Włącz oświetlenie">
    </div>
    <div class="btn-group  btn-group-lg">
<input type="submit"  name="przycisk" id="przycisk2"class="btn btn-primary" value="Wyłącz oświetlenie">
    </div>
    <div class="btn-group  btn-group-lg">
<input type="submit"  name="przycisk" id="przycisk3"class="btn btn-primary" value="Włącz tryb symulacji obecności">
    </div>
    <div class="btn-group  btn-group-lg">
<input type="submit"  name="przycisk" id="przycisk4"class="btn btn-primary" value="Wyłącz tryb symulacji obecności">
    </div>
   
    
  </div>
  <input type="submit" name="przycisk" id="przycisk5" class="btn btn-primary btn-block btn-danger" value="Usuń wszystkie dane">
  </form>

   
</div>
</div>
</div>






    </div>
     <div id="menu1" class="tab-pane fade">
      <h3>Temperatura, wilgotność powietrza i gaz</h3><div class="text-right">
	  <form method="post"><input type="submit" name="przycisk" id="przycisk7" class="btn btn-danger" value="Usuń dane">
	  </form>
	  <p>				</p>
        <style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: center;
  padding: 16px;
}

tr:nth-child(even) {
  background-color: #f2f2f2
}
</style>
<?php


$sql = "SELECT id, mdate, humidity,temperature FROM temp ORDER BY Id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	//Nagłówek tabeli
    echo "<table><tr><th>ID</th><th>Data/Godzina</th><th>Temperatura [ºC]</th><th>Wilgotność powietrza [%]</th><th>Dym i łatwopalne gazy [x/100]</th></tr>";
    //Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["mdate"]. "</td><td> " . $row["temperature"]. "</td><td> " . $row["humidity"]. "</td><td> " . $row["humidity"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

?> 
     
      
      
      
      
      </div>
    </div>
    <div id="menu2" class="tab-pane fade">
      <h3>Ruch i natężenie światła w pomieszczeniu</h3><div class="text-right">
	  <form method="post"><input type="submit" name="przycisk" id="przycisk6" class="btn btn-danger" value="Usuń zapisane pomiary">
	  </form>
      <p>				</p>
        <style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: center;
  padding: 16px;
}

tr:nth-child(even) {
  background-color: #f2f2f2
}
</style>

<?php


$sql = "SELECT id, mdate, ruch, natezenie_swiatla FROM ruch ORDER BY Id DESC";
$result = $conn->query($sql);



if ($result->num_rows > 0) {
	//Nagłówek tabeli
    echo "<table><tr><th>ID</th><th>Data/Godzina</th><th>Ruch</th><th>Natężenie światła</th></tr>";
    //Wpisanie wszystkich pobranych danych do ciała tabeli.
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["mdate"]. "</td><td> " . $row["ruch"]. "</td><td> " . $row["natezenie_swiatla"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}


?> 
          
		  
		  
<?php
//Zdefiniowanie portu USB do którego podłączone jest XBee
$comPort = "/dev/ttyUSB0";

	//Poniższy warunek jest spełniony gdby wciśnieto przycisk.
if(isset($_POST["przycisk"])){
	$wartosc=$_POST["przycisk"];
		//Instrukcja wyboru zależna od tego, który przycisk został wciśniety.
	switch($wartosc){
	
		//Przycisk 'Aktualizuj'
case 'Aktualizuj';
	//Przesłanie do arduino zapytań o dane.
$fp =fopen($comPort, 'w');
	fwrite($fp,'t');
	fclose($fp);
    break;
		
		
		
		
		
	case 'Włącz oświetlenie';
$sql = "UPDATE panel_sterowania SET oswietlenie='1' WHERE id=0";
$result = $conn->query($sql);
	
	
	$fp =fopen($comPort, 'w');
	fwrite($fp,'w1');
	fclose($fp);
    break;
	
	case 'Wyłącz oświetlenie';
	$sql = "UPDATE panel_sterowania SET oswietlenie='0' WHERE id=0";
$result = $conn->query($sql);

 	$fp =fopen($comPort, 'w');
fwrite($fp,'w0');
fclose($fp);
header("Refresh:0");
break;

case 'Włącz tryb symulacji obecności';

	$sql = "UPDATE panel_sterowania SET symulacja='1' WHERE id=0";
$result = $conn->query($sql);

 	
header("Refresh:0");
break;


case 'Wyłącz tryb symulacji obecności';
	$sql = "UPDATE panel_sterowania SET symulacja='0' WHERE id=0";
$result = $conn->query($sql);

header("Refresh:0");
break;

case 'Usuń dane';
//Przesłanie do bazy danych polecenia usunięcia wszystkich danych z tabeli temp.
	$sql = "DELETE FROM temp";
$result = $conn->query($sql);


break;

case 'Usuń zapisane pomiary';
	$sql = "DELETE FROM ruch";
$result = $conn->query($sql);


break;

case 'Usuń wszystkie dane';
	$sql = "DELETE FROM temp";
$result = $conn->query($sql);
$sql = "DELETE FROM ruch";
$result = $conn->query($sql);


header("Refresh:0");
break;

    
    }
}
$conn->close();
?>        		  
		  
		  
		  
		  
		  
      </div>
      
    </div>
    
    
	
</body>
</html>

  
  
  
