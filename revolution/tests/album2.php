<?php
    class TestAlbum extends ModelTestCase {
        protected $mAppliesTo = 'models/album';
        protected $mCovers = 'Album';

        public function SetUp() {
            clude( 'models/album.php' );
            clude( 'models/types.php' );
            clude( 'models/user.php' );

            $this->GenerateTestUsers( 3 );
        }
        public function TearDown() {
            $this->DeleteTestUsers();
        }
        public function PreConditions() {
            $this->AssertClassExists( 'Album' );
            $this->AssertMethodExists( 'Album', 'Item' );
            $this->AssertMethodExists( 'Album', 'Create' );
            $this->AssertMethodExists( 'Album', 'Delete' );
        }
        /**
         * @dataProvider ValidIds
         */
        public function TestItem( $id ) {
            $album = Album::Item( $id );
            $this->AssertArrayHasKeys( $album, array( 'id', 'ownerid', 'mainimageid' ) );
            $this->AssertArrayValues( $album, array( 'id' => ( string )$id ) );
        }
        /**
         * @dataProvider ExampleData
         */
        public function TestCreate( $userid, $name, $description ) {
            $album = Album::Create( $userid, $name, $description );
            $this->AssertArrayHasKeys( $album, array( 'id', 'url', 'created' ) );
            $this->AssertArrayValues( $album, array(
                'ownerid' => $userid,
                'name' => $name,
                'description' => $description,
                'mainimageid' => 0,
                'delid' => 0,
                'numcomments' => 0,
                'numphotos' => 0
            ) );

            $id = $album[ 'id' ];
            $album = Album::Item( $id );
            $this->Called( "Album::Item" );
            $this->AssertArrayValues( $album, array(
                'id' => ( string )$id,
                'ownerid' => ( string )$userid,
                'name' => $name,
                'description' => $description
            ) );

            return array( 'ownerid' => $album[ 'ownerid' ], 'name' => "neo", 'description' => "gamato asfasf", 'albumid' => $album[ 'id' ], 'mainimageid' => $album[ 'mainimageid' ] );
        }
        /**
         * @producer TestCreate
         */
        public function TestUpdate( $info ) {
            $ownerid = $info[ 'ownerid' ];
            $name = $info[ 'name' ];
            $description = $info[ 'description' ];
            $albumid = $info[ 'albumid' ];
            $mainimageid = $info[ 'mainimageid' ]; 

			$album = Album::Item( $albumid );
            $id = $album[ 'id' ];
			$oldname = $album[ 'name' ];
            $success = Album::Update( $id, $name, $description, $mainimageid );
            $this->Assert( $success, 'Album::Update failed' );

            $album = Album::Item( $albumid );
            $this->Called( "Album::Item" );
			if ( User::GetEgoAlbumId( $ownerid )  != $albumid ) {
		        $this->AssertArrayValues( $album, array(
		            'id' => $id,
		            'name' => $name,
		            'description' => $description,
		            'mainimageid' => $mainimageid
		        ) );
			}
			else {
				$this->AssertArrayValues( $album, array(
		            'id' => $id,
		            'name' => $oldname,
		            'description' => $description,
		            'mainimageid' => $mainimageid
		        ) );
			}
            return array( $album[ 'ownerid' ], $album );
        }


        public function ValidIds( $num = 3 ) {
            $res = db( 'SELECT `album_id` FROM `albums` ORDER BY RAND() LIMIT ' . ( string )$num );
            $ret = array();
            while ( $row = mysql_fetch_array( $res ) ) {
                $ret[] = (int)$row[ 0 ];
            }
            return $ret;
        }
        public function ExampleData() {
            $users = $this->GetTestUsers();
            $userid = (int)( $users[ 0 ][ 'id' ] );
            $userid1 = (int)( $users[ 1 ][ 'id' ] );
            $userid2 = (int)( $users[ 2 ][ 'id' ] );
/*
			$egoalbum1 = User::GetEgoAlbumId( $userid1 );
			return array( 
				array( $userid1, 'barcelona', 'I love this place', $egoalbum1  )
			);
*/
            return array(
                array( $userid, 'kamibu summer meeting', 'photos from our meeting at ioannina' ),
                array( $userid, 'barcelona', 'I love this place' ),
                array( $userid1, 'rome', '' ),
                array( $userid1, 'test', 'haha' ),
                array( $userid2, 'foobar', '' ),
                array( $userid2, 'red green', 'blue' ),
                array( $userid1, 'hello', 'world' )
            );
        }
    }

    return New TestAlbum();

?>
