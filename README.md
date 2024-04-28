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

If a module can be created in 30 minutes, then in one day, a developer can create at least 10 new CRUD modules. Within 2 weeks, a developer can create 100 standard CRUD modules with the features above.

Of course, an application cannot contain only simple CRUD modules. But at least, a simple CRUD module won't take much time to create. Available time can be maximized for other tasks such as data processing, report creation and application testing.

AppBuilder uses MagicObject as its library. MagicObjects is very useful for creating entities from a table without having to type code. Just select the table and specify the name of the entity to be created. Entities will be created automatically by AppBuilder according to the names and column types of a table.

## CRUD Example

### Default CRUD (without Approval and without Trash)

```php
if($inputGet->getUserAction() == UserAction::INSERT)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAdminCreate($currentAction->getUserId());
	$album->setTimeCreate($currentAction->getTime());
	$album->setIpCreate($currentAction->getIp());
	$album->setAdminEdit($currentAction->getUserId());
	$album->setTimeEdit($currentAction->getTime());
	$album->setIpEdit($currentAction->getIp());
	$album->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATION)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->setAlbumId($rowId)->setActive(true)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATION)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->setAlbumId($rowId)->setActive(false)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableDeletionRowIds())
	{
		foreach($inputPost->getDeletionRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->deleteOneByAlbumId($rowId);
		}
	}
}

```

### CRUD without Approval and with Trash


```php
if($inputGet->getUserAction() == UserAction::INSERT)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAdminCreate($currentAction->getUserId());
	$album->setTimeCreate($currentAction->getTime());
	$album->setIpCreate($currentAction->getIp());
	$album->setAdminEdit($currentAction->getUserId());
	$album->setTimeEdit($currentAction->getTime());
	$album->setIpEdit($currentAction->getIp());
	$album->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATION)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->setAlbumId($rowId)->setActive(true)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATION)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->setAlbumId($rowId)->setActive(false)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableDeletionRowIds())
	{
		foreach($inputPost->getDeletionRowIds() as $rowId)
		{
			$album = new Album(null, $database);
			$album->findOneByAlbumId($rowId);
			if(album->hasValueAlbumId())
			{
				$albumTrash = new AlbumTrash($album, $database);
				$albumTrash->insert();
				$album->delete();
			}
		}
	}
}

```

### CRUD with Approval and without Trash


```php
if($inputGet->getUserAction() == UserAction::INSERT)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
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
	$albumApv->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
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
			$approval = new PicoApproval($album, 
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

			$approval->approve($columToBeCopied);
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
			$approval = new PicoApproval($album, 
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
			$approval->reject();
		}
	}
}

```

### CRUD with Approval and with Trash


```php
if($inputGet->getUserAction() == UserAction::INSERT)
{
	$album = new Album(null, $database);
	$album->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
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
	$albumApv->setAlbumId($inputPost->getAlbumId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setName($inputPost->getName(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setTitle($inputPost->getTitle(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setDescription($inputPost->getDescription(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setProducerId($inputPost->getProducerId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setReleaseDate($inputPost->getReleaseDate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setNumberOfSong($inputPost->getNumberOfSong(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setDuration($inputPost->getDuration(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setImagePath($inputPost->getImagePath(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setSortOrder($inputPost->getSortOrder(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
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
			$approval = new PicoApproval($album, 
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

			$approval->approve($columToBeCopied);
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
			$approval = new PicoApproval($album, 
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
			$approval->reject();
		}
	}
}

```
