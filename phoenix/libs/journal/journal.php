<?php
	//DEving for spot  -pagio

    global $libs;

    $libs->Load( 'bulk' );
    $libs->Load( 'url' );
    $libs->Load( 'user/user' );
	$libs->Load( 'journal/frontpage' );

    class JournalFinder extends Finder {
        protected $mModel = 'Journal';
        
        public function FindById( $id ) {
            $prototype = New Journal();
            $prototype->Id = $id;
            $prototype->Delid = 0;

            return $this->FindByPrototype( $prototype );
        }
        public function FindByUser( User $user, $offset = 0, $limit = 25, $deleted = false ) {
            $prototype = New Journal();
            $prototype->Userid = $user->Id;
            if ( !$deleted ) {
                $prototype->Delid = 0;
            }

            return $this->FindByPrototype( $prototype, $offset, $limit, array( 'Id', 'DESC' ) );
        }
		public function FindByUserId( $userid, $offset = 0, $limit = 25 ) {
            $prototype = New Journal();
            $prototype->Userid = $userid;
            $prototype->Delid = 0;

            return $this->FindByPrototype( $prototype, $offset, $limit, array( 'Id', 'DESC' ) );
        }
        public function FindByUserAndUrl( $user, $url, $offset = 0, $limit = 25 ) {
            $prototype = New Journal();
            $prototype->Userid = $user->Id;
            $prototype->Url = $url;
            $prototype->Delid = 0;

            return $this->FindByPrototype( $prototype );
        }
        public function Count() {
            return parent::Count();
        }
        public function FindAll( $offset = 0, $limit = 20 ) {
            $journal = New Journal();
            $journal->Delid = 0;

            $journals = $this->FindByPrototype( $journal, $offset, $limit, array( 'Id', 'DESC' ), true );

            $userids = array();
            $bulkids = array();

            foreach ( $journals as $journal ) {
                $userids[] = $journal->Userid;
                $bulkids[] = $journal->Bulkid;
            }

            $userfinder = New UserFinder();
            $userids = array_flip( array_flip( $userids ) );
            $bulkids = array_flip( array_flip( $bulkids ) );
            $users = $userfinder->FindByIds( $userids );
            $userbyid = array();
            foreach ( $users as $user ) {
                $userbyid[ $user->Id ] = $user;
            }
            $bulks = Bulk::FindById( $bulkids );

            foreach ( $journals as $i => $journal ) {
                if ( isset( $userbyid[ $journal->Userid ] ) ) {
                    $journals[ $i ]->CopyUserFrom( $userbyid[ $journal->Userid ] );
                }
                $journals[ $i ]->Text = $bulks[ $journal->Bulkid ];
            }

            return $journals;
        }
		public function FindFrontpageLatest( $offset = 0, $limit = 4 ) {
            $finder = New FrontpageJournalFinder();
            $found = $finder->FindLatest( $offset, $limit );
            $journalids = array();
            foreach ( $found as $frontpagejournal ) {
                $journalids[] = $frontpagejournal->Journalid;
            }
            if ( !count( $journalids ) ) {
                return array();
            }
            $query = $this->mDb->Prepare(
                'SELECT
                    *
                FROM
                    :journals 
                    LEFT JOIN :users ON
                        `journal_userid` = `user_id`
                WHERE
                    `journal_id` IN :journalids'
            );
            $query->BindTable( 'journals', 'users' );
            $query->Bind( 'journalids', $journalids );
            
            $res = $query->Execute();
            $journals = array();
            while ( $row = $res->FetchArray() ) {
                $journal = New Journal( $row );
                $journal->CopyUserFrom( New User( $row ) );
                $journals[] = $journal;
            }

            $ret = array();
            foreach ( $journals as $journal ) {
                $ret[ $journal->Id ] = $journal;
            }
            krsort( $ret );

            return $ret;
        }
        public function FindByIds( $ids ) {
            return parent::FindByIds( $ids );
        }
        public function FindUserRelated( $user, $extended ) {
            global $libs;
            $libs->Load( 'research/spot' );

			if ( $extended ) {
				$info = Spot::GetJournalsExtended( $user );
				$ids = array();
		        if ( $info === false ) {
		            return $info;
		        }
				foreach( $info as $key => $val ) {
					$ids[] = $val[ "journalid" ];
				}
		        $journals = $this->FindByIds( $ids );
		        $journals->PreloadRelation( 'User' );
				$info[ "journals" ] = $journals;
		        return $info;
			}

            $ids = Spot::GetJournals( $user );
            if ( $ids === false ) {
                return $ids;
            }
            $journals = $this->FindByIds( $ids );
            $journals->PreloadRelation( 'User' );
            return $journals;
        }
    }
    
    class Journal extends Satori {
        protected $mDbTableAlias = 'journals';
        private $mText = false;

        public function LoadDefaults() {
            global $user;

            $this->Userid = $user->Id;
            $this->Created = NowDate();
        }
        public function __get( $key ) {
            switch ( $key ) {
                case 'Text':
                    if ( $this->mText === false ) {
                        $this->mText = Bulk::FindById( $this->Bulkid );
                    }
                    return $this->mText;
                default:
                    return parent::__get( $key );
            }
        }
        public function __set( $key, $value ) {
            switch ( $key ) {
                case 'Text':
                    $this->mText = $value;
                    break;
                default:
                    return parent::__set( $key, $value );
            }
        }
        public function GetText( $length ) {
            global $libs;
            
            $libs->Load( 'wysiwyg' );
            
            return WYSIWYG_PresentAndSubstr( $this->Text, $length );
        }
        public function OnBeforeCreate() {
            global $libs;
            
            $libs->Load( 'url' );
            
            $url = URL_Format( $this->Title );
            $length = strlen( $url );
            $finder = New JournalFinder();
            $exists = true;
            while ( $exists ) {
                $offset = 0;
                $exists = false;
                do {
                    $someOfTheRest = $finder->FindByUser( $this->User, $offset, 100, true );
                    foreach ( $someOfTheRest as $j ) {
                        if ( strtolower( $j->Url ) == strtolower( $url ) ) {
                            $exists = true;
                            if ( $length < 255 ) {
                                $url .= '_';
                                ++$length;
                            }
                            else {
                                $url[ rand( 0, $length - 1 ) ] = '_';
                            }
                            break;
                        }
                    }
                    $offset += 100;
                } while ( count( $someOfTheRest ) && !$exists );
            }
            $this->Url = $url;

            $this->Bulkid = Bulk::Store( $this->Text );
        }
        public function OnUpdate() {            
            Bulk::Store( $this->Text, $this->Bulkid );
        }
        protected function OnCreate() {
            global $libs;
            
            $this->OnUpdate();
			
			$this->MakeFrontpage();

            Sequence_Increment( SEQUENCE_JOURNAL );
        }
		protected function MakeFrontpage() {
			$frontpage = New FrontpageJournal( $this->Userid );
			
            if ( !$frontpage->Exists() ) {
                $frontpage = New FrontpageJournal();
                $frontpage->Userid = $this->Userid;
            }
            $frontpage->Journalid = $this->Id;
            $frontpage->Save();
		}		
        protected function OnBeforeDelete() {
            $this->Delid = 1;
            $this->Save();

            $this->OnDelete();

            return false;
        }
        protected function OnDelete() {
            global $user;
            global $libs;

            $libs->Load( 'comment' );
            $libs->Load( 'adminpanel/adminaction' );
            
            if ( $user->id != $this->userid ) {
                $adminaction = New AdminAction();
                $adminaction->saveAdminAction( $user->id, UserIp(), OPERATION_DELETE, TYPE_JOURNAL, $this->id );
            }

			$frontpage = New FrontpageJournal( $this->Userid );
            if ( $frontpage->Exists() ) {
                if ( $frontpage->Journalid == $this->Id ) {
                    $finder = New JournalFinder();
                    $oldjournal = $finder->FindByUserId( $this->Userid, 0, 1 );
                    if ( count( $oldjournal ) === 0 ) {
                        $frontpage->Delete();
                    }
                    else {
                        $oldjournal = $oldjournal[ 0 ];
                        $frontpage->Journalid = $oldjournal->Id;
                        $frontpage->Save();
                    }
                }
            }

            Sequence_Increment( SEQUENCE_JOURNAL );
        }
        public function CopyUserFrom( $value ) {
            $this->mRelations[ 'User' ]->CopyFrom( $value );
        }
        protected function Relations() {
            $this->User = $this->HasOne( 'User', 'Userid' );
        }
        public function IsDeleted() {
            return $this->Exists() === false;
        }
    }

?>
