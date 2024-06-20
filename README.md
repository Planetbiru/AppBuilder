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

The following PHP code was created in less than 5 minute and already has the following features:

1. create new data
2. change existing data
3. activate data
4. disable data
5. delete data
6. move the deleted data to the trash table
7. approve to the creation, change and deletion of data
8. reject creation, change and deletion of data
9. display data using filters and sorting data
10. update sort order
11. join entity
12. select control with entity and map
13. multiple language support

Apart from the features above, the module is also equipped with data filters that are adjusted to the data type.

### Original Table Structure

```sql

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` varchar(40) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `birth_day` varchar(100) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `time_zone` varchar(255) DEFAULT NULL,
  `user_type_id` varchar(40) DEFAULT NULL,
  `associated_artist` varchar(40) DEFAULT NULL,
  `associated_producer` varchar(40) DEFAULT NULL,
  `current_role` varchar(40) DEFAULT NULL,
  `image_path` text,
  `time_create` timestamp NULL DEFAULT NULL,
  `time_edit` timestamp NULL DEFAULT NULL,
  `admin_create` varchar(40) DEFAULT NULL,
  `admin_edit` varchar(40) DEFAULT NULL,
  `ip_create` varchar(50) DEFAULT NULL,
  `ip_edit` varchar(50) DEFAULT NULL,
  `reset_password_hash` varchar(256) DEFAULT NULL,
  `last_reset_password` timestamp NULL DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);
```

### Module

```php
<?php

// This script is generated automatically by AppBuilder
// Visit https://github.com/Planetbiru/AppBuilder

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
use MagicApp\Field;
use MagicApp\UserAction;
use MagicApp\AppInclude;
use MagicApp\AppModule;
use MagicApp\AppEntityLanguage;
use MagicObject\SetterGetter;
use MagicApp\PicoApproval;
use MagicApp\WaitingFor;
use MagicApp\PicoTestUtil;
use MagicApp\FormBuilder;
use YourApplication\Data\Entity\User;
use YourApplication\Data\Entity\UserApv;
use YourApplication\Data\Entity\UserTrash;
use YourApplication\Data\Entity\UserType;
use YourApplication\Data\Entity\Artist;
use YourApplication\Data\Entity\Producer;

require_once __DIR__ . "/inc.app/auth.php";

