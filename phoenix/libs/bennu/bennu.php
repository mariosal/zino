<?php
    class Rule {
        protected $mAttribute;
        protected $mSigma;
        protected $mValue;
        protected $mSignificance;//low medium high 
        
        public function SetRule( $attribute, $value, $significanse, $sigma ) {
            $this->mAttribute;
            $this->mSigma;
            $this->mValue;
            $this->mSignificance;        
        }
    }

    class Bennu {
        protected $mInput;
        protected $mTarget;
        protected $mRules;
       
        public function SetData( $users, $target ) {
            $this->mInput = $users;
            $this->mTarget = $target;
            return;
        }
        
        public function AddRule( $attribute, $value, $significanse = 'medium', $sigma = 0 ) {
            $rule = new Rule;         
            $attributes = explode( '->', $attribute );
            $rule->SetRule( $attributes[ 1 ], $value, $significanse, $sigma );
            
            $this->mRules[] = $rule;
            
            return $attributes[ 1 ];
        }
        
        public function GetResult() {
            $res = array();
            foreach ( $this->mInput as $sample ) {
                
                if ( $sampe->Id == $this->mTarget->Id ) {//exclude the target from  evaluating
                    continue;
                }
            
                $res[ $sample->Name ] = $this->Calculate( $sample );
            }
            
            w_assert( !empty( $res ), "No results" );
            
            return $res;
        }
        
        protected function Calculate( $sample ) {
            $total_score = 0;
            $score;
            $value = 10;
            
            //date
            
            
            //age sigma = 2
            $score = abs( ( 2 * ( $sample->Profile->Age - $this->mTarget->Profile->Age ) ) / $value );
            if ( $score < $value ) {
                $total_score += $value - $score;
            }
            
            //location
            if ( $sample->Profile->Placeid === $this->mTarget->Profile->Placeid ) {
                $total_score += $value;
            }
            
            //friends
            
            //sex
            if ( $sample->Gender === $this->mTarget->Gender ) {
                $total_score += $value;
            }
            
            //activity sigma = 7*24*60*60
            $sigma = 7*24*60*60;
            $score = abs( ( $sigma * ( strtotime( $sample->Lastlogin ) - strtotime( $this->mTarget->Lastlogin ) ) ) / $value );
            if ( $score < $value ) {
                $total_score += $value - $score;
            }
            
            return $total_score;
        }
    }
?>
