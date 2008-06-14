<?php
   
    define( 'USERPM_UNREAD', 0 );
    define( 'USERPM_READ', 1 );
    define( 'USERPM_DELETED', 2 );
    
    class PMFinder extends Finder { /* aka UserPMFinder */
        protected $mModel = 'UserPM';

        public function FindByPM( PM $pm, $offset = 0, $limit = 1000 ) {
            $prototype = New UserPM();
            $prototype->Pmid = $pm->Id;

            return $this->FindByPrototype( $prototype, $offset, $limit );
        }
        public function FindByFolder( PMFolder $folder, $offset = 0, $limit = 1000 ) {
            $query = $this->mDb->Prepare( '
                SELECT
                    *
                FROM
                    :pmmessageinfolder
                WHERE
                    `pmif_folderid` = :folderid AND
                    `pmif_delid` < :deleteid
                LIMIT
                    :offset, :limit
                ;' );

            $query->BindTable( 'pmmessageinfolder' );
            $query->Bind( 'folderid', $folder->Id );
            $query->Bind( 'deleteid', USERPM_DELETED );
            $query->Bind( 'offset', $offset );
            $query->Bind( 'limit', $limit );

            return $this->FindBySqlResource( $query->Execute() );
        }
    }

    class UserPM extends Satori {
        protected $mDbTableAlias = 'pmmessageinfolder';

        public function IsRead() {
            return $this->Delid == USERPM_READ;
        }
        public function IsDeleted() {
            return $this->Delid == USERPM_DELETED;
        }
        public function Read() {
            $this->Delid = USERPM_READ;
            $this->Save();

            --$this->User->Count->Unreadpms;
            $this->User->Count->Save();
        }
        protected function BeforeDelete() {
            $this->Delid = USERPM_DELETED;
            $this->Save();

            return false;
        }
        public function GetSender() {
            return $this->PM->Sender;
        }
        public function GetText() {
            return $this->PM->Text;
        }
        public function GetReceivers() {
            return $this->PM->Receivers;
        }
        public function GetUser() {
            return $this->Folder->User;
        }
        public function GetDate() {
            return $this->PM->Date;
        }
        protected function Relations() {
            $this->PM = $this->HasOne( 'PM', 'Pmid' );
            $this->Folder = $this->HasOne( 'PMFolder', 'Folderid' );
        }
    }

?>
