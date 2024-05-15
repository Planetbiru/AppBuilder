# AppBuilder

## History

Imagine a large application consisting of dozens of CRUD (Create, Read, Update, Delete) modules. Each module has the following mechanism:

1. create new data
2. change existing data
3. delete existing data
4. requires approval to create new data, change data and delete data
5. have a rule that the user who approves the creation, change and deletion of data must be different from the user who creates, changes and deletes data

This project must be created in a very fast time, even less than 3 months.

In this situation, the project owner definitely needs a tool to create applications very quickly but without errors.

AppBuilder is the answer to all this.

Of course. Because with AppBuilder, a CRUD module that has the features mentioned above can be created in less than 30 minutes. Yes, you didn't read it wrong and I didn't write it wrong. 30 minutes is the time needed for developers to select columns from a module. Is the input in the form of inline text, textarea, select or checkbox and what filter is appropriate for that column. Of course, there is still plenty of time left and enough to edit the program code manually if necessary.

If a module can be created in 30 minutes, then in one day, a developer can create at least 16 new CRUD modules. Within 2 weeks, a developer can create 160 standard CRUD modules with the features above.

Of course, an application cannot contain only simple CRUD modules. But at least, a simple CRUD module won't take much time to create. Available time can be maximized for other tasks such as data processing, report creation and application testing.

AppBuilder uses MagicObject as its library. MagicObjects is very useful for creating entities from a table without having to type code. Just select the table and specify the name of the entity to be created. Entities will be created automatically by AppBuilder according to the names and column types of a table.

## CRUD Example

