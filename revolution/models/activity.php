<?php

    define( 'ACTIVITY_COMMENT', 1 );
    define( 'ACTIVITY_FAVOURITE', 2 );
    define( 'ACTIVITY_FRIEND', 3 );
    define( 'ACTIVITY_FAN', 4 );
    define( 'ACTIVITY_SONG', 5 );
    define( 'ACTIVITY_STATUS', 6 );
    define( 'ACTIVITY_ITEM', 7 );

    class Activity {
        public static function ListByUser( $userid, $limit = 100 ) {
            $res = db( "SELECT * FROM `activities` WHERE `activity_userid` = :userid ORDER BY `activity_id` DESC LIMIT :limit;", compact( 'userid', 'limit' ) );
            $activities = array();
            while ( $row = mysql_fetch_array( $res ) ) {
                $activity = array();
                $activity[ 'typeid' ] = $row[ 'activity_typeid' ];
                $activity[ 'user' ] = array();
                $activity[ 'user' ][ 'id' ] = $row[ 'activity_userid' ];
                switch ( $row[ 'activity_typeid' ] ) {
                    case ACTIVITY_COMMENT:
                        $activity[ 'comment' ] = array(); 
                        $activity[ 'comment' ][ 'id' ] = $row[ 'activity_refid' ];
                        $activity[ 'comment' ][ 'bulkid' ] = $row[ 'activity_bulkid' ];
                        $activity[ 'item' ] = array();
                        $activity[ 'item' ][ 'id' ] = $row[ 'activity_itemid' ];
                        $activity[ 'item' ][ 'typeid' ] = $row[ 'activity_typeid' ];
                        $activity[ 'item' ][ 'title' ] = $row[ 'activity_text' ];
                        $activity[ 'item' ][ 'url' ] = $row[ 'activity_url' ];
                        break;
                    case ACTIVITY_FAVOURITE:
                        $activity[ 'favourite' ] = array();
                        $activity[ 'favourite' ][ 'id' ] = $row[ 'activity_refid' ];
                        $activity[ 'item' ] = array();
                        $activity[ 'item' ][ 'id' ] = $row[ 'activity_itemid' ];
                        $activity[ 'item' ][ 'typeid' ] = $row[ 'activity_itemtype' ];
                        $activity[ 'item' ][ 'bulkid' ] = $row[ 'activity_bulkid' ];
                        $activity[ 'item' ][ 'title' ] = $row[ 'activity_text' ];
                        $activity[ 'item' ][ 'url' ] = $row[ 'activity_url' ];
                        break;
                    case ACTIVITY_FRIEND:
                        $activity[ 'friend' ] = array();
                        $activity[ 'friend' ][ 'id' ] = $row[ 'activity_itemid' ];
                        $activity[ 'friend' ][ 'name' ] = $row[ 'activity_text' ];
                        $activity[ 'friend' ][ 'subdomain' ] = $row[ 'activity_url' ];
                        $activity[ 'relation' ] = array();
                        $activity[ 'relation' ][ 'id' ] = $row[ 'activity_refid' ];
                        break;
                    case ACTIVITY_FAN:
                        $activity[ 'fan' ] = array();
                        $activity[ 'fan' ][ 'id' ] = $row[ 'activity_itemid' ];
                        $activity[ 'fan' ][ 'name' ] = $row[ 'activity_text' ];
                        $activity[ 'fan' ][ 'subdomain' ] = $row[ 'activity_url' ];
                        $activity[ 'relation' ] = array();
                        $activity[ 'relation' ][ 'id' ] = $row[ 'activity_refid' ];
                        break;
                    case ACTIVITY_SONG:
                        $activity[ 'song' ] = array();
                        $activity[ 'song' ][ 'id' ] = $row[ 'activity_refid' ];
                        $activity[ 'song' ][ 'title' ] = $row[ 'activity_text' ];
                        break;
                    case ACTIVITY_STATUS:
                        $activity[ 'status' ] = array();
                        $activity[ 'status' ][ 'id' ] = $row[ 'activity_refid' ];
                        $activity[ 'status' ][ 'message' ] = $row[ 'activity_text' ];
                        break;
                    case ACTIVITY_ITEM:
                        $activity[ 'item' ] = array();
                        $activity[ 'item' ][ 'id' ] = $row[ 'activity_refid' ];
                        $activity[ 'item' ][ 'typeid' ] = $row[ 'activity_itemtype' ];
                        $activity[ 'item' ][ 'bulkid' ] = $row[ 'activity_bulkid' ];
                        $activity[ 'item' ][ 'title' ] = $row[ 'activity_text' ];
                        $activity[ 'item' ][ 'url' ] = $row[ 'activity_url' ];
                        break;
                    default:
                        die( 'unknown activity type' );
                }
                return $activities[] = $activity;
            }
            return $activities;
        }
    }

?>