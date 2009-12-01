<?php
	/* Avoid Satori/Finder base for speed */
	
	class ChannelFinder {
		public static function FindByUserid( $userid ) {
			global $db;
			
			w_assert( is_int( $userid ) );
			
			$query = $db->Prepare(
				'SELECT
					`channel_id`, `channel_authtoken`, `user_name`
				FROM
					:channels
						CROSS JOIN :participants AS me
							ON `channel_id`=me.`participant_channelid`
						CROSS JOIN :participants AS other
							ON `channel_id`=other.`participant_channelid`
						CROSS JOIN :users
							ON other.`participant_userid`=`user_id`
				WHERE
					me.`participant_userid` = :userid
					AND other.`participant_userid` != :userid'
			);
			$query->BindTable( 'channels', 'channelparticipants', 'users' );
			$query->Bind( 'userid', $userid );
			$res = $query->Execute();
			
			$channels = array();
			
			while ( $row = $res->FetchArray() ) {
				if ( !isset( $channels[ $row[ 'channel_id' ] ] ) ) {
					$channels[ $row[ 'channel_id' ] ] = array(
						'authtoken' => $row[ 'channel_authtoken' ],
						'participants' => array()
					);
				}
				$channels[ $row[ 'channel_id' ] ][ 'participants' ][] = $row[ 'user_name' ];
			}
			
			$channels = self::FindByIds( $channelids );
			
			return $channels;
		}
	}
?>
