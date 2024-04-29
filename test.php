<?php

if($inputGet->getUserAction() == UserAction::INSERT)
{
	$songUpdateHistory = new SongUpdateHistory(null, $database);
	$songUpdateHistory->setSongUpdateHistoryId($inputPost->getSongUpdateHistoryId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setSongId($inputPost->getSongId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setUserId($inputPost->getUserId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setUserActivityId($inputPost->getUserActivityId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setAction($inputPost->getAction(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setTimeUpdate($inputPost->getTimeUpdate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setIpUpdate($inputPost->getIpUpdate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistory->setDraft(true);
	$songUpdateHistory->setWaitingFor(1);
	$songUpdateHistory->setAdminCreate($currentAction->getUserId());
	$songUpdateHistory->setTimeCreate($currentAction->getTime());
	$songUpdateHistory->setIpCreate($currentAction->getIp());
	$songUpdateHistory->setAdminEdit($currentAction->getUserId());
	$songUpdateHistory->setTimeEdit($currentAction->getTime());
	$songUpdateHistory->setIpEdit($currentAction->getIp());

	$songUpdateHistory->insert();

	$songUpdateHistoryApv = new SongUpdateHistoryApv($songUpdateHistory, $database);

	$songUpdateHistoryApv->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$songUpdateHistory = new SongUpdateHistory(null, $database);

	$songUpdateHistoryApv = new SongUpdateHistoryApv(null, $database);
	$songUpdateHistoryApv->setSongUpdateHistoryId($inputPost->getSongUpdateHistoryId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setSongId($inputPost->getSongId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setUserId($inputPost->getUserId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setUserActivityId($inputPost->getUserActivityId(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setAction($inputPost->getAction(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setTimeUpdate($inputPost->getTimeUpdate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setIpUpdate($inputPost->getIpUpdate(PicoRequestConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songUpdateHistoryApv->setAdminEdit($currentAction->getUserId());
	$songUpdateHistoryApv->setTimeEdit($currentAction->getTime());
	$songUpdateHistoryApv->setIpEdit($currentAction->getIp());

	$songUpdateHistoryApv->insert();

	$songUpdateHistory->setAdminAskEdit($currentAction->getUserId());
	$songUpdateHistory->setTimeAskEdit($currentAction->getTime());
	$songUpdateHistory->setIpAskEdit($currentAction->getIp());

	$songUpdateHistory->setSongUpdateHistoryId($songUpdateHistory->getSongUpdateHistoryId())->setWaitingFor(3)->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$songUpdateHistory = new SongUpdateHistory(null, $database);

			$songUpdateHistory->setAdminAskEdit($currentAction->getUserId());
			$songUpdateHistory->setTimeAskEdit($currentAction->getTime());
			$songUpdateHistory->setIpAskEdit($currentAction->getIp());

			$songUpdateHistory->setSongUpdateHistoryId($rowId)->setWaitingFor(3)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$songUpdateHistory = new SongUpdateHistory(null, $database);

			$songUpdateHistory->setAdminAskEdit($currentAction->getUserId());
			$songUpdateHistory->setTimeAskEdit($currentAction->getTime());
			$songUpdateHistory->setIpAskEdit($currentAction->getIp());

			$songUpdateHistory->setSongUpdateHistoryId($rowId)->setWaitingFor(4)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$songUpdateHistory = new SongUpdateHistory(null, $database);

			$songUpdateHistory->setAdminAskEdit($currentAction->getUserId());
			$songUpdateHistory->setTimeAskEdit($currentAction->getTime());
			$songUpdateHistory->setIpAskEdit($currentAction->getIp());

			$songUpdateHistory->setSongUpdateHistoryId($rowId)->setWaitingFor(5)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetSongUpdateHistoryId())
	{
		$songUpdateHistoryId = $inputPost->getSongUpdateHistoryId();
		$songUpdateHistory = new SongUpdateHistory(null, $database);
		$songUpdateHistory->findOneBySongUpdateHistoryId($songUpdateHistoryId);
		if($songUpdateHistory->issetSongUpdateHistoryId())
		{
			$approval = new PicoApproval($songUpdateHistory, 
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

			// List of properties to be copied from SongUpdateHistoryApv to SongUpdateHistory. You can add or remove it
			$columToBeCopied = array(
				"songId", 
				"userId", 
				"userActivityId", 
				"action", 
				"timeUpdate", 
				"ipUpdate"
			);

			$approval->approve($columToBeCopied);
		}
	}
}
else if($inputGet->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetSongUpdateHistoryId())
	{
		$songUpdateHistoryId = $inputPost->getSongUpdateHistoryId();
		$songUpdateHistory = new SongUpdateHistory(null, $database);
		$songUpdateHistory->findOneBySongUpdateHistoryId($songUpdateHistoryId);
		if($songUpdateHistory->issetSongUpdateHistoryId())
		{
			$approval = new PicoApproval($songUpdateHistory, 
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


