<?php
/**
 * @version     0.1 - Real Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 * Originaly from/derived/inspired by
 * * @version    0.0.1 - preview
 * @package    Version Control - Component
 * @author     Flavio Adalberto Kubota <flavio.kubota@community.joomla.org>
 * @copyright  Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license	   GNU/GPL
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die( 'Restricted access' );

class TableRealPreviewK2 extends JTable  {
	
	var $id = null;
	
	var $itemid = null;
	var $version = null;
	var $flowstatus = null;
	var $flowstatusId = null;
	
	var $title = null;
	var $alias = null;
	var $catid = null;
	var $published = null;
	var $introtext = null;
	var $fulltext = null;
	var $image_caption = null;
	var $image_credits = null;
	var $video = null;
	var $video_caption = null;
	var $video_credits = null;
	var $gallery = null;
	var $extra_fields = null;
	var $extra_fields_search = null;
	var $created = null;
	var $created_by = null;
	var $created_by_alias = null;
	var $modified = null;
	var $modified_by = null;
	var $publish_up = null;
	var $publish_down = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $trash = null;
	var $access = null;
	var $ordering = null;
	var $featured = null;
	var $featured_ordering = null;
	var $hits = null;
	var $metadata = null;
	var $metadesc = null;
	var $metakey = null;
	var $params = null;
	var $plugins = null;
	var $attachments = null;
	var $tags = null;
	
	var $language = null;

	function __construct(&$db) {

		parent::__construct('#__realpreview_k2', 'id', $db);
	}

	function check() {
	
		jimport('joomla.filter.output');
		
		$is_j16 = RealpreviewHelper::is_j16();

		if (trim($this->title) == '') {
			$this->setError(JText::_('Item must have a title'));
			return false;
		}
		if ($this->catid == '0') {
			$this->setError(JText::_('Item must have a category'));
			return false;
		}
		if ( empty($this->alias)) {
			$this->alias = $this->title;
		}


		//if(JPluginHelper::isEnabled('system', 'unicodeslug'))
			//$this->alias = JFilterOutput::stringURLSafe($this->alias);
			
		if($is_j16 && JFactory::getConfig()->get('unicodeslugs') == 1) {
			$this->alias = JApplication::stringURLSafe($this->alias);
		}
		else if(JPluginHelper::isEnabled('system', 'unicodeslug') || JPluginHelper::isEnabled('system', 'jw_unicodeSlugsExtended')) {
			$this->alias = JFilterOutput::stringURLSafe($this->alias);
		}
		else {
			mb_internal_encoding("UTF-8");
			mb_regex_encoding("UTF-8");
			$this->alias = trim(mb_strtolower($this->alias));
			$this->alias = str_replace('-', ' ', $this->alias);
			$this->alias = str_replace('/', '-', $this->alias);
			$this->alias = mb_ereg_replace('[[:space:]]+', ' ', $this->alias);
			$this->alias = trim(str_replace(' ', '-', $this->alias));
			$this->alias = str_replace('.', '', $this->alias);
			$this->alias = str_replace('"', '', $this->alias);
			$this->alias = str_replace("'", '', $this->alias);

			$stripthese = ',|~|!|@|%|^|(|)|<|>|:|;|{|}|[|]|&|`|„|‹|’|‘|“|�?|•|›|«|´|»|°|�|�|�';
			$strips = explode('|', $stripthese);
			foreach ($strips as $strip) {
				$this->alias = str_replace($strip, '', $this->alias);
			}


			$params = &JComponentHelper::getParams('com_k2');
			$SEFReplacements = array();
			$items = explode(',', $params->get('SEFReplacements'));
			foreach ($items as $item) {
				if (! empty($item)) {
					@list($src, $dst) = explode('|', trim($item));
					$SEFReplacements[trim($src)] = trim($dst);
				}
			}


			foreach ($SEFReplacements as $key=>$value) {
				$this->alias = str_replace($key, $value, $this->alias);
			}

			$this->alias = trim($this->alias, '-.');

			if (trim(str_replace('-', '', $this->alias)) == '') {
				$datenow = &JFactory::getDate();
				$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
			}
			 
			 
		}


		return true;

	}

	function bind($array, $ignore = '') {

		if (key_exists('params', $array) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		if (key_exists('plugins', $array) && is_array($array['plugins'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['plugins']);
			$array['plugins'] = $registry->toString();
		}
		
		if (key_exists('attachment', $array) && is_array($array['attachment'])) {
			
			if(array_key_exists('id',$array['attachments']))
				unset($array['attachments']['id']);
			if(array_key_exists('itemID',$array['attachments']))	
				unset($array['attachments']['itemID']);
			
			$registry = new JRegistry();
			$registry->loadArray($array['attachment']);
			$array['attachment'] = $registry->toString();
		}
				
		if (key_exists('tags', $array) && is_array($array['tags'])) {
			$array['tags'] = implode(',',$array['tags']);
		}

		return parent::bind($array, $ignore);
	}

	function getNextOrder($where = '', $column = 'ordering') {

		$table = $this->get_table_name();
		$query = "SELECT MAX({$column}) FROM {$table}";
		$query .= ($where ? " WHERE ".$where : "");
		$this->_db->setQuery($query);
		$maxord = $this->_db->loadResult();
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $maxord + 1;
	}

	function reorder($where = '', $column = 'ordering') {

		$table = $this->get_table_name();
		$k = $this->_tbl_key;
		$query = "SELECT {$this->_tbl_key}, {$column} 
					FROM {$table} 
					WHERE {$column}>0";
		$query .= ($where ? " AND ".$where : "");
		$query .= " ORDER BY {$column}";

		$this->_db->setQuery($query);
		if (!($orders = $this->_db->loadObjectList())) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}


		for ($i = 0, $n = count($orders); $i < $n; $i++) {
			if ($orders[$i]->$column >= 0) {
				if ($orders[$i]->$column != $i + 1) {
					$orders[$i]->$column = $i + 1;
					$query = "UPDATE #__k2_items SET {$column}=".(int) $orders[$i]->$column;
					$query .= ' WHERE '.$k.' = '.$this->_db->Quote($orders[$i]->$k);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}

		return true;
	}

	function move($dirn, $where = '', $column='ordering') {
		 
		$k = $this->_tbl_key;

		$sql = "SELECT $this->_tbl_key, {$column} FROM $this->_tbl";

		if ($dirn < 0) {
			$sql .= ' WHERE '.$column.' < '.(int) $this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column.' DESC';
		} else if ($dirn > 0) {
			$sql .= ' WHERE '.$column.' > '.(int) $this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column;
		} else {
			$sql .= ' WHERE '.$column.' = '.(int) $this->$column;
			$sql .= ($where ? ' AND '.$where : '');
			$sql .= ' ORDER BY '.$column;
		}

		$this->_db->setQuery($sql, 0, 1);


		$row = null;
		$row = $this->_db->loadObject();

		if (isset($row)) {
			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int) $row->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}

			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int) $this->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($row->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}
			$this->$column = $row->$column;
		} else {
			$query = 'UPDATE '.$this->_tbl.' SET '.$column.' = '.(int) $this->$column.' WHERE '.$this->_tbl_key.' = '.$this->_db->Quote($this->$k);
			$this->_db->setQuery($query);

			if (!$this->_db->query()) {
				$err = $this->_db->getErrorMsg();
				JError::raiseError(500, $err);
			}
		}
		return true;
	}
	

	function loadDraft_v1($itemid,$version) 
	{
		$mainframe = &JFactory::getApplication();
		$db = JFactory::getDBO();
		$table = $this->get_table_name();
		$q = "SELECT * FROM $table 
				WHERE `version`='$version' 
				AND `itemid`='$itemid'";
		$db->setQuery($q);
		
//echo $q;
//die();

		if (!$db->query()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}

		$row = $db->loadObject();
		
//print_r($row);
//die();

		//return $this;
		return $row;
	}
	function loadDraft($itemid,$version) 
	{
		$mainframe = &JFactory::getApplication();
		$db = JFactory::getDBO();
		$table = $this->get_table_name();
		$q = "SELECT * FROM $table 
				WHERE `version`='$version' 
				AND `itemid`='$itemid'";
		$db->setQuery($q);
		
//echo $q;
//die();

		if ($result = $db->loadAssoc( )) 
			return $this->bind($result);			
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
		
	}
	
	function get_table_name(){return "#__realpreview_k2";}
}