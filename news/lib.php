<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants for module news
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the news specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
/**
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Example constant, you probably want to remove this :-)
 */
define('news_ULTIMATE_ANSWER', 42);

/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@link plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function news_supports($feature) {

    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        //case FEATURE_GRADE_HAS_GRADE:
        //    return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Obtains the automatic completion state for this news based on any conditions
 * in news settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function news_get_completion_state($course,$cm,$userid,$type) {
    global $CFG,$DB;

    // Get news details
    $news = $DB->get_record('news', array('id' => $cm->instance), '*', MUST_EXIST);
    return true;
}

/**
 * Saves a new instance of the news into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $news Submitted data from the form in mod_form.php
 * @param mod_news_mod_form $mform The form instance itself (if needed)
 * @return int The id of the newly inserted news record
 */
function news_add_instance(stdClass $news, mod_news_mod_form $mform = null) {
    global $DB;

    $news->timecreated = time();

    // You may have to add extra stuff in here.

    $news->id = $DB->insert_record('news', $news);

    //news_grade_item_update($news);

    return $news->id;
}

/**
 * Updates an instance of the news in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $news An object from the form in mod_form.php
 * @param mod_news_mod_form $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function news_update_instance(stdClass $news, mod_news_mod_form $mform = null) {
    global $DB;

    $news->timemodified = time();
    $news->id = $news->instance;

    // You may have to add extra stuff in here.

    $result = $DB->update_record('news', $news);

    //news_grade_item_update($news);

    return $result;
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every news event in the site is checked, else
 * only news events belonging to the course specified are checked.
 * This is only required if the module is generating calendar events.
 *
 * @param int $courseid Course ID
 * @return bool
 */
function news_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid == 0) {
        if (!$newss = $DB->get_records('news')) {
            return true;
        }
    } else {
        if (!$newss = $DB->get_records('news', array('course' => $courseid))) {
            return true;
        }
    }

    foreach ($newss as $news) {
        // Create a function such as the one below to deal with updating calendar events.
        // news_update_events($news);
    }

    return true;
}

/**
 * Removes an instance of the news from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function news_delete_instance($id) {
    global $DB;

    if (! $news = $DB->get_record('news', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('news', array('id' => $news->id));

    //news_grade_item_delete($news);

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 *
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $news The news instance record
 * @return stdClass|null
 */
function news_user_outline($course, $user, $mod, $news) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * It is supposed to echo directly without returning a value.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $news the module instance record
 */
function news_user_complete($course, $user, $mod, $news) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in news activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function news_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link news_print_recent_mod_activity()}.
 *
 * Returns void, it adds items into $activities and increases $index.
 *
 * @param array $activities sequentially indexed array of objects with added 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 */
function news_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@link news_get_recent_mod_activity()}
 *
 * @param stdClass $activity activity record with added 'cmid' property
 * @param int $courseid the id of the course we produce the report for
 * @param bool $detail print detailed report
 * @param array $modnames as returned by {@link get_module_types_names()}
 * @param bool $viewfullnames display users' full names
 */
function news_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * Note that this has been deprecated in favour of scheduled task API.
 *
 * @return boolean
 */
function news_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * For example, this could be array('moodle/site:accessallgroups') if the
 * module uses that capability.
 *
 * @return array
 */
function news_get_extra_capabilities() {
    return array();
}

/* Gradebook API */

/**
 * Is a given scale used by the instance of news?
 *
 * This function returns if a scale is being used by one news
 * if it has support for grading and scales.
 *
 * @param int $newsid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given news instance
 */
function news_scale_used($newsid, $scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('news', array('id' => $newsid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of news.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any news instance
 */
function news_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('news', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Delete grade item for given news instance
 *
 * @param stdClass $news instance object
 * @return grade_item
 */
function news_grade_item_delete($news) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/news', $news->course, 'mod', 'news',
            $news->id, 0, null, array('deleted' => 1));
}

/**
 * Update news grades in the gradebook
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $news instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 */
function news_update_grades(stdClass $news, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();

    grade_update('mod/news', $news->course, 'mod', 'news', $news->id, 0, $grades);
}

/* File API */

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function news_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for news file areas
 *
 * @package mod_news
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function news_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the news file areas
 *
 * @package mod_news
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the news's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function news_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

/* Navigation API */

/**
 * Extends the global navigation tree by adding news nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the news module instance
 * @param stdClass $course current course record
 * @param stdClass $module current news instance record
 * @param cm_info $cm course module information
 */
function news_extend_navigation(navigation_node $navref, stdClass $course, stdClass $module, cm_info $cm) {
    // TODO Delete this function and its docblock, or implement it.
}

/**
 * Extends the settings navigation with the news settings
 *
 * This function is called when the context for the page is a news module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav complete settings navigation tree
 * @param navigation_node $newsnode news administration node
 */
function news_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $newsnode=null) {
    // TODO Delete this function and its docblock, or implement it.
}
