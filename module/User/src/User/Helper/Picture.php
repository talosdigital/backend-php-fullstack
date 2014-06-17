<?php

namespace User\Helper;

use User\Helper\User as UserHelper;
use User\Entity\User\Picture as PictureEntity;
use Zend\Filter\File\Rename;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class Picture {

	const UPLOAD_FOLDER = '/Images';
	const PICTURE_WIDTH = 500;

	//image magic

	public function addPictureFromRequest($file, $user){
		$picture = $file->get('picture');

		$adapter = new \Zend\File\Transfer\Adapter\Http();

		$size = new \Zend\Validator\File\Size(array('max' => 10485760 ));
        $extension = new \Zend\Validator\File\Extension(array('extension' => array('jpeg', 'jpg', 'gif', 'tiff', 'png', 'bmp')));
		$adapter->setValidators(array($size, $extension), $picture['name']);
		
		if(!$adapter->isValid()){
			throw new \Exception("An error was found while uploading the picture", \User\Module::ERROR_PICTURE_UPLOAD_FAILED);
		}

		if(empty($user->getPicture())){
			$pd = new PictureEntity();
			$pd->setId(new \MongoId());
		}
		else{
			$pd = $user->getPicture();
		}

		$ext = pathinfo($picture['name'], PATHINFO_EXTENSION);
		$tmpFileUrl = $pd->getId().'_tmp.'.$ext;
		$fileUrl = $pd->getId().'.jpg';
		
		$pd->setLongUrl($this::UPLOAD_FOLDER."/".$fileUrl);
		$pd->setType($picture['type']);
		$pd->setSize($picture['size']);
		
		
		$destinationFolder =  dirname(__DIR__).$this::UPLOAD_FOLDER."/".$user->getId();
		$destinationFile = $destinationFolder."/".$tmpFileUrl;
		$destUrl = $destinationFolder."/".$fileUrl;

		$pd->setUrl($fileUrl);

		if(!file_exists($destinationFolder)){
			mkdir($destinationFolder);
		}

		$adapter->addFilter('Rename',  array(
										'target' => $destinationFile, 
										'overwrite' => true)
							);
	       
		if (!$adapter->receive($picture['name'])) {
			throw new \Exception("An error was found while uploading the picture", \User\Module::ERROR_PICTURE_UPLOAD_FAILED);
		}

		$this->resizeImage($destinationFile, $destUrl, $this::PICTURE_WIDTH);
		return $pd;
	}


	public function resizeImage($original, $destination, $width){

		$imagine = new Imagine();
		$image = $imagine->open($original);

		$actualSize = $image->getSize();
		$actualWidth = $actualSize->getWidth();
		$actualHeight = $actualSize->getHeight();

		if($actualWidth > $width){
			$rate = $actualHeight/$actualWidth;
			$height = floor($width*$rate);

			$image->resize(new Box($width, $height))
		   			->save($destination);
		}
		else{
			$image->save($destination);
		}

		unlink($original);
	}
}