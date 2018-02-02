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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Mibew\Settings;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Name of the cookie to remember an operator
 */
define('REMEMBER_OPERATOR_COOKIE_NAME', 'mibew_operator');

/** Permissions constants */

/**
 * Operator can administer Mibew Messenger installation
 */
define('CAN_ADMINISTRATE', 0);

/**
 * Operator can take over threads
 */
define('CAN_TAKEOVER', 1);

/**
 * Operator can view threads of other operators
 */
define('CAN_VIEWTHREADS', 2);

/**
 * Operator can modify own profile
 */
define('CAN_MODIFYPROFILE', 3);

/**
 * Operator can view system statistics
 */
define('CAN_VIEWSTATISTICS', 4);


/** End of permissions constants */

/**
 * Map numerical permissions ids onto string names.
 * @return array Associative array whose keys are numerical permission ids and
 * values are string permission names.
 */
function permission_ids()
{
    return array(
        CAN_ADMINISTRATE => "admin",
        CAN_VIEWSTATISTICS => "statistics",
        CAN_TAKEOVER => "takeover",
        CAN_VIEWTHREADS => "viewthreads",
        CAN_MODIFYPROFILE => "modifyprofile",
    );
}

/**
 * Map numerical permissions ids onto its descriptions.
 *
 * The descriptions are localized.
 *
 * @return array Array whose keys are numerical permission ids and values are
 * localized permission descriptions.
 */
function permission_descriptions()
{
    return array(
        CAN_ADMINISTRATE => getlocal('System administration: settings, operators management, button generation'),
        CAN_VIEWSTATISTICS => getlocal('Ability to view system statistics'),
        CAN_TAKEOVER => getlocal('Take over chat thread'),
        CAN_VIEWTHREADS => getlocal('View another operator\'s chat thread'),
        CAN_MODIFYPROFILE => getlocal('Ability to modify profile'),
    );
}

/**
 * Set new permissions to operator
 * @param int $operator_id Operator ID
 * @param int $perm New permissions value
 */
function update_operator_permissions($operator_id, $perm)
{
    $operator = operator_by_id($operator_id);
    $operator['iperm'] = $perm;
    update_operator($operator);
}

function operator_by_login($login)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {operator} WHERE vclogin = ?",
        array($login),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

function operator_by_email($mail)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {operator} WHERE vcemail = ?",
        array($mail),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

