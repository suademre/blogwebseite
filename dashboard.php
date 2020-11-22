<?php

#**********************************************************************************#
				
				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#

				// Session fortführen
				session_name("blog");
				session_start();

				// Secure Page

				if (!isset($_SESSION["usr_id"])) {
				session_destroy();
				header("Location: index.php");
				exit;
				}
				
				
				

#**********************************************************************************#


				#***********************************#
				#********** CONFIGURATION **********#
				#***********************************#
				
				
				require_once("include/config.inc.php");
				require_once("include/form.inc.php");
				require_once("include/db.inc.php");

				
				#***** LOGOUT *****#
				if( isset($_GET['action']) ) {				
if(DEBUG)		echo "<p class='debug'>URL-Parameter <i>action</i> wurde übergeben.</p>";
				
					// Schritt 2 URL: Parameter-Wert auslesen, entschärfen, DEBUG-Ausgabe
					$action = cleanString($_GET['action']);
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					if( $action == "logout"){
						echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Logout wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\r\n";

						session_destroy();

						// Neuladen der Index.php, da action nicht mehr verfügbar ist
						header("Location: index.php");
						exit;
					
					}
				}
				
#**********************************************************************************#


				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				
				
				
				$usr_id 						= $_SESSION['usr_id'];
				$uberschrift				= NULL;
				$imageAlignmentArray	 	= array("left","right");
				$imagePath					= NULL;
				$content  			 		= NULL;
				
				$neucategories		 		= NULL;
				
				
				$errorUberschrift  		= NULL;
				$errorImageUpload  		= NULL;
				$errorContent		 		= NULL;
				$errorNeuCategories 		= NULL;
				
				$dbMessage					= NULL;
				
				$imageUploadReturnArray = NULL;
				
				

			

#**********************************************************************************#

				#**********************************************#
				#********** DB-VERBINDUNG HERSTELLEN **********#
				#**********************************************#
				
				$pdo = dbConnect("blog");


#**********************************************************************************#




