<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

function demo_print_message($msg,$format) {
	global $webim_encoding;
	if($format == "xml") {
		print "<message>".myiconv($webim_encoding,"utf-8",escape_with_cdata(message_to_html($msg)))."</message>\n";
	} else {
		print topage(message_to_html($msg));
	}
}

function demo_process_thread($act,$outformat,$lastid,$isuser,$canpost,$istyping,$postmessage) {
	global $kind_for_agent, $kind_info, $kind_events, $kind_user, $kind_agent, $webimroot;
	if( $act == "refresh" || $act == "post" ) {
		$lastid++;
		if($outformat == "xml") {
			start_xml_output();
			print("<thread lastid=\"$lastid\" typing=\"".($istyping ? 1 : 0)."\" canpost=\"".($canpost ? 1 : 0)."\">");
		} else {
			start_html_output();
			$url = "$webimroot/thread.php?act=refresh&thread=0&token=123&html=on&user=".($isuser?"true":"false");
			print("<html><head>\n".
				"<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$webimroot/chat.css\" />\n".
				"<meta http-equiv=\"Refresh\" content=\"7; URL=$url&sn=11\">\n".
				"<meta http-equiv=\"Pragma\" content=\"no-cache\">\n".
				"</head>".
				"<body bgcolor='#FFFFFF' text='#000000' link='#C28400' vlink='#C28400' alink='#C28400' marginwidth='0' marginheight='0' leftmargin='0' rightmargin='0' topmargin='0' bottommargin='0'>".
				"<table width='100%' cellspacing='0' cellpadding='0' border='0'><tr><td valign='top' class='message'>" );
		}
		if($lastid == 1) {
			demo_print_message(
				array('ikind'=>$kind_for_agent,'created'=>time()-15,'tname'=>'',
					  'tmessage'=>getstring2('chat.came.from',array("http://google.com"))), $outformat);
			demo_print_message(
				array('ikind'=>$kind_info,'created'=>time()-15,'tname'=>'',
					  'tmessage'=>getstring('chat.wait')), $outformat);
			demo_print_message(
				array('ikind'=>$kind_events,'created'=>time()-10,'tname'=>'',
					  'tmessage'=>getstring2("chat.status.operator.joined", array("Administrator"))), $outformat);
			demo_print_message(
				array('ikind'=>$kind_agent,'created'=>time()-9,'tname'=>'Administrator',
					  'tmessage'=>getstring("demo.chat.welcome")), $outformat);
			demo_print_message(
				array('ikind'=>$kind_user,'created'=>time()-5,'tname'=>getstring("chat.default.username"),
					  'tmessage'=>getstring("demo.chat.question")), $outformat);
			if($canpost && $outformat == 'xml') {
				demo_print_message(
					array('ikind'=>$kind_info,'created'=>time()-5,'tname'=>'',
						  'tmessage'=>'Hint: type something in message field to see typing notification'), $outformat);
			}
		}
		if($act == 'post') {
			demo_print_message(
				array('ikind'=>$isuser?$kind_user:$kind_agent,'created'=>time(),'tmessage'=>$postmessage,
					  'tname'=>$isuser?getstring("chat.default.username"):"Administrator"), $outformat);
		}
		if($outformat == "xml") {
			print("</thread>");
		} else {
			print(
				"</td></tr></table><a name='aend'>".
				"</body></html>" );
		}
	}
}

?>