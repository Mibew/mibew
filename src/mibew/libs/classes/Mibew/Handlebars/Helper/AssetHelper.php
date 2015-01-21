<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;
use Mibew\Asset\Generator\UrlGeneratorInterface as AssetUrlGeneratorInterface;

/**
 * A helper that generates URLs for assets.
 *
 * Example of usage:
 * <code>
 *   {{asset "js/libs/super_lib.js"}}
 * </code>
 *
 * One can use locations passed to class constructor as a prefixes in relative
 * paths. Lets assume that the following array is passed to the constructor:
 * <code>
 *   $helper = new AssetHelper(
 *     $generator,
 *     array('CustomStorage' => 'custom/files/storage')
 *   );
 * </code>
 *
 * Then in a template you can do something like the following:
 * <code>
 *   {{asset "@CustomStorage/images/the_best_logo.png"}}
 * <code>
 */
class AssetHelper implements HelperInterface
{
    /**
     * @var array
     */
    protected $locations = null;

    /**
     * @var AssetUrlGeneratorInterface|null
     */
    protected $generator = null;

    /**
     * Class constructor.
     *
     * @param AssetUrlGeneratorInterface $generator An instance of URL generator
     * @param array $locations Associative array of locations that can be used
     *   as prefixes for asset relative paths. The keys are prefixes and the
     *   values are locations relative paths. These paths must not content
     *   neither leading nor trailing slashes.
     */
    public function __construct(AssetUrlGeneratorInterface $generator, $locations = array())
    {
        $this->generator = $generator;

        // Strip slashes from location paths.
        foreach ($locations as $name => $path) {
            $this->locations[$name] = trim($path, '/');
        }
    }

    /**
     * Gets instance of Asset URL Generator.
     *
     * @return AssetUrlGeneratorInterface
     */
    public function getAssetUrlGenerator()
    {
        return $this->generator;
    }

    /**
     * Sets an instance of Asset URL Generator.
     *
     * @param AssetUrlGeneratorInterface $generator
     */
    public function setAssetUrlGenerator(AssetUrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Template $template, Context $context, $args, $source)
    {
        $parsed_args = $template->parseArguments($args);
        if (count($parsed_args) != 1) {
            throw new \InvalidArgumentException(
                '"asset" helper expects exactly one argument.'
            );
        }
        $relative_path = $context->get($parsed_args[0]);

        if (preg_match("/^@(\w+)\//", $relative_path, $matches)) {
            // Resolve locations
            $relative_path = substr_replace(
                $relative_path,
                $this->resolveLocation($matches[1]),
                0,
                strlen($matches[0]) - 1 // Leave the slash in place
            );
        }

        return $this->generator->generate($relative_path);
    }

    /**
     * Resolves location by it's name
     *
     * @param string $name Location name
     * @return string Relative path of the location.
     * @throws \InvalidArgumentException
     */
    protected function resolveLocation($name)
    {
        foreach ($this->locations as $current_name => $location) {
            if ($name == $current_name) {
                return $location;
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown location %s', $name));
    }
}
