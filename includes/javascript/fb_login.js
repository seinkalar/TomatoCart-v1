/**
 * Load the facebook sdk asnchronously and then init it
 */
;(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];

 	if (d.getElementById(id)) {
     	return;
	}
 	
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
})(document, 'script', 'facebook-jssdk');

var moduleFBLogin = (function(window) {
	function sendRequest(tplCode, data, callback) {
		data.template = tplCode;
		data.module = 'fb_login';
		data.action = 'login';
	    
	    //send the ajax request
	    var loadRequest = new Request({
	      url: 'json.php',
	      method: 'post',
	      data: data,
	      onSuccess: callback.bind(this)
	    }).send();
	}
	
	function fblogin(tplCode) {
		FB.login(function(response){
			if (response.status === 'connected') {
    			FB.api('/me', function(userFbData) {
    				if (userFbData.id) {
    					sendRequest(tplCode, userFbData, function(response) {
    						window.location = '/';
    					});
    				}
    			});
    		}
		}, {scope: 'public_profile,email'});
	}
	
	function fbLogout() {
		FB.getLoginStatus(function(response) {
			if (response.status === 'connected') {
				FB.logout(function(response) {
					window.location = '/account.php?logoff';
			    });
			}else {
				window.location = '/account.php?logoff';
			}
		});
	}
	
	return {
		fbLogin: fblogin,
		fbLogout: fbLogout
	};
})(window);

(function(window, fbLoginConfig) {
	window.addEvent('domready', function() {
		window.fbAsyncInit = function() {
	    	FB.init({
	      		appId: fbLoginConfig.appId,
		      	xfbml: true,
		      	version: 'v2.1'
		    });
	  	};

	  	if ($('fbLogin') !== null) {
	  		$('fbLogin').addEvent('click', function(e) {
		  		e.stop();
		  		
		  		moduleFBLogin.fbLogin(fbLoginConfig.tplCode);

		  		return false;
		  	});
	  	}

	  	if ($('fbLogout') !== null) {
	  		$('fbLogout').addEvent('click', function(e) {
		  		console.log('fblogout');
		  		e.stop();
		  		
		  		moduleFBLogin.fbLogout();

		  		return false;
		  	});
	  	}	  	
	});
	
})(window, fbLoginConfig);