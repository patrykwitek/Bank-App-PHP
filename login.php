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
			<a href='index.php' class='odnosnik' id='first'>
				<div>
					<p>Wróć</p>
				</div>
			</a>
		</header>
		<main>
			<form action="index.php" method="post" class="form">
				<input type="text" name="login" id="login" placeholder="Podaj login"><br/>
				<input type="password" name="password" id="password" placeholder="Podaj hasło"><br/>
				<input type="password" name="password_2" id="password_2" placeholder="Powtórz hasło"><br/>
				<input type="submit" name="send" id="button" value="Zaloguj">
			</form>
		</main>
		<footer>
			<p>Patryk Witek 2022</p>		
		</footer>
	</body>
</html>