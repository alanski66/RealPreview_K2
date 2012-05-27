/**
 * @version 	$Id: k2.js 1381 2011-12-05 11:32:38Z lefteris.kavadas $
 * @package 	K2
 * @author 		JoomlaWorks http://www.joomlaworks.gr
 * @copyright 	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

var $K2 = jQuery.noConflict();

$K2(document).ready(function(){

	// Common functions
	$K2('#jToggler').click(function(){
		if($K2(this).attr('checked')){
			$K2('input[id^=cb]').attr('checked', true);
			$K2('input[name=boxchecked]').val($K2('input[id^=cb]:checked').length);
		}
		else {
			$K2('input[id^=cb]').attr('checked', false);
			$K2('input[name=boxchecked]').val('0')
		}
	});
	$K2('#k2SubmitButton').click(function(){
		this.form.submit();
	});
	$K2('#k2ResetButton').click(function(event){
		event.preventDefault();
		$K2('.k2AdminTableFilters input').val('');
		$K2('.k2AdminTableFilters option').removeAttr('selected');
		this.form.submit();
	});
	$K2('.k2AdminTableFilters select').change(function(){
		this.form.submit();
	});

	// View specific functions
	if($K2('#k2AdminContainer').length > 0) {
		var view = $K2('#k2AdminContainer input[name=view]').val();
	}
	else {
		var view = $K2('#k2FrontendContainer input[name=view]').val();
	}
	
	switch(view){

		case 'comments':
			var flag = false;
			$K2('.editComment').click(function(event){
				event.preventDefault();
				if (flag){
					alert(K2Language[0]);
					return;
				}
				flag = true;
				var commentID = $K2(this).attr('rel');
				var target = $K2('#k2Comment'+commentID+' .commentText');
				var value = target.text();
				$K2('#k2Comment'+commentID+' input').val(value);
				target.empty();
				var textarea = $K2('<textarea/>', {name: 'comment', rows: '5', cols: '40'});
				textarea.html(value).appendTo(target);
				textarea.focus();
				$K2('#k2Comment'+commentID+' .commentToolbar a').css('display','inline');
				$K2(this).css('display','none');
			});
			$K2('.closeComment').click(function(event){
				event.preventDefault();
				flag = false;
				var commentID = $K2(this).attr('rel');
				var target = $K2('#k2Comment'+commentID+' .commentText');
				var value = $K2('#k2Comment'+commentID+' input').val();
				target.html(value);
				$K2('#k2Comment'+commentID+' .commentToolbar a').css('display','none');
				$K2('#k2Comment'+commentID+' .commentToolbar a.editComment').css('display','inline');

			});
			$K2('.saveComment').click(function(event){
				event.preventDefault();
				flag = false;
				var commentID = $K2(this).attr('rel');
				var target = $K2('#k2Comment'+commentID+' .commentText');
				var value = $K2('#k2Comment'+commentID+' .commentText textarea').val();
				$K2('#task').val('saveComment');
				$K2('#commentID').val(commentID);
				$K2('#commentText').val(value);
				var log =  $K2('#k2Comment'+commentID+' .k2CommentsLog');
				log.addClass('k2CommentsLoader');
				$K2.ajax({
					url: 'index.php',
					type: 'post',
					dataType: 'json',
					data: $K2('#adminForm').serialize(),
					success: function(result){
						target.html(result.comment);
						$K2('#k2Comment'+commentID+' input').val(result.comment);
						$K2('#task').val('');
						log.removeClass('k2CommentsLoader').html(result.message).delay(3000).fadeOut();
					}
				});
				$K2('#k2Comment'+commentID+' .commentToolbar a').css('display','none');
				$K2('#k2Comment'+commentID+' .commentToolbar a.editComment').css('display','inline');
			});
			if($K2('input[name=isSite]').val()==1){
				$K2('.k2CommentsPagination a').click(function(event){
					var url = $K2(this).attr('href').split('limitstart=');
					event.preventDefault();
					$K2('input[name=limitstart]').val(url[1]);
					Joomla.submitform();
				});
			}
			break;

		case 'extrafield':
			if ($K2('#groups').val() > 0) {
				$K2('#groupContainer').fadeOut(0);
			}
			$K2('#groups').change(function() {
				var selectedValue = $K2(this).val();
				if (selectedValue == 0) {
					$K2('#group').val('');
					$K2('#isNew').val('1');
					$K2('#groupContainer').fadeIn('slow');
				} else {
					$K2('#groupContainer').fadeOut('slow', function() {
						$K2('#group').val(selectedValue);
						$K2('#isNew').val('0');
					});
				}
			});
			if($K2('input[name=id]').val()){
				newField = 0;
			}
			else {
				newField = 1;
			}
			if (!newField) {
				var values = $K2.parseJSON($K2('#value').val());
			} else {
				var values = new Array();
				values[0] = " ";
			}
			renderExtraFields($K2('#type').val(), values, newField);
			$K2('#type').change(function() {
				var selectedType = $K2(this).val();
				$K2('#exFieldsTypesDiv').fadeOut('slow', function() {
					$K2('#exFieldsTypesDiv').empty();
					renderExtraFields(selectedType, values, newField);
					$K2('#exFieldsTypesDiv').fadeIn('slow');
				});
			});
			break;

		case 'usergroup':
			var value = $K2('input[name=categories]:checked').val();
			if(value=='all'){
				$K2('#paramscategories').attr('disabled', 'disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).attr('disabled', 'disabled');
					$K2(this).attr('selected', 'selected');
				});
			}
			else if(value=='none'){
				$K2('#paramscategories').attr('disabled', 'disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).attr('disabled', 'disabled');
					$K2(this).removeAttr('selected');
				});
			}
			else {
				$K2('#paramscategories').removeAttr('disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).removeAttr('disabled');
				});
			}
			$K2('#categories-all').click(function(){
				$K2('#paramscategories').attr('disabled', 'disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).attr('disabled', 'disabled');
					$K2(this).attr('selected', 'selected');
				});
			});
			$K2('#categories-none').click(function(){
				$K2('#paramscategories').attr('disabled', 'disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).attr('disabled', 'disabled');
					$K2(this).removeAttr('selected');
				});
			});
			$K2('#categories-select').click(function(){
				$K2('#paramscategories').removeAttr('disabled');
				$K2('#paramscategories option').each(function(){
					$K2(this).removeAttr('disabled');
				});
			});
			break;

		case 'category':
			$K2('#k2Accordion').accordion({
				collapsible: true,
				autoHeight: false
			});
			$K2('#k2Tabs').tabs();
			$K2('#k2ImageBrowseServer').click(function(event){
				event.preventDefault();
				SqueezeBox.initialize();
				SqueezeBox.fromElement(this, {
					handler: 'iframe',
					url: K2BasePath+'index.php?option=com_k2&view=media&type=image&tmpl=component&fieldID=existingImageValue',
					size: {x: 800, y: 434}
				});
			});
			break;

		case 'item':
			$K2('#k2Accordion').accordion({
				collapsible: true,
				autoHeight: false
			});
			$K2('#k2Tabs').tabs();
			$K2('#k2VideoTabs').tabs({selected: K2ActiveVideoTab});
			$K2('#k2ToggleSidebar').click(function(event){
				event.preventDefault();
				$K2('#adminFormK2Sidebar').toggle();
			});
			$K2('#catid option[disabled]').css('color', '#808080');
			setTimeout(function(){ initExtraFieldsEditor(); }, 1000);
			$K2('.deleteAttachmentButton').click(function(event){
				event.preventDefault();
				if (confirm(K2Language[3])) {
					var element = $K2(this).parent().parent();
					var url = $K2(this).attr('href');
					$K2.ajax({
						url: url,
						type: 'get',
						success: function(){
							$K2(element).fadeOut('fast', function(){
								$K2(element).remove();
							});
						}
					});
				}
			});
			$K2('#resetHitsButton').click(function(event){
				event.preventDefault();
				Joomla.submitbutton('resetHits');
			});
			$K2('#resetRatingButton').click(function(event){
				event.preventDefault();
				Joomla.submitbutton('resetRating');
			});
			$K2('#addAttachmentButton').click(function(event){
				event.preventDefault();
				addAttachment();
			});
			$K2('#newTagButton').click(function(){
				var log = $K2('#tagsLog');
				log.empty().addClass('tagsLoading');
				var tag = $K2('#tag').val();
				var url = 'index.php?option=com_k2&view=item&task=tag&tag='+tag;
				$K2.ajax({
					url: url,
					type: 'get',
					dataType: 'json',
					success: function(response){
						if (response.status=='success'){
							var option = $K2('<option/>', {value:response.id}).html(response.name).appendTo($K2('#tags'));
							}
						log.html(response.msg);
						log.removeClass('tagsLoading');
					}
				});
			});
			$K2('#addTagButton').click(function(){
				$K2('#tags option:selected').each(function() {
					$K2(this).appendTo($K2('#selectedTags'));
				});
			});
			$K2('#removeTagButton').click(function(){
				$K2('#selectedTags option:selected').each(function(el) {
					$K2(this).appendTo($K2('#tags'));
				});
			});
			$K2('#catid').change(function(){
				if($K2(this).find('option:selected').attr('disabled')){
					alert(K2Language[4]);
					$K2(this).val('0');
					return;
				}
				var selectedValue = $K2(this).val();
				var url = K2BasePath+'index.php?option=com_k2&view=item&task=extraFields&cid='+selectedValue+'&id='+$K2('input[name=id]').val();
				$K2('#extraFieldsContainer').fadeOut('slow', function(){
					$K2.ajax({
						url: url,
						type: 'get',
						success: function(response){
							$K2('#extraFieldsContainer').html(response);
							initExtraFieldsEditor();
							$K2('img.calendar').each(function(){
								inputFieldID = $K2(this).prev().attr('id');
								imgFieldID = $K2(this).attr('id');
								Calendar.setup({
									inputField: inputFieldID,
									ifFormat:"%Y-%m-%d",
									button:imgFieldID,
									align:"Tl",
									singleClick:true
									});
								});
							$K2('#extraFieldsContainer').fadeIn('slow');
						}
					});
					});
			});
			$K2('#k2ImageBrowseServer').click(function(event){
				event.preventDefault();
				SqueezeBox.initialize();
				SqueezeBox.fromElement(this, {
					handler: 'iframe',
					url: K2BasePath+'index.php?option=com_k2&view=media&type=image&tmpl=component&fieldID=existingImageValue',
					size: {x: 800, y: 434}
				});
			});
			$K2('#k2MediaBrowseServer').click(function(event){
				event.preventDefault();
				SqueezeBox.initialize();
				SqueezeBox.fromElement(this, {
					handler: 'iframe',
					url: K2BasePath+'index.php?option=com_k2&view=media&type=video&tmpl=component&fieldID=remoteVideo',
					size: {x: 800, y: 434}
				});
			});
			$K2('.k2AttachmentBrowseServer').live('click', function(event){
				event.preventDefault();
				var k2ActiveAttachmentField = $K2(this).next();
				k2ActiveAttachmentField.attr('id', 'k2ActiveAttachment');
				SqueezeBox.initialize();
				SqueezeBox.fromElement(this, {
					handler: 'iframe',
					url: K2BasePath+'index.php?option=com_k2&view=media&type=attachment&tmpl=component&fieldID=k2ActiveAttachment',
					size: {x: 800, y: 434},
					onClose: function(){
						k2ActiveAttachmentField.removeAttr('id');
					}
				});
			});
			$K2('.tagRemove').click(function(event){
				event.preventDefault();
				$K2(this).parent().remove();
			});
			$K2('ul.tags').click(function(){
				$K2('#search-field').focus();
			});
			$K2('#search-field').keypress(function(event) {
				if (event.which == '13') {
					if($K2(this).val()!=''){
						$K2('<li class="addedTag">'+$K2(this).val()+'<span class="tagRemove" onclick="$K2(this).parent().remove();">x</span><input type="hidden" value="'+$K2(this).val()+'" name="tags[]"></li>').insertBefore('.tags .tagAdd');
						$K2(this).val('');
					}
				}
			});
			$K2('#search-field').autocomplete({
				source : function(request, response) {
				$K2.ajax({
					type: 'post',
					url: K2SitePath+'index.php?option=com_k2&view=item&task=tags',
					data: 'q='+request.term,
					dataType: 'json',
					success: function(data) {
						$K2('#search-field').removeClass('tagsLoading');
						response( $K2.map( data, function( item ) {
							return item;
						}));
					}
				});
				},
				minLength: 3,
				select : function(event, ui) {
					$K2('<li class="addedTag">'+ui.item.label+'<span class="tagRemove" onclick="$K2(this).parent().remove();">x</span><input type="hidden" value="'+ui.item.value+'" name="tags[]"></li>').insertBefore('.tags .tagAdd');
					this.value = '';
					return false;
				},
				search: function(event, ui){
					$K2('#search-field').addClass('tagsLoading');
				}
			});
			if($K2('input[name=isSite]').val()==1){
				parent.$('sbox-overlay').removeEvents('click');
				parent.$('sbox-btn-close').removeEvents('click');
				var elements = [parent.$K2('#sbox-btn-close'), $K2('#toolbar-cancel a')];
				$K2.each(elements, function(index, element){
					element.unbind();
					element.click(function(event){
						event.preventDefault();
						if($K2('input[name=id]').val()){
							$K2.ajax({
								type: 'get',
								cache: false,
								url: K2SitePath+'index.php?option=com_k2&view=item&task=checkin&cid='+$K2('input[name=id]').val()+'&lang='+$K2('input[name=lang]').val(),
								success: function() {
									if(window.opener) {
										window.opener.location.reload();
									}
									else {
										parent.window.location.reload();
									}
									if(typeof(window.parent.SqueezeBox.close=='function')){
										window.parent.SqueezeBox.close();
									}
									else {
										parent.$K2('#sbox-window').close();
									}
									if(window.opener) {
										window.close();
									}
								}
							});
						}
						else {
							if(typeof(window.parent.SqueezeBox.close=='function')){
								window.parent.SqueezeBox.close();
							}
							else {
								parent.$K2('#sbox-window').close();
							}
							if(window.opener) {
								window.close();
							}
						}
					});
				});
			}
			break;
	}
});

// If we are in Joomla! 1.5 define the functions for validation
if (typeof(Joomla) === 'undefined') {
	var Joomla = {};
	Joomla.submitbutton = function(pressbutton){
		submitform(pressbutton);
	}
	function submitbutton(pressbutton) {
		Joomla.submitbutton(pressbutton);
	}
}

// Media manager
function elFinderUpdate(fieldID, value) {
	$K2('#'+fieldID).val(value);
	if(typeof(window.parent.SqueezeBox.close=='function')){
		SqueezeBox.close();
	} else {
		parent.$K2('#sbox-window').close();
	}
}

// Extra fields
function addOption(){
	var div = $K2('<div/>').appendTo($K2('#select_dd_options'));
	var input = $K2('<input/>',{name:'option_name[]',type:'text'}).appendTo(div);
	var input = $K2('<input/>',{name:'option_value[]',type:'hidden'}).appendTo(div);
	var input = $K2('<input/>',{value:K2Language[0], type:'button'}).appendTo(div);
	input.click(function(){$K2(this).parent().remove();})
}

function renderExtraFields(fieldType,fieldValues,isNewField){
	var target = $K2('#exFieldsTypesDiv');
	var currentType = $K2('#type').val();

	switch (fieldType){

	case 'textfield':
		var input = $K2('<input/>',{name:'option_value[]',type:'text'}).appendTo(target);
		var notice = $K2('<span/>').html('('+K2Language[1]+')').appendTo(target);
		if (!isNewField && currentType==fieldType) {
			input.val(fieldValues[0].value);
		}
		break;

	case 'labels':
		var input = $K2('<input/>',{name:'option_value[]',type:'text'}).appendTo(target);
		var notice = $K2('<span/>').html(K2Language[2]+' ('+K2Language[1]+')').appendTo(target);
		if (!isNewField && currentType==fieldType) {
			input.val(fieldValues[0].value);
		}
		break;

	case 'textarea':
		var textarea = $K2('<textarea/>', {name:'option_value[]', cols:'40', rows:'10'}).appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var label = $K2('<label/>').html(K2Language[3]).appendTo(target);
		var input = $K2('<input/>', {name:'option_editor[]', type:'checkbox', value:'1'}).appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var notice = $K2('<span/>').html('('+K2Language[4]+')').appendTo(target);
		if (!isNewField && currentType==fieldType) {
			textarea.val(fieldValues[0].value);
			if(fieldValues[0].editor){
				input.attr('checked',true);
			}
			else {
				input.attr('checked',false);
			}
		}
		break;

	case 'select':
	case 'multipleSelect':
	case 'radio':
		var input = $K2('<input/>',{ value:K2Language[5], type:'button'}).appendTo(target);
		input.click(function(){addOption();});
		var br = $K2('<br/>').appendTo(target);
		var div = $K2('<div/>',{id:'select_dd_options'}).appendTo(target);
		if (isNewField || currentType!=fieldType) {
			addOption();
		}
		else {
			$K2.each(fieldValues, function(index, value){
				var div = $K2('<div/>').appendTo($K2('#select_dd_options'));
				var input = $K2('<input/>',{name:'option_name[]',type:'text',value:value.name}).appendTo(div);
				var input = $K2('<input/>',{name:'option_value[]',type:'hidden',value:value.value}).appendTo(div);
				var input = $K2('<input/>',{value:K2Language[0],type:'button'}).appendTo(div);
				input.click(function(){$K2(this).parent().remove();})
			});
		}
		break;

	case 'link':

		var label = $K2('<label/>').html(K2Language[6]).appendTo(target);
		var inputName = $K2('<input/>',{name:'option_name[]',type:'text'}).appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var label = $K2('<label/>').html(K2Language[7]).appendTo(target);
		var inputValue = $K2('<input/>',{name:'option_value[]',type:'text'}).appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var label = $K2('<label/>').html(K2Language[8]).appendTo(target);
		var select = $K2('<select/>',{name:'option_target[]'}).appendTo(target);
		var option = $K2('<option/>',{value:'same'}).html(K2Language[9]).appendTo(select);
		var option = $K2('<option/>',{value:'new'}).html(K2Language[10]).appendTo(select);
		var option = $K2('<option/>',{value:'popup'}).html(K2Language[11]).appendTo(select);
		var option = $K2('<option/>',{value:'lightbox'}).html(K2Language[12]).appendTo(select);
		var br = $K2('<br/>').appendTo(target);
		var br = $K2('<br/>').appendTo(target);
		var notice = $K2('<span/>').html('('+K2Language[4]+')').appendTo(target);
		if (!isNewField && currentType==fieldType) {
			inputName.val(fieldValues[0].name);
			inputValue.val(fieldValues[0].value);
			select.children().each(function(){
				if ($K2(this).val()==fieldValues[0].target){
					$K2(this).attr('selected','selected');
				}
			});
		}

		break;

	case 'csv':
		var input = $K2('<input/>',{name:'csv_file',type:'file'}).appendTo(target);
		var inputValue = $K2('<input/>',{name:'option_value[]',type:'hidden'}).appendTo(target);
		if(!isNewField && currentType==fieldType){
			inputValue.val(JSON.stringify(fieldValues[0].value));
			var table = $K2('<table/>', {'class':'csvTable'}).appendTo(target);
			fieldValues[0].value.each(function(row, index) {
				var tr = $K2('<tr/>').appendTo(table);
				row.each(function(cell){
					if(index>0){
						var td = $K2('<td/>').html(cell).appendTo(tr);
					}
					else {
						var th = $K2('<th/>').html(cell).appendTo(tr);
					}
				})
			});
			var label = $K2('<label/>').html(K2Language[13]).appendTo(target);
			var input = $K2('<input/>',{name:'K2ResetCSV',type:'checkbox'}).appendTo(target);
			var br = $K2('<br/>',{'class':'clr'}).appendTo(target);
		}
		var notice = $K2('<span/>').html('('+K2Language[1]+')').appendTo(target);
		break;

	case 'date':
		var id = 'k2DateField'+$K2.now();
		var input = $K2('<input/>',{name:'option_value[]',type:'text', id:id, value:fieldValues[0].value, readonly:'readonly'}).appendTo(target);
		var img = $K2('<img/>',{id:id+'_img','class':'calendar',src:'templates/system/images/calendar.png', alt:K2Language[14]}).appendTo(target);
		Calendar.setup({
			inputField: id,
			ifFormat:"%Y-%m-%d",
			button:id+'_img',
			align:"Tl",
			singleClick:true
		});
		var notice = $K2('<span/>').html('('+K2Language[1]+')').appendTo(target);
		break;

	default:
		var title = $K2('<span/>',{'class':'notice'}).html(K2Language[15]).appendTo(target);
	break;

	}

}

// JSON.stringify for browser that do not support it. Used by the extra fields functions
JSON.stringify = JSON.stringify || function(obj) {
	var t = typeof (obj);
	if (t != "object" || obj === null) {
		if (t == "string") {
			obj = '"' + obj + '"';
		}
		return String(obj);
	} else {
		var n, v, json = [], arr = (obj && obj.constructor == Array);
		for (n in obj) {
			v = obj[n];
			t = typeof (v);
			if (t == "string") {
				v = '"' + v + '"';
			} else if (t == "object" && v !== null) {
				v = JSON.stringify(v);
			}
			json.push((arr ? "" : '"' + n + '":') + String(v));
		}
		return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
	}
};

function initExtraFieldsEditor() {
	$K2('.k2ExtraFieldEditor').each(function() {
		var id = $K2(this).attr('id');
		if( typeof tinymce != 'undefined') {
			if(tinyMCE.get(id)) {
				tinymce.EditorManager.remove(tinyMCE.get(id));
			}
			tinyMCE.execCommand('mceAddControl', false, id);
		} else {
			new nicEditor({
				fullPanel : true,
				maxHeight : 180,
				iconsPath : K2SitePath + 'media/k2/assets/images/system/nicEditorIcons.gif'
			}).panelInstance($K2(this).attr('id'));
		}
	});
}

function syncExtraFieldsEditor() {
	$K2('.k2ExtraFieldEditor').each(function() {
		editor = nicEditors.findEditor($K2(this).attr('id'));
		if (typeof editor != 'undefined') {
			if(editor.content == '<br>' || editor.content == '<br />'){
				editor.setContent('');
			}
			editor.saveContent();
		}
	});
}

function addAttachment(){
	var div = $K2('<div/>', {style:'border-top: 1px dotted #ccc; margin: 4px; padding: 10px;'}).appendTo($K2('#itemAttachments'));
	var input = $K2('<input/>', {name:'attachment_file[]', type:'file'}).appendTo(div);
	var label = $K2('<a/>', {href:'index.php?option=com_k2&view=media&type=attachment&tmpl=component&fieldID=k2ActiveAttachment', 'class':'k2AttachmentBrowseServer'}).html(K2Language[5]).appendTo(div);
	var input = $K2('<input/>', {name:'attachment_existing_file[]', type:'text'}).appendTo(div);
	var input = $K2('<input/>', {value: K2Language[0], type:'button' }).appendTo(div);
	input.click(function(){$K2(this).parent().remove();});
	var br = $K2('<br/>').appendTo(div);
	var label = $K2('<label/>').html(K2Language[1]).appendTo(div);
	var input = $K2('<input/>', {name:'attachment_title[]', type:'text', 'class':'linkTitle'}).appendTo(div);
	var br = $K2('<br/>').appendTo(div);
	var label = $K2('<label/>').html(K2Language[2]).appendTo(div);
	var textarea = $K2('<textarea/>', {name:'attachment_title_attribute[]', cols:'30', rows:'3'}).appendTo(div);
}

function jSelectUser(id, name){
	$K2('#k2Author').html(name);
	$K2('input[name=created_by]').val(id);
	if(typeof(window.parent.SqueezeBox.close=='function')){
		SqueezeBox.close();
	} else {
		parent.$K2('#sbox-window').close();
	}
}

/*
// Mootools code to be converted to jQuery ???
window.addEvent('domready', function(){
	document.formvalidator.setHandler('passverify', function(value){
		return ($('password').value == value);
	});
});
*/
