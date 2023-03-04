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
				$error = 0;
				if(isset($_POST['send'])){
					$login=$_POST['login'];
					$password=$_POST['password'];
					$password2=$_POST['password_2'];
					if($password != $password2){
						$error = 1;
					}
					else{
					$password = md5($password);
					$zapytanie = 'select * from users where login like "'.$login.'" and password like "'.$password.'"';
					$wynik = mysqli_query($polaczenie, $zapytanie);
					$liczba_wierszy = mysqli_num_rows($wynik);
					if($liczba_wierszy == 1) $_SESSION['login'] = $login;
					}
				}
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
				if($error == 1){
					echo "
					<div class='info'>
						<p id ='logerror'>Błąd: Podane hasła nie zgadzają się ze sobą</p>
						<a href='login.php'>Spróbuj jeszcze raz</a>
					</div>
					";
				}
				else{
					if(@$_SESSION['login'] == "admin"){
						echo "
						<div class='admin_menu'>
							<h3>Administrator</h3>
							<p><a href='users_list.php'>Lista klientów</a></p>
							<p><a href='user_add.php'>Dodaj klienta</a></p>
							<p><a href='add_funds.php'>Dodaj środki</a></p>
							<p><a href='transfers_history.php'>Historia przelewów</a></p>
						</div>
						";
					}
					else if(@$_SESSION['login'] == true){
						$login = $_SESSION['login'];
						$q = "select nr_konta from users inner join konta on users.id=konta.user_id where users.login='".$login."' order by rodzaj";
						$w=$polaczenie->query($q);
						$ile=$w->num_rows;
						
						if(@$_GET['account_display']==true){
							@$account_display=$_GET['account_display'];
						}
						else{
							$account_display=0;
						}
						
						echo "<div class='accounts'>";
						for($i=0;$i<$ile;$i++){
							$wiersz = $w->fetch_row();
							echo "<a href='index.php?account_display=".$i."' class='account_ref'>";
							if($i==$account_display){
								echo "<div class='highlighted'>";
							}
							else{
								echo "<div class='account'>";
							}
							if($i == 0){
								echo "<p>Konto oszczędnościowe</p>";
							}
							else if($i == 1){
								echo "<p>Konto przekorzystne</p>";
							}
							else if($i == 2){
								echo "<p>Konto walutowe</p>";
							}
							echo "<p id='grey_collor'>".$wiersz[0]."</p>";
							echo "</div>";
							echo "</a>";
						}
						echo "</div>";
							
						echo 
						"<div class='user_left_panel'>";
							if(isset($_POST['send_money'])){
								if(@$_GET['method']=='wlasny'){
									$pin=$_POST['pin'];
									$account_display=$_GET['account_display'];
									if($account_display == 0){
										$account_type = 'oszczednosciowe';
									}
									else if($account_display == 1){
										$account_type = 'przekorzystne';
									}
									else if($account_display == 2){
										$account_type = 'walutowe';
									}
									
									$q = "select pin from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
									$w=$polaczenie->query($q);
									$ile=$w->num_rows;
									$pin_check=0;
									for($i=0;$i<$ile;$i++){
										$wiersz = $w->fetch_row();
										$pin_check=$wiersz[0];
									}
									
									if($pin_check==$pin){
										$kwota=$_POST['kwota'];
										
										$q = "select nr_konta from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$nadawca='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$nadawca=$wiersz[0];
										}
										
										$q = "select saldo from konta WHERE nr_konta='".$nadawca."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$saldo='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$saldo=$wiersz[0];
										}
										
										if($kwota > $saldo){
											echo "
											<div class='info'>
												<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
												<p id ='logerror'>Masz za mało środków na koncie</p>
											</div>
											";
										}
										else{
											$odbiorca=$_POST['odbiorca'];

											$rodzaj_konta_odbiorcy = '';
											$q = "select rodzaj from konta where nr_konta='".$odbiorca."'";
											$w=$polaczenie->query($q);
											$ile=$w->num_rows;
											for($i=0;$i<$ile;$i++){
												$wiersz = $w->fetch_row();
												$rodzaj_konta_odbiorcy = $wiersz[0];
											}
											$rodzaj_konta_nadawcy=$account_display;
											
											$kurs_euro=4.71;
											$migration1="";
											$migration2="";
											$add_transfer="";
											$kwota1=$kwota/$kurs_euro;
											$kwota2=$kwota*$kurs_euro;
											
											if($rodzaj_konta_odbiorcy == 'walutowe'){
												$migration1="UPDATE konta SET saldo = saldo + ".$kwota1." WHERE nr_konta='".$odbiorca."'";
												$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
												$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('Przelew własny', '".$nadawca."','".$odbiorca."',".$kwota1.",'".date("Y-m-d H:i:s")."','€')";
											}
											else if($rodzaj_konta_nadawcy == 2){
												$migration1="UPDATE konta SET saldo = saldo + ".$kwota2." WHERE nr_konta='".$odbiorca."'";
												$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
												$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('Przelew własny', '".$nadawca."','".$odbiorca."',".$kwota2.",'".date("Y-m-d H:i:s")."','zł')";
											}
											else{
												$migration1="UPDATE konta SET saldo = saldo + ".$kwota." WHERE nr_konta='".$odbiorca."'";
												$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
												$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('Przelew własny', '".$nadawca."','".$odbiorca."',".$kwota.",'".date("Y-m-d H:i:s")."','zł')";
											}
											
											if($polaczenie->query($migration1)== TRUE && $polaczenie->query($migration2)== TRUE && $polaczenie->query($add_transfer)== TRUE){
												echo "
												<div class='info'>
													<h3 id='success'>Pomyślnie dokonano przelewu</h3>
													<p id='grey_collor'>".$nadawca." &rArr; ".$odbiorca."</p>
												</div>
												";
											}
											else {
												echo "Błąd: " . $add_transfer . "<br>" . $polaczenie->error;
											}
										}
									}
									else{
										echo "
										<div class='info'>
											<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
											<p id ='logerror'>Niepoprawny kod PIN</p>
										</div>
										";
									}
								}
								else if(@$_GET['method']=='zwykly'){
									$pin=$_POST['pin'];
									$account_display=$_GET['account_display'];
									
									if($account_display == 0){
										$account_type = 'oszczednosciowe';
									}
									else if($account_display == 1){
										$account_type = 'przekorzystne';
									}
									else if($account_display == 2){
										$account_type = 'walutowe';
									}
									
									$q = "select pin from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
									$w=$polaczenie->query($q);
									$ile=$w->num_rows;
									$pin_check=0;
									for($i=0;$i<$ile;$i++){
										$wiersz = $w->fetch_row();
										$pin_check=$wiersz[0];
									}
									
									if($pin_check==$pin){
										$kwota=$_POST['kwota'];
										
										$q = "select nr_konta from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$nadawca='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$nadawca=$wiersz[0];
										}
										
										$q = "select saldo from konta WHERE nr_konta='".$nadawca."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$saldo='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$saldo=$wiersz[0];
										}
										
										if($kwota > $saldo){
											echo "
											<div class='info'>
												<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
												<p id ='logerror'>Masz za mało środków na koncie</p>
											</div>
											";
										}
										else{
											$nr_konta_odbiorcy=$_POST['odbiorca'];
											$q = "select * from konta where nr_konta = '".$nr_konta_odbiorcy."'";
											$w=$polaczenie->query($q);
											$ile=$w->num_rows;
											if($ile == 0){
												echo "
												<div class='info'>
													<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
													<p id ='logerror'>Nie znaleziono takiego numeru konta</p>
													<p id ='grey_collor'>Upewnij się czy numer konta został poprawnie wprowadzony</p>
												</div>
												";
											}
											else{
												$tytul=$_POST['tytul'];
												
												$odbiorca=$nr_konta_odbiorcy;
												$rodzaj_konta_odbiorcy = '';
												$q = "select rodzaj from konta where nr_konta='".$odbiorca."'";
												$w=$polaczenie->query($q);
												$ile=$w->num_rows;
												for($i=0;$i<$ile;$i++){
													$wiersz = $w->fetch_row();
													$rodzaj_konta_odbiorcy = $wiersz[0];
												}
												$rodzaj_konta_nadawcy=$account_display;
											
												$kurs_euro=4.71;
												$migration1="";
												$migration2="";
												$add_transfer="";
												$kwota1=$kwota/$kurs_euro;
												$kwota2=$kwota*$kurs_euro;
												
												if($rodzaj_konta_odbiorcy == 'walutowe' && $rodzaj_konta_nadawcy == 2){
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota.",'".date("Y-m-d H:i:s")."','€')";
												}
												else if($rodzaj_konta_odbiorcy == 'walutowe'){
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota1." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota1.",'".date("Y-m-d H:i:s")."','€')";
												}
												else if($rodzaj_konta_nadawcy == 2){
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota2." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota2.",'".date("Y-m-d H:i:s")."','zł')";
												}
												else{
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota.",'".date("Y-m-d H:i:s")."','zł')";
												}
												
												if($polaczenie->query($migration1)== TRUE && $polaczenie->query($migration2)== TRUE && $polaczenie->query($add_transfer)== TRUE){
													echo "
													<div class='info'>
														<h3 id='success'>Pomyślnie dokonano przelewu</h3>
														<p id='grey_collor'>".$nadawca." &rArr; ".$odbiorca."</p>
													</div>
													";
												}
												else {
													echo "Błąd: " . $add_transfer . "<br>" . $polaczenie->error;
												}
											}
										}
									}
									else{
										echo "
										<div class='info'>
											<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
											<p id ='logerror'>Niepoprawny kod PIN</p>
										</div>
										";
									}
								}
								else if(@$_GET['method']=='na_telefon'){
									$pin=$_POST['pin'];
									$account_display=$_GET['account_display'];
									
									if($account_display == 0){
										$account_type = 'oszczednosciowe';
									}
									else if($account_display == 1){
										$account_type = 'przekorzystne';
									}
									else if($account_display == 2){
										$account_type = 'walutowe';
									}
									
									$q = "select pin from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
									$w=$polaczenie->query($q);
									$ile=$w->num_rows;
									$pin_check=0;
									for($i=0;$i<$ile;$i++){
										$wiersz = $w->fetch_row();
										$pin_check=$wiersz[0];
									}
									
									if($pin_check==$pin){
										$kwota=$_POST['kwota'];
										
										$q = "select nr_konta from users inner join konta on users.id=konta.user_id where users.login='".$login."' and rodzaj = '".$account_type."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$nadawca='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$nadawca=$wiersz[0];
										}
										
										$q = "select saldo from konta WHERE nr_konta='".$nadawca."'";
										$w=$polaczenie->query($q);
										$ile=$w->num_rows;
										$saldo='';
										for($i=0;$i<$ile;$i++){
											$wiersz = $w->fetch_row();
											$saldo=$wiersz[0];
										}
										
										if($kwota > $saldo){
											echo "
											<div class='info'>
												<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
												<p id ='logerror'>Masz za mało środków na koncie</p>
											</div>
											";
										}
										else{
											$phone=$_POST['phone'];
											$q = "select * from users where numer_telefonu = '".$phone."'";
											$w=$polaczenie->query($q);
											$ile=$w->num_rows;
											if($ile == 0){
												echo "
												<div class='info'>
													<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
													<p id ='logerror'>Nie znaleziono takiego numeru telefonu</p>
													<p id ='grey_collor'>Upewnij się czy numer telefonu został poprawnie wprowadzony</p>
												</div>
												";
											}
											else{
												$q = "SELECT nr_konta FROM konta INNER JOIN users ON konta.user_id=users.id WHERE numer_telefonu=".$phone." AND rodzaj='przekorzystne'";
												$w=$polaczenie->query($q);
												$ile=$w->num_rows;
												$odbiorca='';
												for($i=0;$i<$ile;$i++){
													$wiersz = $w->fetch_row();
													$odbiorca=$wiersz[0];
												}
												
												$tytul=$_POST['tytul'];
												
												$rodzaj_konta_nadawcy=$account_display;
											
												$kurs_euro=4.71;
												$migration1="";
												$migration2="";
												$add_transfer="";
												$kwota2=$kwota*$kurs_euro;
												
												if($rodzaj_konta_nadawcy == 2){
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota2." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota2.",'".date("Y-m-d H:i:s")."','zł')";
												}
												else{
													$migration1="UPDATE konta SET saldo = saldo + ".$kwota." WHERE nr_konta='".$odbiorca."'";
													$migration2="UPDATE konta SET saldo = saldo - ".$kwota." WHERE nr_konta='".$nadawca."'";
													$add_transfer="INSERT INTO przelewy(tytul, nr_konta_nadawcy, nr_konta_odbiorcy, kwota, data, waluta) VALUES('".$tytul."', '".$nadawca."','".$odbiorca."',".$kwota.",'".date("Y-m-d H:i:s")."','zł')";
												}
												
												if($polaczenie->query($migration1)== TRUE && $polaczenie->query($migration2)== TRUE && $polaczenie->query($add_transfer)== TRUE){
													echo "
													<div class='info'>
														<h3 id='success'>Pomyślnie dokonano przelewu</h3>
														<p id='grey_collor'>".$nadawca." &rArr; ".$odbiorca."</p>
													</div>
													";
												}
												else {
													echo "Błąd: " . $add_transfer . "<br>" . $polaczenie->error;
												}
											}
										}
									}
									else{
										echo "
										<div class='info'>
											<h3 id ='logerror'>Nie udało się dokonać przelewu</h3>
											<p id ='logerror'>Niepoprawny kod PIN</p>
										</div>
										";
									}
								}
							}
						
							$account_type = '';
							if($account_display == 0){
								$account_type = 'oszczednosciowe';
							}
							else if($account_display == 1){
								$account_type = 'przekorzystne';
							}
							else if($account_display == 2){
								$account_type = 'walutowe';
							}
							$q = "select saldo from users inner join konta on users.id=konta.user_id where login='".$login."' and rodzaj='".$account_type."'";
							$w=$polaczenie->query($q);
							$ile=$w->num_rows;
							echo "<h1>Dostępne środki: ";
							for($i=0;$i<$ile;$i++){
								$wiersz = $w->fetch_row();
								echo $wiersz[0];
							}
							if($account_display == 2){
								echo " €";
							}
							else{
								echo " zł";
							}
							echo "</h1></div>";
						
						echo "<div class='user_middle_panel'>";
						if(@$_GET['przelew']==true){
							@$przelew=$_GET['przelew'];
							if($przelew=='wlasny'){
								echo "<h3>Przelew własny</h3>";
								
								$q = "select nr_konta, rodzaj from users inner join konta on users.id=konta.user_id where users.login='".$login."' order by rodzaj";
								$w=$polaczenie->query($q);
								$ile=$w->num_rows;
								
								if($account_display == 0){
									$account_type = 'oszczednosciowe';
								}
								else if($account_display == 1){
									$account_type = 'przekorzystne';
								}
								else if($account_display == 2){
									$account_type = 'walutowe';
								}

								echo"
								<form action='index.php?method=".$przelew."&account_display=".$account_display."' method='post' class='transfer_form'>
									<select name='odbiorca'>";
									for($i=0;$i<$ile;$i++){
										$wiersz = $w->fetch_row();
										if($wiersz[1] != $account_type){
											echo "<option value='".$wiersz[0]."'>".$wiersz[1]."</option>";
										}
									}
								echo"</select>";
								if($account_display != 2){
									echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w zł)' required>";
								}
								else{
									echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w €)' required>";
								}
								echo "
									<input type='password' minlength=4 maxlength=4 name='pin' placeholder='PIN' required>
									<input type='submit' name='send_money' id='button' value='Zatwierdź'>
								</form>
								";
							}
							else if($przelew=='na_telefon'){
								echo "<h3>Przelew na telefon</h3>";
								
								echo"
								<form action='index.php?method=".$przelew."&account_display=".$account_display."' method='post' class='transfer_form'>
									<input type='text' name='tytul' maxlength=15 placeholder='Tytuł przelewu' required>
									<input type='number' min=100000000 max=999999999 name='phone' placeholder='Nr. tel. odbiorcy' required>";
									if($account_display != 2){
										echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w zł)' required>";
									}
									else{
										echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w €)' required>";
									}
									echo"<input type='password' minlength=4 maxlength=4 name='pin' placeholder='PIN' required>
									<input type='submit' name='send_money' id='button' value='Zatwierdź'>
								</form>
								";
							}
							else if($przelew=='zwykly'){
								echo "<h3>Nowy przelew</h3>";
								echo"
								<form action='index.php?method=".$przelew."&account_display=".$account_display."' method='post' class='transfer_form'>
									<input type='text' name='tytul' maxlength=15 placeholder='Tytuł przelewu' required>
									<input type='text' minlength=26 maxlength=26 name='odbiorca' placeholder='Nr. konta odbiorcy' required>";
									if($account_display != 2){
										echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w zł)' required>";
									}
									else{
										echo "<input type='number' min=1 step='0.01' name='kwota' placeholder='Kwota (w €)' required>";
									}
									echo"<input type='password' minlength=4 maxlength=4 name='pin' placeholder='PIN' required>
									<input type='submit' name='send_money' id='button' value='Zatwierdź'>
								</form>
								";
							}
						}
						else{
							echo "
							<h3>Przelewy</h3>
							<ul>
								<li><a href='index.php?przelew=zwykly&account_display=".$account_display."'>Nowy przelew</a></li>
								<li><a href='index.php?przelew=na_telefon&account_display=".$account_display."'>Przelew na telefon</a></li>
								<li><a href='index.php?przelew=wlasny&account_display=".$account_display."'>Przelew własny</a></li>
							</ul>
						";
						}
						echo "</div>";
						
						echo "
						<div class='user_right_panel'>
							<h3>Historia przelewów</h3>";
							
							if($account_display == 0){
								$account_type = 'oszczednosciowe';
							}
							else if($account_display == 1){
								$account_type = 'przekorzystne';
							}
							else if($account_display == 2){
								$account_type = 'walutowe';
							}
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
							if($ile == 0){
								echo "<p>Brak</p>";
							}
							else{
								for($i=0;$i<3;$i++){
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
								if($ile > 3){
									echo"<a href='user_all_transfers.php?account=".$account_type."'>Zobacz więcej</a>";
								}
							}
						echo"</div>";
					}
					else{
						echo "
						<div class='left_panel'>
							<h3>Nasza lokalizacja</h3>
							<iframe src='https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d5121.619481837915!2d19.93921520420639!3d50.07112468308784!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47165b9d21b44827%3A0xc0a45a7baff3fe44!2sStoneX%20Poland%20sp%20z%20o.o.!5e0!3m2!1spl!2spl!4v1668714761079!5m2!1spl!2spl' width='500' height='450' style='border:0;' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>
						</div>
						<div class='right_panel'>
							<h3>Nowości</h3>
							<div id='nowosc'>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc non fermentum lacus. Sed egestas libero risus, sed 
								faucibus leo mollis quis. Aliquam sit amet lorem tempus, sollicitudin enim non, bibendum sem. Quisque a nisi nec elit 
								tincidunt laoreet id et nisl. Fusce rutrum enim id eros facilisis, vel tempor ex efficitur. Duis tincidunt sit amet 
								sem a rutrum. Proin euismod fringilla mauris et sagittis. Sed iaculis eu nunc et accumsan. Mauris pellentesque urna 
								vitae posuere vulputate. Integer varius volutpat nunc at placerat. Sed eu nibh vitae nunc vulputate blandit ut quis 
								metus. Sed quam purus, cursus in hendrerit vel, viverra quis lectus. Fusce ac neque id ex semper efficitur non eget 
								tellus. Maecenas ut ligula id ipsum porttitor tristique id et massa. Etiam hendrerit congue lacus, ut euismod sapien 
								posuere vel. Aenean ultrices euismod tortor sit amet hendrerit.</p>
								<div id='line'></div>
							</div>
							<div id='nowosc'>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc non fermentum lacus. Sed egestas libero risus, sed 
								faucibus leo mollis quis. Aliquam sit amet lorem tempus, sollicitudin enim non, bibendum sem. Quisque a nisi nec elit 
								tincidunt laoreet id et nisl. Fusce rutrum enim id eros facilisis, vel tempor ex efficitur. Duis tincidunt sit amet 
								sem a rutrum. Proin euismod fringilla mauris et sagittis. Sed iaculis eu nunc et accumsan. Mauris pellentesque urna 
								vitae posuere vulputate. Integer varius volutpat nunc at placerat. Sed eu nibh vitae nunc vulputate blandit ut quis 
								metus. Sed quam purus, cursus in hendrerit vel, viverra quis lectus. Fusce ac neque id ex semper efficitur non eget 
								tellus. Maecenas ut ligula id ipsum porttitor tristique id et massa. Etiam hendrerit congue lacus, ut euismod sapien 
								posuere vel. Aenean ultrices euismod tortor sit amet hendrerit.</p>
								<div id='line'></div>
							</div>
							<div id='nowosc'>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc non fermentum lacus. Sed egestas libero risus, sed 
								faucibus leo mollis quis. Aliquam sit amet lorem tempus, sollicitudin enim non, bibendum sem. Quisque a nisi nec elit 
								tincidunt laoreet id et nisl. Fusce rutrum enim id eros facilisis, vel tempor ex efficitur. Duis tincidunt sit amet 
								sem a rutrum. Proin euismod fringilla mauris et sagittis. Sed iaculis eu nunc et accumsan. Mauris pellentesque urna 
								vitae posuere vulputate. Integer varius volutpat nunc at placerat. Sed eu nibh vitae nunc vulputate blandit ut quis 
								metus. Sed quam purus, cursus in hendrerit vel, viverra quis lectus. Fusce ac neque id ex semper efficitur non eget 
								tellus. Maecenas ut ligula id ipsum porttitor tristique id et massa. Etiam hendrerit congue lacus, ut euismod sapien 
								posuere vel. Aenean ultrices euismod tortor sit amet hendrerit.</p>
							</div>
						</div>
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