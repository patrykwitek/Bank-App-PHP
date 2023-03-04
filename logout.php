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
		</header>
		<main>
			<?php
				if(@$_GET['user']==true){
					@$user=$_GET['user'];
					$q="SELECT name FROM users WHERE login='".$user."'";
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					$imie='';
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						$imie=$wiersz[0];
					}
					if(substr($imie, -1) == 'a'){
						echo "
						<div class='info'>
							<p>Zostałaś wylogowana</p>
							<a href='index.php'>Powrót do strony głównej</a>
						</div>
						";
					}
					else{
						echo "
						<div class='info'>
							<p>Zostałeś wylogowany</p>
							<a href='index.php'>Powrót do strony głównej</a>
						</div>
						";
					}
				}
				session_destroy();
			?>
		</main>
		<footer>
			<p>Patryk Witek 2022</p>
		</footer>
	</body>
</html>