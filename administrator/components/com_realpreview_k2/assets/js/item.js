try{
window.addEvent('domready', function(){
	$K2('#rpk2ImageBrowseServer').click(function(event){
		event.preventDefault();
		SqueezeBox.initialize();
		SqueezeBox.fromElement(this, {
			handler: 'iframe',
			url: K2BasePath+'index.php?option=com_realpreview_k2&view=media&type=image&tmpl=component&fieldID=existingImageValue',
			size: {x: 800, y: 434}
		});
	});
	$K2('#rpk2MediaBrowseServer').click(function(event){
		event.preventDefault();
		SqueezeBox.initialize();
		SqueezeBox.fromElement(this, {
			handler: 'iframe',
			url: K2BasePath+'index.php?option=com_realpreview_k2&view=media&type=video&tmpl=component&fieldID=remoteVideo',
			size: {x: 800, y: 434}
		});
	});
	$K2('.rpk2AttachmentBrowseServer').live('click', function(event){
		event.preventDefault();
		var k2ActiveAttachmentField = $K2(this).next();
		k2ActiveAttachmentField.attr('id', 'k2ActiveAttachment');
		SqueezeBox.initialize();
		SqueezeBox.fromElement(this, {
			handler: 'iframe',
			url: K2BasePath+'index.php?option=com_realpreview_k2&view=media&type=attachment&tmpl=component&fieldID=k2ActiveAttachment',
			size: {x: 800, y: 434},
			onClose: function(){
				k2ActiveAttachmentField.removeAttr('id');
			}
		});
	});
	$K2('#rpk2addAttachmentButton').click(function(event){
				event.preventDefault();
				addRpk2Attachment();
			});
});	
function addRpk2Attachment(){
	var div = $K2('<div/>', {style:'border-top: 1px dotted #ccc; margin: 4px; padding: 10px;'}).appendTo($K2('#itemAttachments'));
	var input = $K2('<input/>', {name:'attachment_file[]', type:'file'}).appendTo(div);
	var label = $K2('<a/>', {href:'index.php?option=com_realpreview_k2&view=media&type=attachment&tmpl=component&fieldID=k2ActiveAttachment', 'class':'rpk2AttachmentBrowseServer'}).html(K2Language[5]).appendTo(div);
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

}catch(e){
	//alert(e)
}