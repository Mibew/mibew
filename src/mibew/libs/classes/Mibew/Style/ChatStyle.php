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

// Import namespaces and classes of the core
use Mibew\Settings;

/**
 * Represents a chat style
 */
class ChatStyle extends AbstractHandlebarsPoweredStyle implements StyleInterface
{
    /**
     * Builds base path for style files. This path is relative Mibew Messenger
     * root and does not contain neither leading nor trailing slash.
     *
     * @return string Base path for style files
     */
    public function getFilesPath()
    {
        return 'styles/chats/' . $this->getName();
    }

    /**
     * Renders template file to HTML.
     *
     * @param string $template_name Name of the template file with neither path
     *   nor extension.
     * @param array $data Associative array of values that should be used for
     *   substitutions in a template.
     * @return string Rendered template.
     */
    public function render($template_name, $data = array())
    {
        // Pass additional variables to template
        $data['mibewVersion'] = MIBEW_VERSION;
        $data['currentLocale'] = get_current_locale();

        $locale_info = get_locale_info(get_current_locale());
        $data['rtl'] = $locale_info && $locale_info['rtl'];

        $data['stylePath'] = $this->getFilesPath();
        $data['styleName'] = $this->getName();

        return $this->getHandlebars()->render($template_name, $data);
    }

    /**
     * Returns name of the style which shoud be used for the current request.
     *
     * Result of the method can depends on user role, requested page or any
     * other criteria.
     *
     * @return string Name of a style
     * @throws \RuntimeException
     */
    public static function getCurrentStyle()
    {
        // Ceck if request contains chat style
        $style_name = verify_param("style", "/^\w+$/", "");
        if (!$style_name) {
            // Use the default style
            $style_name = self::getDefaultStyle();
        }

        // Get all style list and make sure that in has at least one style.
        $available_styles = self::getAvailableStyles();
        if (empty($available_styles)) {
            throw new \RuntimeException('There are no dialog styles in the system');
        }

        // Check if selected style exists. If it does not exist try to fall back
        // to "default". Finally, if there is no appropriate style in the system
        // throw an exception.
        if (in_array($style_name, $available_styles)) {
            return $style_name;
        } elseif (in_array('default', $available_styles)) {
            return 'default';
        } else {
            throw new \RuntimeException('There is no appropriate dialog style in the system');
        }
    }

    /**
     * Returns name of the style which is used in the system by default.
     *
     * @return string Name of a style
     */
    public static function getDefaultStyle()
    {
        // Load value from system settings
        return Settings::get('chat_style');
    }

    /**
     * Sets style which is used in the system by default
     *
     * @param string $style_name Name of a style
     */
    public static function setDefaultStyle($style_name)
    {
        Settings::set('chat_style', $style_name);
    }

    /**
     * Returns an array which contains names of available styles.
     *
     * @param array List of styles names
     */
    public static function getAvailableStyles()
    {
        $styles_root = MIBEW_FS_ROOT . '/styles/chats';

        return self::getStyleList($styles_root);
    }

    /**
     * Returns array of default configurations for concrete style object. This
     * method uses "Template method" design pattern.
     *
     * @return array Default configurations of the style
     */
    protected function getDefaultConfigurations()
    {
        return array(
            'chat' => array(
                'window' => array(),
                'iframe' => array(),
            ),
            'mail' => array(
                'window' => array(),
            ),
            'screenshots' => array(),
        );
    }
}
