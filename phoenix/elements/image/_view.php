<?php
    class ElementImageView extends Element {
		protected $mPersistent = array( 'imageid' , 'type' , 'class' , 'alttitle' , 'style' , 'cssresizable' , 'csswidth' , 'cssheight' , 'numcom' );
		public function Render( $imageid, $imageuserid , $imagewidth , $imageheight , $type = IMAGE_PROPORTIONAL_210x210, $class = '', $alttitle = '' , $style = '' , $cssresizable = false , $csswidth = 0 , $cssheight = 0 , $numcom = 0 ) {
            //imageid  , imageuserid, imagewidth, imageheight
			global $xc_settings;
            global $rabbit_settings;

			switch ( $type ) {
				case IMAGE_PROPORTIONAL_210x210:
					list( $width , $height ) = ProportionalSize( 210 , 210 , $imagewidth , $imageheight );
					break;
				case IMAGE_CROPPED_100x100:
					$width = $height = 100;
					break;
				case IMAGE_CROPPED_150x150:
					$width = $height = 150;
					break;
				case IMAGE_FULLVIEW:
					$width = $imagewidth;
					$height = $imageheight;
					break;
				default:
					throw New Exception( 'Invalid image type' );
			}
            ?><span class="imageview"><img src="<?php
            Element( 'image/url', $imageid , $imageuserid , $type );
            ?>"<?php
            if ( $class != "" ) {
                ?> class="<?php
                echo htmlspecialchars( $class );
                ?>"<?php
            }
            ?> style="<?php
            if ( $cssresizable ) {
                ?>width:<?php
                echo $csswidth;
                ?>px;height:<?php
                echo $cssheight;
                ?>px;<?php
            }
            else {
                ?>width:<?php
                echo $width;
                ?>px;height:<?php
                echo $height;
                ?>px;<?php
            }
            if ( $style != "" ) {
                echo htmlspecialchars( $style );
            }
            ?>" title="<?php
			echo htmlspecialchars( $alttitle );
			?>" alt="<?php
			echo htmlspecialchars( $alttitle );
			?>" /><?php
            if ( $numcom != 0 ) {
                ?><span class="info"><span class="commentsnumber">&nbsp;</span><?php
                echo $numcom;
                ?></span></span><?php
            }
        }
    }
?>
