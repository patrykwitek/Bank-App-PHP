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
				if(@$_SESSION['login'] == "admin"){
					echo "
					<form action='users_list.php' method='post' class='form'>
						Login: <input type='text' name='login' required><br>
						Hasło: <input type='password' name='password' required><br>
						PIN: <input type='password' minlength=4 maxlength=4 name='pin' required><br>
						Imię: <input type='text' name='name' required><br>
						Nazwisko: <input type='text' name='surname' required><br>
						Adres zamieszkania: <input type='text' name='address'><br>
						Miasto: <input type='text' name='city'><br>
						E-Mail: <input type='text' name='email'><br>
						nr. tel.: <input type='number' min=100000000 max=999999999 name='phone' required><br>
						nr. konta przekorzystnego: <input type='text' minlength=26 maxlength=26 name='konto_przekorzystne' required>
						nr. konta oszczędnościowego: <input type='text' minlength=26 maxlength=26 name='konto_oszczednosciowe' required>
						nr. konta walutowego: <input type='text' minlength=26 maxlength=26 name='konto_walutowe' required><br>
						<input type='submit' name='add_user' id='add_button' value='Dodaj klienta'>
					</form>
					";
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
		<footer>
			<p>Patryk Witek 2022</p>		
		</footer>
	</body>
</html>