<?php
/*	Project:	EQdkp-Plus
 *	Package:	MediaCenter Plugin
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

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'localitembase'                    => 'Lokale Itemdatenbank',

  // Description
  'localitembase_short_desc'         => 'Lokale Itemdatenbank',
  'localitembase_long_desc'          => 'Erstelle und Verwalte eigene Items',
  
  'lit_plugin_not_installed'		=> 'Das LocalItembase-Plugin ist nicht installiert.',
  'lit_config_saved'				=> 'Die Einstellungen wurden erfolgreich gespeichert.',

	'lit_fs_items' => 'Items',
	'lit_f_base_layout' => 'HTML-Grundaussehen des Itemtooltips',
	'lit_f_base_css' => 'CSS-Aussehen des Itemtooltips',
	'lit_f_infotext' => 'Informationstext für Benutzer',
	'lit_fs_export_import' => 'Import & Export',
	'lit_f_export' => 'Items exportieren',
	'lit_f_import' => 'Items importieren',
	'lit_f_importfield' => 'Ein-/Ausgabe',
	'lit_add_item' => 'Neues Item anlegen',
	'lit_delete_selected_items' => 'Ausgewählte Items löschen',
	'lit_delete_confirm' => 'Möchtest du die ausgewählten Items %s wirklich löschen?',
	'lit_fs_general' => 'Allgemeines',
	'lit_f_item_gameid' => 'Game-ID',
	'lit_f_quality' => 'Item-Quality',
	'lit_f_item_text' => 'Item-Beschreibung',
	'lit_f_item_text_help' => 'Du kannst ein hochgeladenes Itembild über {IMAGE} einbinden',
	'lit_f_item_name' => 'Itemname',
	'lit_f_item_images' => 'Itembild',
	'lit_f_icon' => 'Item-Icon',
 );

?>
