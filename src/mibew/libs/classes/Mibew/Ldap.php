<?php
/**
 * Date: 05.07.2015
 * Time: 11:25
 */

namespace Mibew;

use adLDAP\adLDAP;

/**
 * Encapsulates work with Active Directory by the LDAP. Implements singleton pattern to provide only
 * one instance.
 */
class Ldap {
    /**
     * An instance of Ldap class
     * @var Ldap
     */
    protected static $instance = null;

    /**
     * Account suffix by default ("@mydomain.local")
     * @var string
     */
    protected $account_suffix = null;

    /**
     * Base DN ("DC=mydomain,DC=local")
     * @var string
     */
    protected $base_dn = null;

    /**
     * Array of domain controllers names (["dc1.mydomain.local"])
     * @var Array
     */
    protected $domain_controllers = null;

    /**
     * Name of AD group with operators
     * @var string
     */
    protected $operator_group = null;

    /**
     * Name of AD group with administrators
     * @var string
     */
    protected $admin_group = null;

    /**
     * User login for search
     * @var string
     */
    protected $admin_username = null;

    /**
     * User password for search
     * @var string
     */
    protected $admin_password = null;

    /**
     * Port for connect
     * @var integer
     */
    protected $port = null;

    /**
     * @var bool
     */
    protected $useSSL = false;

    /**
     * @var bool
     */
    protected $useTLS = false;

    /**
     * Object adLDAP
     * @var adLDAP
     */
    protected $adLDAP = null;

    /**
     * Controls if exception must be processed into class or thrown
     * @var boolean
     */
    protected $useExceptions = false;

    /**
     * Get instance of Ldap class.
     *
     * If no instance exists, creates new instance.
     * Use Ldap::initialize() before try to get an instance. If LDAP
     * was not initilized coorectly, triggers an error with E_USER_ERROR level.
     *
     * @return Ldap
     * @see Ldap::initialize()
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            trigger_error('LDAP was not initialized correctly', E_USER_ERROR);
        }

        return self::$instance;
    }

    /**
     * Destroy internal Ldap object
     */
    public static function destroy()
    {
        if (!is_null(self::$instance)) {
            self::$instance->__destruct();
            self::$instance = null;
        }
    }

    /**
     * Initialize Ldap
     *
     * Set internal LDAP connection and properties. Create Ldap object.
     *
     * @param string $account_suffix Account suffix by default ("@mydomain.local")
     * @param string $base_dn Base DN ("DC=mydomain,DC=local")
     * @param array $domain_controllers Array of domain controllers names (["dc1.mydomain.local"])
     * @param string $operator_group Name of AD group, which belongs operators
     * @param string $admin_group Name of AD group, which belongs administrators
     * @param string $admin_username Name of admin user for search
     * @param string $admin_password Password of admin user for search
     * @param integer $port Port number to connect. Use default if null
     * @param bool $useSSL
     * @param bool $useTLS
     */
    public static function initialize($domain, $account_suffix, $base_dn, $domain_controllers, $operator_group, $admin_group,
                                      $admin_username, $admin_password, $port, $useSSL, $useTLS)
    {
        // Check if initialization
        if (!is_null(self::$instance)) {
            throw new \Exception('LDAP already initialized');
        }

        // Create LDAP instance
        $instance = new Ldap();

        // Set database and connection properties
        $instance->account_suffix = $account_suffix ?: "@" . $domain;
        $instance->domain_controllers = $domain_controllers ?: [$domain];
        $instance->base_dn = $base_dn;
        if (is_null($base_dn)) {
            $arr = explode(".", $domain);
            for($i = 0; $i < count($arr); $i++) {
                $arr[$i] = "DC=" . $arr[$i];
            }
            $instance->base_dn = implode(",", $arr);
        }
        $instance->operator_group = $operator_group;
        $instance->admin_group = $admin_group;
        $instance->admin_username = $admin_username;
        $instance->admin_password = $admin_password;
        $instance->port = $port;
        $instance->useSSL = $useSSL ?: false;
        $instance->useTLS = $useTLS ?: false;

        $ldap_params = [
            "account_suffix" => $instance->account_suffix,
            "base_dn" => $instance->base_dn,
            "domain_controllers" => $instance->domain_controllers,
            "admin_username" => $instance->admin_username,
            "admin_password" => $instance->admin_password,
            "use_ssl" => $instance->useSSL,
            "use_tls" => $instance->useTLS
        ];

        if (!is_null($port)) {
            $ldap_params['ad_port'] = $port;
        }

        // Create adLDAP object
        $instance->adLDAP = new adLDAP($ldap_params);

        // Store instance
        self::$instance = $instance;

        // Check groups
        if (is_null($operator_group)) {
            throw new \Exception('Operator group is not specified');
        }
        if (is_null($admin_group)) {
            throw new \Exception('Admin group is not specified');
        }
        if (!$instance->adLDAP->group()->info($operator_group,["name"])) {
            throw new \Exception('Operator group ' . $operator_group . ' was not found in AD');
        }
        if (!$instance->adLDAP->group()->info($admin_group,["name"])) {
            throw new \Exception('Admin group ' . $admin_group . ' was not found in AD');
        }
    }