function operator_by_id($id)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {operator} WHERE operatorid = ?",
        array($id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

/**
 * Load operator info by specified operators code
 *
 * @param string $code Operators code
 * @return array|boolean Operators info array or boolean false if there is no
 *   operator with specified code.
 */
function operator_by_code($code)
{
    $db = Database::getInstance();

    return $db->query(
        "SELECT * FROM {operator} WHERE code = ?",
        array($code),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );
}

/**
 * Get list of operators taking into account $options
 * @param array $options Associative array of options. It can contains following
 *   keys:
 *    - 'sort': an associative array of sorting options.
 *    - 'isolated_operator_id': id of current operators. If it set - function
 *       would return only operators from adjacent groups.
 *
 *   'sort' array must contains two keys: 'by' and 'desc'.
 *   'by' means the field by which operators would be sort and can take
 *   following values: 'commonname', 'localename', 'login', 'lastseen'. 'desc'
 *   means order in which operators would be sort. If it's 'true' operators
 *   would be sort in descending order and in ascending order overwise.
 *
 */
function get_operators_list($options = array())
{
    $db = Database::getInstance();

    if (!empty($options['sort']) && isset($options['sort']['by']) && isset($options['sort']['desc'])) {
        switch ($options['sort']['by']) {
            case 'commonname':
                $orderby = 'vccommonname';
                break;
            case 'localename':
                $orderby = 'vclocalename';
                break;
            case 'lastseen':
                $orderby = 'time';
                break;
            default:
                $orderby = 'vclogin';
                break;
        }
        $orderby = $orderby . ' ' . ($options['sort']['desc'] ? 'DESC' : 'ASC');
    } else {
        $orderby = "vclogin";
    }

    $query = "SELECT DISTINCT "
        . "{operator}.operatorid, "
        . "vclogin, "
        . "vclocalename, "
        . "vccommonname, "
        . "code, "
        . "istatus, "
        . "idisabled, "
        . "(:now - dtmlastvisited) AS time "
        . "FROM {operator}"
        . (empty($options['isolated_operator_id'])
            ? ""
            : ", {operatortoopgroup} "
                . "WHERE {operator}.operatorid = {operatortoopgroup}.operatorid "
                . "AND {operatortoopgroup}.groupid IN ("
                    . "SELECT g.groupid FROM {opgroup} g, "
                        . "(SELECT {opgroup}.groupid, {opgroup}.parent FROM {opgroup}, {operatortoopgroup} "
                        . "WHERE {opgroup}.groupid = {operatortoopgroup}.groupid "
                        . "AND {operatortoopgroup}.operatorid = :operatorid) i "
                    . "WHERE g.groupid = i.parent "
                        . "OR g.parent = i.groupid "
                        . "OR (g.parent = i.parent AND g.parent IS NOT NULL) "
                        . "OR g.groupid = i.groupid "
                . ")")
        . " ORDER BY " . $orderby;

    $values = array(
        ':now' => time(),
    );

    if (!empty($options['isolated_operator_id'])) {
        $values[':operatorid'] = $options['isolated_operator_id'];
    }

    $operators = $db->query(
        $query,
        $values,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    return $operators;
}

/*
 * Get list of all operators
 *
 * @return array|null Operators list. Each its element contains (operatorid
 * integer, vclogin string, vclocalename string, vccommonname string, istatus
 * boolean, code string, idisabled integer, time integer)
 */
function operator_get_all()
{
    $db = Database::getInstance();

    return $operators = $db->query(
        ("SELECT operatorid, vclogin, vclocalename, vccommonname, istatus, "
            . "code, idisabled, (:now - dtmlastvisited) AS time "
            . "FROM {operator} ORDER BY vclogin"),
        array(':now' => time()),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );
}

function operator_is_online($operator)
{
    return $operator['time'] < Settings::get('online_timeout');
}

function operator_is_available($operator)
{
    return ($operator['istatus'] == 0 && $operator['time'] < Settings::get('online_timeout'))
        ? "1"
        : "";
}

function operator_is_away($operator)
{
    return ($operator['istatus'] != 0 && $operator['time'] < Settings::get('online_timeout'))
        ? "1"
        : "";
}

function operator_is_disabled($operator)
{
    return $operator['idisabled'] == '1';
}

/**
 * Update existing operator's info.
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_UPDATE} event.
 *
 * @param array $operator Associative array of operator's fields. This array
 * must contain the following keys:
 *   - operatorid,
 *   - vclogin,
 *   - vcpassword,
 *   - vclocalename,
 *   - vccommonname,
 *   - vcemail,
 *   - dtmlastvisited,
 *   - istatus,
 *   - idisabled,
 *   - vcavatar,
 *   - iperm,
 *   - dtmrestore,
 *   - vcrestoretoken,
 *   - code
 *
 * @throws \InvalidArgumentException if not all operator's fields are in place.
 */
function update_operator($operator)
{
    if (!check_operator_fields($operator)) {
        throw new \InvalidArgumentException('Not all operator fields are specified');
    }

    // Get the original operator to trigger the "update" event later
    $original_operator = operator_by_id($operator['operatorid']);

    Database::getInstance()->query(
        ('UPDATE {operator} SET vclogin = :login, vcpassword=:password, '
            . 'vclocalename = :local_name, vccommonname = :common_name, '
            . 'vcemail = :email, dtmlastvisited = :last_visited, '
            . 'istatus = :status, idisabled = :disabled, vcavatar = :avatar, '
            . 'iperm = :permissions, dtmrestore = :restore_time, '
            . 'vcrestoretoken = :restore_token, code = :code '
            . 'WHERE operatorid = :id'),
        array(
            ':id' => $operator['operatorid'],
            ':login' => $operator['vclogin'],
            ':password' => $operator['vcpassword'],
            ':local_name' => $operator['vclocalename'],
            ':common_name' => $operator['vccommonname'],
            ':email' => $operator['vcemail'],
            ':last_visited' => $operator['dtmlastvisited'],
            ':status' => $operator['istatus'],
            ':disabled' => $operator['idisabled'],
            ':avatar' => $operator['vcavatar'],
            ':permissions' => $operator['iperm'],
            ':restore_time' => $operator['dtmrestore'],
            ':restore_token' => $operator['vcrestoretoken'],
            ':code' => $operator['code'],
        )
    );

    $args = array(
        'operator' => $operator,
        'original_operator' => $original_operator,
    );
    EventDispatcher::getInstance()->triggerEvent(Events::OPERATOR_UPDATE, $args);
}

function update_operator_avatar($operator_id, $avatar)
{
    $operator = operator_by_id($operator_id);
    $operator['vcavatar'] = $avatar;
    update_operator($operator);
}

/**
 * Create new operator
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_CREATE} event.
 *
 * @param string $login Operator's login
 * @param string $email Operator's
 * @param string $password Operator's password
 * @param string $locale_name Operator's local name
 * @param string $common_name Operator's international name
 * @param string $avatar Operator's avatar
 * @param string $code Operator's code which use to start chat with specified
 *   operator
 * @return array Operator's array
 */
function create_operator(
    $login,
    $email,
    $password,
    $locale_name,
    $common_name,
    $avatar,
    $code
) {
    $db = Database::getInstance();
    $db->query(
        ("INSERT INTO {operator} ("
            . "vclogin, vcpassword, vclocalename, vccommonname, vcavatar, "
            . "vcemail, code "
        . ") VALUES ("
            . ":login, :pass, :localename, :commonname, :avatar, "
            . ":email, :code"
        . ")"),
        array(
            ':login' => $login,
            ':pass' => calculate_password_hash($login, $password),
            ':localename' => $locale_name,
            ':commonname' => $common_name,
            ':avatar' => $avatar,
            ':email' => $email,
            ':code' => $code,
        )
    );

    $id = $db->insertedId();
    $new_operator = $db->query(
        "SELECT * FROM {operator} WHERE operatorid = ?",
        array($id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    $event = array('operator' => $new_operator);
    EventDispatcher::getInstance()->triggerEvent(Events::OPERATOR_CREATE, $event);

    return $new_operator;
}

/**
 * Delete operator
 *
 * This function remove operator and associations with groups for this operator
 * from datatabse.
 * It triggers {@link \Mibew\EventDispatcher\Events::OPERATOR_DELETE} event.
 *
 * @param int $operator_id Operator ID
 */
function delete_operator($operator_id)
{
    $db = Database::getInstance();
    $db->query(
        "DELETE FROM {operatortoopgroup} WHERE operatorid = ?",
        array($operator_id)
    );
    $db->query(
        "DELETE FROM {operator} WHERE operatorid = ?",
        array($operator_id)
    );

    // Trigger 'operatorDelete' event
    $dispatcher = EventDispatcher::getInstance();
    $args = array('id' => $operator_id);
    $dispatcher->triggerEvent(Events::OPERATOR_DELETE, $args);
}

/**
 * Set current status of the operator('available' or 'away')
 *
 * @param int $operator_id Id of the operator
 * @param int $istatus Operator status: '0' means 'available' and '1' means
 *   'away'
 */
function notify_operator_alive($operator_id, $istatus)
{
    $operator = operator_by_id($operator_id);
    $operator['istatus'] = $istatus;
    $operator['dtmlastvisited'] = time();
    update_operator($operator);
}

/**
 * Indicates if at least one operator of the group is online
 * @param int $group_id Id of the group
 * @return boolean true if the group have online operators and false otherwise
 */
function has_online_operators($group_id = "")
{
    $db = Database::getInstance();

    $query = "SELECT count(*) AS total, MIN(:now - dtmlastvisited) AS time "
        . "FROM {operator}";
    $values = array(':now' => time());
    if ($group_id) {
        $query .= ", {operatortoopgroup}, {opgroup} "
            . "WHERE {opgroup}.groupid = {operatortoopgroup}.groupid "
                . "AND ({opgroup}.groupid = :groupid OR {opgroup}.parent = :groupid) "
                . "AND {operator}.operatorid = {operatortoopgroup}.operatorid "
                . "AND istatus = 0";
        $values[':groupid'] = $group_id;
    } else {
        if (Settings::get('enablegroups') == 1) {
            // If the groups and prechat survey are enabled and a button was
            // generated not for concrete group a user must select a group to
            // chat with. All groups will be checked for online operators. If
            // only operators, who do not related with groups, are online a user
            // cannot complete prechat survey because there will be no online
            // groups. The following code fixes this strange behaviour.
            $query .= ", {operatortoopgroup} "
                . "WHERE {operator}.operatorid = {operatortoopgroup}.operatorid "
                . "AND istatus = 0";
        } else {
            $query .= " WHERE istatus = 0";
        }
    }

    $row = $db->query(
        $query,
        $values,
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return ($row['time'] < Settings::get('online_timeout')) && ($row['total'] > 0);
}

/**
 * Gets list of operators that are currently online.
 *
 * @param int|null $group_id ID of the group online operators should belong to
 * or null to search within all groups.
 * @return Array List of online operators. Each item of the list is an array
 * with operators info. For details of its structure See description of
 * {@link operator_by_id()} function's return value.
 */
function get_online_operators($group_id = null)
{
    $db = Database::getInstance();

    $groups = false;
    if (!$group_id) {
        // We should not care about groups. Just return all online operators.
        $groups = $db->query(
            ('SELECT * FROM {operator} '
                . 'WHERE istatus = 0 AND (:now - dtmlastvisited) < :timeout'),
            array(
                ':now' => time(),
                ':timeout' => Settings::get('online_timeout'),
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );
    } else {
        $groups = $db->query(
            ('SELECT o.* FROM {operator} o, {operatortoopgroup} r, {opgroup} g '
                // The operator should be online
                . 'WHERE o.istatus = 0 AND (:now - o.dtmlastvisited) < :timeout '
                    // And it should belong to the specified group or to one of
                    // its children.
                    . 'AND o.operatorid = r.operatorid '
                    . 'AND r.groupid = g.groupid '
                    . 'AND (g.groupid = :group_id OR g.parent = :group_id)'),
            array(
                ':now' => time(),
                ':timeout' => Settings::get('online_timeout'),
                ':group_id' => $group_id,
            ),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );
    }

    return $groups ? $groups : array();
}

/**
 * Indicates if operator online or not
 *
 * @param int $operator_id Id of the operator
 * @return boolean true if operator is online and false otherwise
 */
function is_operator_online($operator_id)
{
    $db = Database::getInstance();
    $row = $db->query(
        ("SELECT count(*) AS total, "
            . "MIN(:now - dtmlastvisited) AS time "
            . "FROM {operator} WHERE operatorid = :operatorid"),
        array(
            ':now' => time(),
            ':operatorid' => $operator_id,
        ),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return ($row['time'] < Settings::get('online_timeout')) && ($row['total'] == 1);
}

/**
 * Returns name of the operator. Choose between vclocalname and vccommonname
 *
 * @param array $operator Operator's array
 * @return string Operator's name
 */
function get_operator_name($operator)
{
    if (get_home_locale() == get_current_locale()) {
        return $operator['vclocalename'];
    } else {
        return $operator['vccommonname'];
    }
}

function setup_redirect_links(UrlGeneratorInterface $url_generator, $threadid, $operator, $token)
{
    $result = array();

    $operator_in_isolation = in_isolation($operator);

    $list_options = $operator_in_isolation
        ? array('isolated_operator_id' => $operator['operatorid'])
        : array();
    $operators = get_operators_list($list_options);
    $operators_count = count($operators);

    $groups_count = 0;
    $groups = array();
    if (Settings::get('enablegroups') == "1") {
        $groupslist = $operator_in_isolation
            ? get_groups_for_operator($operator, true)
            : get_groups(true);
        foreach ($groupslist as $group) {
            if ($group['inumofagents'] == 0) {
                continue;
            }
            $groups[] = $group;
        }
        $groups_count = count($groups);
    }

    $p = pagination_info(max($operators_count, $groups_count), 8);
    $result['pagination'] = $p;

    $operators = array_slice($operators, $p['start'], $p['end'] - $p['start']);
    $groups = array_slice($groups, $p['start'], $p['end'] - $p['start']);

    $agent_list = "";
    $params = array('thread_id' => $threadid, 'token' => $token);
    foreach ($operators as $agent) {
        $params['nextAgent'] = $agent['operatorid'];
        $status = $agent['time'] < Settings::get('online_timeout')
            ? ($agent['istatus'] == 0
                ? getlocal("(online)")
                : getlocal("(away)"))
            : "";
        $agent_list .= "<li><a href=\"" . $url_generator->generate('chat_operator_redirect', $params)
            . "\" title=\"" . get_operator_name($agent) . "\">"
            . get_operator_name($agent)
            . "</a> $status</li>";
    }
    $result['redirectToAgent'] = $agent_list;

    $group_list = "";
    if (Settings::get('enablegroups') == "1") {
        $params = array('thread_id' => $threadid, 'token' => $token);
        foreach ($groups as $group) {
            $params['nextGroup'] = $group['groupid'];
            $status = group_is_online($group)
                ? getlocal("(online)")
                : (group_is_away($group) ? getlocal("(away)") : "");
            $group_list .= "<li><a href=\"" . $url_generator->generate('chat_operator_redirect', $params)
                . "\" title=\"" . get_group_name($group) . "\">"
                . get_group_name($group)
                . "</a> $status</li>";
        }
    }
    $result['redirectToGroup'] = $group_list;

    return $result;
}

function get_permission_list()
{
    static $permission_list = array();

    if (count($permission_list) == 0) {
        $descriptions = permission_descriptions();
        foreach (permission_ids() as $perm_code => $perm_id) {
            $permission_list[] = array(
                'id' => $perm_id,
                'descr' => $descriptions[$perm_code],
            );
        }
    }

    return $permission_list;
}

function is_capable($perm, $operator)
{
    $permissions = $operator && isset($operator['iperm']) ? $operator['iperm'] : 0;

    return $perm >= 0 && $perm < 32 && ($permissions & (1 << $perm)) != 0;
}

function in_isolation($operator)
{
    return !is_capable(CAN_ADMINISTRATE, $operator)
        && Settings::get('enablegroups')
        && Settings::get('enablegroupsisolation');
}

/**
 * Prepare values to render page menu.
 *
 * @param array $operator An array with operators data.
 * @param boolean $has_right Restricts access to menu items. If it equals to
 *   FALSE only "Home", "Visitors", and "Chat history" items will be displayed.
 *   Otherwise items set depends on operator's permissions and system settings.
 *   Default value is TRUE.
 * @return array
 */
function prepare_menu($operator, $has_right = true)
{
    $result = array();

    $result['operator'] = get_operator_name($operator);
    $result['isOnline'] = is_operator_online($operator['operatorid']);
    if ($has_right) {
        $result['showban'] = Settings::get('enableban') == "1";
        $result['showstat'] = is_capable(CAN_VIEWSTATISTICS, $operator) && (Settings::get('enablestatistics') == "1");
        $result['showadmin'] = is_capable(CAN_ADMINISTRATE, $operator);
        $result['currentopid'] = $operator['operatorid'];
    }
    return $result;
}

/**
 * Calculate hashed password value based upon operator's login and password
 *
 * By default function tries to make use of Blowfish encryption algorithm,
 * with salted MD5 as a second possible choice, and unsalted MD5 as a fallback
 * option
 *
 * @param string $login operator's login
 * @param string $password Operator's password (as plain text)
 *
 * @return string hashed password value
 */
function calculate_password_hash($login, $password)
{
    $hash = '*0';
    if (CRYPT_BLOWFISH == 1) {
        if (defined('PHP_VERSION_ID') && (PHP_VERSION_ID > 50306)) {
            $hash = crypt($password, '$2y$08$' . generate_bf_salt($login));
        } else {
            $hash = crypt($password, '$2a$08$' . generate_bf_salt($login));
        }
    }

    if ((CRYPT_MD5 == 1) && !strcmp($hash, '*0')) {
        $hash = crypt($password, '$1$' . $login);
    }

    return strcmp($hash, '*0') ? $hash : md5($password);
}

/**
 * Generates correct blowfish salt based a string.
 *
 * @param string $string A string which should be turned to blowfish salt.
 * @return string Correct blowfish salt.
 */
function generate_bf_salt($string)
{
    $result = '';
    $bin = unpack('C*', md5($string, true));
    for ($i = 0; $i < count($bin); $i++) {
        $shift = 2 + ($i % 3) * 2;
        $first = ($bin[$i + 1] >> $shift);
        $second = ($bin[$i + 1] & bindec(str_repeat('1', $shift)));
        switch ($shift) {
            case 2:
                $result .= bf_salt_character($first);
                $tmp = $second;
                break;
            case 4:
                $result .= bf_salt_character(($tmp << 4) | $first);
                $tmp = $second;
                break;
            case 6:
                $result .= bf_salt_character(($tmp << 2) | $first);
                $result .= bf_salt_character($second);
                break;
        }
    }
    if ($shift == 2) {
        $result .= bf_salt_character($second);
    }

    return $result;
}

/**
 * Convert character code to a correct blowfish character.
 *
 * @param integer $num Character code.
 * @return string Character that can be used in blowfish salt.
 */
function bf_salt_character($num)
{
    if ($num > 63) {
        return chr(46);
    } elseif ($num < 12) {
        return chr(46 + $num);
    } elseif ($num < 38) {
        return chr(53 + $num);
    } else {
        return chr(59 + $num);
    }
}

/**
 * Validate incoming hashed value to be the hashed value of operator's password
 *
 * @param string $login operator's login
 * @param string $password Operator's password (as plain text)
 * @param string $hash incoming hashed value
 *
 * @return boolean true if incoming value is the correct hashed value of
 *   operators' password and false otherwise
 */
function check_password_hash($login, $password, $hash)
{
    if (preg_match('/^\$/', $hash)) {
        return !strcmp(calculate_password_hash($login, $password), $hash);
    } else {
        return !strcmp(md5($password), $hash);
    }
}

/**
 * Updates set of groups the operator belongs to.
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::GROUP_UPDATE_OPERATORS} event.
 *
 * @param int $operator_id ID of the operator.
 * @param array $new_value List of operator's groups IDs.
 */
function update_operator_groups($operator_id, $new_value)
{
    // Get difference of groups the operator belongs to before and after the
    // update.
    $original_groups = get_operator_group_ids($operator_id);
    $groups_union = array_unique(array_merge($original_groups, $new_value));
    $groups_intersect = array_intersect($original_groups, $new_value);
    $updated_groups = array_diff($groups_union, $groups_intersect);

    // Get members of all updated groups. It will be used to trigger the
    // "update" event later.
    $original_relations = array();
    foreach ($updated_groups as $group_id) {
        $original_relations[$group_id] = get_group_members($group_id);
    }

    // Update group members
    $db = Database::getInstance();
    $db->query(
        "DELETE FROM {operatortoopgroup} WHERE operatorid = ?",
        array($operator_id)
    );

    foreach ($new_value as $group_id) {
        $db->query(
            "INSERT INTO {operatortoopgroup} (groupid, operatorid) VALUES (?,?)",
            array($group_id, $operator_id)
        );
    }

    // Trigger the "update" event
    foreach ($original_relations as $group_id => $operators) {
        $args = array(
            'group' => group_by_id($group_id),
            'original_operators' => $operators,
            'operators' => get_group_members($group_id),
        );
        EventDispatcher::getInstance()->triggerEvent(Events::GROUP_UPDATE_OPERATORS, $args);
    }
}

/**
 * Makes an operator disabled.
 *
 * @param int $operator_id ID of the operator to disable.
 */
function disable_operator($operator_id)
{
    $operator = operator_by_id($operator_id);
    $operator['idisabled'] = 1;
    update_operator($operator);
}

/**
 * Makes an operator enabled.
 *
 * @param int $operator_id ID of the operator to enable.
 */
function enable_operator($operator_id)
{
    $operator = operator_by_id($operator_id);
    $operator['idisabled'] = 0;
    update_operator($operator);
}

/**
 * Checks that operator array has all needed fields.
 *
 * @param array $operator Associative operator's array.
 * @return bool Boolean true if all the fields are in place and false otherwise.
 */
function check_operator_fields($operator)
{
    $obligatory_fields = array(
        'operatorid',
        'vclogin',
        'vcpassword',
        'vclocalename',
        'vccommonname',
        'vcemail',
        'dtmlastvisited',
        'istatus',
        'idisabled',
        'vcavatar',
        'iperm',
        'dtmrestore',
        'vcrestoretoken',
        'code',
    );

    return (count(array_diff($obligatory_fields, array_keys($operator))) == 0);
}
