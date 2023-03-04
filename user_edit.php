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
				if(@$_SESSION['login'] == "admin" && @$_GET['id']==true){
					@$id=$_GET['id'];
					$q="select * from users where id = '".$id."'";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						$find_id=$wiersz[0];
						
						echo "
						<form action='users_list.php' method='post' class='form'>
							<input type='text' name='id' value='".$wiersz[0]."' id='unvisible'><br>
							Login: <input type='text' name='login' value='".$wiersz[1]."'><br>
							PIN: <input type='number' name='pin' value=".$wiersz[3]."><br>
							Imię: <input type='text' name='name' value='".$wiersz[4]."'><br>
							Nazwisko: <input type='text' name='surname' value='".$wiersz[5]."'><br>
							Adres zamieszkania: <input type='text' name='address' value='".$wiersz[6]."'><br>
							Miasto: <input type='text' name='city' value='".$wiersz[7]."'><br>
							E-Mail: <input type='text' name='email' value='".$wiersz[8]."'><br>
							nr. tel.: <input type='number' name='phone' value=".$wiersz[9]."><br>
							<input type='submit' name='edit_user' id='add_button' value='Edytuj'>
						</form>
						";
					}
				}
			?>
		</main>
		<footer>
			<p>Patryk Witek 2022</p>		
		</footer>
	</body>
</html>