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
use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
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
        "SELECT * FROM {opgroup} WHERE groupid = ?",
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
        "SELECT * FROM {opgroup} WHERE vclocalname = ?",
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
    $use_local_name = (get_home_locale() == get_current_locale())
        || !isset($group['vccommonname'])
        || !$group['vccommonname'];
    if ($use_local_name) {
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
            "SELECT groupid FROM {operatortoopgroup} WHERE operatorid = ? ORDER BY groupid",
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
        'vclocalname' => getlocal("-none-"),
    );

    $db = Database::getInstance();
    $groups_list = $db->query(
        ("SELECT {opgroup}.groupid AS groupid, parent, vclocalname "
            . "FROM {opgroup} ORDER BY vclocalname"),
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
        "SELECT COUNT(*) AS count FROM {opgroup} WHERE parent = ?",
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
    $use_local_description = (get_home_locale() == get_current_locale())
        || !isset($group['vccommondescription'])
        || !$group['vccommondescription'];

    if ($use_local_description) {
        return $group['vclocaldescription'];
    } else {
        return $group['vccommondescription'];
    }
}

/**
 * Checks that group's array has all necessary fields.
 *
 * @param array $group Associative group's array.
 * @return bool Boolean true if all the fields are in place and false otherwise.
 */
function check_group_fields($group)
{
    $obligatory_fields = array(
        'groupid',
        'vclocalname',
        'vclocaldescription',
        'vccommonname',
        'vccommondescription',
        'vcemail',
        'iweight',
        'parent',
        'vctitle',
        'vcchattitle',
        'vchosturl',
        'vclogo',
    );

    return (count(array_diff($obligatory_fields, array_keys($group))) == 0);
}

/**
 * Creates group
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::GROUP_CREATE} event.
 *
 * @param array $group Operators' group. The $group array must contains the
 *   following keys:
 *     - vclocalname,
 *     - vclocaldescription,
 *     - vccommonname,
 *     - vccommondescription,
 *     - vcemail,
 *     - iweight,
 *     - parent,
 *     - vctitle,
 *     - vcchattitle,
 *     - vchosturl,
 *     - vclogo,
 * @return array Created group
 * @throws \InvalidArgumentException if not all of the group fields are
 *   specified.
 */
function create_group($group)
{
    $group += array('groupid' => false);
    if (!check_group_fields($group)) {
        throw new \InvalidArgumentException('Not all group fields are specified');
    }

    $db = Database::getInstance();
    $db->query(
        ("INSERT INTO {opgroup} ("
            . "parent, vclocalname, vclocaldescription, vccommonname, "
            . "vccommondescription, vcemail, vctitle, vcchattitle, vchosturl, "
            . "vclogo, iweight"
            . ") values ("
            . ":parent, :name, :desc, :common_name, "
            . ":common_desc, :email, :title, :chat_title, :url, "
            . ":logo, :weight)"),
        array(
            ':parent' => ($group['parent'] ? (int) $group['parent'] : null),
            ':name' => $group['vclocalname'],
            ':desc' => $group['vclocaldescription'],
            ':common_name' => $group['vccommonname'],
            ':common_desc' => $group['vccommondescription'],
            ':email' => $group['vcemail'],
            ':title' => $group['vctitle'],
            ':chat_title' => $group['vcchattitle'],
            ':url' => $group['vchosturl'],
            ':logo' => $group['vclogo'],
            ':weight' => $group['iweight'],
        )
    );
    $id = $db->insertedId();

    $new_group = $db->query(
        "SELECT * FROM {opgroup} WHERE groupid = ?",
        array($id),
        array('return_rows' => Database::RETURN_ONE_ROW)
    );

    $args = array('group' => $new_group);
    EventDispatcher::getInstance()->triggerEvent(Events::GROUP_CREATE, $args);

    return $new_group;
}

/**
 * Updates group info
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::GROUP_UPDATE} event.
 *
 * @param array $group Operators' group. The $group array must contains the
 *   following keys:
 *     - groupid,
 *     - vclocalname,
 *     - vclocaldescription,
 *     - vccommonname,
 *     - vccommondescription,
 *     - vcemail,
 *     - iweight,
 *     - parent,
 *     - vctitle,
 *     - vcchattitle,
 *     - vchosturl,
 *     - vclogo,
 * @throws \InvalidArgumentException if not all of the group fields are
 *   specified.
 */
