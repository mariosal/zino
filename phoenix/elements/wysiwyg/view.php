<?php
    class ElementWYSIWYGView extends Element {
        public function Render( $id = 'wysiwyg', $contents = '' ) {
            global $user;

            ?><div id="<?php
            echo $id;
            ?>" class="wysiwyg"><?php
            echo $contents;
            ?></div><?php
        }
    }
?>
