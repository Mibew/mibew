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

namespace Mibew\Controller;

use Mibew\Settings;
use Mibew\Style\StyleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with styles.
 */
class StyleController extends AbstractController
{
    const TYPE_CHAT = 'chat';
    const TYPE_INVITATION = 'invitation';
    const TYPE_PAGE = 'page';

    /**
     * Generates a page with style preview.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function previewAction(Request $request)
    {
        $operator = $this->getOperator();
        $class_name = $this->resolveClassName($request->attributes->get('type'));

        $style_list = call_user_func($class_name . '::getAvailableStyles');

        $preview = $request->query->get('preview');
        if (!in_array($preview, $style_list)) {
            $style_names = array_keys($style_list);
            $preview = $style_list[$style_names[0]];
        }

        $style = new $class_name($preview);
        $screenshots = $this->buildScreenshotList($style);

        $page['formpreview'] = $preview;
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page['availablePreviews'] = $style_list;
        $page['screenshotsList'] = $screenshots;
        $page['title'] = getlocal('Site style');
        $page['menuid'] = 'styles';

        $page = array_merge($page, prepare_menu($operator));

        $page['tabs'] = $this->buildTabs($request);

        return $this->render('style_preview', $page);
    }

    /**
     * Builds list of the styles tabs.
     *
     * @param Request $request Current request.
     * @return array Tabs list. The keys of the array are tabs titles and the
     *   values are tabs URLs.
     */
    protected function buildTabs(Request $request)
    {
        $tabs = array();
        $type = $request->attributes->get('type');

        $tabs[getlocal("Operator pages themes preview")] = ($type != self::TYPE_PAGE)
            ? $this->generateUrl('style_preview', array('type' => self::TYPE_PAGE))
            : '';

        $tabs[getlocal("Chat themes preview")] = ($type != self::TYPE_CHAT)
            ? $this->generateUrl('style_preview', array('type' => self::TYPE_CHAT))
            : '';

        if (Settings::get('enabletracking')) {
            $tabs[getlocal("Invitation themes preview")] = ($type != self::TYPE_INVITATION)
                ? $this->generateUrl('style_preview', array('type' => self::TYPE_INVITATION))
                : '';
        }

        return $tabs;
    }

    /**
     * Builds a list of screenshots.
     *
     * @param StyleInterface $style A style for which screenshots list should be
     *   built.
     * @return array List of available screenshots. Each element is an
     *   associative array with the following keys:
     *     - "name": string, name of the screenshot.
     *     - "file": string, URL of the screenshot.
     *     - "description" string, screenshots description.
     */
    protected function buildScreenshotList(StyleInterface $style)
    {
        $base_path = $style->getFilesPath() . '/screenshots';
        $style_config = $style->getConfigurations();

        $screenshots = array();
        foreach ($style_config['screenshots'] as $name => $desc) {
            $screenshots[] = array(
                'name' => $name,
                'file' => $this->asset($base_path . '/' . $name . '.png'),
                'description' => $desc,
            );
        }

        return $screenshots;
    }

    /**
     * Resolves style class name by style type.
     *
     * @param string $style_type Type of the style. Can be one of
     *   StyleController::TYPE_* constants.
     * @return string Name of the style class
     */
    protected function resolveClassName($style_type)
    {
        switch ($style_type) {
            case self::TYPE_CHAT:
                $class_name = '\\Mibew\\Style\\ChatStyle';
                break;
            case self::TYPE_INVITATION:
                $class_name = '\\Mibew\\Style\\InvitationStyle';
                break;
            case self::TYPE_PAGE:
                $class_name = '\\Mibew\\Style\\PageStyle';
                break;
            default:
                throw new \RuntimeException('Style type cannot be resolved.');
                break;
        }

        return $class_name;
    }
}
