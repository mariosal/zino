<?php
	class TestImageTag extends Testcase {
        protected $mAppliesTo = 'libs/image/tag';
        private $mImage;
        private $mUser;
        private $mUser2;
        private $mTag1;
        private $mTag2;

        public function SetUp() {
            $finder = New UserFinder();
            $user = $finder->FindByName( 'testimagetags' );
            if ( $user !== false ) {
                $user->Delete();
            }
            $user = $finder->FindByName( 'testimagetags2' );
            if ( $user !== false ) {
                $user->Delete();
            }

            $this->mUser = New User();
            $this->mUser->Name = 'testimagetags';
            $this->mUser->Subdomain = 'testimagetags';
            $this->mUser->Save();
            
            $this->mUser2 = New User();
            $this->mUser2->Name = 'testimagetags2';
            $this->mUser2->Subdomain = 'testimagetags2';
            $this->mUser2->Save();

            $this->mImage = New Image();
            $temp = tempnam( '/tmp', 'excalibur_' );
            $im = imagecreatetruecolor( 100, 100 );
            imagefill( $im, 50, 50, imagecolorallocate( $im, 255, 0, 0 ) );
            imagejpeg( $im, $temp );
            $this->mImage->LoadFromFile( $temp );
            $this->mImage->Name = 'test';
            $this->mImage->Userid = $this->mUser->Id;
            $this->mImage->Save();

            w_assert( is_object( $this->mImage ) );
        }
	    public function TestClassesExist() {
            $this->Assert( class_exists( 'ImageTag' ), 'ImageTag class does not exist' );
            $this->Assert( class_exists( 'ImageTagFinder' ), 'ImageTagFinder class does not exist' );
        }
        public function TestCreate() {
            $this->mTag1 = New ImageTag(); 
            $this->mTag1->Left = 20;
            $this->mTag1->Top = 21;
            $this->mTag1->Width = 150;
            $this->mTag1->Height = 150;
            $this->mTag1->Imageid = $this->mImage->Id;
            $this->mTag1->Ownerid = $this->mUser->Id;
            $this->mTag1->Personid = $this->mUser->Id;
            $this->mTag1->Save();

            $this->mTag2 = New ImageTag(); 
            $this->mTag2->Left = 50;
            $this->mTag2->Top = 60;
            $this->mTag2->Width = 200;
            $this->mTag2->Height = 100;
            $this->mTag2->Imageid = $this->mImage->Id;
            $this->mTag2->Ownerid = $this->mUser->Id;
            $this->mTag2->Personid = $this->mUser2->Id;
            $this->mTag2->Save();

            $this->Assert( $this->mTag1->Exists(), 'Failed to create tag 1' );
            $this->Assert( $this->mTag2->Exists(), 'Failed to create tag 2' );
        }
        public function TestFindByImage() {
            $finder = New ImageTagFinder();
            $tags = $finder->FindByImage( $this->mImage );
            $this->AssertEquals( 2, count( $tags ), 'Image must have two tags' );
            $i = 0;
            foreach ( $tags as $tag ) {
                switch ( $i ) {
                    case 0:
                        $this->Assert( $tag->Exists(), 'Tag2 found, but does not exist' );
                        $this->AssertEquals( $this->mUser2->Id, $tag->Personid, 'Tag2 has wrong personid' );   
                        $this->AssertEquals( $this->mUser->Id, $tag->Ownerid, 'Tag2 has wrong ownerid' );   
                        $this->AssertEquals( $this->mImage->Id, $tag->Imageid, 'Tag2 has wrong imageid' );
                        $this->AssertEquals( 50, $tag->Left, 'Tag1 X is wrong' );
                        $this->AssertEquals( 60, $tag->Top, 'Tag1 Y is wrong' );
                        $this->AssertEquals( 200, $tag->Width, 'Tag2 Width is wrong' );
                        $this->AssertEquals( 100, $tag->Height, 'Tag2 Height is wrong' );
                        break;
                    case 1:
                        $this->Assert( $tag->Exists(), 'Tag1 found, but does not exist' );
                        $this->AssertEquals( $this->mUser->Id, $tag->Personid, 'Tag1 has wrong personid' );   
                        $this->AssertEquals( $this->mUser->Id, $tag->Ownerid, 'Tag1 has wrong ownerid' );   
                        $this->AssertEquals( $this->mImage->Id, $tag->Imageid, 'Tag1 has wrong imageid' );
                        $this->AssertEquals( 20, $tag->Left, 'Tag1 Left is wrong' );
                        $this->AssertEquals( 21, $tag->Top, 'Tag1 Top is wrong' );
                        $this->AssertEquals( 150, $tag->Width, 'Tag1 Width is wrong' );
                        $this->AssertEquals( 150, $tag->Height, 'Tag1 Height is wrong' );
                        break;
                }
                ++$i;
            }
        }
        public function TestDelete() {
            $this->mTag1->Delete();
            $this->mTag2->Delete();

            $tag1 = New ImageTag( $this->mTag1->Id );
            $tag2 = New ImageTag( $this->mTag2->Id );

            $this->AssertFalse( $tag1->Exists(), 'Tag1 exists after deletion' );
            $this->AssertFalse( $tag2->Exists(), 'Tag2 exists after deletion' );
        }
        public function TearDown() {
            w_assert( is_object( $this->mImage ) );
            $this->mImage->Delete();
            $this->mUser->Delete();
            $this->mUser2->Delete();
        }
	}

    return New TestImageTag();
?>
