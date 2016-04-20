<?php

use Imagine\Image\Box,
    Imagine\Image\Font,
    Imagine\Image\Palette\RGB,
    Imagine\Image\Point,
    Imagine\Image\Point\Center,
    Imagine\Image\ImagineInterface,
    Imagine\Test\ImagineTestCase,
    Phalcon\Logger,
    Phalcon\Logger\Adapter\File as FileAdapter;

class MainTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        $logger = new FileAdapter(
            APPLICATION_PATH.'/log/app.log',
            array(
                'mode'=> 'w'
                )
            );
        $db = $this->getDI()->getShared("db");
$sql = "SELECT * 
FROM (

SELECT COUNT( * ) AS parts_qty, email, id_competitive_work, name, surname
FROM  `moderation_stack_grouped` 
WHERE result =  'одобрено'
GROUP BY email
ORDER BY id_competitive_work
)t
WHERE parts_qty <2";
$resultSet = $db->query($sql);
$resultSet->setFetchMode(Phalcon\Db::FETCH_ASSOC);
$targetWorks = $resultSet->fetchAll();

       
 		foreach($targetWorks as $key=>&$works) {
 				$name = $works['name'];
 				$surname = $works['surname'];
 				$fullname =$name." ".$surname;
 				$id = $works["id_competitive_work"];
 				
                if(!empty($fullname))
                    $logger->info("got name and id:".$fullname.", ".$id);
                else
                    $logger->error("couldn't get name and id");
 				

                $t_image = new Imagick();
 				$image = new Imagick();
 				$draw = new ImagickDraw();
 				$color = new ImagickPixel('#000000');
 				$background = new ImagickPixel('none');

 				$draw->setFont(APPLICATION_PATH.'/Sunline_trafaret.otf');
 				$draw->setFontSize(190);
 				$draw->setFillColor($color);
 				$draw->setStrokeAntialias(true);
				$draw->setTextAntialias(true);

				$metrics = $image->queryFontMetrics($draw,$fullname);

				$draw->annotation(0,$metrics['ascender'], $fullname);
				$t_image->newImage($metrics['textWidth'], $metrics['textHeight'], $background);
				$t_image->setImageFormat('jpg');
				$t_image->drawImage($draw);

				$image->readImage(APPLICATION_PATH.'/diplom_kosmos.jpg');
        $image->setImageFormat('pdf');
				$image->compositeImage($t_image, Imagick::COMPOSITE_DEFAULT, (2481/2 - $metrics['textWidth']/2), 820);
    			
          
    			$filename =APPLICATION_PATH."/result/".$id.".pdf";
    			
          $image->writeImage($filename);
          $logger->info("success");
 			}
 			
    }
	
}