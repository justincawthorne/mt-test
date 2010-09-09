// set up tabs

	$(function() {
		$("#settings_tabs").tabs();
	});
	
// click link for headers in the aside

	$(function(){
		$('h6.settings_header').click(function(){
			var tab_id = $(this).attr('id').replace('tab_','');
			tab_id = parseInt(tab_id);
			$("#settings_tabs").tabs('select', tab_id);
		});
	});

// dynamically style checkboxes
 
	$(document).ready(function(){  
    
		// first run - change style for already checked checkboxes
		
		$("span.checkbox input:checked").parent().addClass("yes");
		
		// click action
		
		$("span.checkbox input").change(function(){  
			if($(this).is(":checked")){  
				$(this).parent("span.checkbox").addClass("yes");  
			} else {  
				$(this).parent("span.checkbox").removeClass("yes");  
			}  
		});  
	});
	
// dynamically style select checkboxes

	$(document).ready(function(){  
    
	
		// click action
		
		$("select.select_checkbox").change(function(){  
			if($(this).val() == 1){  
				$(this).addClass("opt_yes");  
			} else {  
				$(this).removeClass("opt_yes");  
			}  
		});  
	});

