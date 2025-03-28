"use strict";

var prompt = {
   isShowing: function() {
         return $('#promptMsg').hasClass('show');
      },
   show: function(msg, type, timeout) {
         var msgBox = $('#promptMsg');
         msgBox.html('');
         var msgObj = $('<div></div>').html(msg);
         if (type == 'alert') {
	        msgObj.addClass('alert alert-danger');
	     } else {
	        msgObj.addClass('alert alert-'+type);
	     }
	     msgBox.html(msgObj);
	     window.scrollTo(0, 0);
	     msgBox.addClass('show');
	     if (timeout != 0) {
	        setTimeout(function () {
	           if (prompt.isShowing()) {
	              prompt.hide();
	           }
	        }, timeout);
	     }
      },
   hide: function () {
         $('#promptMsg').html('').removeClass('show');
         if (typeof prompt.callback === 'function')
         	prompt.callback();
      },
}

$(function () {
   $('#promptMsg').click(function (e) {
      prompt.hide();
   });
});