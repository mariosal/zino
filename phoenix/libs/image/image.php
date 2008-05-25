<?php
	/*
	Images media structure for images 
	*/
	
	global $libs;
	
	$libs->Load( 'album' );
    $libs->Load( 'image/server' );
	$libs->Load( 'image/frontpage' );
    $libs->Load( 'rabbit/helpers/file' );
   
    class ImageException extends Exception {
    }

    class ImageFinder extends Finder {
        protected $mModel = 'Image';
        
        public function FindByIds( $imageids ) {
            w_assert( is_array( $imageids ), 'ImageFinder->FindByIds() expects an array' );
            foreach ( $imagesids as $imageid ) {
                w_assert( is_int( $imageid ), 'Each item of the array passed to ImageFinder->FindByIds() must be an integer' );
            }
            if ( !count( $imageids ) ) {
                return array();
            }
            
            $query = $this->mDb->Prepare(
                'SELECT
                    *
                FROM
                    :images
                WHERE
                    `image_id` IN :imageids'
            );
            $query->BindTable( 'images' );
            $query->Bind( 'imageids', $imageids );
            
            return $this->FindBySQLResource( $query->Execute() );
        }
        public function FindByUser( User $theuser, $offset = 0, $limit = 15 ) {
            $prototype = New Image();
            $prototype->Userid = $theuser->Id;
            
            return $this->FindByPrototype( $prototype, $offset, $limit, array( 'Id', 'DESC' ) );
        }
        public function FindByAlbum( Album $album, $offset = 0, $limit = 25 ) {
            $prototype = New Image();
            $prototype->Albumid = $album->Id;
            $prototype->Delid = 0;

            return $this->FindByPrototype( $prototype, $offset, $limit, array( 'Id', 'DESC' ) );
        }
        public function FindAround( Image $pivot, $limit = 6 ) {
            w_assert( $pivot->Exists(), 'Image->FindAround() must only be called for an existing pivot image' );
            w_assert( $pivot->Album->Exists(), 'Image->FindAround() must only be called for a pivot image within an album' );

            $query = $this->mDb->Prepare(
                'SELECT
                    *
                FROM
                    :images
                WHERE
                    `image_albumid` = :albumid
                ORDER BY
                    ABS(`image_id` - :imageid)
                LIMIT
                    :limit'
            );
            $query->BindTable( 'images' );
            $query->Bind( 'albumid', $pivot->Album->Id );
            $query->Bind( 'imageid', $pivot->Id );
            $query->Bind( 'limit', $limit );
            $res = $query->Execute();

            $ret = array();
            while ( $row = $res->FetchArray() ) {
                $ret[ $row[ 'image_id' ] ] = New Image( $row );
            }
            krsort( $ret );

            $ret = array_values( $ret );

            return $ret;
        }
        public function FindFrontpage( $offset = 0, $limit = 15 ) {
            $finder = New FrontpageImageFinder();
            $found = $finder->FindLatest( $offset, $limit );
            $imageids = array();
            foreach ( $found as $frontpageimage ) {
                $imageids[] = $frontpageimage->Imageid;
            }
            if ( !count( $imageids ) ) {
                return array();
            }
            $query = $this->mDb->Prepare(
                'SELECT
                    *
                FROM
                    :images
                WHERE
                    `image_id` IN :imageids'
            );
            $query->BindTable( 'images' );
            $query->Bind( 'imageids', $imageids );
            return $this->FindBySQLResource( $query->Execute() );
        }
        public function Count() {
    		$query = $this->mDb->Prepare("
    			SELECT 
    				COUNT(*) AS imagesnum
    			FROM 
    				:images
    			WHERE
    				`image_delid` = 0;");
            $query->BindTable( 'images' );
            
    		$res = $query->Execute();
    		$row = $res->FetchArray();

    		return ( int )$row[ "imagesnum" ];
    	}
    }
	
    class Image extends Satori {
        protected $mDbTableAlias = 'images';
        protected $mTemporaryFile;
        
        protected function Relations() {
            $this->User = $this->HasOne( 'User', 'Userid' );
            $this->Album = $this->HasOne( 'Album', 'Albumid' );
        }
		public function GetServerUrl() {
			global $rabbit_settings;
			
			return $rabbit_settings[ 'resourcesdir' ] . '/' . $this->Userid . '/' . $this->Id;
		}
		public function IsDeleted() {
            return $this->Delid > 0 || !$this->Exists();
        }
		public function ProportionalSize( $maxw , $maxh ) {
			$propw = 1;
			$proph = 1;
			if ( $this->Width > $maxw ) {
				$propw = $this->Width / $maxw;
			}
			if ( $this->Height > $maxh ) {
				$proph = $this->Height / $maxh;
			}
			$prop = max( $propw , $proph );
			$size[ 0 ] = round( $this->Width / $prop , 0 );
			$size[ 1 ] = round( $this->Height / $prop , 0 );
			
			return $size;
		}
		public function CommentAdded() {
            if ( $this->Albumid > 0 ) {
                $this->Album->CommentAdded();
            }
		   
            ++$this->Numcomments;
		    return $this->Save();
		}
        public function AddPageview() {
            ++$this->Pageviews;
            return $this->Save();
        }
		public function Delete() {
            $this->Delid = 1;
            $this->Save();

            $this->Album->ImageDeleted( $this );

            $finder = New ImageFinder();
            
			// update latest images
            if ( $this->Album->Id == $this->User->EgoAlbum->Id ) {
    			$images = $finder->FindByAlbum( $this->User->EgoAlbum, 0, 1 );
                $frontpageimage = New FrontpageImage( $this->Userid );
                if ( !count( $images ) ) {
                    // no previous images uploaded
                    $frontpageimage->Delete();
                }
                else {
                    $frontpageimage->Imageid = $images[ 0 ]->Id;
                    $frontpageimage->Save();
                }
            }

            $this->OnDelete();
		}
        public function CommentDeleted() {
            if ( !$this->Album->CommentDeleted() ) {
                return false;
            }

            --$this->Numcomments;
            return $this->Save();
        }
        public function LoadFromFile( $value ) {
            $this->mTemporaryFile = $value;

            if ( filesize( $value ) > 1024 * 1024 ) {
                return -1;
            }
            return 0;
        }
        public function SetName( $value ) {
            if ( strlen( $value ) > 96 ) { // TODO: utf8_strlen()
                $value = utf8_substr( $value , 0 , 96 );
            }
            
            $this->mCurrentValues[ 'Name' ] = $value;
        }
        public function Upload( $resizeto = false ) {
            global $water;

            $path = $this->Userid . "/" . $this->Id;

            // throws ImageException
            $data = Image_Upload( $path, $this->mTemporaryFile, $resizeto );

            // else success
            w_assert( is_array( $data ), 'Image_Upload did not return an array' );

            if ( $data[ 'width' ] < 10 || $data[ 'height' ] < 10 ) {
                throw New ImageException( 'The resolution of target image is too small: ' . $data[ 'width' ] . 'x' . $data[ 'height' ] );
            }

            $this->Width = $data[ 'width' ];
            $this->Height = $data[ 'height' ];
            $this->Size = $data[ 'filesize' ];

            return true;
        }
        public function Save( $resizeto = false ) {
            if ( $this->Exists() ) {
                return parent::Save();
            }
            
            // else: only when creating 
            $this->Size = filesize( $this->mTemporaryFile );
            
            if ( !parent::Save() ) {
                return -1;
            }

            // throws ImageException
            $upload = $this->Upload( $resizeto );

            if ( parent::Save() ) { // save (update) again: Upload() has set size, width and height 
                if ( $this->Album->Id == $this->User->EgoAlbum->Id ) {
                    $frontpageimage = New FrontpageImage( $this->Userid );
                    if ( !$frontpageimage->Exists() ) {
                        $frontpageimage = New FrontpageImage();
                        $frontpageimage->Userid = $this->Userid;
                    }
                    $frontpageimage->Imageid = $this->Id;
                    $frontpageimage->Save();
                }
            }

            return true;
        }
        protected function OnDelete() {
            --$this->User->Count->Images;
            $this->User->Count->Save();

            if ( $this->Album->Exists() ) {
                --$this->Album->Numphotos;
                $this->Album->Save();
            }
        }
        protected function OnCreate() {
            ++$this->User->Count->Images;
            $this->User->Count->Save();

            if ( $this->Album->Exists() ) {
                ++$this->Album->Numphotos;
                $this->Album->Save();
            }
        }
        public function LoadDefaults() {
            global $user;

            $this->Created = NowDate();
            $this->Userip  = UserIp();
            $this->Width   = 0;
            $this->Height  = 0;
            $this->Userid  = $user->Id;
        }
    }
?>
