<?php
#**********************************************************************************#



				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#
				
				// Session fortführen
				// session_start() legt eine neue Session an, ODER führt eine bestehende Session fort
				// session_start() holt sich das Session-Cookie vom Browser und vergleicht, ob es eine 
				// passende Session dazu auf dem Server gibt. Falls ja, wird diese Session fortgeführt;
				// falls nein (Cookie existiert nicht/Session existiert nicht), wird eine neue Session angelegt
				session_name("blog");
				session_start();
/*				
if(DEBUG)	echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)	print_r($_SESSION);					
if(DEBUG)	echo "</pre>";	
*/

#**********************************************************************************#


				#*********************************#
				#********** SECURE PAGE **********#
				#*********************************#
				//echo($_SESSION);
				
				
				if( !isset($_SESSION['usr_id']) ) {
					// Leere Session löschen
					session_destroy();
				}



#**********************************************************************************#


				#***********************************#
				#********** CONFIGURATION **********#
				#***********************************#
				
				// include(Pfad zur Datei): Bei Fehler wird das Skript weiter ausgeführt. Problem mit doppelter Einbindung derselben Datei
				// require(Pfad zur Datei): Bei Fehler wird das Skript gestoppt. Problem mit doppelter Einbindung derselben Datei
				// include_once(Pfad zur Datei): Bei Fehler wird das Skript weiter ausgeführt. Kein Problem mit doppelter Einbindung derselben Datei
				// require_once(Pfad zur Datei): Bei Fehler wird das Skript gestoppt. Kein Problem mit doppelter Einbindung derselben Datei
				require_once("include/config.inc.php");
				require_once("include/form.inc.php");
				require_once("include/db.inc.php");
				require_once("include/dateTime.inc.php");



#**********************************************************************************#


				
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				
				
				
				$categoryId = NULL;
				
				$errorLogin = NULL;
				
			

#**********************************************************************************#



				#**********************************************#
				#********** DB-VERBINDUNG HERSTELLEN **********#
				#**********************************************#
				
				$pdo = dbConnect("blog");


#**********************************************************************************#

				#**********************************************#
				#********** KATEGORIES AUSLESEN ***************#
				#**********************************************#


				// Schritt 2 DB: SQL-Statement vorbereiten
				$statement = $pdo->prepare("SELECT * FROM categories");
					
				// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
				$statement->execute();
if(DEBUG)	if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>";
					
				// Schritt 4 DB: Daten weiterverarbeiten
				// In diesem Fall: Die IDs aus der Tabelle Werke auslesen und für spätere Verwendung
				// (Erzeugen einer Select-Box im Formular) in ein Array speichern
				$categoriesArray = $statement->fetchAll();
				
				


			
#**********************************************************************************#




