<?php
/*
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

namespace Mibew\Controller;

use Mibew\Installer;
use Mibew\Style\PageStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * Process all pages related with installation.
 */
class InstallController extends AbstractController
{
    /**
     * An instance of Installer that is curently used by the controller.
     *
     * @var Installer
     */
    protected $installer = null;

    /**
     * The main entry point of installation process.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     */
    public function indexAction(Request $request)
    {
        $page = array(
            'version' => MIBEW_VERSION,
            'localeLinks' => get_locale_links(),
            'fixedwrap' => true,
            'title' => getlocal("Installation"),
        );

        $installer = $this->getInstaller();
        $state = $installer->install($request->getBasePath());
        $installation_error = $state == Installer::STATE_NEED_UPDATE_TABLES
            || $state == Installer::STATE_ERROR;

        if ($installation_error) {
            $page['title'] = getlocal('Problem');
            $page['no_right_menu'] = true;

            if ($state == Installer::STATE_NEED_UPDATE_TABLES) {
                // The installer should not update tables structure.
                $page['errors'] = array(
                    getlocal('Mibew is already installed and must be updated. Use the updater.')
                );
            } else {
                // Installer thinks that something went wrong. Believe it and
                // use its errors.
                $page['errors'] = $installer->getErrors();
            }

            return $this->render('install_err', $page);
        }

        $page['done'] = $installer->getLog();
        $page['errors'] = $installer->getErrors();

        if ($state == Installer::STATE_SUCCESS || $state == Installer::STATE_NEED_CHANGE_PASSWORD) {
            // Everything is ok. The installation is completed.
            $page['soundcheck'] = true;
            $page['done'][] = getlocal("Click to check the sound: {0} and {1}", array(
                "<a id='check-nv' href='javascript:void(0)'>" . getlocal("New Visitor") . "</a>",
                "<a id='check-nm' href='javascript:void(0)'>" . getlocal("New Message") . "</a>"
            ));
            $page['done'][] = getlocal("<b>Application installed successfully.</b>");

            if ($state == Installer::STATE_NEED_CHANGE_PASSWORD) {
                $notice = getlocal('You can logon as <b>admin</b> with empty password.')
                    . '<br /><br />'
                    . '<span class=\"warning\">'
                    . getlocal(
                        'For security reasons please change your password immediately and remove {0} file from your server.',
                        array(MIBEW_WEB_ROOT . '/install.php')
                    )
                    . '</span>';

                $page['nextstep'] = getlocal("Proceed to the login page");
                $page['nextnotice'] = $notice;
                $page['nextstepurl'] = $this->generateUrl('login', array('login' => 'admin'));
            }
        } elseif ($state == Installer::STATE_NEED_CREATE_TABLES) {
            // There is no tables in the database. We need to create them.
            $page['nextstep'] = getlocal("Create required tables.");
            $page['nextstepurl'] = $this->generateUrl('install_create_tables');
        } else {
            throw new \RuntimeException(
                sprintf('Unknown installer state "%s".', $state)
            );
        }

        return $this->render('install_index', $page);
    }

    /**
     * An action that create necessary database tables.
     *
     * @param Request $request Incoming request
     * @return string Rendered page content.
     */
    public function createTablesAction(Request $request)
    {
        $installer = $this->getInstaller();

        if (!$installer->createTables()) {
            // By some reasons tables cannot be created. Tell it to the user.
            return $this->render(
                'install_err',
                array(
                    'version' => MIBEW_VERSION,
                    'localeLinks' => get_locale_links(),
                    'title' => getlocal('Problem'),
                    'no_right_menu' => true,
                    'fixedwrap' => true,
                    'errors' => $installer->getErrors(),
                )
            );
        }

        // Tables are successfully created. Go back to the main installation
        // page.
        return $this->redirect($this->generateUrl('install'));
    }

    /**
     * Initialize installer.
     *
     * @return \Mibew\Installer
     */
    protected function getInstaller()
    {
        if (is_null($this->installer)) {
            $this->installer = new Installer(load_system_configs());
        }

        return $this->installer;
    }

    /**
     * {@inheritdoc}
     */
    protected function getStyle()
    {
        if (is_null($this->style)) {
            $this->style = new PageStyle('default');
        }

        return $this->style;
    }
}
