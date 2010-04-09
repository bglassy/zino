<?php
    class User {
        public static function Login( $username, $password ) {
            $res = db(
                'SELECT
                    `user_id` AS id, `user_name` AS name,
                    `user_authtoken` AS authtoken, `user_gender` AS gender
                FROM
                    `users`
                WHERE
                    `user_name` = :username
                    AND `user_password` = MD5( :password ) LIMIT 1',
                compact( 'username', 'password' )
            );
            if ( mysql_num_rows( $res ) ) {
                $row = mysql_fetch_array( $res );
                $row[ 'user_id' ] = ( int )$row[ 'user_id' ];
                return $row;
            }
            return false;
        }
        public static function Item( $id ) {
            $res = db(
                'SELECT
                    `user_id` AS id,
                    `user_deleted` as userdeleted, `user_name` AS username, `user_gender` AS gender, `user_subdomain` AS subdomain, `user_avatarid` AS avatarid,
                    `place_name` AS location,
                FROM
                    `users`
                WHERE
                    `user_id` = :id
                LIMIT 1;', array( 'id' => $id )
            );
			return mysql_fetch_array( $res );
        }
        public static function ItemDetails( $useird ) {
            $res = db(
                'SELECT
                    `user_id` AS id,
                    `user_deleted` as userdeleted, `user_name` AS username, `user_gender` AS gender, `user_subdomain` AS subdomain, `user_avatarid` AS avatarid,
                    `place_name` AS location,
                    `profile_numcomments` AS numcomments
                    `profile_height`,
                    `profile_weight`,
                    `profile_smoker`,
                    `profile_drinker`,
                    `profile_skype`,
                    `profile_msn`,
                    `profile_gtalk`,
                    `profile_yim`,
                    `profile_eyecolor`,
                    `profile_haircolor`,
                    `profile_sexualorientation`,
                    `profile_relationship`,
                    `profile_religion`,
                    `profile_politics`,
                    `profile_slogan`,
                    `profile_aboutme`,
                    `profile_dob`,
                    `mood_labelmale`, `mood_labelfemale`,
                    `mood_url`
                FROM
                    `users`
                    CROSS JOIN `places`
                        ON `user_placeid`=`place_id`
                    CROSS JOIN `userprofiles`
                        ON `user_id`=`profile_userid`
                    CROSS JOIN `moods`
                        ON `profile_moodid`=`mood_id`
                WHERE
                    `user_id` = :id
                LIMIT 1;', array( 'id' => $id )
            );
			$row = mysql_fetch_array( $res );
            $row[ 'mood' ] = array(
                'labelmale' => $row[ 'mood_labelmale' ],
                'labelfemale' => $row[ 'mood_labelfemale' ],
                'url' => $row[ 'mood_url' ]
            );
            unset( $row[ 'mood_labelmale' ] );
            unset( $row[ 'mood_labelfemale' ] );
            unset( $row[ 'mood_url' ] );
            $row[ 'profile' ] = array(
                'height' => $row[ 'profile_height' ],
                'weight' => $row[ 'profile_weight' ],
                'smoker' => $row[ 'profile_smoker' ],
                'drinker' => $row[ 'profile_drinker' ],
                'skype' => $row[ 'profile_skype' ],
                'msn' => $row[ 'profile_msn' ],
                'gtalk' => $row[ 'profile_gtalk' ],
                'eyecolor' => $row[ 'profile_eyecolor' ],
                'haircolor' => $row[ 'profile_haircolor' ],
                'sexualorientation' => $row[ 'profile_sexualorientation' ],
                'relationship' => $row[ 'profile_relationship' ],
                'religion' => $row[ 'profile_religion' ],
                'politics' => $row[ 'profile_politics' ],
                'slogan' => $row[ 'profile_slogan' ],
                'aboutme' => $row[ 'profile_aboutme' ],
                'dob' => $row[ 'profile_dob' ]
            );
            unset( $row[ 'profile_height' ] );
            unset( $row[ 'profile_weight' ] );
            unset( $row[ 'profile_smoker' ] );
            unset( $row[ 'profile_drinker' ] );
            unset( $row[ 'profile_skype' ] );
            unset( $row[ 'profile_msn' ] );
            unset( $row[ 'profile_gtalk' ] );
            unset( $row[ 'profile_eyecolor' ] );
            unset( $row[ 'profile_haircolor' ] );
            unset( $row[ 'profile_sexualorientation' ] );
            unset( $row[ 'profile_relationship' ] );
            unset( $row[ 'profile_religion' ] );
            unset( $row[ 'profile_politics' ] );
            unset( $row[ 'profile_slogan' ] );
            unset( $row[ 'profile_aboutme' ] );
            unset( $row[ 'profile_dob' ] );
            return $row;
        }
        public static function ListOnline() {
            $res = db(
                'SELECT
                    `user_id` AS id, `user_name` AS name
                FROM
                    `users`
                    CROSS JOIN `lastactive` ON
                        `user_id` = `lastactive_userid`
                WHERE
                    `lastactive_updated` > NOW() - INTERVAL 5 MINUTE
                ORDER BY
                    `lastactive_updated` DESC'
            );
            $ret = array();
            while ( $row = mysql_fetch_array( $res ) ) {
                $ret[ $row[ 'name' ] ] = $row;
            }
            ksort( $ret );
            $ret = array_values( $ret );
            return $ret;
        }
    }
?>
