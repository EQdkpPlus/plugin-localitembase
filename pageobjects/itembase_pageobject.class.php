<?php
/*	Project:	EQdkp-Plus
 *	Package:	Local Itembase Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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


class itembase_pageobject extends pageobject {
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('social' => 'socialplugins');
   	return array_merge(parent::__shortcuts(), $shortcuts);
  }  
  
  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('localitembase', PLUGIN_INSTALLED))
      message_die($this->user->lang('lit_plugin_not_installed'));
    
    $this->user->check_auth('u_localitembase_view');
    if(!$this->user->is_signedin()) $this->user->check_auth('u_something');
    
    $handler = array(
    	'i'			=> array('process' => 'showitem'),
    );
    parent::__construct(false, $handler, array('localitembase', 'html_item_name'), null, 'selected_ids[]');

    $this->process();
  }


  
  public function display(){
  	//Success Message For Import
  	if($this->in->exists('success')){
  		$this->core->message($this->user->lang('lit_f_import_success'), $this->user->lang('success'), 'success', false);
  	}
  	
  	$strSearchValue = $this->in->get('litsvalue', '');
  	$maybeHit = array();
  	
  	if(strlen($strSearchValue)){
  		$strItemname = filter_var($strSearchValue, FILTER_SANITIZE_STRING);

  		$objQuery = $this->db->prepare("SELECT * FROM __plugin_localitembase WHERE item_gameid=?")->execute($strItemname);
  		if($objQuery){
  			while($row = $objQuery->fetchAssoc()){
  				$maybeHit[] = $row['id'];
  			}
  		}
  		
  		$objQuery = $this->db->query("SELECT * FROM __plugin_localitembase WHERE LOWER(item_name) LIKE ".$this->db->escapeString('%'.utf8_strtolower($strItemname).'%'));
  		
  		if($objQuery){
  			while($row = $objQuery->fetchAssoc()){
  				$maybeHit[] = $row['id']; 
  			}
  		}
  	}	
  	
  	$view_list = $maybeHit;
  	
  	infotooltip_js();
  	
  	$hptt_page_settings = array(
  			'name'				=> 'hptt_localitembase',
  			'table_main_sub'	=> '%intItemID%',
  			'table_subs'		=> array('%intItemID%'),
  			'page_ref'			=> 'manage_media.php',
  			'show_numbers'		=> false,
  			'show_select_boxes'	=> false,
  			'show_detail_twink'	=> false,
  			'table_sort_dir'	=> 'asc',
  			'table_sort_col'	=> 0,
  			'table_presets'		=> array(
  					//array('name' => 'localitembase_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
  					array('name' => 'localitembase_name_itt',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_item_gameid',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_quality',	'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_added_date',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_update_date',		'sort' => true, 'th_add' => '', 'td_add' => ''),
  					array('name' => 'localitembase_editicon',	'sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
  			),
  	);
  	
  	$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('ItemBase'), '%link_url_suffix%' => ''), md5($strSearchValue));
  	
  	
  	$page_suffix = '&amp;start='.$this->in->get('start', 0).'&amp;litsvalue='.$strSearchValue;
  	$sort_suffix = '?sort='.$this->in->get('sort').'&amp;litsvalue='.$strSearchValue;
  	$hptt->setPageRef($this->strPath);
  	$intLimit = 25;
  	$start	  = $this->in->get('start', 0);
  	
  	$item_count = count($view_list);
  	
  	$this->confirm_delete($this->user->lang('lit_delete_confirm'));

  	
  	$this->tpl->assign_vars(array(
  			'ITEM_LIST'			=> $hptt->get_html_table($this->in->get('sort'), $page_suffix, $start, $intLimit),
  			'HPTT_COLUMN_COUNT'	=> $hptt->get_column_count(),
  			'PAGINATION'		=> generate_pagination($this->strPath.$this->SID.$sort_suffix, $item_count, $intLimit, $start),
  			'NEW_ITEM_LINK'		=> $this->routing->build('itembase', 'New-Item', 'i0'),
  			'S_HAS_IMPORT_PERM' => $this->user->check_auth('u_localitembase_import', false),
  			'LIT_SEARCH_VALUE'	=> sanitize($strSearchValue),
  			'S_LIT_SEARCHRESULTS' => (count($view_list) > 0),
	));
  	
  	$this->core->set_vars(array(
  			'page_title'		=> $this->user->lang('localitembase'),
  			'template_path'		=> $this->pm->get_data('localitembase', 'template_path'),
  			'page_path'			=> [
  					['title'=>$this->user->lang('localitembase'), 'url'=>' '],
  			],
  			'template_file'		=> 'search.html',
  			'display'			=> true)
  	);

  }
 	
}
?>
