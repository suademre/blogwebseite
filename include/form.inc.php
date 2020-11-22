<?php
#**********************************************************************************#


				/**
				*
				*	Entschärft und säubert einen String, falls er einen Wert besitzt
				*	Falls der String keinen Wert besitzt (NULL, "", 0, false) wird er 
				*	1:1 zurückgegeben
				*
				*	@param String $value - Der zu entschärfende und zu bereinigende String
				*
				*	@return String 		- Originalwert oder der entschärfte und bereinigte String
				*
				*/
				function cleanString($value) {
if(DEBUG_F)		echo "<p class='debugCleanString'><b>Line " . __LINE__ .  "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\r\n";	
					
					// htmlspecialchars() wandelt potentiell gefährliche Steuerzeichen wie
					// < > "" & in HTML-Code um (&lt; &gt; &quot; &amp;)
					// Der Parameter ENT_QUOTES wandelt zusätzlich einfache '' in &apos; um
					// Der Parameter ENT_HTML5 sorgt dafür, dass der generierte HTML-Code HTML5-konform ist
					// Der optionale Parameter 'false' steuert, dass bereits vorhandene HTL-Entities nicht
					// noch einmal codiert werden (&auml; => &amp;auml;)
					$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
					
					// trim() entfernt am Anfang und am Ende eines Strings alle 
					// sog. Whitespaces (Leerzeichen, Tabulatoren, Zeilenumbrüche)
					$value = trim($value);
					
					// Damit cleanString() nicht NULL-Werte in Leerstings verändert, wird 
					// ein evetueller Leerstring in $value mit NULL überschrieben 
					if( $value === "" ) {
						$value = NULL;
					}
					
					return $value;
				}


#**********************************************************************************#


				/**
				*
				*	Prüft einen String auf Leerstring, Mindest- und Maxmimallänge
				*
				*	@param String $value 									- Der zu prüfende String
				*	@param [Integer $minLength=INPUT_MIN_LENGTH] 	- Die erforderliche Mindestlänge
				*	@param [Integer $maxLength=INPUT_MAX_LENGTH] 	- Die erlaubte Maximallänge
				*
				*	@return String/NULL - Ein String bei Fehler, ansonsten NULL
				*	
				*/
				function checkInputString($value, $minLength=INPUT_MIN_LENGTH, $maxLength=INPUT_MAX_LENGTH) {
if(DEBUG_F)		echo "<p class='debugCheckInputString'><b>Line " . __LINE__ .  "</b>: Aufruf " . __FUNCTION__ . "('$value' [$minLength | $maxLength]) <i>(" . basename(__FILE__) . ")</i></p>\r\n";	
					
					// Prüfen auf leeres Feld
					/*
						WICHTIG: Die Prüfung auf Leerfeld muss zwingend den Datentyp Sting mitprüfen,
						da ansonsten bei einer Eingabe 0 (z.B. Anzahl der im Haushalt lebenden Kinder: 0)
						die 0 als false und somit als leeres Feld gewertet wird!
					*/
					if( $value === "" OR $value === NULL ) {
						$errorMessage = "Dies ist ein Pflichtfeld!";
					
					// Prüfen auf Mindestlänge
					} elseif( mb_strlen($value) < $minLength ) {	
						$errorMessage = "Muss mindestens $minLength Zeichen lang sein!";
					
					// Prüfen auf Maximallänge
					} elseif( mb_strlen($value) > $maxLength ) {
						$errorMessage = "Darf maximal $maxLength Zeichen lang sein!";
						
					} else {
						$errorMessage = NULL;
					}
					
					return $errorMessage;
					
				}


#**********************************************************************************#


				/**
				*
				*	Prüft eine Email-Adresse auf Leerstring und Validität
				*
				*	@param String $value - Die zu prüfende Email-Adresse
				*
				*	@return String/NULL - Ein String bei Fehler, ansonsten NULL
				*
				*/
				function checkEmail($value) {
if(DEBUG_F)		echo "<p class='debugCheckEmail'><b>Line " . __LINE__ . "</b>: Aufruf checkEmail('$value') <i>(" . basename(__FILE__) . ")</i></p>\r\n";	

					$errorMessage = NULL;
					
					// Prüfen auf Leerstring
					/*
						WICHTIG: Die Prüfung auf Leerfeld muss zwingend den Datentyp Sting mitprüfen,
						da ansonsten bei einer Eingabe 0 (z.B. Anzahl der im Haushalt lebenden Kinder: 0)
						die 0 als false und somit als leeres Feld gewertet wird!
					*/
					if( $value === "" OR $value === NULL ) {
						$errorMessage = "Dies ist ein Pflichtfeld!";

					// Email auf Validität prüfen
					} elseif( !filter_var($value, FILTER_VALIDATE_EMAIL) ) {
						$errorMessage = "Dies ist keine gültige Email-Adresse!";
					}
				
					return $errorMessage;
					
				}


