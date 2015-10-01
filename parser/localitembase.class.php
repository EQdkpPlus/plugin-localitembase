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
		
		private function getItemFromDatabase($intLitItemID){
			$objQuery = $this->db->prepare("SELECT * FROM __plugin_localitembase WHERE id=?")->execute($intLitItemID);
			if($objQuery){
				$arrItemData = $objQuery->fetchAssoc();
				if(count($arrItemData)){
					return $arrItemData;
				}
			}
				
			return false;
		}
		
		private function getItemFromIngameID($strIngameID){
			$objQuery = $this->db->prepare("SELECT * FROM __plugin_localitembase WHERE item_gameid=?")->execute($strIngameID);
			if($objQuery){
				$arrItemData = $objQuery->fetchAssoc();
				if(count($arrItemData)){
					return $arrItemData;
				}
			}
			
			return false;
		}
		
		private function getPluginConfig(){
			$objQuery = $this->db->prepare("SELECT * FROM __config WHERE config_plugin='localitembase'")->execute();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrConfigData[$row['config_name']] = $row['config_value'];
				}

				return $arrConfigData;
			}
			
			return false;
		}

		protected function getItemData($item_id, $lang, $itemname='', $type='items'){
			$orig_id = $item_id;
			
			if(!$item_id) {
				$item['baditem'] = true;
				return $item;
			}
			
			if(strpos($item_id, 'lit:') === 0){
				$intLitItemID = (int)substr($item_id, 4);
				$arrItemData = $this->getItemFromDatabase($intLitItemID);
			} else {
				$arrItemData = $this->getItemFromIngameID($item_id);
				$intLitItemID = $arrItemData['id'];
			}
			
			if($intLitItemID !== false && count($arrItemData)){
				$myLang = isset($this->av_langs[$lang]) ? $this->av_langs[$lang] : false;
				
				$myLang = ($myLang == 'en_US') ? "en_EN" : $myLang;
				
				$arrNames = unserialize($arrItemData['item_name']);
				if(isset($arrNames[$myLang]) && strlen($arrNames[$myLang])){
					$item['name'] = $arrNames[$myLang];
				} else {
					foreach($arrNames as $key => $val){
						if($val != "") {
							$item['name'] = $val; break;
						}
					}
				}
				$item['lang'] = $lang;	
				$item['color'] = $arrItemData['quality'];
				
				//Icon
				$item['icon'] = ($arrItemData['icon'] != "") ? $this->pfh->FileLink('icons/'.$arrItemData['icon'], 'localitembase', 'absolute') : '';
				
				//HTML
				$arrConfig = $this->getPluginConfig();
				$strBaseLayout = $arrConfig['base_layout'];
				
				//Wenn Kein Inhalt, aber Bild, nehme Bild. Wenn Inhalt, replace Image
				$arrText = unserialize($arrItemData['text']);
				if(isset($arrText[$myLang]) && strlen($arrText[$myLang])){
					$itemText = $arrText[$myLang];
				} else {
					foreach($arrNames as $key => $val){
						if($val != "") {
							$itemText = $val; break;
						}
					}
				}
				$itemImage = false;
				$arrImage = unserialize($arrItemData['image']);

				if(isset($arrImage[$myLang]) && strlen($arrImage[$myLang])){
					$itemImage = $arrImage[$myLang];
				}

				if($itemImage && strlen($itemText)){
					$itemImage = $this->pfh->FileLink('images/'.$itemImage, 'localitembase', 'absolute');
					$itemText = str_replace('{IMAGE}', '<img src="'.$itemImage.'" />', $itemText);
				}elseif($itemImage){
					$itemText = '<img src="'.$itemImage.'" />';
				} else {
					$itemText = str_replace('{IMAGE}', '', $itemText);
				}
				
				$itemText = str_replace("{ITEM_CONTENT}", $itemText, $strBaseLayout);
				
				//Icon
				$itemText = str_replace("{ICON}", $item['icon'], $itemText);
				
				$item['html'] = $itemText;

				return $item;
			}

			$item['baditem'] = true;
			return $item;
		}

	}
}
?>