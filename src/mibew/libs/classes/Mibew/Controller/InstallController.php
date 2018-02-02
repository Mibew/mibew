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

use Mibew\Maintenance\Installer;
use Mibew\Style\PageStyle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    const STEP_CHECK_REQUIREMENTS = 0;
    const STEP_CHECK_CONNECTION = 1;
    const STEP_CREATE_TABLES = 2;
    const STEP_SET_PASSWORD = 3;
    const STEP_IMPORT_LOCALES = 4;
    const STEP_DONE = 5;

    /**
     * Redirects the user to the current installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function indexAction(Request $request)
    {
        // Check if Mibew Messenger is already installed.
        $in_progress = !empty($_SESSION[SESSION_PREFIX . 'installation_in_progress']);
        if (!$in_progress) {
            if ($this->getInstaller()->isInstalled()) {
                // The system is already installed.
                return new Response(
                    $this->renderError(
                        'install_err',
                        array('errors' => array(getlocal('The system is already installed!')))
                    ),
                    403
                );
            }

            // The system is not installed. Mark the user to know that he starts
            // installation.
            $_SESSION[SESSION_PREFIX . 'installation_in_progress'] = true;
            $this->setCurrentStep(self::STEP_CHECK_REQUIREMENTS);
        }

        // Run an installation step.
        return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
    }

    /**
     * Renders a page for "Check requirements" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function checkRequirementsAction(Request $request)
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_CHECK_REQUIREMENTS) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_CHECK_REQUIREMENTS) {
            $this->setCurrentStep(self::STEP_CHECK_REQUIREMENTS);
        }

        $installer = $this->getInstaller();
        if (!$installer->checkRequirements()) {
            return $this->renderStep(
                'install_step',
                array('errors' => $installer->getErrors())
            );
        }

        // Everything is fine. Log the messages and go to the next step
        $this->setLog(self::STEP_CHECK_REQUIREMENTS, $installer->getLog());
        $this->setCurrentStep(self::STEP_CHECK_CONNECTION);

        return $this->renderStep(
            'install_step',
            array('nextstep' => getlocal('Check database connection'))
        );
    }

    /**
     * Renders a page for "Check connection" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function checkConnectionAction(Request $request)
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_CHECK_CONNECTION) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_CHECK_CONNECTION) {
            $this->setCurrentStep(self::STEP_CHECK_CONNECTION);
        }

        $installer = $this->getInstaller();
        if (!$installer->checkConnection()) {
            return $this->renderStep(
                'install_step',
                array('errors' => $installer->getErrors())
            );
        }

        // Everything is fine. Go to the next step.
        $this->setLog(self::STEP_CHECK_CONNECTION, $installer->getLog());
        $this->setCurrentStep(self::STEP_CREATE_TABLES);

        return $this->renderStep(
            'install_step',
            array('nextstep' => getlocal('Create necessary tables'))
        );
    }

    /**
     * Renders a page for "Create tables" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function createTablesAction(Request $request)
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_CREATE_TABLES) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_CREATE_TABLES) {
            $this->setCurrentStep(self::STEP_CREATE_TABLES);
        }

        $installer = $this->getInstaller();
        if (!$installer->createTables()) {
            return $this->renderStep(
                'install_step',
                array('errors' => $installer->getErrors())
            );
        }

        $this->setLog(self::STEP_CREATE_TABLES, $installer->getLog());
        $this->setCurrentStep(self::STEP_SET_PASSWORD);

        return $this->renderStep(
            'install_step',
            array('nextstep' => getlocal('Set administrator password'))
        );
    }

    /**
     * Renders a page for "Set password" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function showPasswordFormAction(Request $request)
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_SET_PASSWORD) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_SET_PASSWORD) {
            $this->setCurrentStep(self::STEP_SET_PASSWORD);
        }

        return $this->renderStep(
            'install_password',
            array(
                'errors' => $request->attributes->get('errors', array()),
            )
        );
    }

    /**
     * Processes submitting of password form.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function submitPasswordFormAction(Request $request)
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() != self::STEP_SET_PASSWORD) {
            $this->redirect($this->generateStepUrl(self::STEP_SET_PASSWORD));
        }

        $password = $request->request->get('password');
        $password_confirm = $request->request->get('password_confirm');
        $errors = array();

        // Validate passwords
        if (!$password) {
            $errors[] = no_field('Password');
        }
        if (!$password_confirm) {
            $errors[] = no_field('Confirmation');
        }
        if ($password !== $password_confirm) {
            $errors[] = getlocal('Passwords do not match.');
        }
        if (!empty($errors)) {
            // Something went wrong we should rerender the form.
            $request->attributes->set('errors', $errors);

            return $this->showPasswordFormAction($request);
        }

        $installer = $this->getInstaller();
        if (!$installer->setPassword($password)) {
            return $this->renderStep(
                'install_step',
                array('errors' => $installer->getErrors())
            );
        }

        $this->setLog(
            self::STEP_SET_PASSWORD,
            array(getlocal('Password is set.'))
        );
        $this->setCurrentStep(self::STEP_IMPORT_LOCALES);

        return $this->renderStep(
            'install_step',
            array('nextstep' => getlocal('Import locales'))
        );
    }

    /**
     * Renders a page for "Import locales" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function importLocalesAction()
    {
        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_IMPORT_LOCALES) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_IMPORT_LOCALES) {
            $this->setCurrentStep(self::STEP_IMPORT_LOCALES);
        }

        $installer = $this->getInstaller();
        if (!$installer->importLocales()) {
            return $this->renderStep(
                'install_step',
                array('errors' => $installer->getErrors())
            );
        }

        $this->setLog(self::STEP_IMPORT_LOCALES, $installer->getLog());
        $this->setCurrentStep(self::STEP_DONE);

        return $this->renderStep(
            'install_step',
            array('nextstep' => getlocal('Check sound and lock the installation'))
        );
    }

    /**
     * Renders a page for "Done" installation step.
     *
     * @param Request $request Incoming request.
     * @return Response
     */
    public function doneAction(Request $request)
    {
        if (empty($_SESSION[SESSION_PREFIX . 'installation_in_progress'])) {
            // The installation has been finished (or had not been started yet)
            // We should prevent access to this action but cannot use Access
            // Check functionallity becase the user should be redirected to the
            // beginning.
            return $this->redirect($this->generateUrl('install'));
        }

        // Check if the user can run this step
        if ($this->getCurrentStep() < self::STEP_DONE) {
            return $this->redirect($this->generateStepUrl($this->getCurrentStep()));
        } elseif ($this->getCurrentStep() != self::STEP_DONE) {
            $this->setCurrentStep(self::STEP_DONE);
        }

        // The installation is done.
        unset($_SESSION[SESSION_PREFIX . 'installation_in_progress']);

        // We need to manually change front controller and use normal
        // application's entry point.
        $login_url = str_replace(
            'install.php',
            'index.php',
            $this->generateUrl('login', array('login' => 'admin'))
        );

        $login_link = getlocal(
            'You can login to using <a href="{0}">this</a> link.',
            array($login_url)
        );

        $this->getAssetManager()->attachJs('js/compiled/soundcheck.js');

        return $this->renderStep(
            'install_done',
            array('loginLink' => $login_link)
        );
    }

    /**
     * Renders installation step page.
     *
     * It is just a wrapper for {@link AbstractController::render()} method
     * which adds several default values to $parameters array.
     *
     * @param string $template Name of the template which should be rendered
     * @param array $parameters List of values that should be passed to the
     *   template.
     * @return string Rendered page content
     */
    protected function renderStep($template, array $parameters = array())
    {
        // Add default values
        $parameters += array(
            'version' => MIBEW_VERSION,
            'localeLinks' => get_locale_links(),
            'fixedwrap' => true,
            'title' => getlocal('Installation'),
            'done' => $this->getLog(),
            'error' => array(),
            'nextstep' => false,
            'nextstepurl' => $this->generateStepUrl($this->getCurrentStep()),
            'nextnotice' => false,
        );

        return $this->render($template, $parameters);
    }

    /**
     * Renders installation error page.
     *
     * It is just a wrapper for {@link AbstractController::render()} method
     * which adds several default values to $parameters array.
     *
     * @param string $template Name of the template which should be rendered
     * @param array $parameters List of values that should be passed to the
     *   template.
     * @return string Rendered page content
     */
    protected function renderError($template, array $parameters = array())
    {
        // Add default values
        $parameters += array(
            'version' => MIBEW_VERSION,
            'localeLinks' => get_locale_links(),
            'title' => getlocal('Problem'),
            'fixedwrap' => true,
        );

        return $this->render($template, $parameters);
    }

    /**
     * Returns log messages for all steps excluding current.
     *
     * @return string[] List of logged messages.
     */
    protected function getLog()
    {
        if (!isset($_SESSION[SESSION_PREFIX . 'installation_log'])) {
            return array();
        }

        $log = array();
        foreach ($_SESSION[SESSION_PREFIX . 'installation_log'] as $step => $messages) {
            if ($this->getCurrentStep() <= $step) {
                // Combine only messages for previous steps
                break;
            }
            $log = array_merge($log, $messages);
        }

        return $log;
    }

    /**
     * Sets log for the specified installation step.
     *
     * @param integer $step An installation step. One of
     *   InstallController::STEP_* constants.
     * @param string[] $messages List of logged messages.
     */
    protected function setLog($step, $messages)
    {
        $_SESSION[SESSION_PREFIX . 'installation_log'][$step] = $messages;
    }

    /**
     * Returns current step of the installation process.
     *
     * @return integer An installation step. One of InstallController::STEP_*
     *   constants.
     */
    protected function getCurrentStep()
    {
        // Set current step from the session.
        return $this->currentStep = isset($_SESSION[SESSION_PREFIX . 'installation_step'])
            ? $_SESSION[SESSION_PREFIX . 'installation_step']
            : self::STEP_CHECK_REQUIREMENTS;
    }

    /**
     * Sets the current installation step.
     *
     * @param integer $step An installation step. One of
     *   InstallController::STEP_* constants.
     */
    protected function setCurrentStep($step)
    {
        $_SESSION[SESSION_PREFIX . 'installation_step'] = $step;
    }

    /**
     * Generates URL for the specified installation step.
     *
     * @param integer $step An installation step. One of
     *   InstallController::STEP_* constants.
     * @return string An URL for the specified step.
     * @throws \InvalidArgumentException If the step is unknown.
     */
    protected function generateStepUrl($step)
    {
        $routes_map = array(
            self::STEP_CHECK_REQUIREMENTS => 'install_check_requirements',
            self::STEP_CHECK_CONNECTION => 'install_check_connection',
            self::STEP_CREATE_TABLES => 'install_create_tables',
            self::STEP_SET_PASSWORD => 'install_set_password',
            self::STEP_IMPORT_LOCALES => 'install_import_locales',
            self::STEP_DONE => 'install_done',
        );

        if (!array_key_exists($step, $routes_map)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown step "%s"',
                $step
            ));
        }

        return $this->generateUrl($routes_map[$step]);
    }

    /**
     * Initialize installer.
     *
     * @return \Mibew\Maintenance\Installer
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
            $this->style = $this->prepareStyle(new PageStyle('default'));
        }

        return $this->style;
    }
}
