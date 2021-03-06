<?php
    class ElementDeveloperUserSettingsPersonalPlace extends Element {
        public function Render( $placeid ) {
            global $user;
            
            $finder = New PlaceFinder();
            $places = $finder->FindAll();
            ?><select>
                <option value="-1"<?php
                if ( $placeid == 0 ) {
                    ?> selected="selected"<?php
                }
                ?>>-</option><?php
                foreach( $places as $place ) {
                    ?><option value="<?php
                    echo $place->Id;
                    ?>"<?php
                    if ( $placeid == $place->Id ) {
                        ?> selected="selected"<?php
                    }
                    ?>><?php
                    Element( 'developer/user/trivial/place', $place, $place->Id );
                    ?></option><?php
                }
            ?></select><?php
        }
    }
?>
