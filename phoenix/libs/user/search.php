<?php
   class UserSearch extends UserFinder {
       public function FindByDetails(
            $minage, $maxage, Place $location, $gender, $sexual, $name,
            $offset = 0, $limit = 25
       ) {
           $minage = ( int )$minage;
           $maxage = ( int )$maxage;
           $clauses = array();
           if ( $minage > 0 ) {
               $clauses[] = 'age >= :minage';
           }
           if ( $maxage > 0 ) {
               $clauses[] = 'age <= :maxage';
           }
           if ( $gender !== false ) {
               w_assert( $gender == 'm' || $gender == 'f' );
               $clauses[] = '`user_gender` = :gender';
           }
           if ( $location->Exists() ) {
               $clauses[] = '`profile_placeid` = :placeid';
           }
           if ( !empty( $name ) ) {
               $clauses[] = '`user_name` LIKE "%'  . $name . '%"';
           }
           $where = implode( ' AND ', $clauses );
           $query = $this->mDb->Prepare(
                "SELECT
                    *, FLOOR( DATEDIFF( NOW(), `profile_dob` ) / 365.33 )  AS age
                FROM
                    :users CROSS JOIN :userprofiles
                        ON `user_id`=`profile_userid`
                WHERE
                    $where
                LIMIT
                    :offset, :limit;"
           );
           $query->BindTable( 'users' );

           $query->Bind( 'offset', $offset );
           $query->Bind( 'limit', $limit );

           $query->Bind( 'minage', $minage );
           $query->Bind( 'maxage', $maxage );
           $query->Bind( 'gender', $gender );
           $query->Bind( 'placeid', $location->Id );
           $query->BindLike( 'name', $name );

           $res = $query->Execute(); 

           return $this->FindBySQLResource( $res );
       }
   }
?>
