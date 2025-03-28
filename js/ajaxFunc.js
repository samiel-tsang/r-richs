"use strict";

var ajaxFunc = {
   apiCall: function(method, op, data, enctype, handle) {
         var opt = {
            type: method,
            url: ((BASE_URL === undefined)?"/":BASE_URL)+"api/"+op,
         };
         if (data != undefined || data != null) opt.data = data;
         if (enctype != undefined || enctype != null) {
            opt.enctype = enctype;
            opt.processData = false;
            opt.contentType = false;
            opt.cache = false;
         }
         var jqXhr = $.ajax(opt); 
         jqXhr.done(handle);
         return jqXhr;
      },

   responseHandle: function (data) {
         if (data.objectType == 'multiResp') {
            $(data.actions).each(function (idx, element) {
               ajaxFunc.processResponse(element);
            });
         } else {
            ajaxFunc.processResponse(data);
         }
      },

   processResponse: function (data) {
         if (data.objectType == 'message') {
            prompt.show(data.msg, data.type, 3000);
         }
         ajaxFunc.processActionResponse(data);
      },
   processActionResponse: function (data) {
         if (data.objectType == 'action') {
            if (data.action == 'redirect') {
               window.location = data.script;
            }
			if (data.action == 'refresh') {
				var [timeout, url] = data.script.split(';', 2);
				setTimeout(function () { window.location = url; }, timeout*1000);
			}
			if (data.action == 'script') {
			   eval(data.script);
			}
         }
      },
}