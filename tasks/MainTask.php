<?php
require 'vendor/autoload.php';


#namespace Imagine\Test\Draw;
use Imagine\Image\Box;
use Imagine\Image\Font;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Image\ImagineInterface;
use Imagine\Test\ImagineTestCase;

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
    	
        echo "\nThis is the default task and the default action \n";
        $link = mysql_connect('localhost', 'root', '123');
       	mysql_select_db('contest',$link);
       	mysql_query("SET NAMES utf8");
       	$sql = "SELECT `name`, `surname` ,`id_competitive_work` FROM `moderation_stack_grouped` WHERE `result`='одобрено'";
 		    $result = mysql_query($sql);
       
 		while ($names=mysql_fetch_assoc($result)) {
 				$name = $names['name'];
 				$surname = $names['surname'];
 				$fullname =$name." ".$surname;
 				$id = $names["id_competitive_work"];
 				
 				$t_image = new Imagick();
 				$image = new Imagick();
 				$draw = new ImagickDraw();
 				$color = new ImagickPixel('#000000');
 				$background = new ImagickPixel('none');

 				$draw->setFont('/var/www/app/Sunline_trafaret.otf');
 				$draw->setFontSize(190);
 				$draw->setFillColor($color);
 				$draw->setStrokeAntialias(true);
				$draw->setTextAntialias(true);

				$metrics = $image->queryFontMetrics($draw,$fullname);

				$draw->annotation(0,$metrics['ascender'], $fullname);
				$t_image->newImage($metrics['textWidth'], $metrics['textHeight'], $background);
				$t_image->setImageFormat('jpg');
				$t_image->drawImage($draw);

				$image->readImage('/var/www/app/diplom_kosmos.jpg');
        #$image->setImageFormat('pdf');
				$image->compositeImage($t_image, Imagick::COMPOSITE_DEFAULT, (2481/2 - $metrics['textWidth']/2), 820);
    			

    			$filename = "/var/www/app/result/".$id.".jpg";
    			
          $image->writeImage($filename);
 			}
  
 			mysql_close($link);
 			
    }
	
}