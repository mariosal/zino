<?php
    class Application extends Satori {
        protected $mDbTableAlias = 'applications';
        
        protected function LoadDefaults() {
            require_once( 'rabbit/helpers/hashstring.php' );
            
            $this->Created = NowDate();
            $this->Token = GenerateRandomHash();
        }
        
        protected function Relations() {
            $this->User = $this->HasOne( 'User', 'Userid' );
        }
        
        public function GetToken() {
            return $this->Token . '-' . $this->Id;
        }
    }
    class ApplicationFinder extends Finder {
        protected $mModel = 'Application';
        
        public function FindByUser( User $user ) {
            $prototype = New Application();
            $prototype->Userid = $user->Id;
            
            return $this->FindByPrototype( $prototype, 0, 1000 );
        }
    }
?>