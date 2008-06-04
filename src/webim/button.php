<?php
/*
 * This file is part of Web Instant Messenger project.
 *
 * Copyright (c) 2005-2008 Internet Services Ltd.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Evgeny Gryaznov - initial API and implementation
 */

require_once('libs/common.php');
require_once('libs/operator.php');

$image = verifyparam("image","/^\w+$/", "webim");
$lang = verifyparam("lang", "/^[\w-]{2,5}$/", "");
if( !$lang || !in_array($lang,$available_locales) )
	$lang = $current_locale;

$image_postfix = has_online_operators() ? "on" : "off";
$filename = "locales/${lang}/button/${image}_${image_postfix}.gif";

$fp = fopen($filename, 'rb') or die("no image");
header("Content-Type: image/gif");
header("Content-Length: ".filesize($filename));
fpassthru($fp);
exit;
?>