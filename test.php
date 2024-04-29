<?php

use YourApplication\Song;
use YourApplication\SongApv;
use YourApplication\SongTrash;
use MagicObject\MagicObject;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Request\UserAction;

$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputGet->getUserAction() == UserAction::INSERT)
{
	$song = new Song(null, $database);
	$song->setSongId($inputPost->getSongId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setRandomSongId($inputPost->getRandomSongId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setTitle($inputPost->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setAlbumId($inputPost->getAlbumId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setTrackNumber($inputPost->getTrackNumber(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setProducerId($inputPost->getProducerId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setArtistVocalist($inputPost->getArtistVocalist(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setArtistComposer($inputPost->getArtistComposer(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setArtistArranger($inputPost->getArtistArranger(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFilePath($inputPost->getFilePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFileName($inputPost->getFileName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFileType($inputPost->getFileType(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFileExtension($inputPost->getFileExtension(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFileSize($inputPost->getFileSize(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setFileMd5($inputPost->getFileMd5(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFileUploadTime($inputPost->getFileUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFirstUploadTime($inputPost->getFirstUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLastUploadTime($inputPost->getLastUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFilePathMidi($inputPost->getFilePathMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLastUploadTimeMidi($inputPost->getLastUploadTimeMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFilePathXml($inputPost->getFilePathXml(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLastUploadTimeXml($inputPost->getLastUploadTimeXml(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setFilePathPdf($inputPost->getFilePathPdf(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLastUploadTimePdf($inputPost->getLastUploadTimePdf(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setDuration($inputPost->getDuration(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setGenreId($inputPost->getGenreId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setBpm($inputPost->getBpm(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setTimeSignature($inputPost->getTimeSignature(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setSubtitle($inputPost->getSubtitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setSubtitleComplete($inputPost->getSubtitleComplete(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setLyricMidi($inputPost->getLyricMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLyricMidiRaw($inputPost->getLyricMidiRaw(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setVocalGuide($inputPost->getVocalGuide(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setVocal($inputPost->getVocal(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setInstrument($inputPost->getInstrument(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setMidiVocalChannel($inputPost->getMidiVocalChannel(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setRating($inputPost->getRating(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setComment($inputPost->getComment(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setLastUploadTimeImage($inputPost->getLastUploadTimeImage(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$song->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$song->setDraft(true);
	$song->setWaitingFor(1);
	$song->setAdminCreate($currentAction->getUserId());
	$song->setTimeCreate($currentAction->getTime());
	$song->setIpCreate($currentAction->getIp());
	$song->setAdminEdit($currentAction->getUserId());
	$song->setTimeEdit($currentAction->getTime());
	$song->setIpEdit($currentAction->getIp());

	$song->insert();

	$songApv = new SongApv($song, $database);

	$songApv->insert();
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$song = new Song(null, $database);

	$songApv = new SongApv(null, $database);
	$songApv->setSongId($inputPost->getSongId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setRandomSongId($inputPost->getRandomSongId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setName($inputPost->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setTitle($inputPost->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setAlbumId($inputPost->getAlbumId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setTrackNumber($inputPost->getTrackNumber(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setProducerId($inputPost->getProducerId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setArtistVocalist($inputPost->getArtistVocalist(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setArtistComposer($inputPost->getArtistComposer(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setArtistArranger($inputPost->getArtistArranger(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFilePath($inputPost->getFilePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFileName($inputPost->getFileName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFileType($inputPost->getFileType(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFileExtension($inputPost->getFileExtension(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFileSize($inputPost->getFileSize(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setFileMd5($inputPost->getFileMd5(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFileUploadTime($inputPost->getFileUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFirstUploadTime($inputPost->getFirstUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLastUploadTime($inputPost->getLastUploadTime(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFilePathMidi($inputPost->getFilePathMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLastUploadTimeMidi($inputPost->getLastUploadTimeMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFilePathXml($inputPost->getFilePathXml(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLastUploadTimeXml($inputPost->getLastUploadTimeXml(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setFilePathPdf($inputPost->getFilePathPdf(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLastUploadTimePdf($inputPost->getLastUploadTimePdf(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setDuration($inputPost->getDuration(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setGenreId($inputPost->getGenreId(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setBpm($inputPost->getBpm(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setTimeSignature($inputPost->getTimeSignature(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setSubtitle($inputPost->getSubtitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setSubtitleComplete($inputPost->getSubtitleComplete(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setLyricMidi($inputPost->getLyricMidi(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLyricMidiRaw($inputPost->getLyricMidiRaw(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setVocalGuide($inputPost->getVocalGuide(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setVocal($inputPost->getVocal(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setInstrument($inputPost->getInstrument(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setMidiVocalChannel($inputPost->getMidiVocalChannel(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setRating($inputPost->getRating(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setComment($inputPost->getComment(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setImagePath($inputPost->getImagePath(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setLastUploadTimeImage($inputPost->getLastUploadTimeImage(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS));
	$songApv->setActive($inputPost->getActive(PicoFilterConstant::FILTER_SANITIZE_NUMBER_INT));
	$songApv->setAdminEdit($currentAction->getUserId());
	$songApv->setTimeEdit($currentAction->getTime());
	$songApv->setIpEdit($currentAction->getIp());

	$songApv->insert();

	$song->setAdminAskEdit($currentAction->getUserId());
	$song->setTimeAskEdit($currentAction->getTime());
	$song->setIpAskEdit($currentAction->getIp());

	$song->setSongId($song->getSongId())->setWaitingFor(3)->update();
}
else if($inputGet->getUserAction() == UserAction::ACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$song = new Song(null, $database);

			$song->setAdminAskEdit($currentAction->getUserId());
			$song->setTimeAskEdit($currentAction->getTime());
			$song->setIpAskEdit($currentAction->getIp());

			$song->setSongId($rowId)->setWaitingFor(3)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DEACTIVATE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$song = new Song(null, $database);

			$song->setAdminAskEdit($currentAction->getUserId());
			$song->setTimeAskEdit($currentAction->getTime());
			$song->setIpAskEdit($currentAction->getIp());

			$song->setSongId($rowId)->setWaitingFor(4)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::DELETE)
{
	if($inputPost->countableAtivationRowIds())
	{
		foreach($inputPost->getAtivationRowIds() as $rowId)
		{
			$song = new Song(null, $database);

			$song->setAdminAskEdit($currentAction->getUserId());
			$song->setTimeAskEdit($currentAction->getTime());
			$song->setIpAskEdit($currentAction->getIp());

			$song->setSongId($rowId)->setWaitingFor(5)->update();
		}
	}
}
else if($inputGet->getUserAction() == UserAction::APPROVE)
{
	if($inputPost->issetSongId())
	{
		$songId = $inputPost->getSongId();
		$song = new Song(null, $database);
		$song->findOneBySongId($songId);
		if($song->issetSongId())
		{
			$approval = new PicoApproval($song, 
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

			// List of properties to be copied from SongApv to Song. You can add or remove it
			$columToBeCopied = array(
				"randomSongId", 
				"name", 
				"title", 
				"albumId", 
				"trackNumber", 
				"producerId", 
				"artistVocalist", 
				"artistComposer", 
				"artistArranger", 
				"filePath", 
				"fileName", 
				"fileType", 
				"fileExtension", 
				"fileSize", 
				"fileMd5", 
				"fileUploadTime", 
				"firstUploadTime", 
				"lastUploadTime", 
				"filePathMidi", 
				"lastUploadTimeMidi", 
				"filePathXml", 
				"lastUploadTimeXml", 
				"filePathPdf", 
				"lastUploadTimePdf", 
				"duration", 
				"genreId", 
				"bpm", 
				"timeSignature", 
				"subtitle", 
				"subtitleComplete", 
				"lyricMidi", 
				"lyricMidiRaw", 
				"vocalGuide", 
				"vocal", 
				"instrument", 
				"midiVocalChannel", 
				"rating", 
				"comment", 
				"imagePath", 
				"lastUploadTimeImage", 
				"active"
			);

			$approvalCallback = new ApprovalCallback();
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

			$approval->approve($columToBeCopied, $approvalCallback);
		}
	}
}
else if($inputGet->getUserAction() == UserAction::REJECT)
{
	if($inputPost->issetSongId())
	{
		$songId = $inputPost->getSongId();
		$song = new Song(null, $database);
		$song->findOneBySongId($songId);
		if($song->issetSongId())
		{
			$approval = new PicoApproval($song, 
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


