<?php
include 'vendor/autoload.php';
use Imagine\Image\Box,
    Imagine\Image\Font,
    Imagine\Image\Palette\RGB,
    Imagine\Image\Point,
    Imagine\Image\Point\Center,
    Imagine\Image\ImagineInterface,
    Imagine\Test\ImagineTestCase,
    Phalcon\Logger,
    Phalcon\Logger\Adapter\File as FileAdapter;
    use iio\libmergepdf\Merger;
    use Symfony\Component\Finder\Finder;
    #include_once(APPLICATION_PATH."/vendor/iio/libmergepdf/src/Merger.php");
   ## include_once(APPLICATION_PATH."/vendor/setasign/fpdi/fpdf.php");

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
$sql = "SELECT moderation_stack_material.id_competitive_work, name, surname
FROM `moderation_queue_final` 
JOIN moderation_stack_material ON moderation_queue_final.id_competitive_work = moderation_stack_material.id_competitive_work
ORDER BY `moderation_stack_material`.`id_competitive_work` ASC 
LIMIT 500";
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
 				
 				$image = new Imagick();    
 				$draw = new ImagickDraw();
 				$color = new ImagickPixel('#005d80');
               
 				$draw->setFont(APPLICATION_PATH.'/Sunline_trafaret.otf');
 				$draw->setFontSize(165);
 				$draw->setFillColor($color);
 				$draw->setStrokeAntialias(true);
				$draw->setTextAntialias(true);

				$metrics = $image->queryFontMetrics($draw,$fullname);
				$draw->annotation(0,$metrics['ascender'], $fullname);

                $filename1 =APPLICATION_PATH."/result/".$id."Name.tif";
				$image->readImage(APPLICATION_PATH.'/diplom_kosmos_A5_02.tif');
                $image->transformImageColorspace(Imagick::COLORSPACE_SRGB);
                $image->setImageFormat('pdf');
				$image->annotateImage($draw,(2188/2 - $metrics['textWidth']/2), 896,0, $fullname);
    			$image->transformImageColorspace(Imagick::COLORSPACE_CMYK);
                $filename =APPLICATION_PATH."/result/".$id.".pdf";
    			
          $image->writeImage($filename);
          $logger->info("success");
 			} 			
    }

    public function testAction()
    {
        $finder = new Finder();
        $finder->files()->in(APPLICATION_PATH."/result")->name('*.pdf')->sortByName();

        
        #$m->addFinder($finder);
       # file_put_contents(APPLICATION_PATH."/result/result.pdf", $m->merge());
        $dir = new DirectoryIterator(APPLICATION_PATH."/result");
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                #echo $fileinfo->getFilename();
               # die();
                $m = new Merger();
                $m->addFromFile(APPLICATION_PATH."/result/".$fileinfo->getFilename());
                if (file_exists(APPLICATION_PATH."/result/result.pdf")) {
                    $m->addFromFile(APPLICATION_PATH."/result/result.pdf");
                };
                file_put_contents(APPLICATION_PATH."/result/result.pdf", $m->merge());
            }
          }
        }
	
}