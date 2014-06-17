<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use User\Entity\User;
use User\Helper\User as UserHelper;
use User\Helper\Picture as PictureHelper;
use User\Facade\PictureFacade;

/**
 *
 * @SWG\Model(id="picture")
 */
class PictureController extends AbstractRestfulController
{	

	/** @SWG\Resource(
    *   resourcePath="picture",
    *   basePath = "api/user")
    */

	/**
     *
     * @SWG\Api(
     *   path="/picture",
     *    @SWG\Operation(
     *      nickname="get_user_picture",
     *      method = "GET",
     *         summary = "user profile picture"
     *   )
     *  )
     *)
     */
     public function getList(){
        return new JsonModel(PictureFacade::get(UserHelper::getCurrentUser()));    
     }

    /**
     *
     * @SWG\Api(
     *   path="/picture",
     *    @SWG\Operation(
     *      nickname="upload_picture",
     *      method = "POST",
     *      summary="upload picture",
     *      @SWG\Parameters(
     *          @SWG\Parameter(
     *              name="picture",
     *              paramType="body",
     *              type="file",
     *          required=true
     *          )
     *      )
     *   )
     *  )
     */

    public function create(){
        $user = UserHelper::getCurrentUser();
        $pictureUpload = new PictureHelper();

        $file = $this->getRequest()->getFiles();
        $picture = $pictureUpload->addPictureFromRequest($file, $user);
        
        $user->setPicture($picture);
        UserHelper::getUserMapper()->update($user);

        return new JsonModel(array('message' => 'Picture uploaded'));
    }
}