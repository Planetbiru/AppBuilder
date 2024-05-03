<?php

use MagicObject\MagicObject;
use MagicObject\SetterGetter;
use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Database\PicoSpecification;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Util\AttrUtil;
use AppBuilder\PicoApproval;
use AppBuilder\UserAction;
use YourApplication\Data\Entity\Album;

require_once __DIR__ . "/inc.app/auth.php";

$inputGet = new InputGet();
$inputPost = new InputPost();

if($inputGet->getUserAction() == UserAction::INSERT)
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
if($inputGet->getUserAction() == UserAction::INSERT)
{
?>
<form name="insertform" id="insertform" action="" method="post">
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td>Album</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="album_id" id="album_id"/>
        </td>
      </tr>
      <tr>
        <td>Name</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="name" id="name"/>
        </td>
      </tr>
      <tr>
        <td>Title</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="title" id="title"/>
        </td>
      </tr>
      <tr>
        <td>Description</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="description" id="description"/>
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
        <td>Release Date</td>
        <td>
          <input autocomplete="off" class="form-control" type="date" name="release_date" id="release_date"/>
        </td>
      </tr>
      <tr>
        <td>Number Of Song</td>
        <td>
          <input autocomplete="off" class="form-control" type="number" name="number_of_song" id="number_of_song"/>
        </td>
      </tr>
      <tr>
        <td>Duration</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="duration" id="duration"/>
        </td>
      </tr>
      <tr>
        <td>Image Path</td>
        <td>
          <input autocomplete="off" class="form-control" type="text" name="image_path" id="image_path"/>
        </td>
      </tr>
      <tr>
        <td>Sort Order</td>
        <td>
          <input autocomplete="off" class="form-control" type="number" name="sort_order" id="sort_order"/>
        </td>
      </tr>
      <tr>
        <td>Locked</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1"/> Locked</label>
        </td>
      </tr>
      <tr>
        <td>As Draft</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1"/> As Draft</label>
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
        <td><input type="submit" class="btn btn-success" name="save-insert" id="save-insert" value="<?php  echo $currentLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php  echo $currentLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
      </tr>
    </tbody>
  </table>
</form>
<?php 
}
else if($inputGet->getUserAction() == UserAction::UPDATE)
{
	$album = new Album(null, $database);
	try{
		$album->find($inputGet->getAlbumId());
	} catch(Exception $e){
		$album = new Album(null, $database);
		// Do somtething here
	}
?>
<form name="insertform" id="insertform" action="" method="post">
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td>Album</td>
        <td>
          <input class="form-control" type="text" name="album_id" id="album_id" value="<?php echo $album->getAlbumId();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Name</td>
        <td>
          <input class="form-control" type="text" name="name" id="name" value="<?php echo $album->getName();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Title</td>
        <td>
          <input class="form-control" type="text" name="title" id="title" value="<?php echo $album->getTitle();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Description</td>
        <td>
          <input class="form-control" type="text" name="description" id="description" value="<?php echo $album->getDescription();?>" autocomplete="off"/>
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
        <td>Release Date</td>
        <td>
          <input class="form-control" type="date" name="release_date" id="release_date" value="<?php echo $album->getReleaseDate();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Number Of Song</td>
        <td>
          <input class="form-control" type="number" name="number_of_song" id="number_of_song" value="<?php echo $album->getNumberOfSong();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Duration</td>
        <td>
          <input class="form-control" type="text" name="duration" id="duration" value="<?php echo $album->getDuration();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Image Path</td>
        <td>
          <input class="form-control" type="text" name="image_path" id="image_path" value="<?php echo $album->getImagePath();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Sort Order</td>
        <td>
          <input class="form-control" type="number" name="sort_order" id="sort_order" value="<?php echo $album->getSortOrder();?>" autocomplete="off"/>
        </td>
      </tr>
      <tr>
        <td>Locked</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="locked" id="locked" value="1" <?php echo $album->createCheckedLocked();?>/> Locked</label>
        </td>
      </tr>
      <tr>
        <td>As Draft</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="as_draft" id="as_draft" value="1" <?php echo $album->createCheckedAsDraft();?>/> As Draft</label>
        </td>
      </tr>
      <tr>
        <td>Active</td>
        <td>
          <label><input class="form-check-input" type="checkbox" name="active" id="active" value="1" <?php echo $album->createCheckedActive();?>/> Active</label>
        </td>
      </tr>
    </tbody>
  </table>
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td></td>
        <td><input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php  echo $currentLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php  echo $currentLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
      </tr>
    </tbody>
  </table>
</form>
<?php 
}
else if($inputGet->getUserAction() == UserAction::DETAIL)
{
	$album = new Album(null, $database);
	try{
		$album->find($inputGet->get());
	} catch(Exception $e){
		$album = new Album(null, $database);
		// Do somtething here
	}
?>
<form name="insertform" id="insertform" action="" method="post">
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td>Album</td>
        <td><?php echo $album->getAlbumId();?></td>
      </tr>
      <tr>
        <td>Name</td>
        <td><?php echo $album->getName();?></td>
      </tr>
      <tr>
        <td>Title</td>
        <td><?php echo $album->getTitle();?></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><?php echo $album->getDescription();?></td>
      </tr>
      <tr>
        <td>Producer</td>
        <td><?php echo $album->getProducerId();?></td>
      </tr>
      <tr>
        <td>Release Date</td>
        <td><?php echo $album->getReleaseDate();?></td>
      </tr>
      <tr>
        <td>Number Of Song</td>
        <td><?php echo $album->getNumberOfSong();?></td>
      </tr>
      <tr>
        <td>Duration</td>
        <td><?php echo $album->getDuration();?></td>
      </tr>
      <tr>
        <td>Image Path</td>
        <td><?php echo $album->getImagePath();?></td>
      </tr>
      <tr>
        <td>Sort Order</td>
        <td><?php echo $album->getSortOrder();?></td>
      </tr>
      <tr>
        <td>Locked</td>
        <td><?php echo $album->optionLocked($currentLanguage->getYes(), $currentLanguage->getNo());?></td>
      </tr>
      <tr>
        <td>As Draft</td>
        <td><?php echo $album->optionAsDraft($currentLanguage->getYes(), $currentLanguage->getNo());?></td>
      </tr>
      <tr>
        <td>Active</td>
        <td><?php echo $album->optionActive($currentLanguage->getYes(), $currentLanguage->getNo());?></td>
      </tr>
    </tbody>
  </table>
  <table class="responsive responsive-two-cols" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr>
        <td></td>
        <td><input type="submit" class="btn btn-success" name="save-update" id="save-update" value="<?php  echo $currentLanguage->getButtonSave(); ?>"/> <input type="button" class="btn btn-primary" value="<?php  echo $currentLanguage->getButtonCancel(); ?>" onclick="window.location='<?php echo $selfPath;?>';"/></td>
      </tr>
    </tbody>
  </table>
</form>
<?php 
}

