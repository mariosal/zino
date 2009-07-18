<?php
	
	class StoretypeFinder extends Finder{
		public function FindByName( $name ){
			$prototype = New Storetype();
			$prototype->Name = $name;
			return $this->FindByPrototype( $prototype );
		}
		public function FindAll( $offset = 0, $limit = 25 ){
			return parent::FindAll( $offset, $limit, array( 'Id', 'DESC' ) );
		}
	}
	
	class StoreitemFinder extends Finder{
		public function FindAll( $offset = 0, $limit = 25 ){
			return parent::FindAll( $offset, $limit, array( 'Id', 'DESC' ) );
		}
		public function FindByName( $name ){
			$prototype = New Storeitem();
			$prototype->Name = $name;
			return $this->FindByPrototype( $prototype );
		}
	}
	
	class StorepropertyFinder extends Finder{
		public function FindAll( $offset = 0, $limit = 25 ){
			return parent::FindAll( $offset, $limit, array( 'Id', 'DESC' ) );
		}
	}
	
	class StorepurchaseFinder extends Finder{
		public function FindByItemid( $id, $offset = 0, $limit = 50 ){
			$prototype = New Storepurchase();
			$prototype->Itemid = $id;
			return $this->FindByPrototype(  $prototype );
		}
		public function CountByItemid( $id ){
			$prototype = New Storepurchase();
			$prototype->Itemid = $id;
			return count( $this->FindByPrototype( $prototype ) );
		}
	}
	
	class StorepurchasepropertyFinder extends Finder{
		public function FindAll( $offset = 9, $limit = 25 ){
			return parent::FindAll( $offset, $limit, array( 'Id', 'DESC' ) );
		}
	}
	
	
	class Storetype extends Satori{
		protected $mDbTableAlias = 'storetypes';
	}
	
	class Storeitem extends Satori{
		protected $mDbTableAlias = 'storeitems';
	}
	
	class Storeproperties extends Satori{
		protected $mDbTableAlias = 'storeproperties';
	}
	
	class Storepurchase extends Satori{
		protected $mDbTableAlias = 'storepurchases';
		protected function OnBeforeCreate(){
			$purchaseFinder = new StorepurchaseFinder();
			$purchases = $purchaseFinder->CountByItemid( $this->Itemid );
			if( $purchases >= $this->Total ){
				return false;
			}
		}
	}
	
	class Storepurchaseproperties extends Satori{
		protected $mDbTableAlias = 'storepurchaseproperties';
	}
	
?>