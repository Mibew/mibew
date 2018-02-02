<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
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
     * @param object|string $container Either an instance of the class that
     *   keeps update methods or fully classified name of such class.
     * @return array The keys of this array are version numbers and values are
     *   methods of the $container class that should be performed.
     */
    public static function getUpdates($container)
    {
        $updates = array();

        if (is_object($container)) {
            // If an objects is passed to the method we can use its public
            // static and non-static methods as updates.
            $methods_filter = \ReflectionMethod::IS_PUBLIC;
        } else {
            // If a class name is passed to the method we can use only its
            // public static methods as updates. Also we need to make sure the
            // class exists.
            if (!class_exists($container)) {
                throw new \InvalidArgumentException(sprintf(
                    'Class "%s" does not exist',
                    $container
                ));
            }
            $methods_filter = \ReflectionMethod::IS_PUBLIC & \ReflectionMethod::IS_STATIC;
        }

        $container_reflection = new \ReflectionClass($container);
        foreach ($container_reflection->getMethods() as $method_reflection) {
            // Filter update methods
            $name = $method_reflection->getName();
            if (preg_match("/^update([0-9]+)(?:(Alpha|Beta|Rc)([0-9]+))?$/", $name, $matches)) {
                $version = self::formatVersionId($matches[1]);
                // Check if a beta version is defined.
                if (!empty($matches[2])) {
                    $version .= sprintf('-%s.%u', strtolower($matches[2]), $matches[3]);
                }

                $updates[$version] = array(
                    $method_reflection->isStatic() ? $container_reflection->getName() : $container,
                    $name
                );
            }
        }

        uksort($updates, 'version_compare');

        return $updates;
    }

    /**
     * Generates random unique 64 characters length ID for Mibew Messenger
     * instance.
     *
     * WARNING: This ID should not be used for any security/cryptographic. If
     * you need an ID for such purpose you have to use PHP's
     * {@link openssl_random_pseudo_bytes()} function instead.
     *
     * @return string
     */
    public static function generateInstanceId()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rnd = (string)microtime(true);

        // Add ten random characters before and after the timestamp
        $max_char = strlen($chars) - 1;
        for ($i = 0; $i < 10; $i++) {
            $rnd = $chars[rand(0, $max_char)] . $rnd . $chars[rand(0, $max_char)];
        }

        if (function_exists('hash')) {
            // There is hash function that can give us 64-length hash.
            return hash('sha256', $rnd);
        }

        // We should build random 64 character length hash using old'n'good md5
        // function.
        $middle = (int)floor(strlen($rnd) / 2);
        $rnd_left = substr($rnd, 0, $middle);
        $rnd_right = substr($rnd, $middle);

        return md5($rnd_left) . md5($rnd_right);
    }

    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
}