    /**
     * Checks if the LDAP was initialized correctly.
     *
     * @return boolean True if the LDAP was initialized correctly and false
     *   otherwise.
     */
    public static function isInitialized()
    {
        return !is_null(self::$instance);
    }

    /**
     * Database class destructor.
     */
    public function __destruct()
    {
        $this->adLDAP = null;
        self::$instance = null;
    }

    /**
     * Forbid external object creation
     */
    protected function __construct()
    {
    }

    /**
     * Forbid clone objects
     */
    final private function __clone()
    {
    }

    /**
     * Handles errors
     * @param \Exception $e
     */
    protected function handleError(\Exception $e)
    {
        if ($this->useExceptions) {
            throw $e;
        }
        die($e->getMessage());
    }

    /**
     * Authenticate user with password in the Active Direcory by the LDAP
     *
     * @param string $username login of user in AD
     * @param string $password user password
     * @return bool authenticate result
     */
    public function authenticate($username, $password) {
        try {
            return $this->adLDAP->authenticate($username, $password);
        } catch (\Exception $e) {
            $this->handleError($e);
            return null;
        }
    }

    public function searchForOperator($username) {
        $users = $this->adLDAP->search(false, $username);
        foreach ($users as $userNameFound) {
            if ( (strcasecmp($userNameFound, $username) == 0)
                &&  ( $this->adLDAP->user()->inGroup($userNameFound,$this->operator_group,true)
                   || $this->adLDAP->user()->inGroup($userNameFound,$this->admin_group,true) )) {
                return $userNameFound;
            }
        }
        return false;
    }

    public function userIsAdmin($username) {
        return $this->adLDAP->user()->inGroup($username,$this->admin_group,true);
    }

    public function getOperatorData($username) {
        $infoList = $this->adLDAP->user()->info($username, ["samaccountname", "displayname", "mail", "thumbnailPhoto", "jpegPhoto"]);
        if (!$infoList) return false;
        if ($infoList['count'] < 1 ) return false;
        foreach ($infoList as $idx => $info) {
            if (strcasecmp($info['samaccountname'][0], $username) == 0) {
                $commonname = in_array("displayname", $info, true) ? $info["displayname"][0] : null;
                $email = in_array("mail", $info, true) ? $info["mail"][0] : null;
                $avatar = in_array("jpegphoto", $info, true) ? $info["jpegphoto"][0] : null;
                $avatar = is_null($avatar) && in_array("thumbnailphoto", $info, true) ? $info["thumbnailphoto"][0] : $avatar;
                return array(
                    'commonname' => $commonname,
                    'email' => $email,
                    'avatar' => $avatar
                );
            }
        }
        return false;
    }
}