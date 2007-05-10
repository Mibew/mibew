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
$dbencoding = "cp1251";
$webim_encoding = "cp1251";
$request_encoding = "utf-8";
$output_charset = "cp1251";

/*
 *  Application parameters
 */
$webim_from_email = "webim@yourdomain.com"; # email from field

$available_locales = array("en", "ru");
$home_locale = "ru";                        # native name will be used in this locale
$default_locale = "en";                     # if user does not provide known lang

$online_timeout = 30; # sec

?>