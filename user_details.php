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
				else echo "
				<a href='login.php' class='odnosnik'>
					<div>
						<p>Logowanie</p>
					</div>
				</a>
				";
			?>
		</header>
		<main>
			<div class="details">
				<?php
					if(isset($_POST['chg_psswd'])){
						$psswd1=$_POST['psswd1'];
						$psswd2=$_POST['psswd2'];
						if($psswd1 != $psswd2){
							echo "
							<div class='info'>
								<h3 id='logerror'>Nie udało się zmienić hasła</h3>
								<p id='logerror'>Wprowadzone hasła nie zgadzają się ze sobą</p>
							</div>
							";
						}
						else if(@$_GET['id']==true){
							@$id=$_GET['id'];
							$psswd1 = md5($psswd1);
							$edit="UPDATE users SET password = '".$psswd1."' WHERE id = ".$id;
							if($polaczenie->query($edit)== TRUE){
								echo "<h3 id='success'>Pomyślnie zmieniono hasło</h3>";
							}
							else {
								echo "Błąd: " . $edit . "<br>" . $polaczenie->error;
							}
						}
					}
					
					if(isset($_POST['edit_pin'])){
						$pin1=$_POST['pin1'];
						$pin2=$_POST['pin2'];
						if($pin1 != $pin2){
							echo "
							<div class='info'>
								<h3 id='logerror'>Nie udało się zmienić kodu PIN</h3>
								<p id='logerror'>Wprowadzone kody nie zgadzają się ze sobą</p>
							</div>
							";
						}
						else if(@$_GET['id']==true){
							@$id=$_GET['id'];
							$edit="UPDATE users SET pin = '".$pin1."' WHERE id = ".$id;
							if($polaczenie->query($edit)== TRUE){
								echo "<h3 id='success'>Pomyślnie zmieniono kod PIN</h3>";
							}
							else {
								echo "Błąd: " . $edit . "<br>" . $polaczenie->error;
							}
						}
					}
				
					$login = $_SESSION['login'];
					$q="select * from users where login = '".$login."'";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						echo
						"<h1>".$wiersz[1]."</h1>
						 <p>ID użytkownika: ".$wiersz[0]."</p>
						 <p>Imie: ".$wiersz[4]."</p>
						 <p>Nazwisko: ".$wiersz[5]."</p>
						 <p>Hasło: <a href='chg_pssw.php?id=".$wiersz[0]."'>Zmień hasło</a></p>
						 <p>PIN: ";
							for($i=0;$i<4;$i++){
								echo "*";
							}
						 echo " <a href='edit_pin.php?id=".$wiersz[0]."'>Zmień PIN</a></p>
						 <p>Adres zamieszkania: ".$wiersz[6]."</p>
						 <p>Miasto: ".$wiersz[7]."</p>
						 <p>Email: ".$wiersz[8]."</p>
						 <p>Numer telefonu: ".$wiersz[9]."</p>";
					}
					$polaczenie->close();
				?>
			</div>
		</main>
		<footer>
			<p>Patryk Witek 2022</p>		
		</footer>
	</body>
</html>