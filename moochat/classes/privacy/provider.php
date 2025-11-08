<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
/**
 * Privacy Subsystem implementation for block_moochat
 *
 * @package    block_moochat
 * @copyright  2025 Brian A. Pool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_moochat\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem for block_moochat implementing metadata and plugin providers.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'block_moochat_usage',
            [
                'userid' => 'privacy:metadata:block_moochat_usage:userid',
                'instanceid' => 'privacy:metadata:block_moochat_usage:instanceid',
                'messagecount' => 'privacy:metadata:block_moochat_usage:messagecount',
                'firstmessage' => 'privacy:metadata:block_moochat_usage:firstmessage',
                'lastmessage' => 'privacy:metadata:block_moochat_usage:lastmessage',
            ],
            'privacy:metadata:block_moochat_usage'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {block_instances} bi ON bi.parentcontextid = ctx.id
                  JOIN {block_moochat_usage} bmu ON bmu.instanceid = bi.id
                 WHERE bmu.userid = :userid
                   AND ctx.contextlevel = :contextlevel";

        $params = [
            'userid' => $userid,
            'contextlevel' => CONTEXT_BLOCK,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_block) {
            return;
        }

        $sql = "SELECT bmu.userid
                  FROM {block_moochat_usage} bmu
                  JOIN {block_instances} bi ON bi.id = bmu.instanceid
                 WHERE bi.parentcontextid = :contextid";

        $params = ['contextid' => $context->id];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT bmu.*
                  FROM {block_moochat_usage} bmu
                  JOIN {block_instances} bi ON bi.id = bmu.instanceid
                  JOIN {context} ctx ON ctx.id = bi.parentcontextid
                 WHERE bmu.userid = :userid
                   AND ctx.id {$contextsql}";

        $params = ['userid' => $user->id] + $contextparams;

        $usagerecords = $DB->get_records_sql($sql, $params);

        foreach ($usagerecords as $usage) {
            $context = \context_block::instance($usage->instanceid);
            
            $data = (object) [
                'messagecount' => $usage->messagecount,
                'firstmessage' => \core_privacy\local\request\transform::datetime($usage->firstmessage),
                'lastmessage' => \core_privacy\local\request\transform::datetime($usage->lastmessage),
            ];

            writer::with_context($context)->export_data([], $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_block) {
            return;
        }

        $instanceid = $DB->get_field('block_instances', 'id', ['parentcontextid' => $context->id]);
        if ($instanceid) {
            $DB->delete_records('block_moochat_usage', ['instanceid' => $instanceid]);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_block) {
                continue;
            }

            $instanceid = $DB->get_field('block_instances', 'id', ['parentcontextid' => $context->id]);
            if ($instanceid) {
                $DB->delete_records('block_moochat_usage', [
                    'instanceid' => $instanceid,
                    'userid' => $user->id,
                ]);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_block) {
            return;
        }

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        $instanceid = $DB->get_field('block_instances', 'id', ['parentcontextid' => $context->id]);
        
        if ($instanceid) {
            list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $select = "instanceid = :instanceid AND userid {$usersql}";
            $params = ['instanceid' => $instanceid] + $userparams;
            
            $DB->delete_records_select('block_moochat_usage', $select, $params);
        }
    }
}
