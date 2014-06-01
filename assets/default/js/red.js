/* RED AJAX - REQUIRES JQUERY */
window.getCookie = function(name) {
  match = document.cookie.match(new RegExp(name + '=([^;]+)'));
  if (match) return match[1];
}


$(document).ready(function() {
   /* get cookie value */
   var ajax_data = decodeURIComponent(getCookie('RED_AJAX'));
   if(ajax_data != '') {
     /* parse json object */
     var ajax_data = JSON.parse(ajax_data);

     var path = ajax_data.path;
     
     var events = ajax_data.events;
     
     /* loop through all events */
     for(var i in events) {
	var type = events[i].type;
	var element = events[i].element;
	var method = events[i].method;
	var bind = events[i].bind;
	var parameters = events[i].parameters;

	
	$(document).on(type, element, function() {
	var post_vars = {};
	if(parameters != null) {
		 for(var pi in parameters) {
			 
			var parameter_element = Object.keys(parameters[pi])[0];
			var parameter_attribute = parameters[pi][parameter_element];

			if(parameter_attribute == 'value') {
				post_vars[pi] = $(parameter_element).val();
			}

			else {
				post_vars[pi] = $(parameter_element).attr(parameter_attribute);
			}
			
	 	 }
	}
	  /* send ajax request for element */
	  $.post(path + "/" + method, post_vars ,function(data, status, xhr){
	     if(data != '') {

		var ct = xhr.getResponseHeader("content-type") || "";

		if(ct.indexOf('html') > -1) {

			/* remove + from element */
			bind = bind.split('+').join(' ');

			$(bind).html(data);
		}

		else {
			var newScriptTag = document.createElement('script');
			newScriptTag.appendChild(document.createTextNode(data));
			document.body.appendChild(newScriptTag);
		}
	     }
	  });

	});
     }

     
   }
});
