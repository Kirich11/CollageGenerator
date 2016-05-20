<?php

require 'vendor/autoload.php';


use Imagine\Image\Box,
    Imagine\Image\Font,
    Imagine\Image\Palette\RGB,
    Imagine\Image\Point,
    Imagine\Image\Point\Center,
    Imagine\Image\ImagineInterface,
    Imagine\Test\ImagineTestCase,
    Imagine\Image\Palette\Color,
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

       $i=0;
 		foreach($targetWorks as $key=>&$works) {
            if($i<2) {
 				$name = $works['name'];
 				$surname = $works['surname'];
 				$fullname =$name." ".$surname;
 				$id = $works["id_competitive_work"];
 				
                if(!empty($fullname))
                    $logger->info("got name and id:".$fullname.", ".$id);
                else
                    $logger->error("couldn't get name and id");
 				
                $imagine = new Imagine\Imagick\Imagine();
    
                $palette = new Imagine\Image\Palette\RGB();
                $palette = $palette->color('#002340');
                $img = $imagine->open(APPLICATION_PATH.'/diplom_kosmos_A5.tif');
                $img->usePalette(new Imagine\Image\Palette\RGB());
                $font = $imagine->font(APPLICATION_PATH.'/Sunline_trafaret.otf', 130, $palette);
                $font->box($fullname);
                echo $fullname." ";
                $img->draw()->text($fullname,$font, new Point(2091/2-($font->box($fullname)->getWidth())/2, 700));    			
         		$filenameRGB =APPLICATION_PATH."/result/"."RGB".$id.".tif";
    			$img->save($filenameRGB);
                $filenameCMYK =APPLICATION_PATH."/result/"."CMYK".$id.".tif";
                $img->usePalette(new Imagine\Image\Palette\CMYK());
                $img->save($filenameCMYK);
                unset($img);
          $logger->info("success");
          $i++;
 			}
 		}	
    }
	
}