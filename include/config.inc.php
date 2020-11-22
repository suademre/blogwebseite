<?php
#**********************************************************************************#


				#******************************************#
				#********** GLOBAL CONFUGURATION **********#
				#******************************************#
				
				
				#********** DB CONFIGURATION **********#
				define("DB_SYSTEM", 						"mysql");
				define("DB_HOST", 						"localhost");
				define("DB_NAME", 						"blog");
				define("DB_USER", 						"root");
				define("DB_PWD", 							"");
				
				
				#********** FORMULAR CONFIGURATION **********#
				define("INPUT_MIN_LENGTH", 			2);
				define("INPUT_MAX_LENGTH", 			256);
				
				
				#********** IMAGE UPLOAD CONFIGURATION **********#
				define("IMAGE_MAX_HEIGHT", 			800);
				define("IMAGE_MAX_WIDTH", 				800);
				define("IMAGE_MAX_SIZE", 				128*1024);
				define("IMAGE_ALLOWED_MIMETYPES", 	array("image/jpeg", "image/jpg", "image/gif", "image/png"));
				
				
				#********** STANDARD PATHS CONFIGURATION **********#
				define("IMAGE_UPLOADPATH", 			"uploaded_images/");
				define("RONDOM_DUMMY_PATH", 			"css/images/rondom_dummy.png");
				
				
				#********** DEBUGGING **********#
				define("DEBUG", 							true);		// Debugging for main php document
				define("DEBUG_F", 						true);		// Debugging for functions
				define("DEBUG_DB", 						true);		// Debugging for db operations



#**********************************************************************************#
?>