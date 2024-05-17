<?php

// This script is generated automatically by AppBuilder
// Visit https://github.com/Planetbiru/AppBuilder

use MagicObject\SetterGetter;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoPageable;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Util\AttrUtil;
use AppBuilder\Field;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use AppBuilder\AppInclude;
use AppBuilder\AppModule;
use AppBuilder\AppEntityLanguage;
use AppBuilder\WaitingFor;
use AppBuilder\PicoTestUtil;
use AppBuilder\FormBuilder;
use YourApplication\Data\Entity\Album;
use YourApplication\Data\Entity\AlbumApv;
use YourApplication\Data\Entity\AlbumTrash;
use YourApplication\Data\Entity\Producer;

require_once __DIR__ . "/inc.app/auth.php";

$currentModule = new AppModule("album");
$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputPost->getUserAction() == UserAction::CREATE)
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
	$album->setWaitingFor(WaitingFor::CREATE);
	$album->setAdminCreate($currentAction->getUserId());
	$album->setTimeCreate($currentAction->getTime());
	$album->setIpCreate($currentAction->getIp());
	$album->setAdminEdit($currentAction->getUserId());
	$album->setTimeEdit($currentAction->getTime());
	$album->setIpEdit($currentAction->getIp());

	$album->insert();

	$albumApv = new AlbumApv($album, $database);
	$albumApv->insert();
	$albumUpdate = new Album(null, $database);
	$albumUpdate->setAlbumId($album->getAlbumId())->setApprovalId($albumApv->getAlbumApvId())->update();
}
else if($inputPost->getUserAction() == UserAction::UPDATE)
{
	$albumApv = new AlbumApv(null, $database);
	$albumApv->setAlbumId($inputPost->getAppBuilderNewPkAlbumId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
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

	$album = new Album(null, $database);
	$album->setAdminAskEdit($currentAction->getUserId());
	$album->setTimeAskEdit($currentAction->getTime());
	$album->setIpAskEdit($currentAction->getIp());
	$album->setAlbumId($inputPost->getAlbumId())->setApprovalId($albumApv->getAlbumApvId())->setApprovalIdWaitingFor(WaitingFor::UPDATE)->update();
}
else if($inputPost->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableCheckedRowId())
	{
		foreach($inputPost->getCheckedRowId() as $rowId)
		{
			$album = new Album(null, $database);
			try
			{
				$album->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setAlbumId($rowId))
					->addAnd(PicoPredicate::getInstance()->setWaitingFor(WaitingFor::NOTHING))
				)
				->setAdminAskEdit($currentAction->getUserId())
				->setTimeAskEdit($currentAction->getTime())
				->setIpAskEdit($currentAction->getIp())
				->setWaitingFor(WaitingFor::ACTIVATE)
				->update();
			}
			catch(Exception $e)
			{
				// Do something here when record is not found
			}
		}
	}
}
else if($inputPost->getUserAction() == UserAction::DEACTIVATE)
{
	if($inputPost->countableCheckedRowId())
	{
		foreach($inputPost->getCheckedRowId() as $rowId)
		{
			$album = new Album(null, $database);
			try
			{
				$album->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setAlbumId($rowId))
					->addAnd(PicoPredicate::getInstance()->setWaitingFor(WaitingFor::NOTHING))
				)
				->setAdminAskEdit($currentAction->getUserId())
				->setTimeAskEdit($currentAction->getTime())
				->setIpAskEdit($currentAction->getIp())
				->setWaitingFor(WaitingFor::DEACTIVATE)
				->update();
			}
			catch(Exception $e)
			{
				// Do something here when record is not found
			}
		}
	}
}
else if($inputPost->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableCheckedRowId())
	{
		foreach($inputPost->getCheckedRowId() as $rowId)
		{
			$album = new Album(null, $database);
			try
			{
				$album->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setAlbumId($rowId))
					->addAnd(PicoPredicate::getInstance()->setWaitingFor(WaitingFor::NOTHING))
				)
				->setAdminAskEdit($currentAction->getUserId())
				->setTimeAskEdit($currentAction->getTime())
				->setIpAskEdit($currentAction->getIp())
				->setWaitingFor(WaitingFor::DELETE)
				->update();
			}
			catch(Exception $e)
			{
				// Do something here when record is not found
			}
		}
	}
}
else if($inputPost->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetAlbumId())
	{
		$albumId = $inputPost->getAlbumId();
		$album = new Album(null, $database);
		$album->findOneByAlbumId($albumId);
		if($album->issetAlbumId())
		{
			$approval = new PicoApproval(
			$album, 
			$entityInfo, 
			$entityApvInfo, 
			function($param1, $param2, $param3, $userId) {
				// approval validation here
				// if the return is incorrect, approval cannot take place
				
				// e.g. return $param1->notEqualsAdminAskEdit($userId);
				return true;
			} 
			);

			$approvalCallback = new SetterGetter();
			$approvalCallback->setAfterInsert(function($param1, $param2, $param3) {
				// callback on new data
				// you code here
				
				return true;
			}); 

			$approvalCallback->setBeforeUpdate(function($param1, $param2, $param3) {
				// callback before update data
				// you code here
				
			}); 

			$approvalCallback->setAfterUpdate(function($param1, $param2, $param3) {
				// callback after update data
				// you code here
				
			}); 

			$approvalCallback->setAfterActivate(function($param1, $param2, $param3) {
				// callback after activate data
				// you code here
				
			}); 

			$approvalCallback->setAfterDeactivate(function($param1, $param2, $param3) {
				// callback after deactivate data
				// you code here
				
			}); 

			$approvalCallback->setBeforeDelete(function($param1, $param2, $param3) {
				// callback before delete data
				// you code here
				
			}); 

			$approvalCallback->setAfterDelete(function($param1, $param2, $param3) {
				// callback after delete data
				// you code here
				
			}); 

			$approvalCallback->setAfterApprove(function($param1, $param2, $param3) {
				// callback after approve data
				// you code here
				
			}); 

			// List of properties to be copied from AlbumApv to Album when when the user approves data modification. You can add or delete them.
			$columToBeCopied = array(
				Field::of()->name, 
				Field::of()->title, 
				Field::of()->description, 
				Field::of()->producerId, 
				Field::of()->releaseDate, 
				Field::of()->numberOfSong, 
				Field::of()->duration, 
				Field::of()->imagePath, 
				Field::of()->sortOrder, 
				Field::of()->locked, 
				Field::of()->asDraft, 
				Field::of()->active
			);

			$approval->approve($columToBeCopied, new AlbumApv(), new AlbumTrash(), 
			$currentAction->getUserId(),  
			$currentAction->getTime(),  
			$currentAction->getIp(), 
			$approvalCallback);
		}
	}
}
else if($inputPost->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetAlbumId())
	{
		$albumId = $inputPost->getAlbumId();
		$album = new Album(null, $database);
		$album->findOneByAlbumId($albumId);
		if($album->issetAlbumId())
		{
			$approval = new PicoApproval(
			$album, 
			$entityInfo, 
			$entityApvInfo, 
			function($param1, $param2, $param3, $userId) {
				// approval validation here
				// if the return is incorrect, approval cannot take place
				
				// e.g. return $param1->notEqualsAdminAskEdit($userId);
				return true;
			} 
			);

			$approvalCallback->setBeforeReject(function($param1, $param2, $param3) {
				// callback before reject data
				// you code here
				
			}); 

			$approvalCallback->setAfterReject(function($param1, $param2, $param3) {
				// callback after reject data
				// you code here
				
			}); 

			$approval->reject(new AlbumApv(),
			$currentAction->getUserId(),  
			$currentAction->getTime(),  
			$currentAction->getIp(), 
			$approvalCallback
			);
		}
	}
}
if($inputGet->getUserAction() == UserAction::CREATE)
{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new Album(), $appConfig);
?>
<div class="page page-insert">
	<div class="row">
		<form name="createform" id="createform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getAlbumId();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="album_id" id="album_id"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="name" id="name"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTitle();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="title" id="title"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDescription();?></td>
						<td>
							<textarea class="form-control" name="description" id="description" spellcheck="false"></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getProducerId();?></td>
						<td>
							<select class="form-control" name="producer_id" id="producer_id"><option value=""><?php echo $appLangauge->getSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->showList(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setDraft(false))
									->addAnd(PicoPredicate::getInstance()->setActive(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByProducerId(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name, null, array(Field::of()->numberOfSong, Field::of()->releaseDate)); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getReleaseDate();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="date" name="release_date" id="release_date"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getNumberOfSong();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="number" name="number_of_song" id="number_of_song"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDuration();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="duration" id="duration"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="image_path" id="image_path"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getSortOrder();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="number" name="sort_order" id="sort_order"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getLocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1"/> <?php echo $appEntityLanguage->getLocked();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAsDraft();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1"/> <?php echo $appEntityLanguage->getAsDraft();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="active" id="active" value="1"/> <?php echo $appEntityLanguage->getActive();?></label>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td>
							<input type="submit" class="btn btn-success" name="save-create" id="save-create" value="<?php echo $appLanguage->getButtonSave(); ?>"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
							<input type="hidden" name="user_action" value="<?php echo UserAction::CREATE;?>"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php 
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$album = new Album(null, $database);
	try{
		$album->findOneByAlbumId($inputGet->getAlbumId());
		if($album->hasValueAlbumId())
		{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new Album(), $appConfig);
?>
<div class="page page-update">
	<div class="row">
		<form name="updateform" id="updateform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getAlbumId();?></td>
						<td>
							<input class="form-control" type="text" name="app_builder_new_pk_album_id" id="album_id" value="<?php echo $album->getAlbumId();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<input class="form-control" type="text" name="name" id="name" value="<?php echo $album->getName();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTitle();?></td>
						<td>
							<input class="form-control" type="text" name="title" id="title" value="<?php echo $album->getTitle();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDescription();?></td>
						<td>
							<textarea class="form-control" name="description" id="description" spellcheck="false"><?php echo $album->getDescription();?></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getProducerId();?></td>
						<td>
							<select class="form-control" name="producer_id" id="producer_id"><option value=""><?php echo $appLangauge->getSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->showList(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setDraft(false))
									->addAnd(PicoPredicate::getInstance()->setActive(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByProducerId(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name, $album->getProducerId(), array(Field::of()->numberOfSong, Field::of()->releaseDate)); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getReleaseDate();?></td>
						<td>
							<input class="form-control" type="date" name="release_date" id="release_date" value="<?php echo $album->getReleaseDate();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getNumberOfSong();?></td>
						<td>
							<input class="form-control" type="number" name="number_of_song" id="number_of_song" value="<?php echo $album->getNumberOfSong();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDuration();?></td>
						<td>
							<input class="form-control" type="text" name="duration" id="duration" value="<?php echo $album->getDuration();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<input class="form-control" type="text" name="image_path" id="image_path" value="<?php echo $album->getImagePath();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getSortOrder();?></td>
						<td>
							<input class="form-control" type="number" name="sort_order" id="sort_order" value="<?php echo $album->getSortOrder();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getLocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1" <?php echo $album->createCheckedLocked();?>/> <?php echo $appEntityLanguage->getLocked();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAsDraft();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1" <?php echo $album->createCheckedAsDraft();?>/> <?php echo $appEntityLanguage->getAsDraft();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="active" id="active" value="1" <?php echo $album->createCheckedActive();?>/> <?php echo $appEntityLanguage->getActive();?></label>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td>
							<input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php echo $appLanguage->getButtonSave(); ?>"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
							<input type="hidden" name="user_action" value="<?php echo UserAction::UPDATE;?>"/>
							<input type="hidden" name="album_id" value="<?php echo $album->getAlbumId();?>"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php 
		}
		else
		{
			// Do somtething here when data is not found
			?>
			<div class="alert alert-warning"><?php echo $appLanguage->getMessageDataNotFound();?></div>
			<?php
		}
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
	}
	catch(Exception $e)
	{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
		// Do somtething here when exception
		?>
		<div class="alert alert-danger"><?php echo $e->getMessage();?></div>
		<?php
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
	}
}
else if($inputGet->getUserAction() == UserAction::DETAIL)
{
	$album = new Album(null, $database);
	try{
		$album->findOneByAlbumId($inputGet->getAlbumId());
		if($album->hasValueAlbumId())
		{
			if($album->notNullApprovalId())
			{
				$albumApv = new AlbumApv(null, $database);
				try
				{
					$albumApv->find($album->getApprovalId());
				}
				catch(Exception $e)
				{
					// do something here
				}
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new Album(), $appConfig);
?>
<div class="page page-detail">
	<div class="row">
		<form name="detailform" id="detailform" action="" method="post">
			<div class="alert alert-info">	
			<?php
			if($album->getWaitingFor() == WaitingFor::CREATE)
			{
			    echo $appLanguage->getMessageWaitingForCreate();
			}
			else if($album->getWaitingFor() == WaitingFor::UPDATE)
			{
			    echo $appLanguage->getMessageWaitingForUpdate();
			}
			else if($album->getWaitingFor() == WaitingFor::ACTIVATE)
			{
			    echo $appLanguage->getMessageWaitingForActivate();
			}
			else if($album->getWaitingFor() == WaitingFor::DEACTIVATE)
			{
			    echo $appLanguage->getMessageWaitingForDeactivate();
			}
			else if($album->getWaitingFor() == WaitingFor::DELETE)
			{
			    echo $appLanguage->getMessageWaitingForDelete();
			}
			?>
			</div>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getAlbumId();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAlbumId($albumApv->getAlbumId()));?>"><?php echo $album->getAlbumId();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAlbumId($albumApv->getAlbumId()));?>"><?php echo $albumApv->getAlbumId();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsName($albumApv->getName()));?>"><?php echo $album->getName();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsName($albumApv->getName()));?>"><?php echo $albumApv->getName();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTitle();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTitle($albumApv->getTitle()));?>"><?php echo $album->getTitle();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTitle($albumApv->getTitle()));?>"><?php echo $albumApv->getTitle();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDescription();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDescription($albumApv->getDescription()));?>"><?php echo $album->getDescription();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDescription($albumApv->getDescription()));?>"><?php echo $albumApv->getDescription();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getProducerId();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsProducerId($albumApv->getProducerId()));?>"><?php echo $album->getProducerId();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsProducerId($albumApv->getProducerId()));?>"><?php echo $albumApv->getProducerId();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getReleaseDate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsReleaseDate($albumApv->getReleaseDate()));?>"><?php echo $album->getReleaseDate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsReleaseDate($albumApv->getReleaseDate()));?>"><?php echo $albumApv->getReleaseDate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getNumberOfSong();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsNumberOfSong($albumApv->getNumberOfSong()));?>"><?php echo $album->getNumberOfSong();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsNumberOfSong($albumApv->getNumberOfSong()));?>"><?php echo $albumApv->getNumberOfSong();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDuration();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDuration($albumApv->getDuration()));?>"><?php echo $album->getDuration();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDuration($albumApv->getDuration()));?>"><?php echo $albumApv->getDuration();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsImagePath($albumApv->getImagePath()));?>"><?php echo $album->getImagePath();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsImagePath($albumApv->getImagePath()));?>"><?php echo $albumApv->getImagePath();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getSortOrder();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsSortOrder($albumApv->getSortOrder()));?>"><?php echo $album->getSortOrder();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsSortOrder($albumApv->getSortOrder()));?>"><?php echo $albumApv->getSortOrder();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeCreate($albumApv->getTimeCreate()));?>"><?php echo $album->getTimeCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeCreate($albumApv->getTimeCreate()));?>"><?php echo $albumApv->getTimeCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeEdit($albumApv->getTimeEdit()));?>"><?php echo $album->getTimeEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeEdit($albumApv->getTimeEdit()));?>"><?php echo $albumApv->getTimeEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminCreate($albumApv->getAdminCreate()));?>"><?php echo $album->getAdminCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminCreate($albumApv->getAdminCreate()));?>"><?php echo $albumApv->getAdminCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminEdit($albumApv->getAdminEdit()));?>"><?php echo $album->getAdminEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminEdit($albumApv->getAdminEdit()));?>"><?php echo $albumApv->getAdminEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpCreate($albumApv->getIpCreate()));?>"><?php echo $album->getIpCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpCreate($albumApv->getIpCreate()));?>"><?php echo $albumApv->getIpCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpEdit($albumApv->getIpEdit()));?>"><?php echo $album->getIpEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpEdit($albumApv->getIpEdit()));?>"><?php echo $albumApv->getIpEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getLocked();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsLocked($albumApv->getLocked()));?>"><?php echo $album->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsLocked($albumApv->getLocked()));?>"><?php echo $albumApv->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAsDraft();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAsDraft($albumApv->getAsDraft()));?>"><?php echo $album->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAsDraft($albumApv->getAsDraft()));?>"><?php echo $albumApv->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsActive($albumApv->getActive()));?>"><?php echo $album->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsActive($albumApv->getActive()));?>"><?php echo $albumApv->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td>
							<?php
							if($inputGet->getNextAction() == UserAction::APPROVE)
							{
							?>
							<input type="submit" class="btn btn-success" name="action_approval" id="action_approval" value="<?php echo $appLanguage->getButtonApprove(); ?>"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
							<input type="hidden" name="user_action" value="<?php echo UserAction::APPROVE;?>"/>
							<input type="hidden" name="album_id" value="<?php echo $album->getAlbumId();?>"/>
							<?php
							}
							else
							{
							?>
							<input type="submit" class="btn btn-success" name="action_approval" id="action_approval" value="<?php echo $appLanguage->getButtonReject(); ?>"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
							<input type="hidden" name="user_action" value="<?php echo UserAction::REJECT;?>"/>
							<input type="hidden" name="album_id" value="<?php echo $album->getAlbumId();?>"/>
							<?php
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php 
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
			}
			else
			{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new Album(), $appConfig);
