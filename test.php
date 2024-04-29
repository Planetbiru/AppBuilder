<?php

use MagicObject\MagicObject;
use MagicObject\SetterGetter;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use YourApplication\Data\Entity\DraftRating;
use YourApplication\Data\Entity\DraftRatingApv;
use YourApplication\Data\Entity\DraftRatingTrash;

require_once __DIR__ . "/inc.app/auth.php";

$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputGet->getUserAction() == UserAction::INSERT)
{
	$draftRating = new DraftRating(null, $database);
	$draftRating->setDraftRatingId($inputPost->getDraftRatingId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRating->setUserId($inputPost->getUserId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRating->setSongDraftId($inputPost->getSongDraftId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRating->setRating($inputPost->getRating(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRating->setDraft(true);
	$draftRating->setWaitingFor(1);
	$draftRating->setAdminCreate($currentAction->getUserId());
	$draftRating->setTimeCreate($currentAction->getTime());
	$draftRating->setIpCreate($currentAction->getIp());
	$draftRating->setAdminEdit($currentAction->getUserId());
	$draftRating->setTimeEdit($currentAction->getTime());
	$draftRating->setIpEdit($currentAction->getIp());

	$draftRating->insert();

	$draftRatingApv = new DraftRatingApv($draftRating, $database);

	$draftRatingApv->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$draftRating = new DraftRating(null, $database);

	$draftRatingApv = new DraftRatingApv(null, $database);
	$draftRatingApv->setDraftRatingId($inputPost->getDraftRatingId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRatingApv->setUserId($inputPost->getUserId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRatingApv->setSongDraftId($inputPost->getSongDraftId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRatingApv->setRating($inputPost->getRating(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$draftRatingApv->setAdminEdit($currentAction->getUserId());
	$draftRatingApv->setTimeEdit($currentAction->getTime());
	$draftRatingApv->setIpEdit($currentAction->getIp());

	$draftRatingApv->insert();

	$draftRating->setAdminAskEdit($currentAction->getUserId());
	$draftRating->setTimeAskEdit($currentAction->getTime());
	$draftRating->setIpAskEdit($currentAction->getIp());

	$draftRating->setDraftRatingId($draftRating->getDraftRatingId())->setWaitingFor(3)->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$draftRating = new DraftRating(null, $database);

			$draftRating->setAdminAskEdit($currentAction->getUserId());
			$draftRating->setTimeAskEdit($currentAction->getTime());
			$draftRating->setIpAskEdit($currentAction->getIp());

			$draftRating->setDraftRatingId($rowId)->setWaitingFor(3)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$draftRating = new DraftRating(null, $database);

			$draftRating->setAdminAskEdit($currentAction->getUserId());
			$draftRating->setTimeAskEdit($currentAction->getTime());
			$draftRating->setIpAskEdit($currentAction->getIp());

			$draftRating->setDraftRatingId($rowId)->setWaitingFor(4)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$draftRating = new DraftRating(null, $database);

			$draftRating->setAdminAskEdit($currentAction->getUserId());
			$draftRating->setTimeAskEdit($currentAction->getTime());
			$draftRating->setIpAskEdit($currentAction->getIp());

			$draftRating->setDraftRatingId($rowId)->setWaitingFor(5)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetDraftRatingId())
	{
		$draftRatingId = $inputPost->getDraftRatingId();
		$draftRating = new DraftRating(null, $database);
		$draftRating->findOneByDraftRatingId($draftRatingId);
		if($draftRating->issetDraftRatingId())
		{
			$approval = new PicoApproval($draftRating, $entityInfo, 
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

			// List of properties to be copied from DraftRatingApv to DraftRating. You can add or remove it
			$columToBeCopied = array(
				"userId", 
				"songDraftId", 
				"rating"
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

			$approval->approve($columToBeCopied, new DraftRatingApv(), new DraftRatingTrash(), $approvalCallback);
		}
	}
}
else if($inputGet->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetDraftRatingId())
	{
		$draftRatingId = $inputPost->getDraftRatingId();
		$draftRating = new DraftRating(null, $database);
		$draftRating->findOneByDraftRatingId($draftRatingId);
		if($draftRating->issetDraftRatingId())
		{
			$approval = new PicoApproval($draftRating, $entityInfo, 
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
			$approval->reject(new DraftRatingApv());
		}
	}
}


