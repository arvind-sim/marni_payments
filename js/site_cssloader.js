var browser = navigator.appName;
		if ( browser == "Microsoft Internet Explorer" || browser == "Opera")
			document.write( "<link rel=stylesheet type=text/css href=css/site_ie.css>");
		else
		if (browser == "Netscape" && navigator.vendor == "Apple Computer, Inc.")
			document.write( "<link rel=stylesheet type=text/css href=css/site_safari.css>");
		else
		if (browser == "Netscape" && navigator.vendor == "Google Inc.")
			document.write( "<link rel=stylesheet type=text/css href=css/site_chrome.css>");
	    else
			document.write( "<link rel=stylesheet type=text/css href=css/site_firefox.css>");

		  