/*
if(DEBUG)	echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)	print_r($_POST);					
if(DEBUG)	echo "</pre>";
*/

				#**********************************#
				#********** PROCESS FORM **********#
				#**********************************#
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formsentIndex']) ) {
if(DEBUG)		echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Login wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
					
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
					$email 			= cleanString( $_POST['email'] );
					$password 		= cleanString( $_POST['password'] );
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$email: $email <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$password: $password <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					// Schritt 3 FORM: Werte validieren
					$errorEmail 		= checkEmail($email);
					$errorPassword 	= checkInputString($password);
					
					
					#********** ERROR MESSAGE **********#
					if( $errorEmail OR $errorPassword ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält Fehler! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
						$errorLogin = "Benutzername oder Passwort falsch!";
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei und wird nun verarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
						
						// Schritt 4 FORM: Daten weiterverarbeiten
						
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						
						#********** DATENSATZ ZUM ACCOUNTNAMEN AUSLESEN **********#
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare("SELECT * FROM users
															 WHERE usr_email = :ph_usr_email");
															 
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						$statement->execute( array("ph_usr_email" => $email) );
if(DEBUG)			if($statement->errorInfo()[2]) echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: " . $statement->errorInfo()[2] . " <i>(" . basename(__FILE__) . ")</i></p>\r\n";														
						
						// Schritt 4 DB: Daten weiterverarbeiten
						// Bei lesendem Zugriff: Datensätze abholen
						$row = $statement->fetch(PDO::FETCH_ASSOC);

/*						
if(DEBUG)			echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)			print_r($row);					
if(DEBUG)			echo "</pre>";	

						echo gettype($row);
*/						
						
						#********** 1. VERIFY ACCOUNTNAME **********#
						// Prüfen, ob ein Datensatz zurückgeliefert wurde
						// Wenn ein Datensatz geliefert wurde, muss der Email stimmen
						if( !$row ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Email '$email' existiert nicht in der DB! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
							$errorLogin = "Benutzername oder Passwort falsch!";
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Email '$email' existiert in der DB. <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
							
							
							#********** 2. VERIFY PASSWORD **********#
							if( !password_verify( $password, $row['usr_password'] ) ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Passworte stimmen nicht überein! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
								$errorLogin = "Benutzername oder Passwort falsch!";
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Passworte stimmen überein. <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
								
								
								#********** PROCESS LOGIN **********#
if(DEBUG)					echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Login wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\r\n";
									
									
								#********** START SESSION **********#
								session_name("blog");
								session_start();
									
								$_SESSION['usr_id'] = $row['usr_id'];
								$_SESSION['usr_firstname'] = $row['usr_firstname'];
								$_SESSION['usr_lastname'] = $row['usr_lastname'];

									
/*								
if(DEBUG)					echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)					print_r($_SESSION);					
if(DEBUG)					echo "</pre>";									
*/									

								#********** REDIRECT TO profile.php **********#
								header("Location: dashboard.php");
								
							} // 2. VERIFY PASSWORD END
							
						} // 1. VERIFY ACCOUNTNAME END
						
					} // FINAL FORM VALIDATION END
					
				} // PROCESS FORM END
				

#**********************************************************************************#

				#***********************************************#
				#********** URL-PARAMETERVERARBEITUNG **********#
				#***********************************************#
					
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurden
				if( isset($_GET['action']) ) {				
if(DEBUG)		echo "<p class='debug'>URL-Parameter <i>action</i> wurde übergeben.</p>";
					
					// Schritt 1 DB: DB-Verbindung herstellen
					// Ist bereits geschehen

					// Schritt 2 URL: Parameter-Wert auslesen, entschärfen, DEBUG-Ausgabe
					$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// Schritt 3 URL: Parameter auswerten: Verzweigung
					
					#***** LOGOUT *****#
					if( $action == "logout"){
						echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Logout wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\r\n";

						session_destroy();

						// Neuladen der Index.php, da action nicht mehr verfügbar ist
						header("Location: index.php");
						exit;
					
					}
					
				}
				
				
#**********************************************************************************#

				if( isset($_GET['category']) ) {				
if(DEBUG)		echo "<p class='debug'>URL-Parameter <i>category</i> wurde übergeben.</p>";
					
					// Schritt 1 DB: DB-Verbindung herstellen
					// Ist bereits geschehen

					// Schritt 2 URL: Parameter-Wert auslesen, entschärfen, DEBUG-Ausgabe
					$categoryId = cleanString($_GET['category']);
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$categoryId: $categoryId <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// Schritt 3 URL: Parameter auswerten: Verzweigung
					
				}	