```php
<?php

// This script is generated automatically by AppBuilder
// Visit https://github.com/Planetbiru/AppBuilder

use MagicObject\SetterGetter;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use AppBuilder\AppInclude;
use AppBuilder\EntityLabel;
use AppBuilder\WaitingFor;
use AppBuilder\PicoTestUtil;
use YourApplication\Data\Entity\Album;
use YourApplication\Data\Entity\AlbumApv;
use YourApplication\Data\Entity\AlbumTrash;
use YourApplication\Data\Entity\Producer;

require_once __DIR__ . "/inc.app/auth.php";

$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputGet->getUserAction() == UserAction::CREATE)
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
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
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

	$album = new Album(null, $database);
	$album->setAdminAskEdit($currentAction->getUserId());
	$album->setTimeAskEdit($currentAction->getTime());
	$album->setIpAskEdit($currentAction->getIp());
	$album->setAlbumId($albumApv->getAlbumId())->setApprovalId($albumApv->getAlbumApvId())->setApprovalIdWaitingFor(WaitingFor::UPDATE)->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATE)
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
else if($inputGet->getUserAction() == UserAction::DEACTIVATE)
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
else if($inputGet->getUserAction() == UserAction::DELETE)
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
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetAlbumId())
	{
		$albumId = $inputPost->getAlbumId();
		$album = new Album(null, $database);
		$album->findOneByAlbumId($albumId);
		if($album->issetAlbumId())
		{
			$approval = new PicoApproval($album, $entityInfo, $entityApvInfo, $currentAction->getUserId(), 

			function($param1, $param2, $param3, $userId){
				// approval validation here
				// if the return is incorrect, approval cannot take place
				
				// return $param1->notEqualsAdminAskEdit($userId);
				return true;
			}, 
			function($param1, $param2, $param3){
				// callback when approved
			}, 
			function($param1, $param2, $param3){
				// callback when rejected
			} 
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

			// List of properties to be copied from AlbumApv to Album when user approve data modification. You can add or remove it
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
			$approval = new PicoApproval($album, $entityInfo, $entityApvInfo, $currentAction->getUserId(), 

			function($param1, $param2, $param3, $userId){
				// approval validation here
				// if the return is incorrect, approval cannot take place
				
				// return $param1->notEqualsAdminAskEdit($userId);
				return true;
			}, 
			function($param1, $param2, $param3){
				// callback when approved
			}, 
			function($param1, $param2, $param3){
				// callback when rejected
			} 
			);
			$approval->reject(new AlbumApv());
		}
	}
}
if($inputGet->getUserAction() == UserAction::CREATE)
{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLabel = new EntityLabel(new Album(), $appConfig);
?>
<div class="page page-insert">
	<div class="row">
		<form name="insertform" id="insertform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLabel->getAlbumId();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="album_id" id="album_id"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getName();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="name" id="name"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTitle();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="title" id="title"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDescription();?></td>
						<td>
							<textarea class="form-control" name="description" id="description" spellcheck="false"></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getProducerId();?></td>
						<td>
							<select class="form-control" name="producer_id" id="producer_id"><option value=""><?php echo $appLangauge->getSelectOne();?></option>
								<?php echo $selecOptionReference->showList(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setNumberOfSong(3))
									->addAnd(PicoPredicate::getInstance()->setReleaseDate('2024-01-03'))
									->addAnd(PicoPredicate::getInstance()->setActive(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByTimeCreate(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByProducerId(PicoSort::ORDER_TYPE_ASC)), 
								"producerId", "name", null, array("numberOfSong", "releaseDate")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getReleaseDate();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="date" name="release_date" id="release_date"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getNumberOfSong();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="number" name="number_of_song" id="number_of_song"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDuration();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="duration" id="duration"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getImagePath();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="image_path" id="image_path"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getSortOrder();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="number" name="sort_order" id="sort_order"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getLocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1"/> <?php echo $appEntityLabel->getLocked();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAsDraft();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1"/> <?php echo $appEntityLabel->getAsDraft();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getActive();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="active" id="active" value="1"/> <?php echo $appEntityLabel->getActive();?></label>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td><input type="submit" class="btn btn-success" name="save-insert" id="save-insert" value="<?php echo $appLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
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
$appEntityLabel = new EntityLabel(new Album(), $appConfig);
?>
<div class="page page-update">
	<div class="row">
		<form name="updateform" id="updateform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLabel->getAlbumId();?></td>
						<td>
							<input class="form-control" type="text" name="album_id" id="album_id" value="<?php echo $album->getAlbumId();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getName();?></td>
						<td>
							<input class="form-control" type="text" name="name" id="name" value="<?php echo $album->getName();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTitle();?></td>
						<td>
							<input class="form-control" type="text" name="title" id="title" value="<?php echo $album->getTitle();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDescription();?></td>
						<td>
							<textarea class="form-control" name="description" id="description" spellcheck="false"><?php echo $album->getDescription();?></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getProducerId();?></td>
						<td>
							<select class="form-control" name="producer_id" id="producer_id"><option value=""><?php echo $appLangauge->getSelectOne();?></option>
								<?php echo $selecOptionReference->showList(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setNumberOfSong(3))
									->addAnd(PicoPredicate::getInstance()->setReleaseDate('2024-01-03'))
									->addAnd(PicoPredicate::getInstance()->setActive(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByTimeCreate(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByProducerId(PicoSort::ORDER_TYPE_ASC)), 
								"producerId", "name", $album->getProducerId(), array("numberOfSong", "releaseDate")); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getReleaseDate();?></td>
						<td>
							<input class="form-control" type="date" name="release_date" id="release_date" value="<?php echo $album->getReleaseDate();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getNumberOfSong();?></td>
						<td>
							<input class="form-control" type="number" name="number_of_song" id="number_of_song" value="<?php echo $album->getNumberOfSong();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDuration();?></td>
						<td>
							<input class="form-control" type="text" name="duration" id="duration" value="<?php echo $album->getDuration();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getImagePath();?></td>
						<td>
							<input class="form-control" type="text" name="image_path" id="image_path" value="<?php echo $album->getImagePath();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getSortOrder();?></td>
						<td>
							<input class="form-control" type="number" name="sort_order" id="sort_order" value="<?php echo $album->getSortOrder();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getLocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1" <?php echo $album->createCheckedLocked();?>/> <?php echo $appEntityLabel->getLocked();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAsDraft();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1" <?php echo $album->createCheckedAsDraft();?>/> <?php echo $appEntityLabel->getAsDraft();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getActive();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="active" id="active" value="1" <?php echo $album->createCheckedActive();?>/> <?php echo $appEntityLabel->getActive();?></label>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td><input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php echo $appLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
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
			// Do somtething here when data is not found
		}
	}
	catch(Exception $e)
	{
		// Do somtething here when exception
	}
}
else if($inputGet->getUserAction() == UserAction::DETAIL)
{
	$album = new Album(null, $database);
	try{
		$album->findOneByAlbumId($inputGet->getAlbumId());
		if($album->hasValueAlbumId())
		{
			if($album->nonNullApprovalId())
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
$appEntityLabel = new EntityLabel(new Album(), $appConfig);
?>
<div class="page page-detail">
	<div class="row">
		<form name="detailform" id="detailform" action="" method="post">
			<div class="alert alert-warning"><?php echo $appLanguage->message($album->getWaitingFor());?></div>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLabel->getAlbumId();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAlbumId($albumApv->getAlbumId()));?>"><?php echo $album->getAlbumId();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAlbumId($albumApv->getAlbumId()));?>"><?php echo $albumApv->getAlbumId();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getName();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsName($albumApv->getName()));?>"><?php echo $album->getName();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsName($albumApv->getName()));?>"><?php echo $albumApv->getName();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTitle();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTitle($albumApv->getTitle()));?>"><?php echo $album->getTitle();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTitle($albumApv->getTitle()));?>"><?php echo $albumApv->getTitle();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDescription();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDescription($albumApv->getDescription()));?>"><?php echo $album->getDescription();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDescription($albumApv->getDescription()));?>"><?php echo $albumApv->getDescription();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getProducerId();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsProducerId($albumApv->getProducerId()));?>"><?php echo $album->getProducerId();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsProducerId($albumApv->getProducerId()));?>"><?php echo $albumApv->getProducerId();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getReleaseDate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsReleaseDate($albumApv->getReleaseDate()));?>"><?php echo $album->getReleaseDate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsReleaseDate($albumApv->getReleaseDate()));?>"><?php echo $albumApv->getReleaseDate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getNumberOfSong();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsNumberOfSong($albumApv->getNumberOfSong()));?>"><?php echo $album->getNumberOfSong();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsNumberOfSong($albumApv->getNumberOfSong()));?>"><?php echo $albumApv->getNumberOfSong();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDuration();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDuration($albumApv->getDuration()));?>"><?php echo $album->getDuration();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsDuration($albumApv->getDuration()));?>"><?php echo $albumApv->getDuration();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getImagePath();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsImagePath($albumApv->getImagePath()));?>"><?php echo $album->getImagePath();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsImagePath($albumApv->getImagePath()));?>"><?php echo $albumApv->getImagePath();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getSortOrder();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsSortOrder($albumApv->getSortOrder()));?>"><?php echo $album->getSortOrder();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsSortOrder($albumApv->getSortOrder()));?>"><?php echo $albumApv->getSortOrder();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTimeCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeCreate($albumApv->getTimeCreate()));?>"><?php echo $album->getTimeCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeCreate($albumApv->getTimeCreate()));?>"><?php echo $albumApv->getTimeCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTimeEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeEdit($albumApv->getTimeEdit()));?>"><?php echo $album->getTimeEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsTimeEdit($albumApv->getTimeEdit()));?>"><?php echo $albumApv->getTimeEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAdminCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminCreate($albumApv->getAdminCreate()));?>"><?php echo $album->getAdminCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminCreate($albumApv->getAdminCreate()));?>"><?php echo $albumApv->getAdminCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAdminEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminEdit($albumApv->getAdminEdit()));?>"><?php echo $album->getAdminEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAdminEdit($albumApv->getAdminEdit()));?>"><?php echo $albumApv->getAdminEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getIpCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpCreate($albumApv->getIpCreate()));?>"><?php echo $album->getIpCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpCreate($albumApv->getIpCreate()));?>"><?php echo $albumApv->getIpCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getIpEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpEdit($albumApv->getIpEdit()));?>"><?php echo $album->getIpEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsIpEdit($albumApv->getIpEdit()));?>"><?php echo $albumApv->getIpEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getLocked();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsLocked($albumApv->getLocked()));?>"><?php echo $album->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsLocked($albumApv->getLocked()));?>"><?php echo $albumApv->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAsDraft();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAsDraft($albumApv->getAsDraft()));?>"><?php echo $album->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($album->notEqualsAsDraft($albumApv->getAsDraft()));?>"><?php echo $albumApv->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getActive();?></td>
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
						<td><input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php echo $appLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
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
$appEntityLabel = new EntityLabel(new Album(), $appConfig);
?>
<div class="page page-detail">
	<div class="row">
		<form name="detailform" id="detailform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLabel->getAlbumId();?></td>
						<td><?php echo $album->getAlbumId();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getName();?></td>
						<td><?php echo $album->getName();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTitle();?></td>
						<td><?php echo $album->getTitle();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDescription();?></td>
						<td><?php echo $album->getDescription();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getProducerId();?></td>
						<td><?php echo $album->getProducerId();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getReleaseDate();?></td>
						<td><?php echo $album->getReleaseDate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getNumberOfSong();?></td>
						<td><?php echo $album->getNumberOfSong();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getDuration();?></td>
						<td><?php echo $album->getDuration();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getImagePath();?></td>
						<td><?php echo $album->getImagePath();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getSortOrder();?></td>
						<td><?php echo $album->getSortOrder();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTimeCreate();?></td>
						<td><?php echo $album->getTimeCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getTimeEdit();?></td>
						<td><?php echo $album->getTimeEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAdminCreate();?></td>
						<td><?php echo $album->getAdminCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAdminEdit();?></td>
						<td><?php echo $album->getAdminEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getIpCreate();?></td>
						<td><?php echo $album->getIpCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getIpEdit();?></td>
						<td><?php echo $album->getIpEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getLocked();?></td>
						<td><?php echo $album->optionLocked($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getAsDraft();?></td>
						<td><?php echo $album->optionAsDraft($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLabel->getActive();?></td>
						<td><?php echo $album->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td><input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php echo $appLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
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
			// Do somtething here when data is not found
		}
	}
	catch(Exception $e)
	{
		// Do somtething here when exception
	}
}
```