#**********************************************************************************#

				
				/**
				*
				*	Prüft ein hochgeladenes Bild auf MIME-Type, Datei- und Bildgröße
				*	Bereinigt den Dateinamen von Leerzeichen und Umlauten und wandelt ihn in Kleinbuchstaben um
				*	Speichert das erfolgreich geprüfte Bild unter dem bereinigten Dateinamen mit einem zufällig generierten Präfix
				*
				*	@param Array $uploadedImage											- Das in $_FILES enthaltene Array mit den Informationen zum hochgeladenen Bild
				*	@param [Int $maxWidth 				= IMAGE_MAX_WIDTH]			- Die maximal erlaubte Bildbreite in PX
				*	@param [Int $maxHeight 				= IMAGE_MAX_HEIGHT]			- Die maximal erlaubte Bildhöhe in PX
				*	@param [Int $maxSize 				= IMAGE_MAX_SIZE]				- Die maximal erlaubte Dateigröße in Bytes
				*	@param [Array $allowedMimeTypes 	= IMAGE_ALLOWED_MIMETYPES]	- Whitelist der erlaubten MIME-Types
				*	@param [String $uploadPath 		= IMAGE_UPLOADPATH]			- Das Speicherverzeichnis auf dem Server
				*
				*	@return Array { "imageError" => String/NULL 						- Fehlermeldung im Fehlerfall, 
				*						 "imagePath"  => String/NULL						- Der Speicherpfad auf dem Server im Erfolgsfall }
				*
				*/
				function imageUpload( $uploadedImage,
											 $imageMaxHeight		= IMAGE_MAX_HEIGHT,
											 $imageMaxWidth		= IMAGE_MAX_WIDTH,
											 $imageMaxSize			= IMAGE_MAX_SIZE,
											 $uploadPath 			= IMAGE_UPLOADPATH,
											 $allowedMimeTypes 	= IMAGE_ALLOWED_MIMETYPES
											) {
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ .  "</b>: Aufruf " . __FUNCTION__ . "() <i>(" . basename(__FILE__) . ")</i></p>\r\n";	
					
/*					
if(DEBUG_F)		echo "<pre class='debugImageUpload'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)		print_r($uploadedImage);					
if(DEBUG_F)		echo "</pre>";						
*/
					/*
						Das Array $_FILES['rondom'] bzw. $uploadedImage enthält:
						Den Dateinamen [name]
						Den generierten (also ungeprüften) MIME-Type [type]
						Den temporären Pfad auf dem Server [tmp_name]
						Die Dateigröße in Bytes [size]
					*/
					
					#********** BILDINFORMATIONEN SAMMELN **********#
					// Dateigröße
					$fileSize = $uploadedImage['size'];
					// temporärer Pfad auf dem Server
					$fileTemp = $uploadedImage['tmp_name'];
					// Dateiname
					$fileName = cleanString( $uploadedImage['name'] );
					// $fileName = "?mein #blöde.r rondom's 07.jpg";
					
					
					#********** DATEINAMEN URL-KONFORM AUFBEREITEN **********#				
					// ggf. vorhandene Leerzeichen durch _ ersetzen
					$fileName = str_replace(" ", "_", $fileName);
					// Dateinamen in Kleinbuchstaben umwandeln
					$fileName = mb_strtolower($fileName);
					// Umlaute ersetzen
					$fileName = str_replace( array("ä","ö","ü","ß"), array("ae","oe","ue","ss"), $fileName );
								
					// Dateinamen von zusätzlichen . bereinigen 
					// Erlaubt sein soll nur der letzte Punkt vor der Dateiendung
					
					// Position des letzten Punktes im Dateinamen ermitteln
					$startPositionDateiEndung = strrpos($fileName, ".");
// if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$startPositionDateiEndung: $startPositionDateiEndung <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					// Dateiendung kopieren
					$dateiEndung = substr($fileName,$startPositionDateiEndung);
// if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$dateiEndung: $dateiEndung <i>(" . basename(__FILE__) . ")</i></p>\r\n";

					// Dateiendung von ursprünglichem Dateinamen abschneiden
					$fileNamePrefix = str_replace($dateiEndung, "", $fileName);

					// Nicht erlaubte Zeichen aus ursprünglichem Dateinamen löschen
					// $fileNamePrefix = str_replace( array("'","#","?","!","&",'"',"@",",","|","~","*","´","`","°","[","]","{","}","/","²","§","³","$","%","^","<",">","(",")",",",";",":","+",".","/"), "", $fileName );
					// Bessere Variante mittels Regulärem Ausdruck (RegEx):
					$fileNamePrefix = preg_replace('/[^a-z0-9_-]/', "", $fileNamePrefix);
					
					// Dateiendung wieder an den bereinigten Dateinamen anhängen
					$fileName = $fileNamePrefix . $dateiEndung;
					#********************************************************#
					
					// zufälligen Dateinamenszusatz generieren
					$randomPrefix = rand(1,999999) . str_shuffle("abcdefghijklmnopqrstuvwxyz") . time();
					
					// Zielverzeichnis festlegen
					$fileTarget = $uploadPath . $randomPrefix . "_" . $fileName;
					
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$fileSize: " . round($fileSize/1024,2) . "kB <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$fileTemp: $fileTemp <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$fileName: $fileName <i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$fileTarget: $fileTarget <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					
					#********** INFORMATIONEN ZUR BILDDATEI AUSLESEN **********#
					/*
						Die Funktion getimagesize() liefert bei gültigen Bildern ein Array zurück:
						Die Bildbreite in PX [0]
						Die Bildhöhe in PX [1]
						Einen für die HTML-Ausgabe vorbereiteten String für das IMG-Tag
						(width="480" height="532") [3]
						Die Anzahl der Bits pro Kanal ['bits']
						Die Anzahl der Farbkanäle (somit auch das Farbmodell: RGB=3, CMYK=4) ['channels']
						Den echten(!) MIME-Type ['mime']
					*/
					$imageDataArray = @getimagesize($fileTemp);					
/*					
if(DEBUG_F)		echo "<pre class='debugImageUpload'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\r\n";					
if(DEBUG_F)		print_r($imageDataArray);					
if(DEBUG_F)		echo "</pre>";						
*/					
										
					$imageWidth		= $imageDataArray[0];
					$imageHeight	= $imageDataArray[1];
					$imageMimeType	= $imageDataArray['mime'];
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$imageWidth: $imageWidth px<i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$imageHeight: $imageHeight px<i>(" . basename(__FILE__) . ")</i></p>\r\n";
if(DEBUG_F)		echo "<p class='debugImageUpload'><b>Line " . __LINE__ . "</b>: \$imageMimeType: $imageMimeType <i>(" . basename(__FILE__) . ")</i></p>\r\n";
					
					
					#********** VALIDATE IMAGE ATTRIBUTES **********#					
					// MIME TYPE prüfen
					// Whitelist mit erlaubten Bildtypen
					// $allowedMimeTypes = array("image/jpeg", "image/jpg", "image/gif", "image/png");
					if( !in_array($imageMimeType, $allowedMimeTypes) ) {
						$errorMessage = "Dies ist kein erlaubter Bildtyp!";
					
					// maximal erlaubte Bildhöhe
					} elseif( $imageHeight > $imageMaxHeight ) {
						$errorMessage = "Die Bildhöhe darf maximal $imageMaxHeight Pixel betragen!";
						
					// maximal erlaubte Bildbreite	
					} elseif( $imageWidth > $imageMaxWidth ) {
						$errorMessage = "Die Bildbreite darf maximal $imageMaxWidth Pixel betragen!";
					
					// maximal erlaubte Dateigröße
					} elseif( $fileSize > $imageMaxSize ) {
						$errorMessage = "Die Dateigröße darf maximal " . round($imageMaxSize/1024, 2) . "kB betragen!";
					
					// wenn es keine Fehler gab
					} else {
						$errorMessage = NULL;
					}
					
					
					#********** FINAL IMAGE VALIDATION **********#
					if( $errorMessage ) {
						// Fehlerfall
if(DEBUG_F)			echo "<p class='debugImageUpload err'><b>Line " . __LINE__ . "</b>: $errorMessage <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						$fileTarget = NULL;
						
					} else {
						// Erfolgsfall
if(DEBUG_F)			echo "<p class='debugImageUpload ok'><b>Line " . __LINE__ . "</b>: Die Bildprüfung ergab keine Fehler. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
						
						
						#********** SAVE IMAGE TO DISK **********#
						if( !@move_uploaded_file($fileTemp, $fileTarget) ) {
							// Fehlerfall
if(DEBUG_F)				echo "<p class='debugImageUpload err'><b>Line " . __LINE__ . "</b>: Fehler beim Verschieben des Bildes von <i>'$fileTemp'</i> nach <i>'$fileTarget'</i>! <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							$fileTarget = NULL;
							
						} else {
							// Erfolgsfall
if(DEBUG_F)				echo "<p class='debugImageUpload ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich von <i>'$fileTemp'</i> nach <i>'$fileTarget'</i> verschoben. <i>(" . basename(__FILE__) . ")</i></p>\r\n";
							
						} // SAVE IMAGE TO DISK END

					} // FINAL IMAGE VALIDATION END
					
					
					#********** BILDPFAD UND GGF. FEHLERMELDUNG ZURÜCKGEBEN **********#
					return array('imagePath' => $fileTarget, 'imageError' => $errorMessage);
					
				} // IMAGE UPLOAD END


#**********************************************************************************#
?>