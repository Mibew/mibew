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
 *  Application path on server
 */
$webimroot = "/webim";

/*
 *  Internal encoding
 */
$webim_encoding = "utf-8";

/*
 *  MySQL Database parameters
 */
$mysqlhost = "localhost";
$mysqldb = "webim_db";
$mysqllogin = "webim_lite";
$mysqlpass = "123";

$dbencoding = "utf8";
$force_charset_in_connection = true;

/*
 *  Mailbox
 */
$webim_mailbox = "webim@yourdomain.com";
$mail_encoding = "utf-8";

/*
 *  Locales
 */
$home_locale = "en";						/* native name will be used in this locale */
$default_locale = "en";						/* if user does not provide known lang */

?>
