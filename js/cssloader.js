var browser = navigator.appName;
		if ( browser == "Microsoft Internet Explorer" || browser == "Opera")
			document.write( "<link rel=stylesheet type=text/css href=../css/ie.css>");
		else
		if (browser == "Netscape" && navigator.vendor == "Apple Computer, Inc.")
			document.write( "<link rel=stylesheet type=text/css href=../css/safari.css>");
	    else
			document.write( "<link rel=stylesheet type=text/css href=../css/firefox.css>");

		  
