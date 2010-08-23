  $(document).ready(function() {
  
  	$(function() {
  		// Here we have the auto_save() function run every 5 secs
  		// We also pass the argument 'editor_id' which is the ID for the textarea tag
  		setInterval("auto_save('body')",10000);
  	});
  	
  });
  
	// Here is the auto_save() function that will be called every 30 secs
	function auto_save(editor_id) {
		// First we check if any changes have been made to the editor window
		if(tinyMCE.getInstanceById(editor_id).isDirty()) {
			// If so, then we start the auto-save process
			// First we get the content in the editor window and make it URL friendly
			var content = tinyMCE.get(editor_id);
			var notDirty = tinyMCE.get(editor_id);
			content = escape(content.getContent());
			content = content.replace("+", "%2B");
			content = content.replace("/", "%2F");
			// We then start our jQuery AJAX function
			$.ajax({
			url: "_content/auto_save.php", // the path/name that will process our request
			type: "POST", 
			data: content, 
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