if(DEBUG)	echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)	print_r($_POST);					
if(DEBUG)	echo "</pre>";


				#***********************************************#
				#********** PROCESS FORM NEU KATEGORIE**********#
				#***********************************************#
				if(isset($_POST['saveNewCategory']) ) {
if(DEBUG)		echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Neucategorie 'in Kotegorie section' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
					
					// Schritt 1 DB: DB-Verbindung herstellen
					//schon bereit
					
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
					$neucategories = cleanString($_POST['neucategories']);
					
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$neucategories: $neucategories <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// Schritt 3 FORM: Daten validieren
			
					$errorNeuCategories = checkInputString($neucategories);
					
					// Schritt 4 FORM: Daten weiterverarbeiten
					
					#********** prüfen, ob es hinzugefügte kategorie schon in DB gibt **********#
					
					// Schritt 1 DB: DB-Verbindung herstellen
					//schon bereit
					
					// Schritt 2 DB: SQL-Statement vorbereiten
					$statement = $pdo->prepare("SELECT COUNT(cat_name) FROM categories WHERE cat_name = :ph_cat_name");
					
					// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
					$statement->execute(array("ph_cat_name" => $neucategories));
if (DEBUG) 		if ($statement->errorInfo()[2]) echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: " . $statement->errorInfo()[2] . " <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// Schritt 4 DB: Daten weiterverarbeiten
					$existCategory = $statement->fetchColumn();

if (DEBUG) 		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$existCategory: $existCategory <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					
					#********** CATEGORY FORMULARPRÜFUNG **********#
					if(!$existCategory ) {
						
						
						#********** DATENBANKOPERATION **********#
					
						// Schritt 1 DB: DB-Verbindung herstellen
						//$pdo = dbConnect();
						
						// Schritt 2 DB: SQL-Statement vorbereiten
						$statement = $pdo->prepare("INSERT INTO categories
															(cat_name)
															VALUES
															(:ph_cat_name)");
															
						// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen
						$statement->execute(array("ph_cat_name" => $neucategories));
if(DEBUG)			if($statement->errorInfo()[2]) echo "<p class='debug err'>" . $statement->errorInfo()[2] . "</p>";
						
						// Schritt 4 DB: Daten weiterverarbeiten
						$rowCount = $statement->rowCount();
						// User über das Anlegen der Kategorie in der DB informieren
						if (!$rowCount) {
							// Fehler
if (DEBUG) 				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>Fehler beim Speichern der Kategorie in die Datenbank<i>(" . basename(__FILE__) . ")</i></p>\r\n";
							$dbMessage = "<span class='error'>Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später noch einmal.</span>";

						} else {
							// Erfolg
if (DEBUG) 				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Kategorie wurde erfolgreich in der Datenbank angelegt. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							$dbMessage = "<span class='success'>Die Kategorie <b>$neucategories</b> wurde erfolgreich in der Datenbank angelegt.</span>";

						} // User über das Anlegen der Kategorie in der DB informieren Ende
					

					} else{
if (DEBUG) 			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>Neu kategories ist schon in DB<i>(" . basename(__FILE__) . ")</i></p>\r\n";

						$errorNeuCategories = "Neu kategories ist schon in DB";
					}
				}
				
				
#**********************************************************************************#
				
				
				#**********************************************#
				#********** KATEGORIES DATEI AUSLESEN *********#
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
				
				
				#******************************************#
				#********** PROCESS FORM NEU BLOG**********#
				#******************************************#

/*
if(DEBUG)	echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)	print_r($_POST);					
if(DEBUG)	echo "</pre>";
*/
					
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formsentNewBlog']) ) {
if(DEBUG)		echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Blok 'in Index seite' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\r\n";					
					
					
					// Schritt 2 FORM: Werte abholen, entschärfen, DEBUG-Ausgabe
					$catId = cleanString($_POST['catId']);
					$uberschrift = cleanString($_POST['uberschrift']);
					$imageAlignment = cleanString($_POST['imageAlignment']);
					//$imagePath		= cleanString($_POST['imagePath']);
					$content = cleanString($_POST['content']);
					

if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$catId: $catId <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$uberschrift: $uberschrift <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$imageAlignment: $imageAlignment <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG)		echo "<p class='debug'><b>Line " . __LINE__ . "</b>: \$content: $content <i>(" . basename(__FILE__) . ")</i></p>\r\n";
	
					
					
					// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					//$errorCatId				= checkInputString($catId); // Es ist schon selected
					$errorUberschrift 		= checkInputString($uberschrift);
					$errorContent 				= checkInputString($content,2,1000);
					
					
					//Wenn es keine fehler gibt,geht wieter
					if($errorUberschrift OR $errorContent ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Bitte nochmal check Uberschrift or Content! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
												
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Uberschrift and Content sind fehlerfrei und wird nun verarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
					
					
						#********** IMAGE UPLOAD **********#
						
if(DEBUG)			echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)			print_r($_FILES);					
if(DEBUG)			echo "</pre>";	


						// Prüfen, ob Bildupload vorliegt
						if( $_FILES['imagePath']['tmp_name'] ) {
if(DEBUG)				echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Bildupload aktiv... <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							
							$imageUploadReturnArray = imageUpload( $_FILES['imagePath'] );
							
if(DEBUG)				echo "<pre class='debug'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG)				print_r($imageUploadReturnArray);					
if(DEBUG)				echo "</pre>";


							// Prüfen, ob es einen Bilduploadfehler gab
							if( $imageUploadReturnArray['imageError'] ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: $imageUploadReturnArray[imageError] <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
								$errorImageUpload = $imageUploadReturnArray['imageError'];
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Bild wurde erfolgreich unter <i>'$imageUploadReturnArray[imagePath]'</i> gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
								
								
								
								// Neuen Bildpfad in DB speichern
								$imagePath = $imageUploadReturnArray['imagePath'];
								
							}//Prüfen, ob es einen Bilduploadfehler gab ende
						
						}//IMAGE UPLOAD ENDE
						#**********************************#
					
						#********** FINAL FORM VALIDATION PART II (IMAGE UPLOAD) **********#
						if( $errorImageUpload ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Final form validation II: Beim upload der image enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Final form validation II: Beim upload der image ist insgesamt fehlerfrei und wird nun verarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
							
							// Schritt 4 FORM: Daten weiterverarbeiten
							
							#********** SAVE BLOGDATA INTO DB **********#
							// Schritt 1 DB: DB-Verbindung herstellen
							//$pdo = dbConnect();
							
							// Schritt 2 DB: SQL-Statement vorbereiten
							$statement = $pdo->prepare("INSERT INTO blogs
																(cat_id, blog_headline,blog_imageAlignment,blog_imagePath, blog_content,usr_id)
																 VALUES
																(:ph_cat_id, :ph_blog_headline, :ph_blog_imageAlignment, :ph_blog_imagePath, :ph_blog_content, :ph_usr_id)
																		");
							
							// Schritt 3 DB: SQL-Statement ausführen und ggf. Platzhalter füllen categories
							$statement->execute( array(
																"ph_cat_id" 					=> $catId,
																"ph_blog_headline" 			=> $uberschrift,
																"ph_blog_imagePath"			=> $imagePath,
																"ph_blog_content" 			=> $content,
																"ph_blog_imageAlignment"	=> $imageAlignment,
																"ph_usr_id"						=>	$usr_id
																
																			) );
							
							// Schritt 4 DB: weiter verarbeitet
							//
							$rowCount = $statement->rowCount();
	if(DEBUG)			echo "<p class='debug'>\$rowCount: $rowCount</p>";
							// Nach dem erfolgreichen Schreiben in die DB die letzte vergebene ID auslesen
							$lastInsertId = $pdo->lastInsertId();
	if(DEBUG)			echo "<p class='debug'>\$lastInsertId: $lastInsertId</p>";
							
							// Wenn der rowCount einen anderen Wert als 0 hat, war das Schreiben erfolgreich
							if( $rowCount ) {
								// Erfolgsfall
								$dbMessage = "<p class='success'>Ein neuer Datensatz mit der ID $lastInsertId wurde erfolgreich angelegt.</p>";
								
								// Vorbelegungen der Formularfelder wieder löschen
								//catId bereits selected.
								$uberschrift = NULL;
								$content = NULL;
								//$imageAlignment bereits selected.
								
								
							} else {
								// Fehlerfall
								$dbMessage = "<p class='error'>Fehler beim Anlegen des neuen Blog!</p>";
							
							}//Wenn der rowCount einen anderen Wert als 0 hat, war das Schreiben erfolgreich ENDE
						
						}//FINAL FORM VALIDATION PART II (IMAGE UPLOAD)
					
					}//Wenn es keine fehler gibt,geht wieter ENDE
				
				}//PROCESS FORM END
				

			