$currentModule = new AppModule("user");
$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputPost->getUserAction() == UserAction::CREATE)
{
	$user = new User(null, $database);
	$user->setUsername($inputPost->getUsername(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setPassword($inputPost->getPassword(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setAdmin($inputPost->getAdmin(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$user->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setBirthDay($inputPost->getBirthDay(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setGender($inputPost->getGender(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setEmail($inputPost->getEmail(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setTimeZone($inputPost->getTimeZone(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setUserTypeId($inputPost->getUserTypeId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setAssociatedArtist($inputPost->getAssociatedArtist(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setAssociatedProducer($inputPost->getAssociatedProducer(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setCurrentRole($inputPost->getCurrentRole(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$user->setBlocked($inputPost->getBlocked(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$user->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$user->setDraft(true);
	$user->setWaitingFor(WaitingFor::CREATE);
	$user->setAdminCreate($currentAction->getUserId());
	$user->setTimeCreate($currentAction->getTime());
	$user->setIpCreate($currentAction->getIp());
	$user->setAdminEdit($currentAction->getUserId());
	$user->setTimeEdit($currentAction->getTime());
	$user->setIpEdit($currentAction->getIp());

	$user->insert();

	$userApv = new UserApv($user, $database);
	$userApv->insert();
	$userUpdate = new User(null, $database);
	$userUpdate->setUserId($user->getUserId())->setApprovalId($userApv->getUserApvId())->update();
}
else if($inputPost->getUserAction() == UserAction::UPDATE)
{
	$userApv = new UserApv(null, $database);
	$userApv->setUsername($inputPost->getUsername(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setPassword($inputPost->getPassword(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setAdmin($inputPost->getAdmin(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$userApv->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setBirthDay($inputPost->getBirthDay(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setGender($inputPost->getGender(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setEmail($inputPost->getEmail(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setTimeZone($inputPost->getTimeZone(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setUserTypeId($inputPost->getUserTypeId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setAssociatedArtist($inputPost->getAssociatedArtist(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setAssociatedProducer($inputPost->getAssociatedProducer(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setCurrentRole($inputPost->getCurrentRole(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS, false, false, true));
	$userApv->setBlocked($inputPost->getBlocked(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$userApv->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT, false, false, true));
	$userApv->setAdminEdit($currentAction->getUserId());
	$userApv->setTimeEdit($currentAction->getTime());
	$userApv->setIpEdit($currentAction->getIp());

	$userApv->insert();

	$user = new User(null, $database);
	$user->setAdminAskEdit($currentAction->getUserId());
	$user->setTimeAskEdit($currentAction->getTime());
	$user->setIpAskEdit($currentAction->getIp());
	$user->setUserId($inputPost->getUserId())->setApprovalId($userApv->getUserApvId())->setApprovalIdWaitingFor(WaitingFor::UPDATE)->update();
}
else if($inputPost->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableCheckedRowId())
	{
		foreach($inputPost->getCheckedRowId() as $rowId)
		{
			$user = new User(null, $database);
			try
			{
				$user->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setUserId($rowId))
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
			$user = new User(null, $database);
			try
			{
				$user->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setUserId($rowId))
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
			$user = new User(null, $database);
			try
			{
				$user->where(PicoSpecification::getInstance()
					->addAnd(PicoPredicate::getInstance()->setUserId($rowId))
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
	if($inputPost->issetUserId())
	{
		$userId = $inputPost->getUserId();
		$user = new User(null, $database);
		$user->findOneByUserId($userId);
		if($user->issetUserId())
		{
			$approval = new PicoApproval(
			$user, 
			$entityInfo, 
			$entityApvInfo, 
			function($param1, $param2, $param3, $userId) {
				// approval validation here
				// if the return is incorrect, approval cannot take place
				
				// e.g. return $param1->notEqualsAdminAskEdit($userId);
				return true;
			}
, 
			true, 
			new UserTrash() 
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

			// List of properties to be copied from UserApv to User when when the user approves data modification. You can add or delete them.
			$columToBeCopied = array(
				Field::of()->username, 
				Field::of()->password, 
				Field::of()->admin, 
				Field::of()->name, 
				Field::of()->birthDay, 
				Field::of()->gender, 
				Field::of()->email, 
				Field::of()->timeZone, 
				Field::of()->userTypeId, 
				Field::of()->associatedArtist, 
				Field::of()->associatedProducer, 
				Field::of()->currentRole, 
				Field::of()->imagePath, 
				Field::of()->blocked, 
				Field::of()->active
			);

			$approval->approve($columToBeCopied, new UserApv(), new UserTrash(), 
			$currentAction->getUserId(),  
			$currentAction->getTime(),  
			$currentAction->getIp(), 
			$approvalCallback);
		}
	}
}
else if($inputPost->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetUserId())
	{
		$userId = $inputPost->getUserId();
		$user = new User(null, $database);
		$user->findOneByUserId($userId);
		if($user->issetUserId())
		{
			$approval = new PicoApproval(
			$user, 
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

			$approval->reject(new UserApv(),
			$currentAction->getUserId(),  
			$currentAction->getTime(),  
			$currentAction->getIp(), 
			$approvalCallback
			);
		}
	}
}
else if($inputPost->getUserAction() == UserAction::SORT_ORDER)
{
	$user = new User(null, $database);
	if($inputPost->getDataToSort() != null && $inputPost->countableDataToSort())
	{
		foreach($inputPost->getDataToSort() as $dataItem)
		{
			$primaryKeyValue = $dataItem->getPrimaryKey();
			$sortOrder = $dataItem->getSortOrder();
			$user->where(PicoSpecification::getInstance()->addAnd(new PicoPredicate(Field::of()->userId, $primaryKeyValue)))->setSortOder($sortOrder)->update();
		}
	}
}
if($inputGet->getUserAction() == UserAction::CREATE)
{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new User(), $appConfig);
?>
<div class="page page-jambi page-insert">
	<div class="jambi-wrapper">
		<form name="createform" id="createform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getUsername();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="username" id="username"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getPassword();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="password" id="password"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdmin();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="admin" id="admin" value="1"/> <?php echo $appEntityLanguage->getAdmin();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="name" id="name"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBirthDay();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="birth_day" id="birth_day"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getGender();?></td>
						<td>
							<select class="form-control" name="gender" id="gender"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="M">Laki-Laki</option>
								<option value="W">Perempuan</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getEmail();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="email" name="email" id="email"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeZone();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="time_zone" id="time_zone"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getUserTypeId();?></td>
						<td>
							<select class="form-control" name="user_type_id" id="user_type_id"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new UserType(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->userTypeId, Field::of()->name); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedArtist();?></td>
						<td>
							<select class="form-control" name="associated_artist" id="associated_artist"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Artist(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->artistId, Field::of()->name); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedProducer();?></td>
						<td>
							<select class="form-control" name="associated_producer" id="associated_producer"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getCurrentRole();?></td>
						<td>
							<select class="form-control" name="current_role" id="current_role"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="admin">Admin</option>
								<option value="producer">Producer</option>
								<option value="artist">Artist</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<input autocomplete="off" class="form-control" type="text" name="image_path" id="image_path"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBlocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="blocked" id="blocked" value="1"/> <?php echo $appEntityLanguage->getBlocked();?></label>
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
	$user = new User(null, $database);
	try{
		$user->findOneByUserId($inputGet->getUserId());
		if($user->hasValueUserId())
		{
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new User(), $appConfig);
?>
<div class="page page-jambi page-update">
	<div class="jambi-wrapper">
		<form name="updateform" id="updateform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getUsername();?></td>
						<td>
							<input class="form-control" type="text" name="username" id="username" value="<?php echo $user->getUsername();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getPassword();?></td>
						<td>
							<input class="form-control" type="text" name="password" id="password" value="<?php echo $user->getPassword();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdmin();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="admin" id="admin" value="1" <?php echo $user->createCheckedAdmin();?>/> <?php echo $appEntityLanguage->getAdmin();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<input class="form-control" type="text" name="name" id="name" value="<?php echo $user->getName();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBirthDay();?></td>
						<td>
							<input class="form-control" type="text" name="birth_day" id="birth_day" value="<?php echo $user->getBirthDay();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getGender();?></td>
						<td>
							<select class="form-control" name="gender" id="gender" data-value="<?php echo $user->getGender();?>"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="M" <?php echo AttrUtil::selected($user->getGender(), 'M');?>>Laki-Laki</option>
								<option value="W" <?php echo AttrUtil::selected($user->getGender(), 'W');?>>Perempuan</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getEmail();?></td>
						<td>
							<input class="form-control" type="email" name="email" id="email" value="<?php echo $user->getEmail();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeZone();?></td>
						<td>
							<input class="form-control" type="text" name="time_zone" id="time_zone" value="<?php echo $user->getTimeZone();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getUserTypeId();?></td>
						<td>
							<select class="form-control" name="user_type_id" id="user_type_id"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new UserType(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->userTypeId, Field::of()->name, $user->getUserTypeId()); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedArtist();?></td>
						<td>
							<select class="form-control" name="associated_artist" id="associated_artist"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Artist(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->artistId, Field::of()->name, $user->getAssociatedArtist()); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedProducer();?></td>
						<td>
							<select class="form-control" name="associated_producer" id="associated_producer"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name, $user->getAssociatedProducer()); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getCurrentRole();?></td>
						<td>
							<select class="form-control" name="current_role" id="current_role" data-value="<?php echo $user->getCurrentRole();?>"><option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="admin" <?php echo AttrUtil::selected($user->getCurrentRole(), 'admin');?>>Admin</option>
								<option value="producer" <?php echo AttrUtil::selected($user->getCurrentRole(), 'producer');?>>Producer</option>
								<option value="artist" <?php echo AttrUtil::selected($user->getCurrentRole(), 'artist');?>>Artist</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<input class="form-control" type="text" name="image_path" id="image_path" value="<?php echo $user->getImagePath();?>" autocomplete="off"/>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBlocked();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="blocked" id="blocked" value="1" <?php echo $user->createCheckedBlocked();?>/> <?php echo $appEntityLanguage->getBlocked();?></label>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td>
							<label><input class="form-check-input" type="checkbox" name="active" id="active" value="1" <?php echo $user->createCheckedActive();?>/> <?php echo $appEntityLanguage->getActive();?></label>
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
							<input type="hidden" name="user_id" value="<?php echo $user->getUserId();?>"/>
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
	$user = new User(null, $database);
	try{
		$user->findOneByUserId($inputGet->getUserId());
		if($user->hasValueUserId())
		{
			// define map here
			$mapForGender = array(
				"M" => array("value" => "M", "label" => "Laki-Laki", "default" => "false"),
				"W" => array("value" => "W", "label" => "Perempuan", "default" => "false")
			);
			$mapForCurrentRole = array(
				"admin" => array("value" => "admin", "label" => "Admin", "default" => "false"),
				"producer" => array("value" => "producer", "label" => "Producer", "default" => "false"),
				"artist" => array("value" => "artist", "label" => "Artist", "default" => "false")
			);
			if($user->notNullApprovalId())
			{
				$userApv = new UserApv(null, $database);
				try
				{
					$userApv->find($user->getApprovalId());
				}
				catch(Exception $e)
				{
					// do something here
				}
require_once AppInclude::mainAppHeader(__DIR__, $appConfig);
$appEntityLanguage = new AppEntityLanguage(new User(), $appConfig);
?>
<div class="page page-jambi page-detail">
	<div class="jambi-wrapper">
		<form name="detailform" id="detailform" action="" method="post">
			<div class="alert alert-info">	
			<?php
			if($user->getWaitingFor() == WaitingFor::CREATE)
			{
			    echo $appLanguage->getMessageWaitingForCreate();
			}
			else if($user->getWaitingFor() == WaitingFor::UPDATE)
			{
			    echo $appLanguage->getMessageWaitingForUpdate();
			}
			else if($user->getWaitingFor() == WaitingFor::ACTIVATE)
			{
			    echo $appLanguage->getMessageWaitingForActivate();
			}
			else if($user->getWaitingFor() == WaitingFor::DEACTIVATE)
			{
			    echo $appLanguage->getMessageWaitingForDeactivate();
			}
			else if($user->getWaitingFor() == WaitingFor::DELETE)
			{
			    echo $appLanguage->getMessageWaitingForDelete();
			}
			?>
			</div>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getUsername();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsUsername($userApv->getUsername()));?>"><?php echo $user->getUsername();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsUsername($userApv->getUsername()));?>"><?php echo $userApv->getUsername();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getPassword();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsPassword($userApv->getPassword()));?>"><?php echo $user->getPassword();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsPassword($userApv->getPassword()));?>"><?php echo $userApv->getPassword();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdmin();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdmin($userApv->getAdmin()));?>"><?php echo $user->optionAdmin($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdmin($userApv->getAdmin()));?>"><?php echo $userApv->optionAdmin($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsName($userApv->getName()));?>"><?php echo $user->getName();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsName($userApv->getName()));?>"><?php echo $userApv->getName();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBirthDay();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsBirthDay($userApv->getBirthDay()));?>"><?php echo $user->getBirthDay();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsBirthDay($userApv->getBirthDay()));?>"><?php echo $userApv->getBirthDay();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getGender();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsGender($userApv->getGender()));?>"><?php echo isset($mapForGender) && isset($mapForGender[$user->getGender()]) && isset($mapForGender[$user->getGender()["label"]]) ? $mapForGender[$user->getGender()]["label"] : "";?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsGender($userApv->getGender()));?>"><?php echo isset($mapForGender) && isset($mapForGender[$userApv->getGender()]) && isset($mapForGender[$userApv->getGender()]["label"]) ? $mapForGender[$userApv->getGender()]["label"] : "";?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getEmail();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsEmail($userApv->getEmail()));?>"><?php echo $user->getEmail();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsEmail($userApv->getEmail()));?>"><?php echo $userApv->getEmail();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeZone();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeZone($userApv->getTimeZone()));?>"><?php echo $user->getTimeZone();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeZone($userApv->getTimeZone()));?>"><?php echo $userApv->getTimeZone();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getUserTypeId();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsUserTypeId($userApv->getUserTypeId()));?>"><?php echo $user->getUserTypeId();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsUserTypeId($userApv->getUserTypeId()));?>"><?php echo $userApv->getUserTypeId();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedArtist();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAssociatedArtist($userApv->getAssociatedArtist()));?>"><?php echo $user->getAssociatedArtist();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAssociatedArtist($userApv->getAssociatedArtist()));?>"><?php echo $userApv->getAssociatedArtist();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedProducer();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAssociatedProducer($userApv->getAssociatedProducer()));?>"><?php echo $user->getAssociatedProducer();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAssociatedProducer($userApv->getAssociatedProducer()));?>"><?php echo $userApv->getAssociatedProducer();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getCurrentRole();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsCurrentRole($userApv->getCurrentRole()));?>"><?php echo isset($mapForCurrentRole) && isset($mapForCurrentRole[$user->getCurrentRole()]) && isset($mapForCurrentRole[$user->getCurrentRole()["label"]]) ? $mapForCurrentRole[$user->getCurrentRole()]["label"] : "";?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsCurrentRole($userApv->getCurrentRole()));?>"><?php echo isset($mapForCurrentRole) && isset($mapForCurrentRole[$userApv->getCurrentRole()]) && isset($mapForCurrentRole[$userApv->getCurrentRole()]["label"]) ? $mapForCurrentRole[$userApv->getCurrentRole()]["label"] : "";?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsImagePath($userApv->getImagePath()));?>"><?php echo $user->getImagePath();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsImagePath($userApv->getImagePath()));?>"><?php echo $userApv->getImagePath();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeCreate($userApv->getTimeCreate()));?>"><?php echo $user->getTimeCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeCreate($userApv->getTimeCreate()));?>"><?php echo $userApv->getTimeCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeEdit($userApv->getTimeEdit()));?>"><?php echo $user->getTimeEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsTimeEdit($userApv->getTimeEdit()));?>"><?php echo $userApv->getTimeEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdminCreate($userApv->getAdminCreate()));?>"><?php echo $user->getAdminCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdminCreate($userApv->getAdminCreate()));?>"><?php echo $userApv->getAdminCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdminEdit($userApv->getAdminEdit()));?>"><?php echo $user->getAdminEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsAdminEdit($userApv->getAdminEdit()));?>"><?php echo $userApv->getAdminEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpCreate();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsIpCreate($userApv->getIpCreate()));?>"><?php echo $user->getIpCreate();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsIpCreate($userApv->getIpCreate()));?>"><?php echo $userApv->getIpCreate();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpEdit();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsIpEdit($userApv->getIpEdit()));?>"><?php echo $user->getIpEdit();?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsIpEdit($userApv->getIpEdit()));?>"><?php echo $userApv->getIpEdit();?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBlocked();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsBlocked($userApv->getBlocked()));?>"><?php echo $user->optionBlocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsBlocked($userApv->getBlocked()));?>"><?php echo $userApv->optionBlocked($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsActive($userApv->getActive()));?>"><?php echo $user->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></span>
						</td>
						<td>
							<span class="<?php echo PicoTestUtil::classCompareData($user->notEqualsActive($userApv->getActive()));?>"><?php echo $userApv->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></span>
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
							<input type="hidden" name="user_id" value="<?php echo $user->getUserId();?>"/>
							<?php
							}
							else
							{
							?>
							<input type="submit" class="btn btn-success" name="action_approval" id="action_approval" value="<?php echo $appLanguage->getButtonReject(); ?>"/>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl();?>';"/>
							<input type="hidden" name="user_action" value="<?php echo UserAction::REJECT;?>"/>
							<input type="hidden" name="user_id" value="<?php echo $user->getUserId();?>"/>
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
$appEntityLanguage = new AppEntityLanguage(new User(), $appConfig);
?>
<div class="page page-jambi page-detail">
	<div class="jambi-wrapper">
		<form name="detailform" id="detailform" action="" method="post">
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td><?php echo $appEntityLanguage->getUsername();?></td>
						<td><?php echo $user->getUsername();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getPassword();?></td>
						<td><?php echo $user->getPassword();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdmin();?></td>
						<td><?php echo $user->optionAdmin($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getName();?></td>
						<td><?php echo $user->getName();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBirthDay();?></td>
						<td><?php echo $user->getBirthDay();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getGender();?></td>
						<td><?php echo isset($mapForGender) && isset($mapForGender[$user->getGender()]) && isset($mapForGender[$user->getGender()]["label"]) ? $mapForGender[$user->getGender()]["label"] : "";?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getEmail();?></td>
						<td><?php echo $user->getEmail();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeZone();?></td>
						<td><?php echo $user->getTimeZone();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getUserTypeId();?></td>
						<td><?php echo $user->hasValueUserType() ? $user->getUserType()->getName() : "";?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedArtist();?></td>
						<td><?php echo $user->hasValueArtist() ? $user->getArtist()->getName() : "";?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAssociatedProducer();?></td>
						<td><?php echo $user->hasValueProducer() ? $user->getProducer()->getName() : "";?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getCurrentRole();?></td>
						<td><?php echo isset($mapForCurrentRole) && isset($mapForCurrentRole[$user->getCurrentRole()]) && isset($mapForCurrentRole[$user->getCurrentRole()]["label"]) ? $mapForCurrentRole[$user->getCurrentRole()]["label"] : "";?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getImagePath();?></td>
						<td><?php echo $user->getImagePath();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeCreate();?></td>
						<td><?php echo $user->getTimeCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getTimeEdit();?></td>
						<td><?php echo $user->getTimeEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminCreate();?></td>
						<td><?php echo $user->getAdminCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getAdminEdit();?></td>
						<td><?php echo $user->getAdminEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpCreate();?></td>
						<td><?php echo $user->getIpCreate();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getIpEdit();?></td>
						<td><?php echo $user->getIpEdit();?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getBlocked();?></td>
						<td><?php echo $user->optionBlocked($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
					<tr>
						<td><?php echo $appEntityLanguage->getActive();?></td>
						<td><?php echo $user->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></td>
					</tr>
				</tbody>
			</table>
			<table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td></td>
						<td>
							<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonUpdate(); ?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl(UserAction::UPDATE, Field::of()->user_id, $user->getUserId());?>';"/>
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
$appEntityLanguage = new AppEntityLanguage(new User(), $appConfig);
?>
<div class="page page-jambi page-list">
	<div class="jambi-wrapper">
		<div class="filter-section">
			<form action="" method="get" class="filter-form">
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getUsername();?></span>
					<span class="filter-control">
						<input type="text" name="username" class="form-control" value="<?php echo $inputGet->getUsername();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getName();?></span>
					<span class="filter-control">
						<input type="text" name="name" class="form-control" value="<?php echo $inputGet->getName();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getGender();?></span>
					<span class="filter-control">
							<select name="gender" class="form-control" data-value="<?php echo $inputGet->getGender();?>">
								<option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="M" <?php echo AttrUtil::selected($inputGet->getGender(), 'M');?>>Laki-Laki</option>
								<option value="W" <?php echo AttrUtil::selected($inputGet->getGender(), 'W');?>>Perempuan</option>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getEmail();?></span>
					<span class="filter-control">
						<input type="text" name="email" class="form-control" value="<?php echo $inputGet->getEmail();?>" autocomplete="off"/>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getUserTypeId();?></span>
					<span class="filter-control">
							<select name="user_type_id" class="form-control">
								<option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new UserType(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortBySortOrder(PicoSort::ORDER_TYPE_ASC))
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->userTypeId, Field::of()->name, $inputGet->getUserTypeId()); ?>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getAssociatedArtist();?></span>
					<span class="filter-control">
							<select name="associated_artist" class="form-control">
								<option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Artist(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->artistId, Field::of()->name, $inputGet->getAssociatedArtist()); ?>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getAssociatedProducer();?></span>
					<span class="filter-control">
							<select name="associated_producer" class="form-control">
								<option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<?php echo FormBuilder::getInstance()->createSelectOption(new Producer(null, $database), 
								PicoSpecification::getInstance()
									->addAnd(PicoPredicate::getInstance()->setActive(true))
									->addAnd(PicoPredicate::getInstance()->setDraft(true)), 
								PicoSortable::getInstance()
									->add(PicoSort::getInstance()->sortByName(PicoSort::ORDER_TYPE_ASC)), 
								Field::of()->producerId, Field::of()->name, $inputGet->getAssociatedProducer()); ?>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<span class="filter-label"><?php echo $appEntityLanguage->getCurrentRole();?></span>
					<span class="filter-control">
							<select name="current_role" class="form-control" data-value="<?php echo $inputGet->getCurrentRole();?>">
								<option value=""><?php echo $appLanguage->getLabelOptionSelectOne();?></option>
								<option value="admin" <?php echo AttrUtil::selected($inputGet->getCurrentRole(), 'admin');?>>Admin</option>
								<option value="producer" <?php echo AttrUtil::selected($inputGet->getCurrentRole(), 'producer');?>>Producer</option>
								<option value="artist" <?php echo AttrUtil::selected($inputGet->getCurrentRole(), 'artist');?>>Artist</option>
							</select>
					</span>
				</span>
				
				<span class="filter-group">
					<input type="submit" class="btn btn-success" value="<?php echo $appLanguage->getButtonSearch();?>"/>
				</span>
		
				<span class="filter-group">
					<input type="button" class="btn btn-primary" value="<?php echo $appLanguage->getButtonAdd();?>" onclick="window.location='<?php echo $currentModule->getRedirectUrl(UserAction::CREATE);?>'"/>
				</span>
			</form>
		</div>
		<div class="data-section">
			<?php 	
			$mapForGender = array(
				"M" => array("value" => "M", "label" => "Laki-Laki", "default" => "false"),
				"W" => array("value" => "W", "label" => "Perempuan", "default" => "false")
			);
			$mapForCurrentRole = array(
				"admin" => array("value" => "admin", "label" => "Admin", "default" => "false"),
				"producer" => array("value" => "producer", "label" => "Producer", "default" => "false"),
				"artist" => array("value" => "artist", "label" => "Artist", "default" => "false")
			);
			$specMap = array(
			    "username" => "username",
				"name" => "name",
				"gender" => "gender",
				"email" => "email",
				"userTypeId" => "userTypeId",
				"associatedArtist" => "associatedArtist",
				"associatedProducer" => "associatedProducer",
				"currentRole" => "currentRole"
			);
			$sortOrderMap = array(
			    "username" => "username",
				"admin" => "admin",
				"name" => "name",
				"birthDay" => "birthDay",
				"gender" => "gender",
				"email" => "email",
				"timeZone" => "timeZone",
				"userTypeId" => "userTypeId",
				"associatedArtist" => "associatedArtist",
				"associatedProducer" => "associatedProducer",
				"currentRole" => "currentRole",
				"blocked" => "blocked",
				"active" => "active"
			);
			
			// You can define your own specifications
			// Pay attention to security issues
			$specification = PicoSpecification::fromUserInput($inputGet, $specMap);
			
			// You can define your own sortable
			// Pay attention to security issues
			$sortable = PicoSortable::fromUserInput($inputGet, $sortOrderMap);
			
			$pageable = new PicoPageable(new PicoPage($inputGet->getPage(), $appConfig->getPageSize()), $sortable);
			$dataLoader = new User(null, $database);
			$pageData = $dataLoader->findAll($specification, $pageable, $sortable);
			$resultSet = $pageData->getResult();
			
			if($pageData->getTotalResult() > 0)
			{
			?>
			<div class="pagination pagination-top">
			    <div class="pagination-number">
			    <?php
			    foreach($pageData->getPagination() as $pg)
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
								<td class="data-controll data-selector" data-key="user_id">
									<input type="checkbox" class="checkbox check-master" data-selector=".checkbox-user-id"/>
								</td>
								<td class="data-controll data-editor">
									<span class="fa fa-edit"></span>
								</td>
								<td class="data-controll data-viewer">
									<span class="fa fa-folder"></span>
								</td>
								<td class="data-controll data-number"><?php echo $appLanguage->getNumero();?></td>
								<td data-field="username"><?php echo $appEntityLanguage->getUsername();?></td>
								<td data-field="admin"><?php echo $appEntityLanguage->getAdmin();?></td>
								<td data-field="name"><?php echo $appEntityLanguage->getName();?></td>
								<td data-field="birth_day"><?php echo $appEntityLanguage->getBirthDay();?></td>
								<td data-field="gender"><?php echo $appEntityLanguage->getGender();?></td>
								<td data-field="email"><?php echo $appEntityLanguage->getEmail();?></td>
								<td data-field="time_zone"><?php echo $appEntityLanguage->getTimeZone();?></td>
								<td data-field="user_type_id"><?php echo $appEntityLanguage->getUserTypeId();?></td>
								<td data-field="associated_artist"><?php echo $appEntityLanguage->getAssociatedArtist();?></td>
								<td data-field="associated_producer"><?php echo $appEntityLanguage->getAssociatedProducer();?></td>
								<td data-field="current_role"><?php echo $appEntityLanguage->getCurrentRole();?></td>
								<td data-field="blocked"><?php echo $appEntityLanguage->getBlocked();?></td>
								<td data-field="active"><?php echo $appEntityLanguage->getActive();?></td>
							</tr>
						</thead>
					
						<tbody>
							<?php 
							foreach($resultSet as $dataIndex => $user)
							{
							?>
							<tr>
								<td class="data-selector" data-key="user_id">
									<input type="checkbox" class="checkbox check-slave checkbox-user-id" name="checked_row_id[]" value="<?php echo $user->getUserId();?>"/>
								</td>
								<td>
									<a class="edit-control" href="<?php echo $currentModule->getRedirectUrl(UserAction::UPDATE, Field::of()->user_id, $user->getUserId());?>"><span class="fa fa-edit"></span></a>
								</td>
								<td>
									<a class="detail-control field-master" href="<?php echo $currentModule->getRedirectUrl(UserAction::DETAIL, Field::of()->user_id, $user->getUserId());?>"><span class="fa fa-folder"></span></a>
								</td>
								<td class="data-number"><?php echo $pageData->getDataOffset() + $dataIndex + 1;?></td>
								<td data-field="username"><?php echo $user->getUsername();?></td>
								<td data-field="admin"><?php echo $user->optionAdmin($appLanguage->getYes(), $appLanguage->getNo());?></td>
								<td data-field="name"><?php echo $user->getName();?></td>
								<td data-field="birth_day"><?php echo $user->getBirthDay();?></td>
								<td data-field="gender"><?php echo isset($mapForGender) && isset($mapForGender[$user->getGender()]) && isset($mapForGender[$user->getGender()]["label"]) ? $mapForGender[$user->getGender()]["label"] : "";?></td>
								<td data-field="email"><?php echo $user->getEmail();?></td>
								<td data-field="time_zone"><?php echo $user->getTimeZone();?></td>
								<td data-field="user_type_id"><?php echo $user->hasValueUserType() ? $user->getUserType()->getName() : "";?></td>
								<td data-field="associated_artist"><?php echo $user->hasValueArtist() ? $user->getArtist()->getName() : "";?></td>
								<td data-field="associated_producer"><?php echo $user->hasValueProducer() ? $user->getProducer()->getName() : "";?></td>
								<td data-field="current_role"><?php echo isset($mapForCurrentRole) && isset($mapForCurrentRole[$user->getCurrentRole()]) && isset($mapForCurrentRole[$user->getCurrentRole()]["label"]) ? $mapForCurrentRole[$user->getCurrentRole()]["label"] : "";?></td>
								<td data-field="blocked"><?php echo $user->optionBlocked($appLanguage->getYes(), $appLanguage->getNo());?></td>
								<td data-field="active"><?php echo $user->optionActive($appLanguage->getYes(), $appLanguage->getNo());?></td>
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
			    foreach($pageData->getPagination() as $pg)
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


```

### Entity

**Main ENtity**

```php
<?php

namespace YourApplication\Data\Entity;

use MagicObject\MagicObject;

/**
 * User is entity of table user. You can join this entity to other entity using annotation JoinColumn. 
 * Visit https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md#entity
 * 
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify=false)
 * @Table(name="user")
 */
class User extends MagicObject
{
	/**
	 * User ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @Column(name="username", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Username")
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @Column(name="password", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Password")
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @Column(name="admin", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Admin")
	 * @var boolean
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Birth Day")
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email")
	 * @var string
	 */
	protected $email;

	/**
	 * Time Zone
	 * 
	 * @Column(name="time_zone", type="varchar(255)", length=255, nullable=true)
	 * @Label(content="Time Zone")
	 * @var string
	 */
	protected $timeZone;

	/**
	 * User Type ID
	 * 
	 * @Column(name="user_type_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Type ID")
	 * @var string
	 */
	protected $userTypeId;

	/**
	 * User Type
	 * 
	 * @JoinColumn(name="user_type_id", referenceColumnName="user_type_id")
	 * @Label(content="User Type")
	 * @var UserType
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @Column(name="associated_artist", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Artist")
	 * @var string
	 */
	protected $associatedArtist;

	/**
	 * Artist
	 * 
	 * @JoinColumn(name="associated_artist", referenceColumnName="artist_id")
	 * @Label(content="Artist")
	 * @var Artist
	 */
	protected $artist;

	/**
	 * Associated Producer
	 * 
	 * @Column(name="associated_producer", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Producer")
	 * @var string
	 */
	protected $associatedProducer;

	/**
	 * Producer
	 * 
	 * @JoinColumn(name="associated_producer", referenceColumnName="producer_id")
	 * @Label(content="Producer")
	 * @var Producer
	 */
	protected $producer;

	/**
	 * Current Role
	 * 
	 * @Column(name="current_role", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Current Role")
	 * @var string
	 */
	protected $currentRole;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

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
	 * Reset Password Hash
	 * 
	 * @Column(name="reset_password_hash", type="varchar(256)", length=256, nullable=true)
	 * @Label(content="Reset Password Hash")
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @Column(name="last_reset_password", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Reset Password")
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @Column(name="blocked", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Blocked")
	 * @var boolean
	 */
	protected $blocked;

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
	 * Admin Ask Edit
	 * 
	 * @Column(name="admin_ask_edit", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Admin Ask Edit")
	 * @var string
	 */
	protected $adminAskEdit;

	/**
	 * IP Ask Edit
	 * 
	 * @Column(name="ip_ask_edit", type="varchar(50)", length=50, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="IP Ask Edit")
	 * @var string
	 */
	protected $ipAskEdit;

	/**
	 * Time Ask Edit
	 * 
	 * @Column(name="time_ask_edit", type="timestamp", length=19, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Time Ask Edit")
	 * @var string
	 */
	protected $timeAskEdit;

	/**
	 * Draft
	 * 
	 * @Column(name="draft", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Draft")
	 * @var boolean
	 */
	protected $draft;

	/**
	 * Waiting For
	 * 
	 * @Column(name="waiting_for", type="int(4)", length=4, nullable=true)
	 * @Label(content="Waiting For")
	 * @var integer
	 */
	protected $waitingFor;

	/**
	 * Approval ID
	 * 
	 * @Column(name="approval_id", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Approval ID")
	 * @var string
	 */
	protected $approvalId;

}
```

**Approval Entity**

```php
<?php

namespace YourApplication\Data\Entity;

use MagicObject\MagicObject;

/**
 * UserApv is entity of table user_apv. You can join this entity to other entity using annotation JoinColumn. 
 * Visit https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md#entity
 * 
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify=false)
 * @Table(name="user_apv")
 */
class UserApv extends MagicObject
{
	/**
	 * User Apv ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @Column(name="user_apv_id", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="User Apv ID")
	 * @var string
	 */
	protected $userApvId;

	/**
	 * User ID
	 * 
	 * @NotNull
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @Column(name="username", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Username")
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @Column(name="password", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Password")
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @Column(name="admin", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Admin")
	 * @var boolean
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Birth Day")
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email")
	 * @var string
	 */
	protected $email;

	/**
	 * Time Zone
	 * 
	 * @Column(name="time_zone", type="varchar(255)", length=255, nullable=true)
	 * @Label(content="Time Zone")
	 * @var string
	 */
	protected $timeZone;

	/**
	 * User Type ID
	 * 
	 * @Column(name="user_type_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Type ID")
	 * @var string
	 */
	protected $userTypeId;

	/**
	 * User Type
	 * 
	 * @JoinColumn(name="user_type_id", referenceColumnName="user_type_id")
	 * @Label(content="User Type")
	 * @var UserType
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @Column(name="associated_artist", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Artist")
	 * @var string
	 */
	protected $associatedArtist;

	/**
	 * Artist
	 * 
	 * @JoinColumn(name="associated_artist", referenceColumnName="artist_id")
	 * @Label(content="Artist")
	 * @var Artist
	 */
	protected $artist;

	/**
	 * Associated Producer
	 * 
	 * @Column(name="associated_producer", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Producer")
	 * @var string
	 */
	protected $associatedProducer;

	/**
	 * Producer
	 * 
	 * @JoinColumn(name="associated_producer", referenceColumnName="producer_id")
	 * @Label(content="Producer")
	 * @var Producer
	 */
	protected $producer;

	/**
	 * Current Role
	 * 
	 * @Column(name="current_role", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Current Role")
	 * @var string
	 */
	protected $currentRole;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

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
	 * Reset Password Hash
	 * 
	 * @Column(name="reset_password_hash", type="varchar(256)", length=256, nullable=true)
	 * @Label(content="Reset Password Hash")
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @Column(name="last_reset_password", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Reset Password")
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @Column(name="blocked", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Blocked")
	 * @var boolean
	 */
	protected $blocked;

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
	 * Admin Ask Edit
	 * 
	 * @Column(name="admin_ask_edit", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Admin Ask Edit")
	 * @var string
	 */
	protected $adminAskEdit;

	/**
	 * IP Ask Edit
	 * 
	 * @Column(name="ip_ask_edit", type="varchar(50)", length=50, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="IP Ask Edit")
	 * @var string
	 */
	protected $ipAskEdit;

	/**
	 * Time Ask Edit
	 * 
	 * @Column(name="time_ask_edit", type="timestamp", length=19, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Time Ask Edit")
	 * @var string
	 */
	protected $timeAskEdit;

	/**
	 * Approval Status
	 * 
	 * @Column(name="approval_status", type="int(4)", length=4, nullable=true)
	 * @Label(content="Approval Status")
	 * @var integer
	 */
	protected $approvalStatus;

}
```

**Trash Entity**

```php
<?php

namespace YourApplication\Data\Entity;

use MagicObject\MagicObject;

/**
 * UserTrash is entity of table user_trash. You can join this entity to other entity using annotation JoinColumn. 
 * Visit https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md#entity
 * 
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify=false)
 * @Table(name="user_trash")
 */
class UserTrash extends MagicObject
{
	/**
	 * User Trash ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @Column(name="user_trash_id", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="User Trash ID")
	 * @var string
	 */
	protected $userTrashId;

	/**
	 * User ID
	 * 
	 * @NotNull
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="User ID")
	 * @var string
	 */
	protected $userId;

	/**
	 * Username
	 * 
	 * @Column(name="username", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Username")
	 * @var string
	 */
	protected $username;

	/**
	 * Password
	 * 
	 * @Column(name="password", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Password")
	 * @var string
	 */
	protected $password;

	/**
	 * Admin
	 * 
	 * @Column(name="admin", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Admin")
	 * @var boolean
	 */
	protected $admin;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Birth Day
	 * 
	 * @Column(name="birth_day", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Birth Day")
	 * @var string
	 */
	protected $birthDay;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Email
	 * 
	 * @Column(name="email", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Email")
	 * @var string
	 */
	protected $email;

	/**
	 * Time Zone
	 * 
	 * @Column(name="time_zone", type="varchar(255)", length=255, nullable=true)
	 * @Label(content="Time Zone")
	 * @var string
	 */
	protected $timeZone;

	/**
	 * User Type ID
	 * 
	 * @Column(name="user_type_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="User Type ID")
	 * @var string
	 */
	protected $userTypeId;

	/**
	 * User Type
	 * 
	 * @JoinColumn(name="user_type_id", referenceColumnName="user_type_id")
	 * @Label(content="User Type")
	 * @var UserType
	 */
	protected $userType;

	/**
	 * Associated Artist
	 * 
	 * @Column(name="associated_artist", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Artist")
	 * @var string
	 */
	protected $associatedArtist;

	/**
	 * Artist
	 * 
	 * @JoinColumn(name="associated_artist", referenceColumnName="artist_id")
	 * @Label(content="Artist")
	 * @var Artist
	 */
	protected $artist;

	/**
	 * Associated Producer
	 * 
	 * @Column(name="associated_producer", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Associated Producer")
	 * @var string
	 */
	protected $associatedProducer;

	/**
	 * Producer
	 * 
	 * @JoinColumn(name="associated_producer", referenceColumnName="producer_id")
	 * @Label(content="Producer")
	 * @var Producer
	 */
	protected $producer;

	/**
	 * Current Role
	 * 
	 * @Column(name="current_role", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Current Role")
	 * @var string
	 */
	protected $currentRole;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

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
	 * Reset Password Hash
	 * 
	 * @Column(name="reset_password_hash", type="varchar(256)", length=256, nullable=true)
	 * @Label(content="Reset Password Hash")
	 * @var string
	 */
	protected $resetPasswordHash;

	/**
	 * Last Reset Password
	 * 
	 * @Column(name="last_reset_password", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Reset Password")
	 * @var string
	 */
	protected $lastResetPassword;

	/**
	 * Blocked
	 * 
	 * @Column(name="blocked", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Blocked")
	 * @var boolean
	 */
	protected $blocked;

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
	 * Admin Ask Edit
	 * 
	 * @Column(name="admin_ask_edit", type="varchar(40)", length=40, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Admin Ask Edit")
	 * @var string
	 */
	protected $adminAskEdit;

	/**
	 * IP Ask Edit
	 * 
	 * @Column(name="ip_ask_edit", type="varchar(50)", length=50, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="IP Ask Edit")
	 * @var string
	 */
	protected $ipAskEdit;

	/**
	 * Time Ask Edit
	 * 
	 * @Column(name="time_ask_edit", type="timestamp", length=19, default_value="NULL", nullable=true)
	 * @DefaultColumn(value="NULL")
	 * @Label(content="Time Ask Edit")
	 * @var string
	 */
	protected $timeAskEdit;

}
```

### Database Query 

When using the approval and trash features in AppBuilder, AppBuilder will add several columns to the related table. Additionally, new tables may also be added if they do not already exist.

AppBuilder creates SQL scripts that must be executed in the database to meet application requirements. Users simply execute the script without worrying about losing data. Data adjustment may be necessary in this case.

```sql
-- SQL for User begin

ALTER TABLE user ADD COLUMN admin_ask_edit varchar(40) NULL DEFAULT NULL AFTER active;
ALTER TABLE user ADD COLUMN ip_ask_edit varchar(50) NULL DEFAULT NULL AFTER admin_ask_edit;
ALTER TABLE user ADD COLUMN time_ask_edit timestamp NULL DEFAULT NULL AFTER ip_ask_edit;
ALTER TABLE user ADD COLUMN draft tinyint(1) NULL AFTER time_ask_edit;
ALTER TABLE user ADD COLUMN waiting_for int(4) NULL AFTER draft;
ALTER TABLE user ADD COLUMN approval_id varchar(40) NULL DEFAULT NULL AFTER waiting_for;

-- SQL for User end


-- SQL for UserApv begin

CREATE TABLE `user_apv` (
`user_apv_id` varchar(40) NULL DEFAULT NULL,
`user_id` varchar(40) NOT NULL,
`username` varchar(100) NULL,
`password` varchar(100) NULL,
`admin` tinyint(1) NULL,
`name` varchar(100) NULL,
`birth_day` varchar(100) NULL,
`gender` varchar(2) NULL,
`email` varchar(100) NULL,
`time_zone` varchar(255) NULL,
`user_type_id` varchar(40) NULL,
`associated_artist` varchar(40) NULL,
`associated_producer` varchar(40) NULL,
`current_role` varchar(40) NULL,
`image_path` text NULL,
`time_create` timestamp NULL,
`time_edit` timestamp NULL,
`admin_create` varchar(40) NULL,
`admin_edit` varchar(40) NULL,
`ip_create` varchar(50) NULL,
`ip_edit` varchar(50) NULL,
`reset_password_hash` varchar(256) NULL,
`last_reset_password` timestamp NULL,
`blocked` tinyint(1) NULL,
`active` tinyint(1) NULL DEFAULT '1',
`admin_ask_edit` varchar(40) NULL DEFAULT NULL,
`ip_ask_edit` varchar(50) NULL DEFAULT NULL,
`time_ask_edit` timestamp NULL DEFAULT NULL,
`approval_status` int(4) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user_apv`
ADD PRIMARY KEY (`user_apv_id`)
;

-- SQL for UserApv end


-- SQL for UserTrash begin

CREATE TABLE `user_trash` (
`user_trash_id` varchar(40) NULL DEFAULT NULL,
`user_id` varchar(40) NOT NULL,
`username` varchar(100) NULL,
`password` varchar(100) NULL,
`admin` tinyint(1) NULL,
`name` varchar(100) NULL,
`birth_day` varchar(100) NULL,
`gender` varchar(2) NULL,
`email` varchar(100) NULL,
`time_zone` varchar(255) NULL,
`user_type_id` varchar(40) NULL,
`associated_artist` varchar(40) NULL,
`associated_producer` varchar(40) NULL,
`current_role` varchar(40) NULL,
`image_path` text NULL,
`time_create` timestamp NULL,
`time_edit` timestamp NULL,
`admin_create` varchar(40) NULL,
`admin_edit` varchar(40) NULL,
`ip_create` varchar(50) NULL,
`ip_edit` varchar(50) NULL,
`reset_password_hash` varchar(256) NULL,
`last_reset_password` timestamp NULL,
`blocked` tinyint(1) NULL,
`active` tinyint(1) NULL DEFAULT '1',
`admin_ask_edit` varchar(40) NULL DEFAULT NULL,
`ip_ask_edit` varchar(50) NULL DEFAULT NULL,
`time_ask_edit` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user_trash`
ADD PRIMARY KEY (`user_trash_id`)
;

-- SQL for UserTrash end
```