<?php
        class BadgeFinder extends Finder{
		protected $mModel = 'Badge';
		public function FindByIds( $ids ){
			return $this->FindByIds( $ids );
		}
	}

        class Badge extends Satori{
		protected $mDbTableAlias = 'badges';
	}
?>
