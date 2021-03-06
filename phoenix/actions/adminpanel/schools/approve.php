<?php
    function ActionAdminpanelSchoolsApprove( tInteger $schoolid ) {
        global $libs;
        
        $schoolid = $schoolid->Get();
        
        $libs->Load( 'school/school' );
        
        $school = New School( $schoolid );    
        if ( $school->Exists() ) {
            $school->Approved = 1;
            $school->Save();
        }
           
        return Redirect( '?p=moderateschools' );
    }
?>
