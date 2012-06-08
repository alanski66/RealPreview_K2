<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
 
$item_versions = $this->version_list;
$linktype = (int)$this->linktype;
//$linkmethod = ($linktype) ? "target=\"_blank\"" : "class=\"modal\""; 
$linkmethod='';
if($linktype)
	$preview_attr =" class=\"modal icon camera\" rel=\"{handler:'iframe',size:{x:1000,y:600}}\" "; 
else	
	$preview_attr = "class=\"icon camera\" target=\"_blank\" "; 

echo '<h3><a href="#">'.JText::_('RPK2_VERSIONS').'</a></h3>';

$link = 'index.php?option=com_k2&amp;view=item&amp;layout=item&amp;id='.$this->row->itemid;
$root = JURI::root();
$previewlink = $root.$link.'&amp;version=';

$confirm_delete = JText::_('RPK2_CONFIRM_DELETE');
$confirm_delete = addslashes($confirm_delete);

if(empty($item_versions))
{
	echo'<tr><td>'.JText::_('RPK2_NO_DRAFTS_CREATED_YET').'</td></tr>';
}
else
{
?>
<div>
<table style="font-size:100%" width="100%" cellspacing="1" class="paramlist admintable">
<tbody>	
	<tr>
	<th><?php echo JText::_('RPK2_NB'); ?></th>
	<th><?php echo JText::_('RPK2_STATE'); ?></th>
	<th><?php echo JText::_('RPK2_LAST_EDIT'); ?></th>
	<th><?php echo JText::_('RPK2_ACTION'); ?></th>
	</tr>
<?php
	
	$current = '';
	$delcurrent = '';
	$class = '';
	$edit = '';
	$preview_txt = JText::_('RPK2_PREVIEW');
	$by_txt = JText::_('RPK2_BY');
	foreach ($item_versions as $v)
	{
		if ( $v->version == $this->row->version)
		{
			$current = '<span style="color:red">'.$v->version.'</span>';
			$class = 'style = "background:lightcyan" ';
			$edit = '<span style="color:red;">*'.JText::_('RPK2_LOADED').'*</span>&nbsp;|';
			$delcurrent='';
		}
		else
		{
			$editlink = 'index.php?option='.$option.'&amp;itemid='.$this->row->itemid.'&amp;version='.$v->version;
			$deletelink = $editlink.'&amp;task=delete&amp;id='.$v->id.'&amp;return_version='.$this->row->version;

			$current =$v->version;
			$class ='';
			$edit ='<a class="icon pencil" href="'.$editlink.'">'.JText::_('RPK2_EDIT').'</a>&nbsp;|&nbsp;';
			//$delcurrent  ='<a href="'.$deletelink.'">'.JText::_('RPK2_DELETE').'</a>';
			$delcurrent  ='<a class="icon trash" href="'.$deletelink.'" onclick="return confirm(\''.$confirm_delete.'\')">';
			$delcurrent .= JText::_('RPK2_DELETE').'</a>';
		}
		
		if((int)$v->published)
		{
			$state = '<span style="color:green;">'.JText::_('RPK2_PUBLISHED').'</span>';
			$delcurrent  ='';
		}
		else
		{
			$state = '<span style="color:orange;">'.JText::_('RPK2_DRAFT').'</span>';
		}

		$previewurl = $previewlink.$v->version;
		
		$creator =& JFactory::getUser($v->created_by);
                $editor =& JFactory::getUser($v->modified_by);
               // Joomkit alan
                //adds check for modified by for correct modifier name
                if($v->modified_by):
                    $name = $editor->name;
                elseif(!$v->modified_by):
                    $name = $creator->name;
                endif;

                //adds check for modified by for correct modifier name
               
                if($v->version == 1):
                    $vdate = $v->created;
                elseif($v->version > 1):
                    $vdate = $v->modified;
                endif;
              
		echo "<tr {$class}>
			<td> {$current}</td>
			<td>{$state}</td>
			<td>
				{$vdate}<br />{$by_txt}<em>{$name}</em>
                                ";
//                echo "<br>v created  by = ". $v->created_by;
//                echo "<br>v modified by = ". $v->modified_by;
                
                echo "
			</td>
			<td nowrap {$class}>{$edit}
			<a {$linkmethod} {$preview_attr}  href=\"{$previewurl}\">{$preview_txt}</a>
			|&nbsp;{$delcurrent}</td>
			</tr>";
	}
	echo"";
}

?>
</tbody></table>
</div>