<?php
/*
 * Copyright 2005-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
$mysqlprefix = "";

$dbencoding = "utf8";
$force_charset_in_connection = true;

$use_persistent_connection = false;

/*
 *  Mailbox
 */
$webim_mailbox = "webim@yourdomain.com";
$mail_encoding = "utf-8";

/*
 *  Locales
 */
$home_locale = "en"; /* native name will be used in this locale */
$default_locale = "en"; /* if user does not provide known lang */

?>
