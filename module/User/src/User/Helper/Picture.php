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
	const MINIMUN_PICTURE_WIDTH = 100;
	const MAXIMUN_PICTURE_SIZE = 10485760;

	public function addPictureFromRequest($file, $user){
		$picture = $file->get('picture');

		$adapter = new \Zend\File\Transfer\Adapter\Http();
		$size = new \Zend\Validator\File\Size(array('max' => $this::MAXIMUN_PICTURE_SIZE ));
        $extension = new \Zend\Validator\File\Extension(array('extension' => array('jpeg', 'jpg', 'gif', 'tiff', 'png', 'bmp')));
		$adapter->setValidators(array($extension, $size), $picture['name']);
		
		if(!$adapter->isValid()){

			$errors = $adapter->getMessages();
			$message = '';

			foreach ($errors as $error) {
				$message .= $error;
				break;
			}

			throw new \Exception($message, \User\Module::ERROR_PICTURE_UPLOAD_FAILED);
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
		elseif($actualWidth < $this::MINIMUN_PICTURE_WIDTH){
			throw new \Exception("The picture is smaller than expected", \User\Module::ERROR_PICTURE_UPLOAD_FAILED);
		}
		else{
			$image->save($destination);
		}

		unlink($original);
	}
}