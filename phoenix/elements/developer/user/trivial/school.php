<?php

    class ElementDeveloperUserTrivialSchool extends Element {
        public function Render( $school ) {
            if ( $school->Exists() ) {
                echo htmlspecialchars( $school->Name );
            }
        }
    }

?>
