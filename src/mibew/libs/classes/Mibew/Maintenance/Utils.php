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
     * Gets list of all available updates.
     *
     * @param object $container Instance of the class that keeps update methods.
     * @return array The keys of this array are version numbers and values are
     *   methods of the $container class that should be performed.
     */
    public static function getUpdates($container)
    {
        $updates = array();

        $container_reflection = new \ReflectionClass($container);
        foreach ($container_reflection->getMethods() as $method_reflection) {
            // Filter update methods
            $name = $method_reflection->getName();
            if (preg_match("/^update([0-9]+)(?:Beta([0-9]+))?$/", $name, $matches)) {
                $version = self::formatVersionId($matches[1]);
                // Check if a beta version is defined.
                if (!empty($matches[2])) {
                    $version .= '-beta.' . $matches[2];
                }

                $updates[$version] = $name;
            }
        }

        uksort($updates, 'version_compare');

        return $updates;
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
