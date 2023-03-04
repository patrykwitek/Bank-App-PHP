<?php
	session_start();
?>
<!DOCTYPE HTML>
<html lang="pl">
	<head>
		<meta charset="utf-8">
		<title>Bank | StoneX</title>
		<link rel="stylesheet" href="style.css">
		<link rel="icon" href="photos/logo.png">
	</head>
	<body>
		<?php
			$polaczenie = new mysqli ('localhost','root','','bank_stonex');
			$polaczenie -> select_db('bank_stonex');
			if($polaczenie->connect_error){
				echo "Nie udalo sie nazwiazac polaczenia";
			}
		?>
		<header>
			<a href="index.php" class="logo"><img src="photos/logo.png" alt="logo"></a>
			<?php
				if(@$_SESSION['login'] == true){
					echo "<a href='user_details.php'><img src='photos/user_icon.png' alt='user_icon' id='log_as'></a>";
					echo "
					<a href='logout.php?user=".$_SESSION['login']."' class='odnosnik'>
						<div>
							<p>Wyloguj</p>
						</div>
					</a>
					";
				}
			?>
		</header>
		<main>
			<?php
				$error=0;
				if(isset($_POST['add_user'])){
					$login=$_POST['login'];
					$q="select login from users";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						if($login==$wiersz[0]){
							$error=1;
						}
					}
					
					$phone=$_POST['phone'];
					$q="select numer_telefonu from users";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						if($phone==$wiersz[0]){
							$error=2;
						}
					}
					
					$konto_przekorzystne=$_POST['konto_przekorzystne'];
					$konto_oszczednosciowe=$_POST['konto_oszczednosciowe'];
					$konto_walutowe=$_POST['konto_walutowe'];
					$q="select nr_konta from konta";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						if($konto_przekorzystne==$wiersz[0]){
							$error=3;
						}
						if($konto_oszczednosciowe==$wiersz[0]){
							$error=4;
						}
						if($konto_walutowe==$wiersz[0]){
							$error=5;
						}
					}
					if($error != 0){
						echo "<div class='info'>
								<h3 id ='logerror'>Nie udało się dodać nowego użytkownika</h3>
								<p id ='logerror'>Ten ";
						if($error==1){
							echo "login ";
						}
						else if($error==2){
							echo "numer telefonu ";
						}
						else if($error==3){
							echo "numer konta przekorzystnego ";
						}
						else if($error==4){
							echo "numer konta oszczędnościowego ";
						}
						else if($error==5){
							echo "numer konta walutowego ";
						}
						echo "jest już w użyciu</p></div>";
					}
					else{
						$password=$_POST['password'];
						$password=md5($password);
						$pin=$_POST['pin'];
						$name=$_POST['name'];
						$surname=$_POST['surname'];
						$address=$_POST['address'];
						$city=$_POST['city'];
						$email=$_POST['email'];
						$phone=$_POST['phone'];
						$konto_przekorzystne=$_POST['konto_przekorzystne'];
						$konto_oszczednosciowe=$_POST['konto_oszczednosciowe'];
						$konto_walutowe=$_POST['konto_walutowe'];
						$add_user="INSERT INTO users (login, password, pin, name, surname, address, city, email, numer_telefonu, nr_konta_przekorzystnego, nr_konta_oszczednosciowego, nr_konta_walutowego) VALUES ('".$login."', '".$password."', '".$pin."', '".$name."', '".$surname."', '".$address."', '".$city."', '".$email."', '".$phone."', '".$konto_przekorzystne."', '".$konto_oszczednosciowe."', '".$konto_walutowe."')";
						if($polaczenie->query($add_user)=== TRUE){
							echo "
							<div class='info'>
								<h3 id ='success'>Pomyślnie dodano klienta</h3>
							</div>
							";
						}
						
						
						$q="select * from users where nr_konta_przekorzystnego = '".$konto_przekorzystne."'";
						$w=$polaczenie->query($q);
						$ile=$w->num_rows;
						for($i=0;$i<$ile;$i++){
							$wiersz = $w->fetch_row();
							$find_id=$wiersz[0];
						}
						
						$add_konto_przekorzystne="INSERT INTO konta VALUES ('".$konto_przekorzystne."',0,'przekorzystne',".$find_id.")";
						$add_konto_oszczednosciowe="INSERT INTO konta VALUES ('".$konto_oszczednosciowe."',0,'oszczednosciowe',".$find_id.")";
						$add_konto_walutowe="INSERT INTO konta VALUES ('".$konto_walutowe."',0,'walutowe',".$find_id.")";
						
						$polaczenie->query($add_konto_przekorzystne);
						$polaczenie->query($add_konto_oszczednosciowe);
						$polaczenie->query($add_konto_walutowe);
					}
				}
				
				if(@$_GET['id']==true){
					@$id=$_GET['id'];
					$del_user="DELETE FROM users WHERE id=".$id;
					$del_przekorzystne="DELETE FROM konta WHERE user_id=".$id;
					$del_oszczednosciowe="DELETE FROM konta WHERE user_id=".$id;
					$del_walutowe="DELETE FROM konta WHERE user_id=".$id;
					if($polaczenie->query($del_user)== TRUE && $polaczenie->query($del_przekorzystne)== TRUE && $polaczenie->query($del_oszczednosciowe)== TRUE && $polaczenie->query($del_walutowe)== TRUE){
						echo "
						<div class='info'>
							<h3 id ='success'>Pomyślnie usunięto klienta o identyfikatorze ".$id."</h3>
						</div>
						";
					}
					else echo "Błąd: ".$del_user."<br/>".$polaczenie->error;
				}
				
				if(isset($_POST['edit_user'])){
					$id=$_POST['id'];
					$login=$_POST['login'];
					$pin=$_POST['pin'];
					$name=$_POST['name'];
					$surname=$_POST['surname'];
					$address=$_POST['address'];
					$city=$_POST['city'];
					$email=$_POST['email'];
					$phone=$_POST['phone'];
					$edit_user="UPDATE users SET login = '".$login."', pin = '".$pin."', name = '".$name."', surname = '".$surname."', address = '".$address."', city = '".$city."', email = '".$email."', numer_telefonu = '".$phone."' WHERE id = ".$id;
					if($polaczenie->query($edit_user)== TRUE){
						echo "
						<div class='info'>
							<h3 id='success'>Pomyślnie zaktualizowano dane klienta</h3>
						</div>
						";
					}
					else {
						echo "Błąd: " . $edit . "<br>" . $polaczenie->error;
					}
				}
				
				if(@$_SESSION['login'] == "admin"){
					$q="select id, login, pin, name, surname, address, city, email, numer_telefonu, nr_konta_przekorzystnego, nr_konta_oszczednosciowego, nr_konta_walutowego from users";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					echo "<table class='users_list'>";
					echo "<tr>";
					echo "<th>id</th>";
					echo "<th>login</th>";
					echo "<th>PIN</th>";
					echo "<th>Imię</th>";
					echo "<th>Nazwisko</th>";
					echo "<th>adres zamieszkania</th>";
					echo "<th>miasto</th>";
					echo "<th>e-mail</th>";
					echo "<th>nr. tel.</th>";
					echo "<th>konto przekorzystne</th>";
					echo "<th>konto oszczędnościowe</th>";
					echo "<th>konto walutowe</th>";
					echo "</tr>";
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						echo "<tr>";
						for($j=0;$j<12;$j++){
							echo "<td>".$wiersz[$j]."</td>";
						}
						if($wiersz[1] != "admin"){
						echo "<td id='edit_button'><a href='user_edit.php?id=".$wiersz[0]."'>Edytuj</a></td>";
						echo "<td id='remove_button'><a href='users_list.php?id=".$wiersz[0]."'>Usuń</a></td>";
						}
						echo "</tr>";
					}
					echo "</table>";
					$polaczenie->close();
				}
				else{
					echo "
					<div class='info'>
						<p id ='logerror'>Nie posiadasz uprawnień do otwarcia tej strony</p>
						<a href='index.php'>Strona główna</a>
					</div>
					";
				}
			?>
		</main>
	</body>
</html>