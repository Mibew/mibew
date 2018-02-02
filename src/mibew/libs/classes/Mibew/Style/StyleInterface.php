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

namespace Mibew\Style;

/**
 * Determine interface for specific style class.
 */
interface StyleInterface
{

    /**
     * Returns name of the style which shoud be used for the current request.
     *
     * Result of the method can depends on user role, requested page or any
     * other criteria.
     *
     * @return string Name of a style
     */
    public static function getCurrentStyle();

    /**
     * Returns name of the style which is used in the system by default.
     *
     * @return string Name of a style
     */
    public static function getDefaultStyle();

    /**
     * Sets style which is used in the system by default
     *
     * @param string $style_name Name of a style
     */
    public static function setDefaultStyle($style_name);

    /**
     * Returns an array which contains names of available styles.
     *
     * @param array List of styles names
     */
    public static function getAvailableStyles();

    /**
     * Builds base path for style files. This path is relative to Mibew
     * Messenger root and does not contain neither leading nor trailing slash.
     *
     * @return string Base path for style files
     */
    public function getFilesPath();

    /**
     * Loads and returns configurations of the style.
     *
     * @param array $name Style's configuration params
     */
    public function getConfigurations();

    /**
     * Returns name of the style related with the object
     *
     * @return string Name of the style
     */
    public function getName();

    /**
     * Renders template file to HTML.
     *
     * @param string $template_name Name of the template file with neither path
     *   nor extension.
     * @param array $data Associative array of values that should be used for
     *   substitutions in a template.
     * @return string Rendered template.
     */
    public function render($template_name, $data = array());
}