### Entity

```php
<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="album")
 */
class Album extends MagicObject
{
	/**
	 * Album ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=false)
	 * @Label(content="Album ID")
	 * @var string
	 */
	protected $albumId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Title
	 * 
	 * @Column(name="title", type="text", nullable=true)
	 * @Label(content="Title")
	 * @var string
	 */
	protected $title;

	/**
	 * Description
	 * 
	 * @Column(name="description", type="longtext", nullable=true)
	 * @Label(content="Description")
	 * @var string
	 */
	protected $description;

	/**
	 * Producer ID
	 * 
	 * @Column(name="producer_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Producer ID")
	 * @var string
	 */
	protected $producerId;

	/**
	 * Release Date
	 * 
	 * @Column(name="release_date", type="date", nullable=true)
	 * @Label(content="Release Date")
	 * @var string
	 */
	protected $releaseDate;

	/**
	 * Number Of Song
	 * 
	 * @Column(name="number_of_song", type="int(11)", length=11, nullable=true)
	 * @Label(content="Number Of Song")
	 * @var integer
	 */
	protected $numberOfSong;

	/**
	 * Duration
	 * 
	 * @Column(name="duration", type="float", nullable=true)
	 * @Label(content="Duration")
	 * @var double
	 */
	protected $duration;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Sort Order
	 * 
	 * @Column(name="sort_order", type="int(11)", length=11, nullable=true)
	 * @Label(content="Sort Order")
	 * @var integer
	 */
	protected $sortOrder;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @Label(content="Time Edit")
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @Label(content="Admin Create")
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Admin Edit")
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="IP Create")
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="IP Edit")
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @Label(content="Active")
	 * @var boolean
	 */
	protected $active;

	/**
	 * As Draft
	 * 
	 * @Column(name="as_draft", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $asDraft;

}
```