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

function demo_print_message($msg) {
	global $webim_encoding;
	print "<message>".myiconv($webim_encoding,"utf-8",escape_with_cdata(message_to_html($msg)))."</message>\n";
}

function demo_process_thread($act,$lastid,$isuser,$canpost) {
	global $kind_for_agent, $kind_info, $kind_events, $kind_user, $kind_agent;
	if( $act == "refresh" ) {
		$lastid++;
		start_xml_output();
		print("<thread lastid=\"$lastid\" typing=\"".((($lastid/3)%2)==1 ? 1 : 0)."\" canpost=\"".($canpost ? 1 : 0)."\">");
		if($lastid == 1) {
			demo_print_message(
				array('ikind'=>$kind_for_agent,'created'=>time()-15,'tname'=>'',
					  'tmessage'=>getstring2('chat.came.from',array("http://google.com"))));
			demo_print_message(
				array('ikind'=>$kind_info,'created'=>time()-15,'tname'=>'',
					  'tmessage'=>getstring('chat.wait')));
			demo_print_message(
				array('ikind'=>$kind_events,'created'=>time()-10,'tname'=>'',
					  'tmessage'=>getstring2("chat.status.operator.joined", array("Administrator"))));
			demo_print_message(
				array('ikind'=>$kind_agent,'created'=>time()-9,'tname'=>'Administrator',
					  'tmessage'=>getstring("demo.chat.welcome")));
			demo_print_message(
				array('ikind'=>$kind_user,'created'=>time()-5,'tname'=>getstring("chat.default.username"),
					  'tmessage'=>getstring("demo.chat.question")));
		}
		print("</thread>");
	}
}

?>