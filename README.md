# AppBuilder
Application builder


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
	$album->setAdminCreate($);
	$album->setTimeCreate($);
	$album->setIpCreate($);
	$album->setAdminEdit($);
	$album->setTimeEdit($);
	$album->setIpEdit($);
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

### Without Approval and with Trash

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
	$album->setAdminCreate($);
	$album->setTimeCreate($);
	$album->setIpCreate($);
	$album->setAdminEdit($);
	$album->setTimeEdit($);
	$album->setIpEdit($);
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

### With Approval and without Trash


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
	$album->setAdminCreate($);
	$album->setTimeCreate($);
	$album->setIpCreate($);
	$album->setAdminEdit($);
	$album->setTimeEdit($);
	$album->setIpEdit($);
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

### With Approval and with Trash


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
	$album->setAdminCreate($);
	$album->setTimeCreate($);
	$album->setIpCreate($);
	$album->setAdminEdit($);
	$album->setTimeEdit($);
	$album->setIpEdit($);
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
