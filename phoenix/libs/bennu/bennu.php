<?php
    class Bennu {
        protected $mInput;
        protected $mTarget;
       
        public function SetData( $users, $target ) {
            foreach ( $users as $_user ) {
                $this->mInput[] = new User( $_user->Id );
            }
            $this->mTarget = new User( $target->Id );
            return;
        }
        
        public function GetResult() {
            $res = array();
            foreach ( $this->mInput as $sample ) {
                $res[ $sample->Name ] = $this->Calculate( $sample );
            }
            
            w_assert( !empty( $res ), "No results" );
            
            rsort( $res );
            
            return $res;
        }
        
        protected function Calculate( $sample ) {
            $total_score = 0;
            $score;
            $value = 10;
            
            //date
            
            /*
            //age sigma = 2
            $score = ( 2 * ( $sample->Profile->Age - $this->mTarget->Profile->Age ) ) / $value;
            if ( $score > 0 ) {
                $total_score += $value - $score;
            }
            
            //location
            if ( $sample->Profile->Placeid === $this->mTarget->Profile->Placeid ) {
                $total_score += $value;
            }
            
            //friends
            
            //sex
            if ( $sample->Profile->Gender === $this->mTarget->Profile->Gender ) {
                $total_score += $value;
            }
            
            //activity sigma = 7*24*60*60
            $sigma = 7*24*60*60;
            $score = ( $sigma * ( strtotime( $sample->Lastlogin ) - strtotime( $this->mTarget->Lastlogin ) ) ) / $value;
            if ( $score > 0 ) {
                $total_score += $value - $score;
            }
            
            w_assert( is_numeric( $total_score ), "total score isnt numeric value" );*/
            return $total_score;
        }
    }
?>
