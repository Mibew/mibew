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

namespace Mibew\Handlebars\Helper;

use Handlebars\Context;
use Handlebars\Helper as HelperInterface;
use Handlebars\Template;
use Mibew\Asset\AssetManagerAwareInterface;
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
 *     $asset_manager_container,
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
     * @var AssetManagerAwareInterface|null
     */
    protected $assetManagerContainer = null;

    /**
     * Class constructor.
     *
     * @param AssetManagerAwareInterface $manager_container An object which
     * knows where to get an appropriate Asset Manager.
     * @param array $locations Associative array of locations that can be used
     *   as prefixes for asset relative paths. The keys are prefixes and the
     *   values are locations relative paths. These paths must not content
     *   neither leading nor trailing slashes.
     */
    public function __construct(AssetManagerAwareInterface $manager_container, $locations = array())
    {
        $this->assetManagerContainer = $manager_container;

        // Strip slashes from location paths.
        foreach ($locations as $name => $path) {
            $this->locations[$name] = trim($path, '/');
        }
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

        return $this->getAssetUrlGenerator()->generate($relative_path);
    }

    /**
     * Extracts an instance of Asset URL Generator from the Asset Manager
     * container related with the object.
     *
     * @return AssetUrlGeneratorInterface
     */
    protected function getAssetUrlGenerator()
    {
        return $this->assetManagerContainer->getAssetManager()->getUrlGenerator();
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
