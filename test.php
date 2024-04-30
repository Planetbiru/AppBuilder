<?php

use MagicObject\MagicObject;
use MagicObject\SetterGetter;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use YourApplication\Data\Entity\Song;
use YourApplication\Data\Entity\SongApv;
use YourApplication\Data\Entity\SongTrash;

require_once __DIR__ . "auth.php";

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
			$approval = new PicoApproval($song, $entityInfo, $entityApvInfo, 
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

			$approval->approve($columToBeCopied, new SongApv(), new SongTrash(), $approvalCallback);
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
			$approval = new PicoApproval($song, $entityInfo, $entityApvInfo, 
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
			$approval->reject(new SongApv());
		}
	}
}
if($inputGet->getUserAction() == UserAction::INSERT)
{
?>

<form name="insertform" id="insertform" action="" method="post">
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td>Name</td>
        <td>
          <input class="form-control" type="text" name="name" id="name"/>
        </td>
      </tr>
      <tr>
        <td>Title</td>
        <td>
          <input class="form-control" type="text" name="title" id="title"/>
        </td>
      </tr>
      <tr>
        <td>Album</td>
        <td>
          <select class="form-control" name="album_id" id="album_id">
            <option value="">- Select One -</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Track Number</td>
        <td>
          <input class="form-control" type="number" name="track_number" id="track_number"/>
        </td>
      </tr>
      <tr>
        <td>Producer</td>
        <td>
          <select class="form-control" name="producer_id" id="producer_id">
            <option value="">- Select One -</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Artist Vocalist</td>
        <td>
          <select class="form-control" name="artist_vocalist" id="artist_vocalist">
            <option value="">- Select One -</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Artist Composer</td>
        <td>
          <select class="form-control" name="artist_composer" id="artist_composer">
            <option value="">- Select One -</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Artist Arranger</td>
        <td>
          <select class="form-control" name="artist_arranger" id="artist_arranger">
            <option value="">- Select One -</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>File Path</td>
        <td>
          <input class="form-control" type="text" name="file_path" id="file_path"/>
        </td>
      </tr>
      <tr>
        <td>File Name</td>
        <td>
          <input class="form-control" type="text" name="file_name" id="file_name"/>
        </td>
      </tr>
      <tr>
        <td>File Type</td>
        <td>
          <input class="form-control" type="text" name="file_type" id="file_type"/>
        </td>
      </tr>
      <tr>
        <td>File Extension</td>
        <td>
          <input class="form-control" type="text" name="file_extension" id="file_extension"/>
        </td>
      </tr>
      <tr>
        <td>File Size</td>
        <td>
          <input class="form-control" type="number" name="file_size" id="file_size"/>
        </td>
      </tr>
      <tr>
        <td>File Md5</td>
        <td>
          <input class="form-control" type="text" name="file_md5" id="file_md5"/>
        </td>
      </tr>
      <tr>
        <td>File Upload Time</td>
        <td>
          <input class="form-control" type="datetime" name="file_upload_time" id="file_upload_time"/>
        </td>
      </tr>
      <tr>
        <td>First Upload Time</td>
        <td>
          <input class="form-control" type="datetime" name="first_upload_time" id="first_upload_time"/>
        </td>
      </tr>
      <tr>
        <td>Last Upload Time</td>
        <td>
          <input class="form-control" type="datetime" name="last_upload_time" id="last_upload_time"/>
        </td>
      </tr>
      <tr>
        <td>File Path Midi</td>
        <td>
          <input class="form-control" type="text" name="file_path_midi" id="file_path_midi"/>
        </td>
      </tr>
      <tr>
        <td>Last Upload Time Midi</td>
        <td>
          <input class="form-control" type="datetime" name="last_upload_time_midi" id="last_upload_time_midi"/>
        </td>
      </tr>
      <tr>
        <td>File Path Xml</td>
        <td>
          <input class="form-control" type="text" name="file_path_xml" id="file_path_xml"/>
        </td>
      </tr>
      <tr>
        <td>Last Upload Time Xml</td>
        <td>
          <input class="form-control" type="datetime" name="last_upload_time_xml" id="last_upload_time_xml"/>
        </td>
      </tr>
      <tr>
        <td>File Path Pdf</td>
        <td>
          <input class="form-control" type="text" name="file_path_pdf" id="file_path_pdf"/>
        </td>
      </tr>
      <tr>
        <td>Last Upload Time Pdf</td>
        <td>
          <input class="form-control" type="datetime" name="last_upload_time_pdf" id="last_upload_time_pdf"/>
        </td>
      </tr>
      <tr>
        <td>Duration</td>
        <td>
          <input class="form-control" type="text" name="duration" id="duration"/>
        </td>
      </tr>
      <tr>
        <td>Genre</td>
        <td>
          <input class="form-control" type="text" name="genre_id" id="genre_id"/>
        </td>
      </tr>
      <tr>
        <td>BPM</td>
        <td>
          <input class="form-control" type="text" name="bpm" id="bpm"/>
        </td>
      </tr>
      <tr>
        <td>Time Signature</td>
        <td>
          <input class="form-control" type="text" name="time_signature" id="time_signature"/>
        </td>
      </tr>
      <tr>
        <td>Subtitle</td>
        <td>
          <textarea class="form-control" name="subtitle" id="subtitle"></textarea>
        </td>
      </tr>
      <tr>
        <td>Subtitle Complete</td>
        <td>
          <input class="form-control" type="number" name="subtitle_complete" id="subtitle_complete"/>
        </td>
      </tr>
      <tr>
        <td>Lyric Midi</td>
        <td>
          <input class="form-control" type="text" name="lyric_midi" id="lyric_midi"/>
        </td>
      </tr>
      <tr>
        <td>Lyric Midi Raw</td>
        <td>
          <input class="form-control" type="text" name="lyric_midi_raw" id="lyric_midi_raw"/>
        </td>
      </tr>
      <tr>
        <td>Vocal Guide</td>
        <td>
          <input class="form-control" type="text" name="vocal_guide" id="vocal_guide"/>
        </td>
      </tr>
      <tr>
        <td>Vocal</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="vocal" id="vocal" value="1"/> Vocal</label>
        </td>
      </tr>
      <tr>
        <td>Instrument</td>
        <td>
          <input class="form-control" type="text" name="instrument" id="instrument"/>
        </td>
      </tr>
      <tr>
        <td>Midi Vocal Channel</td>
        <td>
          <input class="form-control" type="number" name="midi_vocal_channel" id="midi_vocal_channel"/>
        </td>
      </tr>
      <tr>
        <td>Rating</td>
        <td>
          <input class="form-control" type="text" name="rating" id="rating"/>
        </td>
      </tr>
      <tr>
        <td>Comment</td>
        <td>
          <input class="form-control" type="text" name="comment" id="comment"/>
        </td>
      </tr>
      <tr>
        <td>Image Path</td>
        <td>
          <input class="form-control" type="text" name="image_path" id="image_path"/>
        </td>
      </tr>
      <tr>
        <td>Last Upload Time Image</td>
        <td>
          <input class="form-control" type="datetime" name="last_upload_time_image" id="last_upload_time_image"/>
        </td>
      </tr>
      <tr>
        <td>Active</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="active" id="active" value="1"/> Active</label>
        </td>
      </tr>
    </tbody>
  </table>
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


