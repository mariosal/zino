<?php
	function ElementPollList() {
		global $page;
		
		$page->AttachStyleSheet( 'css/poll/list.css' );
		Element( 'user/sections' , 'poll' );
		?><div id="journallist"><?php
		/*
			<ul>
				<li><?php
					Element( 'poll/small' );
				?></li>
				<li><?php
					Element( 'poll/small' );
				?></li>
				<li><?php
					Element( 'poll/small' );
				?></li>
				<li><?php
					Element( 'poll/small' );
				?></li>
			</ul>
		*/
		Element( 'poll/small' );
		Element( 'poll/small' );
		Element( 'poll/small' );
		Element( 'poll/small' );
		?>
		</div><?php
	}
?>