<?php
	/* Avoid Satori/Finder base for speed */

	class ChannelFinder {
        public static function Auth( $channelid, $userid ) {
            global $db;

            if ( $channelid == 0 ) {
                return true;
            }

            $query = $db->Prepare(
                "SELECT
                    `participant_channelid`
                FROM
                    :chatparticipants
                WHERE
                    `participant_channelid` = :channelid
                    AND `participant_userid` = :userid
                LIMIT 1" 
            );
            $query->BindTable( 'chatparticipants' );
            $query->Bind( 'channelid', $channelid );
            $query->Bind( 'userid', $userid );
            $res = $query->Execute();

            return $res->Results() > 0;
        }
		public static function FindByUserid( $userid ) {
			global $db;
			
			w_assert( is_int( $userid ) );
			
			$query = $db->Prepare(
				'SELECT
					`channel_id`, `user_name`, `user_id`, `user_avatarid`
				FROM
					:chatchannels
						CROSS JOIN :chatparticipants AS me
							ON `channel_id`=me.`participant_channelid`
						CROSS JOIN :chatparticipants AS other
							ON `channel_id`=other.`participant_channelid`
						CROSS JOIN :users
							ON other.`participant_userid`=`user_id`
				WHERE
					me.`participant_userid` = :userid
					AND other.`participant_userid` != :userid'
			);
			$query->BindTable( 'chatchannels', 'chatparticipants', 'users' );
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
				$channels[ $row[ 'channel_id' ] ][ 'participants' ][] = array(
					'id' => $row[ 'user_id' ],
					'name' => $row[ 'user_name' ],
					'avatar' => $row[ 'user_avatarid' ]
				);
			}
			
			return $channels;
		}
		public static function FindParticipantsByChannel( $channelid ) {
			global $db;
			
			w_assert( is_int( $channelid ) );
			
			$query = $db->Prepare(
				'SELECT
					`user_id`, `user_authtoken`
				FROM
					:chatparticipants CROSS JOIN :users
						ON `participant_userid` = `user_id`
				WHERE
					`participant_channelid` = :channelid'
			);
			$query->BindTable( 'chatparticipants', 'users' );
			$query->Bind( 'channelid', $channelid );
			$res = $query->Execute();
			
			$userinfo = array();
			while ( $row = $res->FetchArray() ) {
				$userinfo[] = $row[ 'user_id' ] . 'x' . substr( $row[ 'user_authtoken' ], 0, 10 );
			}
			
			return $userinfo;
		}
	}
?>
