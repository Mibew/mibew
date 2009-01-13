<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2009 Web Messenger Community
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

/*
 * Application path on server
 */
$webimroot = "/webim";

/*
 *  MySQL Database parameters
 */
$mysqlhost = "localhost";
$mysqldb = "webim_db";
$mysqllogin = "webim_lite";
$mysqlpass = "123";

/*
 *  Localization parameters
 */

/* Use UTF-8 database */
$dbencoding = "utf8";
$webim_encoding = "utf-8";
$force_charset_in_connection = true;

/*
 * Web Messenger 1.0.8 an earlier stored user name in cookie in "webim_encoding". If
 * you used this versions of webim set your previous encoding here.
 */
$compatibility_encoding = "cp1251";

/*
 * This encoding will be used for emails
 */
$mail_encoding = $webim_encoding;

/*
 *   From field in outgoing mail.
 */
$webim_from_email = "webim@yourdomain.com"; /* email from field */

/*
 *   Inbox for left messages encoding
 */
$webim_messages_locale = "en";

/*
 *   Locales
 */
$home_locale = "en";						/* native name will be used in this locale */
$default_locale = "en";						/* if user does not provide known lang */

/*
 *   Method of getting information about remote user. For example, you could
 *   have user name or id in session. Default value: visitor_from_request
 */
$remote_visitor = 'visitor_from_request';

?>
