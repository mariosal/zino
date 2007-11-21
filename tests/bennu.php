<?php

    global $libs;
    $libs->Load( 'bennu' );

    final class TestBennu extends Testcase { 
        public function TestClassesExist() {
            $this->Assert( class_exists( 'Bennu' ), 'Class bennu does not exist' );
            $this->Assert( class_exists( 'BennuRule' ), 'BennuRule class does not exist' );

            $this->Assert( class_exists( 'BennuRuleSex' ), 'Default BennuRuleSex class does not exist' );
            $this->Assert( class_exists( 'BennuRuleAge' ), 'Default BennuRuleAge class does not exist' );
            $this->Assert( class_exists( 'BennuRuleCreation' ), 'Default BennuRuleCreation does not exist' );
            $this->Assert( class_exists( 'BennuRulePhotos' ), 'Default BennuRulePhotos does not exist' );
            $this->Assert( class_exists( 'BennuRuleLocation' ), 'Default BennuRuleLocation does not exist' );
            $this->Assert( class_exists( 'BennuRuleFriends' ), 'Default BennuRuleFriends does not exist' );
            $this->Assert( class_exists( 'BennuRuleLastActive' ), 'Default BennuRuleLastActive class does not exist' );
            $this->Assert( class_exists( 'BennuRuleRandom' ), 'Default BennuRuleRandom class does not exist' );
        }
        public function TestMethodsExist() {
            $bennu = New Bennu();
            $this->Assert( method_exists( $bennu, 'AddRule' ), 'Bennu::AddRule method does not exist' );
            $this->Assert( method_exists( $bennu, 'Exclude' ), 'Bennu::Exclude method does not exist' );
            $this->Assert( method_exists( $bennu, 'Get' ), 'Bennu::Get method does not exist' );

            /* methods for extending bennu rule */

            $rule = new ReflectionClass( 'BennuRule' );

            $this->Assert( $rule->HasMethod( 'NormalDistribution' ), 'BennuRule::NormalDistribution method does not exist' );
            $this->Assert( $rule->HasMethod( 'Random' ), 'BennuRule::Random method does not exist' );
            $this->Assert( $rule->HasMethod( 'Get' ), 'BennuRule::Get method does not exist' );
            $this->Assert( $rule->HasMethod( 'Calculate' ), 'BennuRule::Calculate method does not exist' );
        }
        public function TestNoRuleBennu() {
            $bennu = New Bennu();
            $list = $bennu->Get( 20 );

            $this->Assert( is_array( $list ), 'List returned from Bennu::Get is not an array' );
            $this->AssertFalse( empty( $list ), 'List returned from Bennu without rules is empty' ); // no users? duh.
            
            if ( User_Count() >= 20 ) {
                $this->AssertEquals( count( $list ), 20, 'Bennu without rules did not return the number of users requested' );
            }

            $list = $bennu->Get( User_Count() + 10 );
            $this->AssertEquals( count( $list ), User_Count(), 'Bennu should return all the users when the number of users requested are equal to or more than the number of all users' );
        }
        public function TestExclude() {
            $bennu = New Bennu();

            $bennu->Exclude( new User( 1 ) );
            $bennu->Exclude( new User( 1 ) );
            $bennu->Exclude( new User( 2 ) );
            $bennu->Exclude( new User( 3 ) );
            $bennu->Exclude( new User( 5 ) );
            $bennu->Exclude( new User( 8 ) );
            $bennu->Exclude( new User( 13 ) );
            $bennu->Exclude( new User( 21 ) );
            $bennu->Exclude( new User( 34 ) );

            $excluded = array( 1, 2, 3, 5, 8, 13, 21, 34 );

            $users = $bennu->Get(); // everyone

            foreach ( $users as $user ) {
                $this->Assert( in_array( $user->Id, $excluded ), 'User excluded but he is still in the list returned by Bennu::Get' );
            }
        }
        public function TestOneRule() {
            $bennu = New Bennu();
            
            $age = New BennuRuleAge();
            $age->Value = 16;
            $age->Sigma = 2;
            $age->Score = 10;

            $bennu->AddRule( $age );

            $list = $bennu->Get( 20 );

            $diff = 0;
            foreach ( $list as $user ) {
                $curdiff = abs( $user->Age - 16 );
                $this->Assert( $curdiff >= $diff, 'Bennu did not return the users on the right order when one rule was added' );
                $diff = $curdiff;
            }
        }
        public function TestTwoRules() {
            $bennu = New Bennu();

            $now = time();

            $creation = New BennuRuleCreation();
            $creation->Value = $now;
            $creation->Sigma = $now - 60 * 60 * 24 * 7 * 2;
            $creation->Score = 10000;

            $random = New BennuRuleRandom();
            $random->Score = 1;
            
            $bennu->AddRule( $creation );
            $bennu->AddRule( $random );

            $users = $bennu->Get( 20 );

            $diff = 0;
            foreach ( $user as $user ) {
                $curdiff = abs( strtotime( $user->Creation ) - $now );
                $this->Assert( $curdiff >= $diff, 'Bennu did not return the users on the right order when two rules were added' );
                $diff = $curdiff;
            }
        }
    }

    return new TestBennu();

?>
