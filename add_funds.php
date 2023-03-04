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
					if(isset($_POST['search'])){
						$phrase=$_POST['login'];
						echo "
						<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
						<form class='search' action='add_funds.php' method='post'>
							<input type='text' value='".$phrase."' placeholder='Wyszukaj...' name='login'>
							<button type='submit' name='search'><i class='fa fa-search'></i></button>
						</form>
						";
						$q = "select login, nr_konta, rodzaj from users inner join konta on users.id=konta.user_id where login like '%".$phrase."%' AND id != 1";
						$w=$polaczenie->query($q);
						$ile=$w->num_rows;
						echo "<div class='search_menu'>";
						if($ile==0){
							echo "<p>Brak użytkowników o określonym loginie</p>";
						}
						else{
							for($i=0;$i<$ile;$i++){
								$wiersz = $w->fetch_row();
								echo "
								<p id='break'>
									<a href='add_funds.php?nr_konta=".$wiersz[1]."'>
										<div>
											<h4>".$wiersz[0]."</h4>
											<p id='grey_collor'>".$wiersz[1]."</p>
											<p id='grey_collor'>".$wiersz[2]."</p>
										</div>
									</a>
								</p>
								";
							}
						}
						echo "</div>";
					}
					else if(@$_GET['nr_konta']==true){
						@$nr_konta=$_GET['nr_konta'];
						echo "
						<div class='info'>
							<h3>Dodawanie środków</h3>
						</div>
						<form action='add_funds.php' method='post' class='form'> Podaj kwotę do przelania: ";
						
						$q = "select rodzaj from konta where nr_konta = '".$nr_konta."'";
						$w=$polaczenie->query($q);
						$ile=$w->num_rows;
						$rodzaj = '';
						for($i=0;$i<$ile;$i++){
							$wiersz = $w->fetch_row();
							$rodzaj = $wiersz[0];
						}
						if($rodzaj == 'walutowe'){
							echo
							"<input type='number' min=1 name='kwota' placeholder='w euro' required><br>
							<input type='submit' name='funds' id='add_button' value='Przelej'><br>
							<input type='text' name='waluta' value='€' id='unvisible'>
							<input type='text' name='konto' value='".$nr_konta."' id='unvisible'>
							</form>
							";
						}
						else{
							echo
							"<input type='number' min=1 name='kwota' placeholder='w złotówkach' required><br>
							<input type='submit' name='funds' id='add_button' value='Przelej'><br>
							<input type='text' name='waluta' value='zł' id='unvisible'>
							<input type='text' name='konto' value='".$nr_konta."' id='unvisible'>
							</form>
							";
						}
					}
					else{
						if(isset($_POST['funds'])){
							$waluta=$_POST['waluta'];
							$kwota=$_POST['kwota'];
							$konto=$_POST['konto'];
							$send_funds="UPDATE konta SET saldo = '".$kwota."' WHERE nr_konta='".$konto."'";
							$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('Dodanie środków', '12345678910111213141516171','".$konto."',".$kwota.",'".date("Y-m-d H:i:s")."','".$waluta."')";
							
							if($polaczenie->query($send_funds) == TRUE && $polaczenie->query($add_transfer) == TRUE){
								echo "
								<div class='info'>
									<h3 id ='success'>Pomyślnie przelano ".$kwota." ".$waluta." na konto ".$konto."</h3>
								</div>
								";
							}
						}
						echo "
						<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
						<form class='search' action='add_funds.php' method='post'>
							<input type='text' placeholder='Wyszukaj...' name='login'>
							<button type='submit' name='search'><i class='fa fa-search'></i></button>
						</form>
						";
						
						$q = "select login, nr_konta, rodzaj from users inner join konta on users.id=konta.user_id where id != 1";
						$w=$polaczenie->query($q);
						$ile=$w->num_rows;
						echo "<div class='search_menu'>";
						for($i=0;$i<$ile;$i++){
							$wiersz = $w->fetch_row();
							echo "
							<p id='break'>
								<a href='add_funds.php?nr_konta=".$wiersz[1]."'>
									<div>
										<h4>".$wiersz[0]."</h4>
										<p id='grey_collor'>".$wiersz[1]."</p>
										<p id='grey_collor'>".$wiersz[2]."</p>
									</div>
								</a>
							</p>
							";
						}
						echo "</div>";
					}
				}
				else{
					echo "
					<div class='info'>
						<p id ='logerror'>Nie posiadasz wystarczających uprawnień do otwarcia tej strony</p>
						<a href='index.php'>Strona główna</a>
					</div>
					";
				}
			?>
		</main>
	</body>
</html>