?>
<div class="page page-detail">
	<div class="row">
		<form name="detailform" id="detailform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getAlbumId();?></td>
						<td><?php echo $album->getAlbumId();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td><?php echo $album->getName();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTitle();?></td>
						<td><?php echo $album->getTitle();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDescription();?></td>
						<td><?php echo $album->getDescription();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getProducerId();?></td>
						<td><?php echo $album->getProducerId();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getReleaseDate();?></td>
						<td><?php echo $album->getReleaseDate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getNumberOfSong();?></td>
						<td><?php echo $album->getNumberOfSong();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getDuration();?></td>
						<td><?php echo $album->getDuration();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td><?php echo $album->getImagePath();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getSortOrder();?></td>
						<td><?php echo $album->getSortOrder();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeCreate();?></td>
						<td><?php echo $album->getTimeCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeEdit();?></td>
						<td><?php echo $album->getTimeEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminCreate();?></td>
						<td><?php echo $album->getAdminCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminEdit();?></td>
						<td><?php echo $album->getAdminEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpCreate();?></td>
						<td><?php echo $album->getIpCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpEdit();?></td>
						<td><?php echo $album->getIpEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getLocked();?></td>
						<td><?php echo $album->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAsDraft();?></td>
						<td><?php echo $album->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td><?php echo $album->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonUpdate(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl(UserAction::UPDATE, Field::of()->album_id, $album->getAlbumId());?>';"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonBackToList(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
<?php 
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
			}
		}
		else
		{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
			// Do somtething here when data is not found
			?>
			<div class="alert alert-warning"><?php echo $appLanguage->getMessageDataNotFound();?></div>
			<?php
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
		}
	}
	catch(Exception $e)
	{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
		// Do somtething here when exception
		?>
		<div class="alert alert-danger"><?php echo $e->getMessage();?></div>
		<?php
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
	}
}
else 
{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new Album(), $appConfig);
?>
<div class="page page-list">
	<div class="row">
		<div class="filter-section">
			<form action="" method="get" class="filter-form">
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getName();?></span>
					<span class="filter-control">
						<input type="text" name="name" class="form-control" value="<?php echo $inputGet->getName();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getTitle();?></span>
					<span class="filter-control">
						<input type="text" name="title" class="form-control" value="<?php echo $inputGet->getTitle();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getProducerId();?></span>
					<span class="filter-control">
							<select name="producer_id" class="form-control">
								<?php echo FormBuilder::getInstance()->showList(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setDraft(false))
									->addAnd(PicoPredicate::getInstance()->setActive(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByProducerId(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name, $inputGet->getProducerId(), array(Field::of()->numberOfSong, Field::of()->releaseDate)); ?>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getReleaseDate();?></span>
					<span class="filter-control">
						<input type="text" name="release_date" class="form-control" value="<?php echo $inputGet->getReleaseDate();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getDuration();?></span>
					<span class="filter-control">
						<input type="text" name="duration" class="form-control" value="<?php echo $inputGet->getDuration();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getActive();?></span>
					<span class="filter-control">
							<select name="active" class="form-control" data-value="<?php echo $inputGet->getActive();?>">
								<option value="" <?php echo AttrUtil::selected($inputGet->getActive(), '');?>><?php echo $appLanguage->getOptionLabelSelectOne();?></option>
								<option value="true" <?php echo AttrUtil::selected($inputGet->getActive(), 'true');?>><?php echo $appLanguage->getOptionLabelYes();?></option>
								<option value="false" <?php echo AttrUtil::selected($inputGet->getActive(), 'false');?>><?php echo $appLanguage->getOptionLabelNo();?></option>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<input type="submit" class="btn btn-success" value="<?php echo $appLanguage->getButtonSearch();?>"/>
				</span>
		
				<span class="filter-group">
					<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonAdd();?>" onlick="window.location='<?php echo $currentModule->getRedirectUrl(UserAction::CREATE);?>'"/>
				</span>
			</form>
		</div>
		<div class="data-section">
			<?php 	
			$specMap = array(
			    "name" => "name",
				"title" => "title",
				"producerId" => "producerId",
				"releaseDate" => "releaseDate",
				"duration" => "duration",
				"active" => "active"
			);
			$sortOrderMap = array(
			    "albumId" => "albumId",
				"name" => "name",
				"title" => "title",
				"description" => "description",
				"producerId" => "producerId",
				"releaseDate" => "releaseDate",
				"numberOfSong" => "numberOfSong",
				"duration" => "duration",
				"imagePath" => "imagePath",
				"sortOrder" => "sortOrder",
				"timeCreate" => "timeCreate",
				"timeEdit" => "timeEdit",
				"adminCreate" => "adminCreate",
				"adminEdit" => "adminEdit",
				"ipCreate" => "ipCreate",
				"ipEdit" => "ipEdit",
				"locked" => "locked",
				"asDraft" => "asDraft",
				"active" => "active"
			);
			
			// You can define your own specifications
			// Pay attention to security issues
			$specification = PicoSpecification::fromUserInput($inputGet, $specMap);
			
			// You can define your own sortable
			// Pay attention to security issues
			$sortable = PicoSortable::fromUserInput($inputGet, $sortOrderMap);
			
			$pageable = new PicoPageable(new PicoPage($inputGet->getPage(), $appConfig->getPageSize()), $sortable);
			$dataLoader = new Album(null, $database);
			$pageData = $dataLoader->findAll($specification, $pagable, $sortable);
			$resultSet = $pageData->getResult();
			
			if($pageData->getTotalResult() > 0)
			{
			?>
			<div class="pagination pagination-top">
			    <div class="pagination-number">
			    <?php
			    foreach($rowData->getPagination() as $pg)
			    {
			        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="<?php echo PicoPagination::getPageUrl($pg['page']);?>"><?php echo $pg['page'];?></a></span><?php
			    }
			    ?>
			    </div>
			</div>
			<form action="" method="post" class="data-form">
				<div class="data-wrapper">
					<table class="table table-row">
						<thead>
							<tr>
								<td class="data-sort data-sort-header"></td>
								<td class="data-selector" data-key="album_id">
									<input type="checkbox" class="checkbox check-master" data-selector=".checkbox-album-id"/>
								</td>
								<td>
									<span class="fa fa-edit"></span>
								</td>
								<td>
									<span class="fa fa-folder"></span>
								</td>
								<td data-field="album_id"><?php echo $appEntityLanguage->getAlbumId();?></td>
								<td data-field="name"><?php echo $appEntityLanguage->getName();?></td>
								<td data-field="title"><?php echo $appEntityLanguage->getTitle();?></td>
								<td data-field="description"><?php echo $appEntityLanguage->getDescription();?></td>
								<td data-field="producer_id"><?php echo $appEntityLanguage->getProducerId();?></td>
								<td data-field="release_date"><?php echo $appEntityLanguage->getReleaseDate();?></td>
								<td data-field="number_of_song"><?php echo $appEntityLanguage->getNumberOfSong();?></td>
								<td data-field="duration"><?php echo $appEntityLanguage->getDuration();?></td>
								<td data-field="image_path"><?php echo $appEntityLanguage->getImagePath();?></td>
								<td data-field="sort_order"><?php echo $appEntityLanguage->getSortOrder();?></td>
								<td data-field="time_create"><?php echo $appEntityLanguage->getTimeCreate();?></td>
								<td data-field="time_edit"><?php echo $appEntityLanguage->getTimeEdit();?></td>
								<td data-field="admin_create"><?php echo $appEntityLanguage->getAdminCreate();?></td>
								<td data-field="admin_edit"><?php echo $appEntityLanguage->getAdminEdit();?></td>
								<td data-field="ip_create"><?php echo $appEntityLanguage->getIpCreate();?></td>
								<td data-field="ip_edit"><?php echo $appEntityLanguage->getIpEdit();?></td>
								<td data-field="locked"><?php echo $appEntityLanguage->getLocked();?></td>
								<td data-field="as_draft"><?php echo $appEntityLanguage->getAsDraft();?></td>
								<td data-field="active"><?php echo $appEntityLanguage->getActive();?></td>
							</tr>
						</thead>
					
						<tbody>
							<?php 
							foreach($resultSet as $dataIndex => $album)
							{
							?>
							<tr data-primary-key="<?php echo $album->getAlbumId();?>" data-sort-order="<?php echo $album->getSortOrder();?>">
								<td class="data-sort data-sort-body data-sort-control"></td>
								<td class="data-selector" data-key="album_id">
									<input type="checkbox" class="checkbox check-slave checkbox-album-id" name="checked_row_id[]" value="<?php echo $album->getAlbumId();?>"/>
								</td>
								<td>
									<a class="edit-control" href="<?php echo $currentModule->getRedirectUrl(UserAction::UPDATE, Field::of()->album_id, $objectName->getAlbumId);?>"><span class="fa fa-edit"></span></a>
								</td>
								<td>
									<a class="detail-control field-master" href="<?php echo $currentModule->getRedirectUrl(UserAction::DETAIL, Field::of()->album_id, $objectName->getAlbumId);?>"><span class="fa fa-folder"></span></a>
								</td>
								<td data-field="album_id"><?php echo $album->getAlbumId();?></td>
								<td data-field="name"><?php echo $album->getName();?></td>
								<td data-field="title"><?php echo $album->getTitle();?></td>
								<td data-field="description"><?php echo $album->getDescription();?></td>
								<td data-field="producer_id"><?php echo $album->getProducerId();?></td>
								<td data-field="release_date"><?php echo $album->getReleaseDate();?></td>
								<td data-field="number_of_song"><?php echo $album->getNumberOfSong();?></td>
								<td data-field="duration"><?php echo $album->getDuration();?></td>
								<td data-field="image_path"><?php echo $album->getImagePath();?></td>
								<td data-field="sort_order"><?php echo $album->getSortOrder();?></td>
								<td data-field="time_create"><?php echo $album->getTimeCreate();?></td>
								<td data-field="time_edit"><?php echo $album->getTimeEdit();?></td>
								<td data-field="admin_create"><?php echo $album->getAdminCreate();?></td>
								<td data-field="admin_edit"><?php echo $album->getAdminEdit();?></td>
								<td data-field="ip_create"><?php echo $album->getIpCreate();?></td>
								<td data-field="ip_edit"><?php echo $album->getIpEdit();?></td>
								<td data-field="locked"><?php echo $album->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></td>
								<td data-field="as_draft"><?php echo $album->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></td>
								<td data-field="active"><?php echo $album->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></td>
							</tr>
							<?php 
							}
							?>
						</tbody>
					</table>
				</div>
			</form>
			<div class="pagination pagination-bottom">
			    <div class="pagination-number">
			    <?php
			    foreach($rowData->getPagination() as $pg)
			    {
			        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="<?php echo PicoPagination::getPageUrl($pg['page']);?>"><?php echo $pg['page'];?></a></span><?php
			    }
			    ?>
			    </div>
			</div>
			
			<?php 
			}
			else
			{
			?>
			    <div class="alert alert-info"><?php echo $appLanguage->getMessageDataNotFound();?></div>
			<?php
			}
			?>
		</div>
	</div>
</div>
<?php 
require_once AppInclude::mainAppFooter(__DIR__, $appConfig);
}

