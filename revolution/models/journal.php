<?php

	class Journal {
		public static function ListRecent( $amount ) { //<---TODO
		    $res = db(
                'SELECT
					`user_name` as username, `user_subdomain` as subdomain, `user_avatarid` as avatarid, `user_gender` as gender, `journal_id` as id, `journal_title` as title, `journal_url` as url, `journal_userid` as userid, `journal_created` as created , `journal_numcomments` as numcomments 
				FROM 
					`journals`
				CROSS JOIN `users` ON
					`journal_userid` = `user_id`
				WHERE 
                    `journal_delid` = 0 AND
                    `user_deleted` = 0
				ORDER BY `journal_id` DESC
				LIMIT :amount', array( 'amount' => $amount ) 
            );
            $journals = array();
            while ( $row = mysql_fetch_array( $res ) ) {
                $journals[ $row[ 'id' ] ] = $row;
                $journals[ $row[ 'id' ] ][ 'user' ] = array( 
                    'name' => $row[ 'username' ],
                    'subdomain' => $row[ 'subdomain' ],
                    'gender' => $row[ 'gender' ]
                );
            }
            return $journals;
        }
        public static function ListByUser( $userid ) {
            $res = db(
                'SELECT
					`journal_id` as id, `journal_title` as title, `journal_url` as url, `journal_userid` as userid, `journal_created` as created , `journal_numcomments` as numcomments, `user_name` AS username, `user_subdomain` AS subdomain, `user_gender` AS gender, `user_avatarid` AS avatarid
				FROM 
					`journals` 
                    LEFT JOIN `users` ON `journal_userid` = `user_id`
                WHERE
                    `journal_userid` = :userid
                    AND `journal_delid` = 0
                ORDER BY
                    `journal_id` DESC', compact( 'userid' )
            );
            $journals = array();
            while ( $journal = mysql_fetch_array( $res ) ) {
                $journal[ 'id' ] = (int)$journal[ 'id' ];
                $journal[ 'user' ] = array(
                    'id' => (int)$journal[ 'userid' ],
                    'name' => $journal[ 'username' ],
                    'subdomain' => $journal[ 'subdomain' ],
                    'gender' => $journal[ 'gender' ],
                    'avatarid' => (int)$journal[ 'avatarid' ]
                );
                $journals[] = $journal;
            }

            return $journals;
        }
		public static function Item( $id ) {
			$res = db(
					'SELECT
						`bulk_text` as text, `user_deleted` as userdeleted, `user_name` as username, `user_subdomain` as subdomain, `user_avatarid` as avatarid, `user_gender` as gender, `journal_id` as id, `journal_created` as created, `journal_numcomments` as numcomments, `journal_title` as title, `journal_url` as url, `journal_userid` as userid 
					FROM 
						`journals`
					CROSS JOIN `users` ON
						`journal_userid` = `user_id`
					CROSS JOIN `bulk` ON
						`journal_bulkid` = `bulk_id`
					WHERE `journal_id` = :id
					LIMIT 1', compact( 'id' ) 
			);

            if (  mysql_num_rows( $res ) == 0 ) {
                return false;
            }
			
			$item = array();
			$item = mysql_fetch_array( $res );
            $item[ 'user' ] = array(
                'id' => $item[ 'userid' ],
                'name' => $item[ 'username' ],
                'gender' => $item[ 'gender' ],
                'subdomain' => $item[ 'subdomain' ],
                'avatarid' => $item[ 'avatarid' ],
                'deleted' => ( int )$item[ 'userdeleted' ]
            );
			return $item;
		}
		public static function Items( $ids ) {
            if ( empty( $ids ) ) {
                return array();
            }

			$res = db(
					'SELECT
						`bulk_text` as text, `user_deleted` as userdeleted, `user_name` as username, `user_subdomain` as subdomain, `user_avatarid` as avatarid, `user_gender` as gender, `journal_id` as id, `journal_created` as created, `journal_numcomments` as numcomments, `journal_title` as title, `journal_url` as url, `journal_userid` as userid 
					FROM 
						`journals`
					CROSS JOIN `users` ON
						`journal_userid` = `user_id`
					CROSS JOIN `bulk` ON
						`journal_bulkid` = `bulk_id`
					WHERE `journal_id` IN :ids
					LIMIT 10000', compact( 'ids' ) 
			);

            if (  mysql_num_rows( $res ) == 0 ) {
                return false;
            }
			
			$journals = array();
			while ( $row = mysql_fetch_array( $res ) ) {
				$item = array();
				$item = $row;
		        $item[ 'user' ] = array(
		            'id' => $item[ 'userid' ],
		            'name' => $item[ 'username' ],
		            'gender' => $item[ 'gender' ],
		            'subdomain' => $item[ 'subdomain' ],
		            'avatarid' => $item[ 'avatarid' ],
		            'deleted' => ( int )$item[ 'userdeleted' ]
		        );
				$journals[ $item[ "id" ] ] = $item;
			}
			return $journals;
		}
		public static function ListByIds( $ids ) {
			clude( 'models/db.php' );
			if ( empty( $ids ) ) {
				return array();
			}

			$res = db(
					'SELECT
						`user_deleted` as userdeleted, `user_name` as username, `user_subdomain` as subdomain, `user_avatarid` as avatarid, `user_gender` as gender, `journal_id` as id, `journal_created` as created, `journal_numcomments` as numcomments, `journal_title` as title, `journal_url` as url, `journal_userid` as userid 
					FROM 
						`journals`
					CROSS JOIN `users` ON
						`journal_userid` = `user_id`
					WHERE `journal_id` IN :ids
					LIMIT 1000', compact( 'ids' ) 
			);

            $ret = array();
            while ( $row = mysql_fetch_array( $res ) ) {
                $ret[ $row[ 'id' ] ] = $row;
            }

			return $ret;
		}
        public static function Create( $userid, $title, $text ) {
            clude( 'models/url.php' );
            clude( 'models/bulk.php' );
            clude( 'models/wysiwyg.php' );

            is_int( $userid ) or die;

            $url = URL_FormatUnique( $title, $userid, 'Journal::ItemByUrlAndUserid' );
            $text = nl2br( htmlspecialchars( $text ) );
            $text = WYSIWYG_PostProcess( $text );
            $bulkid = Bulk::Store( $text );

            $res = db( 
                        "INSERT INTO `journals` 
                            ( `journal_id`, `journal_userid`, `journal_title`, `journal_url`, `journal_bulkid`, `journal_created`, `journal_delid`, `journal_numcomments` )
                        VALUES ( 0, :userid, :title, :url, :bulkid, NOW(), 0, 0 )", 
                        compact( 'userid', 'title', 'url', 'bulkid' )
            );

            return array(
                'id' => mysql_insert_id(),
                'url' => $url,
                'bulkid' => $bulkid,
                'userid' => $userid,
                'title' => $title,
                'text' => $text,
                'created' => date( 'Y-m-d H:i:s', time() ),
                'numcomments' => 0,
            );
        }
        public static function Delete( $id ) {
            return db( "DELETE FROM `journals` WHERE `journal_id` = :id LIMIT 1;", array( 'id' => $id ) );
        }
        // for checking if url is already taken. see URL_FormatUnique()
        public static function ItemByUrlAndUserid( $url, $userid ) {
            $res = db( 
                'SELECT 
                    * 
                FROM 
                    `journals` 
                WHERE 
                    `journal_url` = :url AND
                    `journal_userid` = :userid
                LIMIT 1;', compact( 'url', 'userid' )
            );

            return mysql_fetch_array( $res );
        }
        public static function Update( $id, $title, $text = false ) {
            clude( 'models/bulk.php' );

            $details = array(
                'id' => $id,
                'title' => $tite
            );

            $sql = 'UPDATE `journals` SET ';
            if ( $text !== false ) {
                $bulkid = Bulk::Store( $text );
                $details[ 'bulkid' ] = $bulkid;
                $sql .= '`journal_bulkid` = :bulkid, ';
            }
            
            $sql .= '`journal_title` = :title WHERE `journal_id` = :id LIMIT 1;';

            db( $sql, $details );
        }
	}
?>
