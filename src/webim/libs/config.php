<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2007 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

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

// Use CP-1251 database
$dbencoding = "cp1251";
$webim_encoding = "cp1251";
$request_encoding = "utf-8";
$output_charset = "Windows-1251";
$force_charset_in_connection = true;


// Use UTF-8 database
/*
$dbencoding = "utf8";
$webim_encoding = "cp1251";
$request_encoding = "utf-8";
$output_charset = "Windows-1251";
$force_charset_in_connection = true;
*/

/*
 *   From field in outgoing mail.
 */
$webim_from_email = "webim@yourdomain.com"; // email from field

/*
 *   Company international name.
 */
$company_name = "My Company Ltd.";

/*
 *   Company logo. 
 */
$company_logo_link = "";

/*
 *   Locales 
 */
$available_locales = array("en", "ru");
$home_locale = "ru";                        // native name will be used in this locale
$default_locale = "en";                     // if user does not provide known lang

/*
 *   Allows users to change their names
 */
$user_can_change_name = true;

/*
 *   How to build presentable visitor name from {name}. Default: {name}
 */ 
$presentable_name_pattern = "{name}";

/*
 *   Method of getting information about remote user. For example, you could
 *   have user name or id in session. Default value: visitor_from_request
 */
$remote_visitor = 'visitor_from_request';

/*
 *   Timeout (in seconds) when online operator becomes offline.
 */
$online_timeout = 30;



?>
