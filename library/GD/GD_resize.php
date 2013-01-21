<?php
	// Kévin DUBUC
	
	// Fonction pour redimensionner une image
	function GD_resize($source, $destination, $width, $height="")
	{
		// Check la source si elle est une image
		// Format supporté : JPG / JPEG, PNG, GIF
		$mime=exif_imagetype($source);
		switch($mime) {
			case IMAGETYPE_JPEG :
				$source = imagecreatefromjpeg($source);
			break;
			
			case IMAGETYPE_PNG :
				$source = imagecreatefrompng($source);
			break;
				
			case IMAGETYPE_GIF :
				$source = imagecreatefromgif($source);
			break;
			
			default :
				return; // Format non supporté
			break;
		}

		// Hauteur et largeur de l'image source
		$width_src = imagesx($source);
		$height_src = imagesy($source);
		
		// Variable servant au redimensionement de l'image
		$width_dest=0;
		$height_dest=0;
		
		
		// Si la hauteur n'est pas renseignée, c'est donc un redimensionnement gardant les proportions de l'image
		if(!$height) {
		
			// Ratio entre la longueur de l'image source et la longeur redimensionnée
			$ratio=($width*100)/$width_src;
			
			// L'image a t'elle besoin d'un redimensionnement ?
			if ($ratio>100) {
				imagejpeg($source, $destination, 70);
				imagedestroy($source);
				return;
			}
			
			// largeur et hauteur de l'image redimensionnée
			$width_dest = $width;
			$height_dest = $height_src * $ratio/100;
			
		}
		else {
		
			if($height_src>=$width_src ) {
				$height_dest = ($height_src * $width ) / $width_src;
				$width_dest = $width;
			}
			else if($height_src<$width_src) {
				$width_dest = ($width_src * $height ) / $height_src;
				$height_dest = $height;
			}
		
		}

		// Création de la miniature
		$emptyPicture = imagecreatetruecolor($width, ($height)?$height:$height_dest);
		imagecopyresampled($emptyPicture, $source, 0, 0, 0, 0, $width_dest, $height_dest, $width_src, $height_src);
		
		
		// On enregistre la miniature
		imagejpeg($emptyPicture, $destination, 70);
		
		// Destruction des images temporaires
		imagedestroy($source);
		imagedestroy($emptyPicture);
		
		return;
	}
?>
