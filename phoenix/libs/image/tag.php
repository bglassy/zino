<?php
    global $libs;

    $libs->Load( 'image/image' );

    class ImageTagFinder extends Finder {
        protected $mModel = 'ImageTag';
        
        public function FindByPersonId( $personId, $offset = 0 , $limit = 10000 ) {
            $prototype = New ImageTag();
            $prototype->Personid = $personId;

            return $this->FindByPrototype( $prototype, $offset, $limit, array( 'Id', 'DESC' ) );            
        }
        public function FindByImage( Image $image ) {
            $prototype = New ImageTag();
            $prototype->Imageid = $image->Id;

            return $this->FindByPrototype( $prototype, 0, 40, array( 'Id', 'DESC' ) );
        }
        public function FindAll( $offset = 0, $limit = 25 ) {
            return parent::FindAll( $offset, $limit, array( 'Id', 'DESC' ) );
        }
        public function FindByIds( $ids ) {
            return parent::FindByIds( $ids );
        }
        public function LoadDefaults() {
            global $user;

            $this->Ownerid = $user->Id;
        }
    }

    class ImageTag extends Satori {
        protected $mInsertIgnore = true;
        protected $mDbTableAlias = 'imagetags';
       
        protected function OnCreate() {
            global $libs;
            
            $libs->Load( 'rabbit/event' );
            FireEvent( 'ImageTagCreated', $this );
        }
        protected function Relations() {
            $this->Owner = $this->HasOne( 'User', 'Ownerid' );
            $this->Person = $this->HasOne( 'User', 'Personid' );
        }
        protected function OnDelete() {
            global $libs;
            
            $libs->Load( 'rabbit/event' );
            FireEvent( 'ImageTagDeleted', $this );
        }
    }
?>
