<?php
	
	function ElementAlbumPhotoUpload( tInteger $albumid ) {
		global $water;
		
		$water->Disable();
		$albumid = $albumid->Get();
		$album = new Album( $albumid );
		
		?><form method="post" enctype="multipart/form-data" action="do/image/upload2">
			<input type="hidden" name="albumid" value="<?php
			echo $album->Id;
			?>" />
			<input type="file" name="uploadimage" onchange="this.parentNode.submit();" />
			<input type="submit" value="upload" style="display:none" />
		</form><?php	
		return array( 'tiny' => true );
	}
?>
