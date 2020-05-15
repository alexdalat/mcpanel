$(document).ready(function() {
	var childLinks = document.getElementsByClassName('navbar-nav')[0].getElementsByTagName('a');
	console.log(window.location.href)
	for( i=0; i< childLinks.length; i++ ) {  // for each link
		if(window.location.href.indexOf(childLinks[i].getAttribute("href")) !== -1)
			childLinks[i].className += " active"
	}
	/* get current URL path and assign 'active' class
	var pathname = window.location.pathname;
	console.log("path:"+pathname)
	$('.navbar-nav > li > a[href="'+pathname+'"]').parent().addClass('active');*/
});

function delAlerts() {
	window.setTimeout(function() {
		$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
		});
	}, 8000);
}

function errorCheck(modal, data) {
	if(data.error != undefined) {
		msg = ""
		if(data.error === "username") {
			msg = "The username <strong>"+data.value+"</strong> is already taken or invalid.";
		} else if(data.error === "email") {
			msg = "The email <strong>"+data.value+"</strong> is already taken or invalid.";
		} else if(data.error === "password") {
			if(data.value === "syntax")
				msg = "The inputted password is invalid.";
			else if(data.value === "match")
				msg = "The two passwords do not match.";
		}
		modal.find("#errorBox").append("<div class='alert alert-danger' role='alert'><button type='button' class='close' data-dismiss='alert'><span>&times;</span></button>"+msg+"</div>");
		delAlerts();
		return true;
	}
}

function resetModal(modal) {
	modal.find("input").val('').end();
}
function addSuccess(msg) {
	$("#successBox").append("<div class='alert alert-success' role='alert'><button type='button' class='close' data-dismiss='alert'><span>&times;</span></button>"+msg+"<br /></div>");
	delAlerts();
}
function scrollTo(element, to, duration) {
    var start = element.scrollTop,
        change = to - start,
        currentTime = 0,
        increment = 20;
        
    var animateScroll = function(){        
        currentTime += increment;
        var val = Math.easeInOutQuad(currentTime, start, change, duration);
        element.scrollTop = val;
        if(currentTime < duration) {
            setTimeout(animateScroll, increment);
        }
    };
    animateScroll();
}

//t = current time
//b = start value
//c = change in value
//d = duration
Math.easeInOutQuad = function (t, b, c, d) {
  t /= d/2;
	if (t < 1) return c/2*t*t + b;
	t--;
	return -c/2 * (t*(t-2) - 1) + b;
};