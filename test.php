<?php

use MagicObject\MagicObject;
use MagicObject\SetterGetter;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use YourApplication\Data\Entity\Album;
use YourApplication\Data\Entity\AlbumApv;
use YourApplication\Data\Entity\AlbumTrash;

require_once __DIR__ . "auth.php";

$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputGet->getUserAction() == UserAction::INSERT)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDraft(true);
	$album->setWaitingFor(1);
	$album->setAdminCreate($currentAction->getUserId());
	$album->setTimeCreate($currentAction->getTime());
	$album->setIpCreate($currentAction->getIp());
	$album->setAdminEdit($currentAction->getUserId());
	$album->setTimeEdit($currentAction->getTime());
	$album->setIpEdit($currentAction->getIp());

	$album->insert();

	$albumApv = new AlbumApv($album, $database);

	$albumApv->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$album = new Album(null, $database);

	$albumApv = new AlbumApv(null, $database);
	$albumApv->setAlbumId($inputPost->getAlbumId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setTitle($inputPost->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setDescription($inputPost->getDescription(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setProducerId($inputPost->getProducerId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setReleaseDate($inputPost->getReleaseDate(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setNumberOfSong($inputPost->getNumberOfSong(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setDuration($inputPost->getDuration(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setSortOrder($inputPost->getSortOrder(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setLocked($inputPost->getLocked(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setAsDraft($inputPost->getAsDraft(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setAdminEdit($currentAction->getUserId());
	$albumApv->setTimeEdit($currentAction->getTime());
	$albumApv->setIpEdit($currentAction->getIp());

	$albumApv->insert();

	$album->setAdminAskEdit($currentAction->getUserId());
	$album->setTimeAskEdit($currentAction->getTime());
	$album->setIpAskEdit($currentAction->getIp());

	$album->setAlbumId($album->getAlbumId())->setWaitingFor(3)->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);

			$album->setAdminAskEdit($currentAction->getUserId());
			$album->setTimeAskEdit($currentAction->getTime());
			$album->setIpAskEdit($currentAction->getIp());

			$album->setAlbumId($rowId)->setWaitingFor(3)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);

			$album->setAdminAskEdit($currentAction->getUserId());
			$album->setTimeAskEdit($currentAction->getTime());
			$album->setIpAskEdit($currentAction->getIp());

			$album->setAlbumId($rowId)->setWaitingFor(4)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);

			$album->setAdminAskEdit($currentAction->getUserId());
			$album->setTimeAskEdit($currentAction->getTime());
			$album->setIpAskEdit($currentAction->getIp());

			$album->setAlbumId($rowId)->setWaitingFor(5)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetAlbumId())
	{
		$albumId = $inputPost->getAlbumId();
		$album = new Album(null, $database);
		$album->findOneByAlbumId($albumId);
		if($album->issetAlbumId())
		{
			$approval = new PicoApproval($album, $entityInfo, $entityApvInfo, 
			function($param1, $param2, $param3){
				// approval validation here
				// if return false, approval can not be done
				
				return true;
			}, 
			function($param1, $param2, $param3){
				// callback when success
			}, 
			function($param1, $param2, $param3){
				// callback when failed
			} 
			);

			// List of properties to be copied from AlbumApv to Album. You can add or remove it
			$columToBeCopied = array(
				"name", 
				"title", 
				"description", 
				"producerId", 
				"releaseDate", 
				"numberOfSong", 
				"duration", 
				"imagePath", 
				"sortOrder", 
				"locked", 
				"asDraft", 
				"active"
			);

			$approvalCallback = new SetterGetter();
			$approvalCallback->setAfterInsert(function($param1, $param2, $param3){
				// callback on new data
				// you code here
				
				return true;
			}); 

			$approvalCallback->setBeforeUpdate(function($param1, $param2, $param3){
				// callback before update data
				// you code here
				
			}); 

			$approvalCallback->setAfterUpdate(function($param1, $param2, $param3){
				// callback after update data
				// you code here
				
			}); 

			$approvalCallback->setAfterActivate(function($param1, $param2, $param3){
				// callback after activate data
				// you code here
				
			}); 

			$approvalCallback->setAfterDeactivate(function($param1, $param2, $param3){
				// callback after deactivate data
				// you code here
				
			}); 

			$approvalCallback->setBeforeDelete(function($param1, $param2, $param3){
				// callback before delete data
				// you code here
				
			}); 

			$approvalCallback->setAfterDelete(function($param1, $param2, $param3){
				// callback after delete data
				// you code here
				
			}); 

			$approval->approve($columToBeCopied, new AlbumApv(), new AlbumTrash(), $approvalCallback);
		}
	}
}
else if($inputGet->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetAlbumId())
	{
		$albumId = $inputPost->getAlbumId();
		$album = new Album(null, $database);
		$album->findOneByAlbumId($albumId);
		if($album->issetAlbumId())
		{
			$approval = new PicoApproval($album, $entityInfo, $entityApvInfo, 
			function($param1, $param2, $param3){
				// approval validation here
				// if return false, approval can not be done
				
				return true;
			}, 
			function($param1, $param2, $param3){
				// callback when success
			}, 
			function($param1, $param2, $param3){
				// callback when failed
			} 
			);
			$approval->reject(new AlbumApv());
		}
	}
}
if($inputGet->getUserAction() == UserAction::INSERT)
{
?>

<form name="insertform" id="insertform" action="" method="post">
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%"/>
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td></td>
        <td><input type="submit" class="btn btn-success" name="save-button" id="save-insert" value="<?php echo $currentLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php echo $currentLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule;?>';"/></td>
      </tr>
    </tbody>
  </table>
</form>
<?php
}


