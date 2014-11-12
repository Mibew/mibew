<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2014 the original author or authors.
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

namespace Mibew\Maintenance;

/**
 * Contains a set of utility methods.
 */
class Utils
{
    /**
     * Converts version ID to human readable representation.
     *
     * Example of usage:
     * <code>
     *   $version = 50303;
     *   echo(format_version_id($version)); // Outputs "5.3.3"
     * </code>
     *
     * @param int $version_id Version ID
     * @return string Human readable version.
     */
    public static function formatVersionId($version_id)
    {
        $parts = array();
        $tmp = (int)$version_id;

        for ($i = 0; $i < 3; $i++) {
            $parts[] = $tmp % 100;
            $tmp = floor($tmp / 100);
        }

        return implode('.', array_reverse($parts));
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