function update_group($group)
{
    if (!check_group_fields($group)) {
        throw new \InvalidArgumentException('Not all group fields are specified');
    }

    // Get the original state of the group to trigger the "update" event later.
    $original_group = group_by_id($group['groupid']);

    $db = Database::getInstance();
    $db->query(
        ("UPDATE {opgroup} SET "
            . "parent = :parent, vclocalname = :name, vclocaldescription = :desc, "
            . "vccommonname = :common_name, vccommondescription = :common_desc, "
            . "vcemail = :email, vctitle = :title, vcchattitle = :chat_title, "
            . "vchosturl = :host_url, vclogo = :logo, iweight = :weight "
            . "where groupid = :id"),
        array(
            ':parent' => ($group['parent'] ? (int) $group['parent'] : null),
            ':name' => $group['vclocalname'],
            ':desc' => $group['vclocaldescription'],
            ':common_name' => $group['vccommonname'],
            ':common_desc' => $group['vccommondescription'],
            ':email' => $group['vcemail'],
            ':title' => $group['vctitle'],
            ':chat_title' => $group['vcchattitle'],
            ':host_url' => $group['vchosturl'],
            ':logo' => $group['vclogo'],
            ':weight' => $group['iweight'],
            ':id' => $group['groupid'],
        )
    );

    if ($group['parent']) {
        $db->query(
            "UPDATE {opgroup} SET parent = NULL WHERE parent = ?",
            array($group['groupid'])
        );
    }

    $args = array(
        'group' => $group,
        'original_group' => $original_group,
    );
    EventDispatcher::getInstance()->triggerEvent(Events::GROUP_UPDATE, $args);
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
    $rows =  Database::getInstance()->query(
        "SELECT operatorid FROM {operatortoopgroup} WHERE groupid = ?",
        array($group_id),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    $operators = array();
    foreach ($rows as $row) {
        $operators[] = $row['operatorid'];
    }

    return $operators;
}

/**
 * Update operators of specific group
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::GROUP_UPDATE_OPERATORS} event.
 *
 * @param int $group_id ID of the group.
 * @param array $new_value list of all operators of specified group.
 */
function update_group_members($group_id, $new_value)
{
    // Get the unchanged set of operators related with the group to trigger
    // "update" event later
    $original_operators = get_group_members($group_id);

    $db = Database::getInstance();
    $db->query(
        "DELETE FROM {operatortoopgroup} WHERE groupid = ?",
        array($group_id)
    );

    foreach ($new_value as $operator_id) {
        $db->query(
            "INSERT INTO {operatortoopgroup} (groupid, operatorid) VALUES (?, ?)",
            array($group_id, $operator_id)
        );
    }
    if ($original_operators != $new_value) {
        // Trigger the "update" event only if operators set is changed.
        $args = array(
            'group' => group_by_id($group_id),
            'original_operators' => $original_operators,
            'operators' => $new_value,
        );
        EventDispatcher::getInstance()->triggerEvent(Events::GROUP_UPDATE_OPERATORS, $args);
    }
}

/**
 * Deletes a group with specified ID.
 *
 * Triggers {@link \Mibew\EventDispatcher\Events::GROUP_DELETE} event.
 *
 * @param int $group_id ID of the group that should be deleted.
 */
function delete_group($group_id)
{
    $db = Database::getInstance();
    $db->query("DELETE FROM {opgroup} WHERE groupid = ?", array($group_id));
    $db->query("DELETE FROM {operatortoopgroup} WHERE groupid = ?", array($group_id));
    $db->query("UPDATE {thread} SET groupid = 0 WHERE groupid = ?", array($group_id));

    $args = array('id' => $group_id);
    EventDispatcher::getInstance()->triggerEvent(Events::GROUP_DELETE, $args);
}

function get_all_groups()
{
    $db = Database::getInstance();
    $groups = $db->query(
        ("SELECT {opgroup}.groupid AS groupid, parent, "
            . "vclocalname, vclocaldescription "
            . "FROM {opgroup} ORDER BY vclocalname"),
        null,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    return get_sorted_child_groups_($groups);
}

/**
 * Retrieves list of groups the operator can see.
 *
 * This function keeps in mind groups isolation.
 *
 * @deprecated since 2.1.1. Use {@link get_groups_for_operator()} function
 * instead.
 * @param array $operator List of operator's fields. See
 * {@link operator_by_id()} for details of its keys.
 * @return array List of group arrays. See {@link group_by_id()} for details
 * about their structure.
 */
function get_all_groups_for_operator($operator)
{
    return get_groups_for_operator($operator, false);
}

function get_sorted_child_groups_(
    $groups_list,
    $skip_groups = array(),
    $max_level = -1,
    $group_id = null,
    $level = 0
) {
    $child_groups = array();
    foreach ($groups_list as $index => $group) {
        if ($group['parent'] == $group_id && !in_array($group['groupid'], $skip_groups)) {
            $group['level'] = $level;
            $child_groups[] = $group;
            if ($max_level == -1 || $level < $max_level) {
                $child_groups = array_merge(
                    $child_groups,
                    get_sorted_child_groups_(
                        $groups_list,
                        $skip_groups,
                        $max_level,
                        $group['groupid'],
                        $level + 1
                    )
                );
            }
        }
    }

    return $child_groups;
}

function get_groups_($check_away, $operator, $order = null)
{
    $db = Database::getInstance();
    if ($order) {
        switch ($order['by']) {
            case 'weight':
                $orderby = "iweight";
                break;
            case 'lastseen':
                $orderby = "ilastseen";
                break;
            default:
                $orderby = "{opgroup}.vclocalname";
        }
        $orderby = sprintf(
            " IF(ISNULL({opgroup}.parent),CONCAT('_',%s),'') %s, {opgroup}.iweight ",
            $orderby,
            ($order['desc'] ? 'DESC' : 'ASC')
        );
    } else {
        $orderby = "iweight, vclocalname";
    }

    $values = array(
        ':now' => time(),
    );
    $query = "SELECT DISTINCT {opgroup}.groupid AS groupid, "
        . "{opgroup}.parent AS parent, "
        . "vclocalname, vclocaldescription, iweight, "
        . "(SELECT count(*) "
            . "FROM {operatortoopgroup} "
            . "WHERE {opgroup}.groupid = {operatortoopgroup}.groupid"
        . ") AS inumofagents, "
        . "(SELECT MIN(:now - dtmlastvisited) AS time "
            . "FROM {operatortoopgroup}, {operator} "
            . "WHERE istatus = 0 "
                . "AND {opgroup}.groupid = {operatortoopgroup}.groupid "
                . "AND {operatortoopgroup}.operatorid = {operator}.operatorid" .
        ") AS ilastseen"
        . ($check_away
            ? ", (SELECT MIN(:now - dtmlastvisited) AS time "
                    . "FROM {operatortoopgroup}, {operator} "
                    . "WHERE istatus <> 0 "
                    . "AND {opgroup}.groupid = {operatortoopgroup}.groupid "
                    . "AND {operatortoopgroup}.operatorid = {operator}.operatorid"
                . ") AS ilastseenaway"
            : "")
        . " FROM {opgroup} ";

    if ($operator) {
        $query .= ", (SELECT {opgroup}.groupid, {opgroup}.parent "
            . "FROM {opgroup}, {operatortoopgroup} "
            . "WHERE {opgroup}.groupid = {operatortoopgroup}.groupid "
                . "AND {operatortoopgroup}.operatorid = :operatorid) i "
            . "WHERE {opgroup}.groupid = i.parent "
                . "OR {opgroup}.parent = i.groupid "
                . "OR ({opgroup}.parent = i.parent AND {opgroup}.parent IS NOT NULL) "
                . "OR {opgroup}.groupid = i.groupid ";
        $values[':operatorid'] = $operator['operatorid'];
    }

    $query .= " ORDER BY " . $orderby;
    $groups = $db->query(
        $query,
        $values,
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    return get_sorted_child_groups_($groups);
}

function get_groups($check_away)
{
    return get_groups_($check_away, null);
}

function get_groups_for_operator($operator, $check_away = false)
{
    return get_groups_($check_away, $operator);
}

function get_sorted_groups($order)
{
    return get_groups_(true, null, $order);
}

function get_operator_group_ids($operator_id)
{
    $rows = Database::getInstance()->query(
        "SELECT groupid FROM {operatortoopgroup} WHERE operatorid = ?",
        array($operator_id),
        array('return_rows' => Database::RETURN_ALL_ROWS)
    );

    $groups = array();
    foreach ($rows as $row) {
        $groups[] = $row['groupid'];
    }

    return $groups;
}

function get_operators_from_adjacent_groups($operator)
{
    return get_operators_list(array(
        'isolated_operator_id' => $operator['operatorid'],
    ));
}
