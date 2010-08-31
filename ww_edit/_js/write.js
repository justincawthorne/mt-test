// build tabbed sections

	$(function() {
		$("#article_tabs").tabs();
	});


// dynamically style checkboxes
 
	$(document).ready(function(){  
    
    	// set autosave interval
    	// also set the id for the tinymce textarea - which is 'body' in this case
    	
      	$(function() {	
	  		setInterval("auto_save('body')",10000);
	  	});
    
		// first run - change style for already checked checkboxes
		
		$("ul.checkbox_list input:checked").parent().addClass("checked");
		
		// click action
		
		$("ul.checkbox_list input").change(function(){  
			if($(this).is(":checked")){  
				$(this).parent("label").addClass("checked");  
			} else {  
				$(this).parent("label").removeClass("checked");  
			}  
		});  
	});


// preview article	

	$(function(){
		$('input[name=preview]').click(function(){
			var writeform = $('form#write_article');
			// grab original target value of form
			var o_action = writeform.attr('action');
			// process popup preview
			writeform.attr('target','article_preview');	
			writeform.onsubmit = window.open('_content/preview.php','article_preview','width=996,height=800,scrollbars=yes');
			writeform.attr('action','_content/preview.php');
			writeform.submit();
			// reset form values
			writeform.attr('target','_self');
			writeform.attr('action',o_action);
		});
	});

// preview edit link

		$(function(){
		    $('a.preview_edit').click(function(){
				window.open(this.href,'edit','width=640,height=480,location=no,scrollbars=yes,toolbar=no,status=no,titlebar=no');
		        return false;
		    });
		});

/*
	function to queue an attachment for adding to an article
*/

	$(function(){
		$('input[name=queue_attachment]').click(function(){
			// get selected value
			var writeform = $('form#write_article');
			var select_val = $('select[name=add_attachment]').val();
			var select_text = $('select[name=add_attachment] :selected').text();
			// add item as a checkbox
			var new_attachment = '<li><label for="attachments[' + select_val + ']" class="checked" >';
			new_attachment += '<input type="checkbox" name="attachments[' + select_val + ']" ';
			new_attachment += 'id="attachments[' + select_val + ']" value="' + select_val + '" checked="checked"/>';
			new_attachment += select_text + '</label></li>';
			// remove no attachment input placeholder
			$('input[name=no_attachments]').hide();
			// add to attachments list
			$('ul#attachments').append(new_attachment);
		});
	});

/*
	enables newly uploaded attachments to be queued up to an article
*/

	$(function(){
		$('input[name=upload_attachment]').click(function(){
			var writeform = $('form#write_article');
			// grab original target value of form
			var o_action = writeform.attr('action');
			// process popup preview
			writeform.attr('target','upload_attachment');	
			writeform.onsubmit = window.open('_content/upload_attachment.php','upload_attachment','width=360,height=480');
			writeform.attr('action','_content/upload_attachment.php');
			writeform.submit();
			// reset form values
			writeform.attr('target','_self');
			writeform.attr('action',o_action);
		});
	});

	// autosave function
	
	function auto_save(editor_id) {
		// First we check if any changes have been made to the editor window
		if(tinyMCE.getInstanceById(editor_id).isDirty()) {
			// If so, then we start the auto-save process
			// First we get the content in the editor window and make it URL friendly
			var writeform = $('form#write_article');
			var article_id 	= $("input[name=article_id]").val();
			var author_id 	= $("input[name=current_author_id]").val();
			var author_sess	= $("input[name=current_author_sess]").val();
			var content 	= tinyMCE.get(editor_id);
			var notDirty 	= tinyMCE.get(editor_id);
			content = escape(content.getContent());
			content = content.replace("+", "%2B");
			content = content.replace("/", "%2F");
			// We then start our jQuery AJAX function
			$.ajax({
			url: "_content/auto_save.php", // the path/name that will process our request
			type: "POST", 
			data: "article_id=" + article_id + "&content=" + content + "&author_id=" + author_id + "&author_sess=" + author_sess, 
			success: function(msg) {
			$("#last_autosave").empty().append(msg);
			//alert(msg);
			// Here we reset the editor's changed (dirty) status
			// This prevents the editor from performing another auto-save
			// until more changes are made
			notDirty.isNotDirty = true;
			}
			});
			// If nothing has changed, don't do anything
		} else {
			return false;
		}
	}