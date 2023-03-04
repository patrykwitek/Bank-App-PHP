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
				<a href='login.php' class='odnosnik' id='first'>
					<div>
						<p>Logowanie</p>
					</div>
				</a>
				";
			?>
		</header>
		<main>
			<?php
				if(@$_SESSION['login'] == "admin"){
					if(@$_GET['orderby']==true){
						@$orderby=$_GET['orderby'];
						$q="(SELECT nr_konta_nadawcy, nr_konta_odbiorcy, tytul, CONCAT(kwota,' ', waluta), data FROM przelewy order by ".$orderby." desc)";
					}
					else{
						$q="(SELECT nr_konta_nadawcy, nr_konta_odbiorcy, tytul, CONCAT(kwota,' ', waluta), data FROM przelewy order by data desc)";
					}
					$w=$polaczenie->query($q);
					$ile=$w->num_rows;
					echo "<div class='all_transfers'>
							<p id='back'><a href='index.php' id='undo'>Wróć</a></p>
					";
					echo "<table>";
					echo "<tr>";
					echo 	"<th><a href='transfers_history.php?orderby=nr_konta_nadawcy'>Nr. konta nadawcy</a></th>";
					echo 	"<th><a href='transfers_history.php?orderby=nr_konta_odbiorcy'>Nr. konta odbiorcy</a></th>";
					echo 	"<th><a href='transfers_history.php?orderby=tytul'>Tytuł</a></th>";
					echo 	"<th><a href='transfers_history.php?orderby=kwota'>Kwota</a></th>";
					echo 	"<th><a href='transfers_history.php?orderby=data'>Data</a></th>";
					echo "</tr>";
					for($i=0;$i<$ile;$i++){
						$wiersz = $w->fetch_row();
						echo "<tr>";
						echo 	"<td>".$wiersz[0]."</td>";
						echo 	"<td>".$wiersz[1]."</td>";
						echo 	"<td>".$wiersz[2]."</td>";
						echo 	"<td>".$wiersz[3]."</td>";
						echo 	"<td>".$wiersz[4]."</td>";
						echo "</tr>";
					}
					echo "</table>";
					echo "</div>";
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