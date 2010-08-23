  // load jquery from google if not found
  
    if (typeof jQuery == 'undefined') { 
      var head = document.getElementsByTagName("head")[0];
      script = document.createElement('script');
      script.id = 'jQuery';
      script.type = 'text/javascript';
      script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
      head.appendChild(script);
    }
  
  // this writes the reply link within the comment div
      /*
      function write_reply_link(id) {
        var element = $('#reply_link_' + id)
        element.append('<a href="javascript:void(0);" onclick="javascript:set_reply('+ id +');">reply to this</a>');
      }
      */
	if(window.write_reply_link) { 
		
	} else {
		
		function write_reply_link(id) {
			var element = $('#comment_wrapper_' + id)
			element.append('<p class="reply_link"><a href="javascript:void(0);" onclick="javascript:set_reply('+ id +');">reply to this</a></p>');
		}
   }
   
   // when set_reply is clicked this fills the form field and displays the comment being replied to
   // just above the comment form itself
	
	if(window.set_reply) { 
		
	} else {
		
		function set_reply(id) {
			// set reply id in hidden form field
			var form_el = $('#reply_id')
			form_el.val(0)
			form_el.val(id)
			// get text of original comment for reply box
			var wrapper = $('div#comment_wrapper_' + id)
			var name = $('div#comment_wrapper_' + id + ' span.comment_name').html()
			var date = $('div#comment_wrapper_' + id + ' span.comment_date').html()
			var body = $('div#comment_wrapper_' + id + ' p.comment_body').html()
			// set reply text
			var reply_el = $('#reply_text')
			reply_el.html('')
			var reply_header  = '<p class="reply_header">'+name+' - '+date+'</p>'
			var reply_body    = '<p class="reply_body">'+body+'</p>'
			var reply_cancel  = '<p class="cancel_reply"><a href="javascript:void(0);" onclick="javascript:cancel_reply();">cancel reply</a></p>';
			reply_el.append(reply_header);
			reply_el.append(reply_body);
			reply_el.append(reply_cancel);
			var label = $('#comment_form label[for="comment_body"]')
			label.html('Your reply:')
		}
	}   
    // this cancels the reply (resets the hidden field and clears the reply text)
	
	if(window.cancel_reply) { 
	
	} else {
	
		function cancel_reply() {
			var form_el = $('#reply_id')
			form_el.val(0)
			var reply_el = $('#reply_text')
			reply_el.html('')
			var label = $('#comment_form label[for="comment_body"]')
			label.html('Your comment:')
		}
	}
	
	// inserts comment label text into form fields
/*	  
	jQuery(document).ready(function(){
	
	  //call the function for each field with a label

	  $("#comment_form label").each(function(){
	    label = $(this).html();
	    selector = "#"+$(this).attr("for");
	    createValueLabel(selector, label);
	    //use a CSS class to hide the labels from view  
		$(this).addClass("hide-label");
	  });

	
	});
	
	function createValueLabel (selector, defaultValue){
	  //assign the default value to the form selector
	  $(selector).data("default", defaultValue);
	
	  //if the selector is empty, initialize the default value
	  if(!$(selector).val()) $(selector).val(defaultValue);
	
	  //assign a function to the focus and blur events
	  $(selector).bind("focus blur", function(){
	    value = $(this).val();
	
	    //if the current and default value are the same, clear the input field
	    if(value==defaultValue){ $(this)
			.val("")
			.removeClass("value-label"); 
		}
	
	    //if the field is empty, set the value to default
	    if(!value){ $(this)
			.addClass("value-label")
			.val(defaultValue);
		}
	  });
	}
*/