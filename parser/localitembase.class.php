<?php
/*	Project:	EQdkp-Plus
 *	Package:	World of Warcraft game package
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if(!class_exists('localitembase')) {
	class localitembase extends itt_parser {

		public static $shortcuts = array('puf' => 'urlfetcher');

		public $av_langs = array('en' => 'en_US', 'de' => 'de_DE', 'fr' => 'fr_FR', 'ru' => 'ru_RU', 'es' => 'es_ES');

		public $settings = array(
			'itt_icon_loc' => array(
				'type' => 'text',
				'default' => ''),
			'itt_icon_ext' => array(
				'type' => 'text',
				'default' => ''),
			'itt_default_icon' => array(
				'type' => 'text',
				'default' => ''),
		);

		private $searched_langs = array();

		public function __destruct(){
			unset($this->searched_langs);
			parent::__destruct();
		}

		protected function searchItemID($itemname, $lang, $searchagain=0) {
			$searchagain++;
			$this->pdl->log('infotooltip', 'localitembase->searchItemID called: itemname: '.$itemname.', lang: '.$lang.', searchagain: '.$searchagain);
			$item_id = 0;
			
			// Ignore blank names.
			$name = trim($itemname);
			if (empty($name)) { return null; }
			
			$intLitItemID = $this->pdh->get('localitembase', 'item_by_name', array(unsanitize($name)));
			if($intLitItemID){
				$item_id = $this->pdh->get('localitembase', 'item_gameid', array($intLitItemID));
				if(!$item_id) $item_id = 'lit:'.$intLitItemID;
			}

			$debug_out = ($item_id > 0) ? 'Item-ID found: '.$item_id : 'No Item-ID found';
			$this->pdl->log('infotooltip', $debug_out);
			return array($item_id, 'items');
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$orig_id = $item_id;
			
			if(!$item_id) {
				$item['baditem'] = true;
				return $item;
			}
			
			if(strpos($item_id, 'lit:') === 0){
				$intLitItemID = (int)substr($item_id, 4);
			} else {
				$intLitItemID = $this->pdh->get('localitembase', 'item_by_gameid', array($item_id));
			}
			
			if($this->pdh->get('localitembase', 'id', array($intLitItemID))){
				
				$item['name'] = $this->pdh->get('localitembase', 'itemname_for_lang', array($intLitItemID, $lang));
				if($item['name'] && $item['name'] != ""){
					$item['html'] = $this->pdh->get('localitembase', 'itemhtml_for_lang', array($intLitItemID, $lang));
					$item['lang'] = $lang;
						
					$item['icon'] = $this->pdh->get('localitembase', 'icon_for_item', array($intLitItemID));
					$item['color'] = $this->pdh->get('localitembase', 'quality', array($intLitItemID));
						
					//Reset Item ID, because the full name is the one we should store in DB
					$item['id'] = $orig_id;
					
					$item['baditem'] = true;
					return $item;
				}
			}

			$item['baditem'] = true;
			return $item;
		}

	}
}
?>