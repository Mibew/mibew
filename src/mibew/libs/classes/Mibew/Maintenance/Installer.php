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

use Mibew\Database;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Encapsulates installation process.
 */
class Installer
{
    /**
     * Minimal PHP version Mibew Messenger works with.
     */
    const MIN_PHP_VERSION = 50400;

    /**
     * Associative array of system configs.
     *
     * @var array
     */
    protected $configs = null;

    /**
     * List of errors.
     *
     * @var string[]
     */
    protected $errors = array();

    /**
     * List of log messages.
     *
     * @var string[]
     */
    protected $log = array();

    /**
     * An instance of YAML parser.
     *
     * @var Symfony\Component\Yaml\Parser
     */
    protected $parser = null;

    /**
     * Class constructor.
     *
     * @param array $system_configs Associative array of system configs.
     */
    public function __construct($system_configs)
    {
        $this->configs = $system_configs;
        $this->parser = new YamlParser();
    }

    /**
     * Returns list of all errors that took place during installation process.
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Returns list of all information messages.
     *
     * @return string[]
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Checks if Mibew Messenger is already installed or not.
     *
     * @return boolean
     */
    public function isInstalled()
    {
        return ($this->getDatabaseVersion() !== false);
    }

    /**
     * Checks installation requirements.
     *
     * It is one of the installation steps. Normally it should be called the
     * first one.
     *
     * One can get all logged messages of this step using
     * {@link Installer::getLog()} method. Also the list of all errors can be
     * got using {@link \Mibew\Installer::getErrors()}.
     *
     * @return boolean True if all reqirements are satisfied and false otherwise
     */
    public function checkRequirements()
    {
        if (!$this->checkPhpVersion()) {
            return false;
        }
        $this->log[] = getlocal(
            'PHP version {0}',
            array(Utils::formatVersionId($this->getPhpVersionId()))
        );

        if (!$this->checkPhpExtensions()) {
            return false;
        }
        $this->log[] = getlocal('All necessary PHP extensions are loaded');

        if (!$this->checkFsPermissions()) {
            return false;
        }
        $this->log[] = getlocal('Directories permissions are correct');

        return true;
    }

    /**
     * Checks database connection and MySQL version.
     *
     * It is one of the installation steps. Normally it should be called after
     * {@link Installer::checkRequirements()}.
     *
     * One can get all logged messages of this step using
     * {@link Installer::getLog()} method. Also the list of all errors can be
     * got using {@link \Mibew\Installer::getErrors()}.
     *
     * @return boolean True if connection is established and false otherwise.
     */
    public function checkConnection()
    {
        if (!$this->doCheckConnection()) {
            return false;
        }

        if (!$this->checkMysqlVersion()) {
            return false;
        }

        $this->log[] = getlocal(
            'You are connected to MySQL server version {0}',
            array($this->getMysqlVersion())
        );

        return true;
    }

    /**
     * Create tables and prepopulate them with some info.
     *
     * It is one of the installation steps. Normally it should be called after
     * {@link Installer::checkConnection()}.
     *
     * One can get all logged messages of this step using
     * {@link Installer::getLog()} method. Also the list of all errors can be
     * got using {@link \Mibew\Installer::getErrors()}.
     *
     * @return boolean True if all tables are created and false otherwise.
     */
    public function createTables()
    {
        if ($this->tablesExist() && $this->tablesNeedUpdate()) {
            // Tables already exists but they should be updated
            $this->errors[] = getlocal('The tables are alredy in place but outdated. Run the updater to fix it.');

            return false;
        }

        if ($this->tablesExist()) {
            $this->log[] = getlocal('Tables structure is up to date.');

            return true;
        }

        // There are no tables in the database. We need to create them.
        if (!$this->doCreateTables()) {
            return false;
        }
        $this->log[] = getlocal('Tables are created.');

        if (!$this->prepopulateDatabase()) {
            return false;
        }
        $this->log[] = getlocal('Tables are pre popluated with necessary info.');

        return true;
    }

