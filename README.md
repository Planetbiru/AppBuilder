# AppBuilder
Application builder


## CRUD Example

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
	$album->setTimeCreate($inputPost->getTimeCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setTimeEdit($inputPost->getTimeEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setAdminCreate($inputPost->getAdminCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setAdminEdit($inputPost->getAdminEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setIpCreate($inputPost->getIpCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setIpEdit($inputPost->getIpEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$album->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$album->setDraft(true);
	$album->setWaitingFor(1);
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
	$albumApv->setTimeCreate($inputPost->getTimeCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setTimeEdit($inputPost->getTimeEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setAdminCreate($inputPost->getAdminCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setAdminEdit($inputPost->getAdminEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setIpCreate($inputPost->getIpCreate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setIpEdit($inputPost->getIpEdit(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$albumApv->setLocked($inputPost->getLocked(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setAsDraft($inputPost->getAsDraft(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));
	$albumApv->setActive($inputPost->getActive(PicoRequestConstant::FILTER_SANITIZE_NUMBER_INT));

	$albumApv->insert();

	$album->setAdminAskEdit($currentAction->getUserId());
	$album->setTimeAskEdit($currentAction->getTime());
	$album->setIpAskEdit($currentAction->getIp());

	$album->setSongApvId($album->getSongApvId())->setWaitingFor(3)->update();
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

			$album->setAbumId($rowId)->setWaitingFor(3)->update();
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

			$album->setAbumId($rowId)->setWaitingFor(4)->update();
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

			$album->setAbumId($rowId)->setWaitingFor(5)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetAbumId())
	{
		$abumId = $inputPost->getAbumId();
		$album = new Album(null, $database);
		$album->findOneByAbumId($abumId);
		if($album->issetAbumId())
		{
			$approval = new PicoApproval($album);
			$approval->approve();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetAbumId())
	{
		$abumId = $inputPost->getAbumId();
		$album = new Album(null, $database);
		$album->findOneByAbumId($abumId);
		if($album->issetAbumId())
		{
			$approval = new PicoApproval($album);
			$approval->reject();
		}
	}
}
```