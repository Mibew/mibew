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

/**
 * Autoloader for classes which implements PSR-0 standard.
 *
 * @param string $class_name Fully qualified name of the class
 */
function class_autoload($class_name)
{
    $base_dir = MIBEW_FS_ROOT . DIRECTORY_SEPARATOR . 'libs' .
        DIRECTORY_SEPARATOR . 'classes';

    $class_name = ltrim($class_name, '\\');
    $file_name = '';
    $namespace = '';
    if ($last_ns_pos = strrpos($class_name, '\\')) {
        $namespace = substr($class_name, 0, $last_ns_pos);
        $class_name = substr($class_name, $last_ns_pos + 1);
        $file_name = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

    require $base_dir . DIRECTORY_SEPARATOR . $file_name;
}