#**********************************************************************************#

				$postsArray = NULL;
				
				if(!$categoryId){
					$statement = $pdo->prepare("SELECT * FROM blogs
													INNER JOIN categories USING(cat_id)
													INNER JOIN users USING(usr_id)
													ORDER BY blog_date DESC");

				
					$statement->execute();
					$postsArray = $statement->fetchAll(PDO::FETCH_ASSOC);

				} else {
					$statement = $pdo->prepare("SELECT * FROM blogs
													INNER JOIN categories USING(cat_id)
													INNER JOIN users USING(usr_id)
													WHERE cat_id=:ph_cat_id
													ORDER BY blog_date DESC");

				
					$statement->execute( array( "ph_cat_id" =>$categoryId) );
					$postsArray = $statement->fetchAll(PDO::FETCH_ASSOC);
				}
/*				
if (DEBUG)  echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";
if (DEBUG)  print_r($postsArray);
if (DEBUG)  echo "</pre>";
*/				
				
				
				
				
		
#**********************************************************************************#


#**********************************************************************************#
?>

<!doctype html>

	<html>
		<head>
			<meta charset="utf-8">
			<title>PHP-Projekt Blog</title>
			
			<link rel="stylesheet" href="css/main.css">
			<link rel="stylesheet" href="css/debug.css">
		</head>
	
		<body>
		
			<!-- -------- PAGE HEADER -------- -->
			<br>
			<header class="fright">
			
			<br>
			<br>
			<br>
			<br>
				<?php if (!isset($_SESSION["usr_id"])): ?>
				<!-- -------- LOGIN FORM -------- -->
				<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
					<input type="hidden" name="formsentIndex">
					<fieldset>
						<legend>Login</legend>					
						<span class='error'><?= $errorLogin ?></span><br>
						<input class="short" type="text" name="email" placeholder="Email">
						<input class="short" type="password" name="password" placeholder="Passwort">
						
						<input class="short" type="submit" value="Login">
					</fieldset>
					
				</form>
				<!-- -------- LOGIN FORM END -------- -->
				<?php else: ?>
				<p class="fright"><a href="?action=logout"><< Logout</a></p><br>
				<p class="fright"><a href="dashboard.php"> zur blog seite>></a></p><br>
				<?php endif ?>
				
				
				
				
			</header>
			<div class="clearer"></div>
			
			<hr>
			<!-- -------- PAGE HEADER END -------- -->
			
			<h1>PHP-Projekt Blog</h1>
			<p class="alleAnzeige"><a href = "index.php">Alle Antrage anzeigen</a></p>
			
			
			
			<!-- --------------- BLOG ENTRIES ------------ -->
			
			<main class='blogs fleft' style="width:60%">
			
			<?php foreach ($postsArray as $post): ?>
				<article class='blogEntry' >
					<p class=fright>Kategorie: <?= $post['cat_name']?> </p>
					<h3 class="userTitle"><?= $post['blog_headline'] ?></h3> 
					<span class="userdaten"><?= $post['usr_firstname'] ?> <?= $post['usr_lastname'] ?> (<?= $post['usr_city'] ?>) schrieb am <?= isoToEuDateTime($post["blog_date"])["date"] ?> um <?= isoToEuDateTime($post["blog_date"])["time"] ?> Uhr:</span>
					<br>
					<div class="clearfix">
						<?php if($post['blog_imagePath']): ?>
							
							<img class="f<?= $post['blog_imageAlignment'] ?>" src="<?=$post['blog_imagePath'] ?>" style="max-width:300px">
						<?php endif ?>
						<p class="contentText"><?= $post['blog_content'] ?></p>
					</div>
					
				</article>
				<hr>
			<?php endforeach ?>
			
			
			
			</main>
			
			<!-- ---------- CATEGORIES --------------- -->
		
			<nav class="categories fright" style="width:25% ">
			
			
																	<?php if (!is_array($categoriesArray)): ?>
																		<span class="error"><?= $categoriesArray ?></span>
																	<?php else: ?>
																			<ul class="categories" >
																		<?php foreach ($categoriesArray as $categorieResults): ?>
																				<li class="lists">
																					<a href="?category=<?= $categorieResults["cat_id"] ?>"><?= $categorieResults["cat_name"] ?></a>
																				</li>
																		<?php endforeach; ?>
																		</ul>
																	<?php endif ?>
																	
		
		
		
				</nav>
		
		
			<!-- ---------- CATEGORIES ENDE --------------- -->
		
		
			<div class='clearer'></div>
		
		
		
		
			<br>
			<br>
			<br>
			<br>
			<br>

			
		</body>
	</html>