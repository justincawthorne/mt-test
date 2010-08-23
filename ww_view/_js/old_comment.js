/*
    a handful of minor functions for enable 'reply to' links on comments
*/

// this function sets the reply id on the comment form and displays text of the comment being replied to

    function setreply(id,name,date,text) {
        document.getElementById('replytoID').value = id;
        var linktext;
        linktext = '<p>Replying to:</p><p class=\"comment\">';
		linktext += '<em>' + name + " (" + date + ")</em><br />" + text + '</p>';
        linktext += '<p><a href=\"javascript:void(0);\" onclick=\"javascript:unsetreply();\">';
        linktext += 'cancel reply</a></p>';
        document.getElementById('replydetails').innerHTML = linktext;
        window.location.hash = 'goto_commentform';
    }
    
// this function removes the reply id and the comment text from the comment form   
 
    function unsetreply() {
        document.getElementById('replytoID').value = '0';
        document.getElementById('replydetails').innerHTML = '';
        window.location.hash = 'goto_commentform';
    }
 
// this function displays the 'reply link' on the page
    
    function replylink(id,name,date,text) {
        //var id;
        //var text;
        document.write('<p class=\"replylink\">');
       	document.write('<a href=\"javascript:void(0);\" onclick=\"javascript:setreply(\'' + id + '\',\'' + name + '\',\'' + date + '\',\'' + text + '\')\">');
        document.write('reply to this</a></p>');
    }