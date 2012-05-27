<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//=============j17============
class com_realpreview_k2InstallerScript
{
	function install($parent)
	{
		return true;
	}
	function update($parent)
	{
		return true;
	}	
	function postflight($type, $parent)
	{
		//if($type=="install" or $type=="update")
		//{
			rpk2_install_helpers::create_table();
			rpk2_install_helpers::update_table();
		//}	
	}
	
	function uninstall($parent){}
	function preflight($type, $parent){}
}
	
class rpk2_install_helpers
{	
	function update_table() 
	{
		$db = JFactory::getDBO();
		$table_name = '#__realpreview_k2';
		$tables = $db->getTableFields($table_name,true);		
		if(!is_array($tables))return true;		
		$fields=$tables[$table_name];
		$sql = array();

		if(is_array($fields) and !empty($fields))
		{
			if(!array_key_exists('language',$fields))
			{
				$sql[] = "ALTER TABLE `$table_name` ADD COLUMN
						`language` char(7) DEFAULT NULL";
			}
				
			foreach($sql as $q)
			{
				$db->setQuery($q);
				if(!$db->query())
				{
					//JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
					JError::raiseWarning(1, $db->getErrorMsg());
				}
				//else JError::raiseNotice(400,$q);
			}
		}

	}
	
	function create_table()
	{		
		$sql="CREATE TABLE IF NOT EXISTS `#__realpreview_k2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `itemid` int NOT NULL,
  `version` int unsigned DEFAULT NULL,
  `flowstatus` varchar(40) NOT NULL,
  `flowstatusId` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `catid` int NOT NULL,
  `published` smallint(6) NOT NULL DEFAULT '0',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `video` text,
  `gallery` varchar(255) DEFAULT NULL,
  `extra_fields` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `extra_fields_search` text NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL,
  `checked_out` int unsigned NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `trash` smallint(6) NOT NULL DEFAULT '0',
  `access` int NOT NULL DEFAULT '0',
  `ordering` int NOT NULL DEFAULT '0',
  `featured` smallint(6) NOT NULL DEFAULT '0',
  `featured_ordering` int NOT NULL DEFAULT '0',
  `image_caption` text NOT NULL,
  `image_credits` varchar(255) NOT NULL,
  `video_caption` text NOT NULL,
  `video_credits` varchar(255) NOT NULL,
  `hits` int unsigned NOT NULL,
  `params` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `metakey` text NOT NULL,
  `plugins` text NOT NULL,
  `tags` text,
  `attachments` text,
  
  `language` char(7) NOT NULL,
  
  PRIMARY KEY (`id`),
  KEY `item` (`published`,`publish_up`,`publish_down`,`trash`,`access`),
  KEY `catid` (`catid`),
  KEY `created_by` (`created_by`),
  KEY `ordering` (`ordering`),
  KEY `featured` (`featured`),
  KEY `featured_ordering` (`featured_ordering`),
  KEY `hits` (`hits`),
  KEY `created` (`created`),
  
  KEY `language` (`language`),
  
  FULLTEXT KEY `search` (`title`,`introtext`,`fulltext`,`extra_fields_search`,`image_caption`,`image_credits`,`video_caption`,`video_credits`,`metadesc`,`metakey`),
  FULLTEXT KEY `title` (`title`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$db = JFactory::getDBO();
		$db->setQuery($sql);
		if(!$db->query())
		{
			//JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
			JError::raiseWarning(1, $db->getErrorMsg());
		}
	}
}
 	