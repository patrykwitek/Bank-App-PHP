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
			if(@$_GET['account']==true){
				@$account_type=$_GET['account'];
				$login = $_SESSION['login'];
				$q = "select nr_konta from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
				$w=$polaczenie->query($q);
				$ile=$w->num_rows;
				$current_account='';
				for($i=0;$i<$ile;$i++){
					$wiersz = $w->fetch_row();
					$current_account=$wiersz[0];
				}
							
				$q="(SELECT CONCAT('+ ',kwota), nr_konta_nadawcy, data, waluta, tytul FROM przelewy WHERE nr_konta_odbiorcy = '".$current_account."') 
					UNION
					(SELECT CONCAT('- ',kwota), nr_konta_odbiorcy, data, waluta, tytul FROM przelewy WHERE nr_konta_nadawcy = '".$current_account."') order by data desc";
				$w=$polaczenie->query($q);
				$ile=$w->num_rows;
				echo "<div class='user_all_transfers'>
						<p id='back'><a href='index.php'>Wróć</a></p>
				";
				for($i=0;$i<$ile;$i++){
					$wiersz = $w->fetch_row();
					$string = $wiersz[0];
					if($string[0] == '+'){
						echo"<div class='transfer_history' id='income'>";
					}
					else if($string[0] == '-'){
						echo"<div class='transfer_history' id='expenditure'>";
					}
					echo "<h4>".$wiersz[0]." ".$wiersz[3]." ".$wiersz[4]."</h4>
						<p id='grey_collor'>".$wiersz[1]."</p>
						<p id='grey_collor'>".$wiersz[2]."</p>
						</div>";
				}
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