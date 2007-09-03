<?php
	function ElementUserProfileFrelations( $relations, $is_friend, $friendid ) {
		global $user;
		global $libs;
		global $page;
		
		$page->AttachStylesheet( 'css/frelations.css' );
    	$page->AttachScript( 'js/animations.js' );
    	$page->AttachScript( 'js/_friends.js' );
    	
		if ( $is_friend ) {
			$relid = $user->GetRelId( $friendid );
		}
		else {
			$relid = -1;
		}
    	
    	?><div id="friend_relations" class="friend_relations">
                <map id="close" name="close">
				<area shape="rect" coords="94,20,105,30" onclick="Friends.ShowAll( false );return false;" alt="Κλείσιμο" title="Κλείσιμο" href=""/>
				</map>
				
				<img src="https://beta.chit-chat.gr/etc/mockups/frelations/frelations_htmled/top_close.png" usemap="#close" style="border: none;padding: 0px;margin: 0px;" />
                <div class="frelations"><?php
                foreach( $relations as $relation ) {
                	?><div id="frel_<?php
                	echo $relation->Id;
                	?>" class="<?php
                	if( $relid == $relation->Id ) {
                		?>relselected<?php
                	}
                	else {
                		?>frelation<?php
                	}
                	?>" onmouseover="g( 'frel_<?php
                	echo $relation->Id;
                	?>' ).style.color='#5c60bb';" onmouseout="g( 'frel_<?php
                	echo $relation->Id;
                	?>' ).style.color='#757bee';"
                	onclick="Friends.AddFriend( <?php
                	echo $friendid;
                	?>, <?php
                	echo $relation->Id;
                	?> );return false;" ><?php
                	echo $relation->Type;
                	?></div><?php
                }
                ?><div id="frel_-1" class="<?php
                if( $relid == -1 ) {
                	?>relselected<?php
                }
                else {
                	?>frelation<?php
                }
                ?>" onmouseover="g( 'frel_-1' ).style.color='#5c60bb';" onmouseout="g( 'frel_-1' ).style.color='#757bee';" onclick="Friends.AddFriend( <?php
                echo $friendid;
                ?>, -1 );return false;" >Καμία</div>
                </div>
                <img src="https://beta.chit-chat.gr/etc/mockups/frelations/frelations_htmled/bottom.png" style="margin-left:6px;" />
         </div><?php
     }
?>