    /**
     * Sets password of the main administrator of the system.
     *
     * It is one of the installation steps. Normally it should be called after
     * {@link Installer::createTables()}.
     *
     * One can get all logged messages of this step using
     * {@link Installer::getLog()} method. Also the list of all errors can be
     * got using {@link \Mibew\Installer::getErrors()}.
     *
     * @param string $password Administrator password.
     * @return boolean True if the password was set and false otherwise.
     */
    public function setPassword($password)
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $db->query(
                'UPDATE {operator} SET vcpassword = :pass WHERE vclogin = :login',
                array(
                    ':login' => 'admin',
                    ':pass' => calculate_password_hash('admin', $password)
                )
            );
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot set password. Error: {0}',
                array($e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Import locales and all their content to the database.
     *
     * It is one of the installation steps. Normally it should be called after
     * {@link Installer::createTables()}.
     *
     * One can get all logged messages of this step using
     * {@link Installer::getLog()} method. Also the list of all errors can be
     * got using {@link \Mibew\Installer::getErrors()}.
     *
     * @return boolean True if all locales with content are imported
     *   successfully and false otherwise.
     */
    public function importLocales()
    {
        if (!$this->doImportLocales()) {
            return false;
        }
        $this->log[] = getlocal('Locales are imported.');

        if (!$this->importLocalesContent()) {
            return false;
        }
        $this->log[] = getlocal('Locales content is imported.');

        return true;
    }

    /**
     * Creates necessary tables.
     *
     * @return boolean Indicates if tables created or not.
     */
    protected function doCreateTables()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            // Create tables according to database schema
            $schema = $this->getDatabaseSchema();
            foreach ($schema as $table => $table_structure) {
                $table_items = array();

                // Add fields
                foreach ($table_structure['fields'] as $field => $definition) {
                    $table_items[] = sprintf('%s %s', $field, $definition);
                }

                // Add indexes
                if (!empty($table_structure['indexes'])) {
                    foreach ($table_structure['indexes'] as $index => $fields) {
                        $table_items[] = sprintf(
                            'INDEX %s (%s)',
                            $index,
                            implode(', ', $fields)
                        );
                    }
                }

                // Add unique keys
                if (!empty($table_structure['unique_keys'])) {
                    foreach ($table_structure['unique_keys'] as $key => $fields) {
                        $table_items[] = sprintf(
                            'UNIQUE KEY %s (%s)',
                            $key,
                            implode(', ', $fields)
                        );
                    }
                }

                $db->query(sprintf(
                    'CREATE TABLE IF NOT EXISTS {%s} (%s) charset utf8 ENGINE=InnoDb',
                    $table,
                    implode(', ', $table_items)
                ));
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot create tables. Error: {0}',
                array($e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Saves some necessary data in the database.
     *
     * This method is called just once after tables are created.
     *
     * @return boolean Indicates if the data are saved to the database or not.
     */
    protected function prepopulateDatabase()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        // Create The First Administrator if needed
        try {
            list($count) = $db->query(
                'SELECT COUNT(*) FROM {operator} WHERE vclogin = :login',
                array(':login' => 'admin'),
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM
                )
            );
            if ($count == 0) {
                $db->query(
                    ('INSERT INTO {operator} ( '
                            . 'vclogin, vcpassword, vclocalename, vccommonname, '
                            . 'vcavatar, vcemail, iperm '
                        . ') values ( '
                            . ':login, :pass, :local_name, :common_name, '
                            . ':avatar, :email, :permissions)'),
                    array(
                        ':login' => 'admin',
                        ':pass' => md5(''),
                        ':local_name' => 'Administrator',
                        ':common_name' => 'Administrator',
                        ':avatar' => '',
                        ':email' => '',
                        ':permissions' => 65535,
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot create the first administrator. Error {0}',
                array($e->getMessage())
            );

            return false;
        }

        // Initialize chat revision counter if it is needed
        try {
            list($count) = $db->query(
                'SELECT COUNT(*) FROM {revision}',
                null,
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM
                )
            );
            if ($count == 0) {
                $db->query(
                    'INSERT INTO {revision} VALUES (:init_revision)',
                    array(':init_revision' => 1)
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot initialize chat revision sequence. Error {0}',
                array($e->getMessage())
            );

            return false;
        }

        // Set correct database structure version if needed
        try {
            list($count) = $db->query(
                'SELECT COUNT(*) FROM {config} WHERE vckey = :key',
                array(':key' => 'dbversion'),
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM
                )
            );
            if ($count == 0) {
                $db->query(
                    'INSERT INTO {config} (vckey, vcvalue) VALUES (:key, :value)',
                    array(
                        ':key' => 'dbversion',
                        ':value' => MIBEW_VERSION,
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot store database structure version. Error {0}',
                array($e->getMessage())
            );

            return false;
        }

        // Generate Unique ID for Mibew Messenger Instance
        try {
            list($count) = $db->query(
                'SELECT COUNT(*) FROM {config} WHERE vckey = :key',
                array(':key' => '_instance_id'),
                array(
                    'return_rows' => Database::RETURN_ONE_ROW,
                    'fetch_type' => Database::FETCH_NUM,
                )
            );

            if ($count == 0) {
                $db->query(
                    'INSERT INTO {config} (vckey, vcvalue) VALUES (:key, :value)',
                    array(
                        ':key' => '_instance_id',
                        ':value' => Utils::generateInstanceId(),
                    )
                );
            } else {
                // The option is already in the database. It seems that
                // something went wrong with the previous installation attempt.
                // Just update the instance ID.
                $db->query(
                    'UPDATE {config} SET vcvalue = :value WHERE vckey = :key',
                    array(
                        ':key' => '_instance_id',
                        ':value' => Utils::generateInstanceId(),
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot store instance ID. Error {0}',
                array($e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Checks database connection.
     *
     * @return boolean True if connection is established and false otherwise.
     */
    protected function doCheckConnection()
    {
        if (!$this->getDatabase()) {
            return false;
        }

        return true;
    }

    /**
     * Checks if PHP version is high enough to run Mibew.
     *
     * @return boolean True if PHP version is suitable and false otherwise.
     */
    protected function checkPhpVersion()
    {
        $current_version = $this->getPhpVersionId();

        if ($current_version < self::MIN_PHP_VERSION) {
            $this->errors[] = getlocal(
                "PHP version is {0}, but Mibew Messenger works with {1} and later versions.",
                array(
                    Utils::formatVersionId($current_version),
                    Utils::formatVersionId(self::MIN_PHP_VERSION)
                )
            );

            return false;
        }

        return true;
    }

    /**
     * Checks that all necessary PHP extensions are loaded.
     *
     * @return boolean True if all necessary extensions are loaded and false
     *   otherwise.
     */
    protected function checkPhpExtensions()
    {
        $extensions = array('PDO', 'pdo_mysql', 'gd', 'curl', 'mbstring');

        foreach ($extensions as $ext) {
            if (!extension_loaded($ext)) {
                $this->errors[] = getlocal('PHP {0} extension is not loaded', array($ext));

                return false;
            }
        }

        return true;
    }

    /**
     * Checks if files and directories permissions are correct.
     *
     * @return boolean True if all permissions are correct and false otherwise.
     */
    protected function checkFsPermissions()
    {
        // Check cache directory
        if (!is_writable(MIBEW_FS_ROOT . '/cache')) {
            $this->errors[] = getlocal(
                'Cache directory "{0}" is not writable.',
                array('cache/')
            );

            return false;
        }

        // Check avatars directory
        if (!is_writable(MIBEW_FS_ROOT . '/files/avatar')) {
            $this->errors[] = getlocal(
                'Avatars directory "{0}" is not writable.',
                array('files/avatar/')
            );

            return false;
        }

        return true;
    }

    /**
     * Checks if MySQL version is high enough or not to run Mibew.
     *
     * @return boolean True if MySQL version is suitable and false otherwise.
     * @todo Add real version check.
     */
    protected function checkMysqlVersion()
    {
        // At the moment minimal MySQL version is unknown. One should find
        // it out and replace the following with a real check.
        return ($this->getMysqlVersion() !== false);
    }

    /**
     * Returns current PHP version ID.
     *
     * For example, for PHP 5.3.3 the number 50303 will be returned.
     *
     * @return integer Version ID.
     */
    protected function getPhpVersionId()
    {
        // PHP_VERSION_ID is available as of PHP 5.2.7 so we need to use
        // workaround for lower versions.
        return defined('PHP_VERSION_ID') ? PHP_VERSION_ID : 0;
    }

    /**
     * Returns current MySQL server version.
     *
     * @return string|boolean Current MySQL version or boolean false it it
     *   cannot be determined.
     */
    protected function getMysqlVersion()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $result = $db->query(
                "SELECT VERSION() as c",
                null,
                array('return_rows' => Database::RETURN_ONE_ROW)
            );
        } catch (\Exception $e) {
            return false;
        }

        return $result['c'];
    }

    /**
     * Gets version of existing database structure.
     *
     * If Mibew Messenger is not installed yet boolean false will be returned.
     *
     * @return string|boolean Database structure version or boolean false if the
     *   version cannot be determined.
     */
    protected function getDatabaseVersion()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $result = $db->query(
                "SELECT vcvalue AS version FROM {config} WHERE vckey = :key LIMIT 1",
                array(':key' => 'dbversion'),
                array('return_rows' => Database::RETURN_ONE_ROW)
            );
        } catch (\Exception $e) {
            return false;
        }

        if (!$result) {
            // It seems that database structure version isn't stored in the
            // database.
            return '0.0.0';
        }

        return $result['version'];
    }

    /**
     * Checks if the database structure must be updated.
     *
     * @return boolean
     */
    protected function tablesNeedUpdate()
    {
        return version_compare($this->getDatabaseVersion(), MIBEW_VERSION, '<');
    }

    /**
     * Checks if database structure is already created.
     *
     * @return boolean
     */
    protected function tablesExist()
    {
        return ($this->getDatabaseVersion() !== false);
    }

    /**
     * Import all available locales to the database.
     *
     * @return boolean Indicates if the locales were imported correctly. True if
     *   everything is OK and false otherwise.
     */
    protected function doImportLocales()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $rows = $db->query(
                'SELECT code FROM {locale}',
                null,
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );
            $exist_locales = array();
            foreach ($rows as $row) {
                $exist_locales[] = $row['code'];
            }

            $fs_locales = discover_locales();
            foreach ($fs_locales as $locale) {
                if (in_array($locale, $exist_locales)) {
                    // Do not create locales twice.
                    continue;
                }

                $db->query(
                    'INSERT INTO {locale} (code, enabled) values (:code, :enabled)',
                    array(
                        ':code' => $locale,
                        // Mark the locale as disabled to indicate that it's
                        // content is not imported yet.
                        ':enabled' => 0,
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot import locales. Error: {0}',
                array($e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Import locales content, namely translations, canned messages and mail
     * templates.
     *
     * When the content will be imported the locale will be marked as enabled.
     * @return boolean True if all content was imported successfully and false
     *   otherwise.
     */
    protected function importLocalesContent()
    {
        if (!($db = $this->getDatabase())) {
            return false;
        }

        try {
            $locales = $db->query(
                'SELECT * FROM {locale} WHERE enabled = :enabled',
                array(':enabled' => 0),
                array('return_rows' => Database::RETURN_ALL_ROWS)
            );

            foreach ($locales as $locale_info) {
                $locale = $locale_info['code'];

                // Import translations, formats, mail templates, ...
                import_locale_content($locale);

                // Mark the locale as "enabled" to indicate that all its content
                // is imported.
                $db->query(
                    'UPDATE {locale} SET enabled = :enabled WHERE code = :locale',
                    array(
                        ':locale' => $locale,
                        ':enabled' => 1,
                    )
                );
            }
        } catch (\Exception $e) {
            $this->errors[] = getlocal(
                'Cannot import locales content. Error: {0}',
                array($e->getMessage())
            );

            return false;
        }

        return true;
    }

    /**
     * Returns initialized database object.
     *
     * @return \Mibew\Database|boolean A database class instance or boolean
     *   false if something went wrong.
     */
    protected function getDatabase()
    {
        if (!Database::isInitialized()) {
            try {
                Database::initialize(
                    $this->configs['database']['host'],
                    $this->configs['database']['port'],
                    $this->configs['database']['login'],
                    $this->configs['database']['pass'],
                    $this->configs['database']['use_persistent_connection'],
                    $this->configs['database']['db'],
                    $this->configs['database']['tables_prefix']
                );
            } catch (\PDOException $e) {
                $this->errors[] = getlocal(
                    "Could not connect. Please check server settings in config.yml. Error: {0}",
                    array($e->getMessage())
                );

                return false;
            }
        }

        $db = Database::getInstance();
        $db->throwExceptions(true);

        return $db;
    }

    /**
     * Loads database schema.
     *
     * @return array Associative array of database schema. Each key of the array
     *   is a table name and each value is its description. Table array itself
     *   is an associative array with the following keys:
     *     - fields: An associative array, which keys are MySQL columns names
     *       and values are columns definitions.
     *     - unique_keys: An associative array. Each its value is a name of a
     *       table's unique key. Each value is an array with names of the
     *       columns the key is based on.
     *     - indexes: An associative array. Each its value is a name of a
     *       table's index. Each value is an array with names of the
     *       columns the index is based on.
     */
    protected function getDatabaseSchema()
    {
        return $this->parser->parse(file_get_contents(MIBEW_FS_ROOT . '/configs/database_schema.yml'));
    }

    /**
     * Loads available canned messages for specified locale.
     *
     * @param string $locale Locale code.
     * @return string[]|boolean List of canned messages boolean false if
     *   something went wrong.
     */
    protected function getCannedMessages($locale)
    {
        $file_path = MIBEW_FS_ROOT . '/locales/' . $locale . '/canned_messages.yml';
        if (!is_readable($file_path)) {
            return false;
        }
        $messages = $this->parser->parse(file_get_contents($file_path));

        return $messages ? $messages : false;
    }

    /**
     * Loads available mail templates for the specified locale.
     *
     * @param string $locale Locale code.
     * @return array|boolean List of mail template arrays or boolean false if
     *   something went wrong.
     */
    protected function getMailTemplates($locale)
    {
        $file_path = MIBEW_FS_ROOT . '/locales/' . $locale . '/mail_templates.yml';
        if (!is_readable($file_path)) {
            return false;
        }
        $templates = $this->parser->parse(file_get_contents($file_path));

        return $templates ? $templates : false;
    }
}
