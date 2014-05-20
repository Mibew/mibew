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

// Import namespaces and classes of the core
use Mibew\Database;
use Mibew\Settings;

/**
 * Get chatgroup by id
 *
 * @param integer $id ID for the chat group
 *
 * @return null|array It is chatgroup structure. contains (groupId integer,
 * parent integer, vcemail string, vclocalname string, vccommonname string,
 * vclocaldescription string, vccommondescription string, iweight integer,
 * vctitle string, vcchattitle string, vclogo string, vchosturl string)
 */
function group_by_id($id)
{
    $db = Database::getInstance();
    $group = $db->query(
        "SELECT * FROM {chatgroup} WHERE groupid = ?",
        array($id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return $group;
}

/**
 * Get chatgroup by name
 *
 * @param string $name Name of the chat group
 *
 * @return null|array It is chatgroup structure. contains (groupId integer,
 * parent integer, vcemail string, vclocalname string, vccommonname string,
 * vclocaldescription string, vccommondescription string, iweight integer,
 * vctitle string, vcchattitle string, vclogo string, vchosturl string)
 */
function group_by_name($name)
{
    $db = Database::getInstance();
    $group = $db->query(
        "SELECT * FROM {chatgroup} WHERE vclocalname = ?",
        array($name),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return $group;
}

/**
 * Get chatgroup name
 *
 * @param array $group chat group object
 *
 * @return string return chat group name 
 */
function get_group_name($group)
{
    if (HOME_LOCALE == CURRENT_LOCALE || !isset($group['vccommonname']) || !$group['vccommonname']) {
        return $group['vclocalname'];
    } else {
        return $group['vccommonname'];
    }
}

/**
 * Builds list of group ids for specific operator
 *
 * @param int $operator_id ID of the specific operator.
 *
 * @return string Comma separated list of operator groups ids
 */
function get_operator_groups_list($operator_id)
{
    $db = Database::getInstance();
    if (Settings::get('enablegroups') == '1') {
        $group_ids = array(0);
        $all_groups = $db->query(
            "SELECT groupid FROM {chatgroupoperator} WHERE operatorid = ? ORDER BY groupid",
            array($operator_id),
            array('return_rows' => Database::RETURN_ALL_ROWS)
        );
        foreach ($all_groups as $g) {
            $group_ids[] = $g['groupid'];
        }

        return implode(",", $group_ids);
    } else {
        return "";
    }
}

/**
 * List of available groups
 *
 * @param array $skip_group ID of groups which most be skipped.
 *
 * @return array list of all available groups in chatgroup structure. contains
 * (groupId integer, parent integer, vcemail string, vclocalname string,
 * vccommonname string, vclocaldescription string, vccommondescription string,
 * iweight integer, vctitle string, vcchattitle string, vclogo string,
 * vchosturl string)
 */
function get_available_parent_groups($skip_group)
{
    $result = array();

    $result[] = array(
        'groupid' => '',
        'level' => '',
        'vclocalname' => getlocal("form.field.groupparent.root"),
    );

    $db = Database::getInstance();
    $groups_list = $db->query(
        ("SELECT {chatgroup}.groupid AS groupid, parent, vclocalname "
            . "FROM {chatgroup} ORDER BY vclocalname"),
        null,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    if ($skip_group) {
        $skip_group = (array) $skip_group;
    } else {
        $skip_group = array();
    }

    $result = array_merge($result, get_sorted_child_groups_($groups_list, $skip_group, 0));

    return $result;
}

/**
 * Check if group has any child
 *
 * @param int $group_id ID of the specific chat group.
 *
 * @return boolean True if specified group has any child
 */
function group_has_children($group_id)
{
    $db = Database::getInstance();
    $children = $db->query(
        "SELECT COUNT(*) AS count FROM {chatgroup} WHERE parent = ?",
        array($group_id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return ($children['count'] > 0);
}

/**
 * Get parent of chatgroup
 *
 * @param array $group specific chatgroup
 *
 * @return array parent of given group. It is chatgroup structure. contains
 * (groupId integer, parent integer, vcemail string, vclocalname string,
 * vccommonname string, vclocaldescription string, vccommondescription string,
 * iweight integer, vctitle string, vcchattitle string, vclogo string,
 * vchosturl string)
 */
function get_top_level_group($group)
{
    return is_null($group['parent']) ? $group : group_by_id($group['parent']);
}

/**
 * Try to load email for specified group or for its parent.
 *
 * @param int $group_id Group id
 * @return string|boolean Email address or false if there is no email
 */
function get_group_email($group_id)
{
    // Try to get group email
    $group = group_by_id($group_id);
    if ($group && !empty($group['vcemail'])) {
        return $group['vcemail'];
    }

    // Try to get parent group email
    if (!is_null($group['parent'])) {
        $group = group_by_id($group['parent']);
        if ($group && !empty($group['vcemail'])) {
            return $group['vcemail'];
        }
    }

    // There is no email
    return false;
}

/**
 * Check if group online
 *
 * @param array $group Associative group array. Should contain 'ilastseen' key.
 * @return bool
 */
function group_is_online($group)
{
    return $group['ilastseen'] !== null
        && $group['ilastseen'] < Settings::get('online_timeout');
}

/**
 * Check if group is away
 *
 * @param array $group Associative group array. Should contain 'ilastseenaway'
 *   key.
 * @return bool
 */
function group_is_away($group)
{
    return $group['ilastseenaway'] !== null
        && $group['ilastseenaway'] < Settings::get('online_timeout');
}

/**
 * Return local or common group description depending on current locale.
 *
 * @param array $group Associative group array. Should contain following keys:
 *  - 'vccommondescription': string, contain common description of the group;
 *  - 'vclocaldescription': string, contain local description of the group.
 * @return string Group description
 */
function get_group_description($group)
{
    $use_local_description = HOME_LOCALE == CURRENT_LOCALE
        || !isset($group['vccommondescription'])
        || !$group['vccommondescription'];

    if ($use_local_description) {
        return $group['vclocaldescription'];
    } else {
        return $group['vccommondescription'];
    }
}

/**
 * Chaeck availability of chatgroup array params.
 *
 * @param array $group Associative group array.
 * @param array $extra_params extra parameters for chatgroup array.
 */
function check_group_params($group, $extra_params = null)
{
    $obligatory_params = array(
        'name',
        'description',
        'commonname',
        'commondescription',
        'email',
        'weight',
        'parent',
        'chattitle',
        'hosturl',
        'logo',
    );

    $params = is_null($extra_params)
        ? $obligatory_params
        : array_merge($obligatory_params, $extra_params);

    if (count(array_diff($params, array_keys($group))) != 0) {
        die('Wrong parameters set!');
    }
}

/**
 * Creates group
 *
 * @param array $group Operators' group. The $group array must contains the
 *   following keys:
 *     - name,
 *     - description,
 *     - commonname,
 *     - commondescription,
 *     - email,
 *     - weight,
 *     - parent,
 *     - title,
 *     - chattitle,
 *     - hosturl,
 *     - logo
 * @return array Created group
 */
function create_group($group)
{
    check_group_params($group);

    $db = Database::getInstance();
    $db->query(
        ("INSERT INTO {chatgroup} ("
            . "parent, vclocalname, vclocaldescription, vccommonname, "
            . "vccommondescription, vcemail, vctitle, vcchattitle, vchosturl, "
            . "vclogo, iweight"
            . ") values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"),
        array(
            ($group['parent'] ? (int) $group['parent'] : null),
            $group['name'],
            $group['description'],
            $group['commonname'],
            $group['commondescription'],
            $group['email'],
            $group['title'],
            $group['chattitle'],
            $group['hosturl'],
            $group['logo'],
            $group['weight'],
        )
    );
    $id = $db->insertedId();

    $new_group = $db->query(
        "SELECT * FROM {chatgroup} WHERE groupid = ?",
        array($id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    return $new_group;
}

/**
 * Updates group info
 *
 * @param array $group Operators' group. The $group array must contains the
 *   following keys:
 *     - id,
 *     - name,
 *     - description,
 *     - commonname,
 *     - commondescription,
 *     - email,
 *     - weight,
 *     - parent,
 *     - title,
 *     - chattitle,
 *     - hosturl,
 *     - logo
 */
function update_group($group)
{
    check_group_params($group, array('id'));

    $db = Database::getInstance();
    $db->query(
        ("UPDATE {chatgroup} SET "
            . "parent = ?, vclocalname = ?, vclocaldescription = ?, "
            . "vccommonname = ?, vccommondescription = ?, "
            . "vcemail = ?, vctitle = ?, vcchattitle = ?, "
            . "vchosturl = ?, vclogo = ?, iweight = ? "
            . "where groupid = ?"),
        array(
            ($group['parent'] ? (int) $group['parent'] : null),
            $group['name'],
            $group['description'],
            $group['commonname'],
            $group['commondescription'],
            $group['email'],
            $group['title'],
            $group['chattitle'],
            $group['hosturl'],
            $group['logo'],
            $group['weight'],
            $group['id']
        )
    );

    if ($group['parent']) {
        $db->query(
            "UPDATE {chatgroup} SET parent = NULL WHERE parent = ?",
            array($group['id'])
        );
    }
}

/**
 * Builds list of chatgroup operators ids.
 *
 * @param int $group_id ID of the chatgroup.
 *
 * @return array ID of all operators in specified group.
 */
function get_group_members($group_id)
{
    $db = Database::getInstance();
    return $db->query(
        "SELECT operatorid FROM {chatgroupoperator} WHERE groupid = ?",
        array($group_id),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );
}

/**
 * Update operators of specific group
 *
 * @param int $group_id ID of the group.
 * @param array $new_value list of all operators of specified group.
 */
function update_group_members($group_id, $new_value)
{
    $db = Database::getInstance();
    $db->query(
        "DELETE FROM {chatgroupoperator} WHERE groupid = ?",
        array($group_id)
    );

    foreach ($new_value as $operator_id) {
        $db->query(
            "INSERT INTO {chatgroupoperator} (groupid, operatorid) VALUES (?, ?)",
            array($group_id, $operator_id)
        );
    }
}
