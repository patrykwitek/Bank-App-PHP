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
				if(@$_SESSION['login'] == true && @$_GET['id']==true){
					@$id=$_GET['id'];
					echo "
					<form action='user_details.php?id=".$id."' method='post' class='form'>
						Wprowadź nowy PIN: <input type='password' name='pin1'><br/>
						Wprowadź ponownie nowy PIN: <input type='password' name='pin2'><br/>
						<input type='submit' name='edit_pin' id='add_button' value='Zmień PIN'>
					</form>
					";
				}
			?>
		</main>
		<footer>
			<p>Patryk Witek 2022</p>		
		</footer>
	</body>
</html>