#**********************************************************************************#
?>

<!doctype html>

	<html>
		<head>
			<meta charset="utf-8">
			<title>PHP-Projekt Dashboard - Index</title>
			
			<link rel="stylesheet" href="css/main.css">
			<link rel="stylesheet" href="css/debug.css">
		</head>
	
		<body>
		
			<!-- -------- PAGE HEADER -------- -->
			<header class="fright loginheader">
				<p class="fright"><a href="?action=logout"><< Logout</a></p><br>	
				<p class="fright"><a href="index.php"><< zum Frontend</a></p>	
			</header>
			<div class="clearer"></div>
			
			<hr>
			<!-- -------- PAGE HEADER END -------- -->
			
			<h1>PHP-Projekt Dashboard - Index</h1>
		
			<h3 class='info'>Aktiv Benutzer <?= $_SESSION['usr_firstname'] ?> <?= $_SESSION['usr_lastname'] ?></h3>
			<p class='success' <?= $dbMessage ?> 
			

			<!-- -------- FORM FOR BLOG EDITING START -------- -->
			<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="formsentNewBlog">
				
				<fieldset name="userdata">
					<legend>Neuen Blog-Eintrag verfassen</legend>
					
					<select class="categories" name="catId"> 
						
						<?php foreach( $categoriesArray AS $category ): ?>								
							<?php if( $catId == $category['cat_id']  ): ?>
								<option value='<?php echo $category['cat_id'] ?>' selected><?php echo $category['cat_name'] ?></option>
							<?php else: ?>
								<option value='<?php echo $category['cat_id'] ?>' ><?php echo $category['cat_name'] ?></option>
							<?php endif ?>
						<?php endforeach ?>
					</select> <br>
					<span class="error"><?php echo $errorUberschrift ?></span><br>
					
					<input type="text" name="uberschrift" placeholder="Überschrift">  
					
					
					<legend>Bild hochladen</legend>
						
						<select class="imageAlignment" name="imageAlignment">     
						
						<?php 
							foreach( $imageAlignmentArray AS $key=>$value ) {
								echo "<option value='$value'>$value</option>";
							} 
						?>	
							
						</select>
						<br>
						
						<span class="error"><?php echo $errorImageUpload ?></span><br>
						<input type="file" name="imagePath">
						<br>
						
						<span class="error"><?php echo $errorContent ?></span><br>
						<textarea name="content" placeholder="Text..."><?php echo $content ?></textarea>  
					
					
					
				</fieldset>	
				
				<input type="submit" value="Veroffentlichen">
			</form>	
			<!-- -------- FORM FOR BLOG EDITING END -------- -->
				<br>
				<br>
				<br>
			<!-- -------- FORM FOR CATEGORY EDITING START -------- -->
			<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="POST" enctype="multipart/form-data">	
				<input type="hidden" name="saveNewCategory">
				<fieldset name="userdata">
					<input type="text" name="neucategories" placeholder="Name der Kategorie">
					
					<input type="submit" value="Neue Kategorie anlegen">
					<span class="error"><?php echo $errorNeuCategories ?></span><br>
				</fieldset>
		
			</form>
			
			<!-- -------- FORM FOR CATEGORY EDITING END -------- -->
	
	
		</body>
	</html>