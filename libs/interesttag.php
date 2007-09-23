<?php
    
    function InterestTag_List( $user ) {
        global $db;
        global $interesttags;

        if ( $user instanceof User ) {
            $sql = "SELECT
                        *
                    FROM
                        `$interesttags`
                    WHERE
                        `interesttag_userid` = '" . $user->Id() . "'
                    ;";
        }
        else if ( is_string( $user ) ) {
            $tagtext = $user;

            $sql = "SELECT
                        *
                    FROM
                        `$interesttags`
                    WHERE
                        `interesttag_text` = '$tagtext'
                    ;";
        }

        $res = $db->Query( $sql );

        $tags = array();
        $prevs = array();
        $first = false;
        while ( $row = $res->FetchArray() ) {
            $tag = new InterestTag( $row );

            $tags[ $tag->Id ] = $tag;
            $prevs[ $tag->NextId ] = $tag->Id;
            if ( !isset( $prevs[ $tag->Id ] ) ) {
                $first = $tag->Id;
            }
        }

        $ret = array();
        if ( !isset( $tags[ $first ] ) ) {
            return $ret;
        }
        $cur = $tags[ $first ];
        while ( true ) {
            $ret[] = $cur;

            if ( !isset( $tags[ $cur->NextId ] ) ) {
                break;
            }

            $cur = $tags[ $cur->NextId ];
        }
        
        return $ret;
    }

    function InterestTag_Clear( $user ) {
        global $db;
        global $interesttags;

        $sql = "DELETE 
                FROM 
                    `$interesttags` 
                WHERE 
                    `interesttag_userid` = '" . $user->Id() . "'
                ;";

        return $db->Query( $sql );
    }

    class InterestTag extends Satori {
        protected   $mId;
        protected   $mUserId;
        protected   $mText;
        protected   $mNextId;
        private     $mUser;
        private     $mNext;

        public function Exists() {
            return ( $this->mId > 0 && $this->mExists );
        }
        public function GetUser() {
            if ( $this->mUser === false ) {
                $this->mUser = new User( $this->mUserId );
            }
            return $this->mUser;
        }
        public function SetUser( $value ) {
            $this->mUser = $value;
            $this->mUserId = $this->mUser->Id();
        }
        public function GetNext() {
            if ( $this->mNext === false ) {
                $this->mNext = new InterestTag( $this->NextId != "-1" ? $this->NextId : false );
            }
            return $this->mNext;
        }
        public function GetPrevious() {
            $sql = "SELECT
                        *
                    FROM
                        `" . $this->mDbTable . "`
                    WHERE
                        `interesttag_next` = '" . $this->Id . "'
                    LIMIT 1;";

            return new InterestTag( $this->mDb->Query( $sql )->FetchArray() );
        }
        public function MoveAfter( $target ) {
            $prev = $this->GetPrevious();
            if ( $prev->Exists() ) {
                $prev->NextId = $this->NextId;
                $prev->Save();
            }
            
            $this->NextId = $target->NextId;
            $this->Save();

            $target->NextId = $this->Id;
            $target->Save();
        }
        public function MoveBefore( $target ) {
            $prev = $this->GetPrevious();
            if ( $prev->Exists() ) {
                $prev->NextId = $this->NextId;
                w_assert( $prev->NextId == $this->NextId );
                $prev->Save();
            }

            if ( $target->Previous->Exists() ) {
                $target->Previous->NextId = $this->Id;
                $target->Previous->Save();
            }

            $this->NextId = $target->Id;
            $this->Save();

        }
        public function Save() {
            $existed    = $this->Exists();
            $change     = Satori::Save();

            if ( !$existed && $change->Impact() ) {
                $sql = "UPDATE
                            `" . $this->mDbTable . "`
                        SET
                            `interesttag_next` = '" . $this->Id . "'
                        WHERE
                            `interesttag_userid` = '" . $this->UserId . "' AND
                            `interesttag_next`  = '-1' AND
                            `interesttag_id` != '" . $this->Id . "'
                        LIMIT
                            1
                        ;";

                $change = $this->mDb->Query( $sql );
            }

            return $change;
        }
        public function LoadDefaults() {
            $this->NextId = -1;
        }
        public function InterestTag( $construct = false /* text */, $user = false ) {
            global $db;
            global $interesttags;

            $this->mDb      = $db;
            $this->mDbTable = $interesttags;

            $this->SetFields( array(
                'interesttag_id'        => 'Id',
                'interesttag_userid'    => 'UserId',
                'interesttag_text'      => 'Text',
                'interesttag_next'      => 'NextId'
            ) );

            if ( is_string( $construct ) && $user instanceof User ) {
                $sql = "SELECT
                            *
                        FROM
                            `" . $this->mDbTable . "`
                        WHERE
                            `interesttag_text`   = '$construct' AND
                            `interesttag_userid` = '" . $user->Id() . "'
                        LIMIT 1;";

                $construct = $db->Query( $sql )->FetchArray();
            }

            $this->Satori( $construct );

            $this->mNext        = false;
            $this->mPrevious    = false;
            $this->mUser        = $user;
        }
    }

?>
