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
 * Listens for Instant news Notification from Authorize.net
 *
 * This script waits for news notification from Authorize.net,
 *
/**
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
//define('NO_DEBUG_DISPLAY', true);

require("../../config.php");
require_once("lib.php");

global $DB, $CFG;

$id = required_param('id', PARAM_INT);

$response = $DB->get_record('mod_news_result', array('id' => $id));
$responsearray = json_decode($response->auth_json, true);

// Check if the response is from authorize.net.
$merchantmd5hash = get_config('news', 'merchantmd5hash');
$merchantmd5hash = get_config('news', 'merchantmd5hash');
$loginid = get_config('news', 'loginid');
$transactionid = $responsearray['x_trans_id'];
$amount = $responsearray['x_amount'];
$generatemd5hash = strtoupper(md5($merchantmd5hash.$loginid.$transactionid.$amount));
$arraycourseinstance = explode('-', $responsearray['x_cust_id']);

// Required for message_send.
$PAGE->set_context(context_system::instance());

//if ($generatemd5hash != $responsearray['x_MD5_Hash']) {
//    print_error("We can't validate your transaction. Please try again!!"); die;
//}

$arraycourseinstance = explode('-', $responsearray['x_cust_id']);
if (empty($arraycourseinstance) || count($arraycourseinstance) < 3) {
    print_error("Received an invalid news notification!! (Fake news?)"); die;
}

if (! $user = $DB->get_record("user", array("id" => $arraycourseinstance[1]))) {
    print_error("Not a valid user id"); die;
}

if (! $course = $DB->get_record("course", array("id" => $arraycourseinstance[0]))) {
    print_error("Not a valid course id"); die;
}

if (! $context = context_course::instance($arraycourseinstance[0], IGNORE_MISSING)) {
    print_error("Not a valid context id"); die;
}

if (! $cm         = get_coursemodule_from_id('news', $arraycourseinstance[2], 0, false)) {
    print_error("Not a valid instance id"); die;
}


$modnewsresult = $userenrolments = $roleassignments = new stdClass();

$modnewsresult->id = $id;
$modnewsresult->item_name = $responsearray['x_description'];
$modnewsresult->courseid = $arraycourseinstance[0];
$modnewsresult->userid = $arraycourseinstance[1];
$modnewsresult->instanceid = $arraycourseinstance[2];
$modnewsresult->amount = $responsearray['x_amount'];
$modnewsresult->tax = $responsearray['x_tax'];
$modnewsresult->duty = $responsearray['x_duty'];

//update news completion
$cm         = get_coursemodule_from_id('news', $modnewsresult->instanceid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);       //course information
require_login($course, true, $cm);
$completion=new completion_info($course);
$cm->completion=COMPLETION_COMPLETE;
$completion->update_state($cm,COMPLETION_COMPLETE);
$news  = $DB->get_record('news', array('id' => $cm->instance), '*', MUST_EXIST);

if ($responsearray['x_response_code'] == 1) {
    $modnewsresult->news_status = 'Approved';

    $PAGE->set_context($context);
    $coursecontext = context_course::instance($course->id, IGNORE_MISSING);

    if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                         '', '', '', '', false, true)) {
        $users = sort_by_roleassignment_authority($users, $context);
        $teacher = array_shift($users);
    } else {
        $teacher = false;
    }

    $plugin = enrol_get_plugin('news');

    //$mailstudents = get_config('news', 'mailstudents');
    //$mailteachers = get_config('news', 'mailteachers');
    //$mailadmins   = get_config('news', 'mailadmins');
    $sendnotifications=$news->sendnotifications;
    $emailcontent=$news->emailcontent;
    //$emailcontent   = get_config('news', 'emailcontent');
    $shortname = format_string($course->shortname, true, array('context' => $context));

    if (!empty($sendnotifications)) {
        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context' => $coursecontext));
        $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";

        if ($CFG->version >= 2015051100) {
            $eventdata = new \core\message\message();
        } else {
            $eventdata = new stdClass();
        }
        $eventdata->component         = 'mod_news';
        $eventdata->name              = 'notification';
        $eventdata->courseid          = $course->id;
        $eventdata->userfrom          = empty($teacher) ? core_user::get_noreply_user() : $teacher;
        $eventdata->userto            = $user;
        $eventdata->subject           = get_string("newssubject", 'news', $shortname);
        $eventdata->fullmessage       = empty($emailcontent)?get_string('congratulationstext', 'news', $a):$emailcontent;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $emailcontent;
        $eventdata->smallmessage      = '';
        $eventdata->notification=1;
        message_send($eventdata);

    }

    //if (!empty($mailteachers) && !empty($teacher)) {
    //    $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
    //    $a->user = fullname($user);

    //    if ($CFG->version >= 2015051100) {
    //        $eventdata = new \core\message\message();
    //    } else {
    //        $eventdata = new stdClass();
    //    }
    //    $eventdata->component         = 'mod_news';
    //    $eventdata->name              = 'notification';
    //    $eventdata->courseid          = $course->id;
    //    $eventdata->userfrom          = $user;
    //    $eventdata->userto            = $teacher;
    //    $eventdata->subject           = get_string("newssubject", 'news', $shortname);
    //    $eventdata->fullmessage       = empty($emailcontent)?get_string('newsfullmessage', 'news', $a):$emailcontent;
    //    $eventdata->fullmessageformat = FORMAT_PLAIN;
    //    $eventdata->fullmessagehtml   = $emailcontent;
    //    $eventdata->smallmessage      = '';
    //    $eventdata->notification=1;
    //    message_send($eventdata);
    //}

    if (!empty($sendnotifications)) {
        $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
        $a->user = fullname($user);
        //$admins = get_admins();  
		//$admins=array('david@sunnet.us','jimmy@sunnet.us','Rodney.G.Spears@uth.tmc.edu');
		$admins=array('david@sunnet.us','jimmy@sunnet.us');
        foreach ($admins as $admin) {
            if ($CFG->version >= 2015051100) {
                $eventdata = new \core\message\message();
            } else {
                $eventdata = new stdClass();
            }
            $eventdata->component         = 'mod_news';
            $eventdata->name              = 'notification';
            $eventdata->courseid          = $course->id;
            $eventdata->userfrom          = $user;
            $eventdata->userto            = $admin;
            $eventdata->subject           = get_string("newssubject", 'news', $shortname);
            $eventdata->fullmessage       = empty($emailcontent)?get_string('newsfullmessage', 'news', $a):$emailcontent;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = $emailcontent;
            $eventdata->smallmessage      = '';
            $eventdata->notification=1;
            message_send($eventdata);
        }
    }
}
if ($responsearray['x_response_code'] == 2) {
    $modnewsresult->news_status = 'Declined';
}
if ($responsearray['x_response_code'] == 3) {
    $modnewsresult->news_status = 'Error';
}
if ($responsearray['x_response_code'] == 4) {
    $modnewsresult->news_status = 'Held for Review';
}



$modnewsresult->response_code = $responsearray['x_response_code'];
$modnewsresult->response_reason_code = $responsearray['x_response_reason_code'];
$modnewsresult->response_reason_text = $responsearray['x_response_reason_text'];
$modnewsresult->auth_code = $responsearray['x_auth_code'];
$modnewsresult->trans_id = $responsearray['x_trans_id'];
$modnewsresult->method = $responsearray['x_method'];
$modnewsresult->account_number = isset($responsearray['x_account_number']) ? $responsearray['x_account_number'] : '';
$modnewsresult->card_type = isset($responsearray['x_card_type']) ? $responsearray['x_card_type'] : '';
$modnewsresult->first_name = isset($responsearray['x_first_name']) ? $responsearray['x_first_name'] : '';
$modnewsresult->last_name = isset($responsearray['x_last_name']) ? $responsearray['x_last_name'] : '';
$modnewsresult->company = isset($responsearray['x_company']) ? $responsearray['x_company'] : '';
$modnewsresult->phone = isset($responsearray['x_phone']) ? $responsearray['x_phone'] : '';
$modnewsresult->fax = isset($responsearray['x_fax']) ? $responsearray['x_fax'] : '';
$modnewsresult->address = isset($responsearray['x_address']) ? $responsearray['x_address'] : '';
$modnewsresult->city = isset($responsearray['x_city']) ? $responsearray['x_city'] : '';
$modnewsresult->state = isset($responsearray['x_state']) ? $responsearray['x_state'] : '';
$modnewsresult->zip = isset($responsearray['x_zip']) ? $responsearray['x_zip'] : '';
$modnewsresult->country = isset($responsearray['x_country']) ? $responsearray['x_country'] : '';
$modnewsresult->email = isset($responsearray['x_email']) ? $responsearray['x_email'] : '';
$modnewsresult->invoice_num = $responsearray['x_invoice_num'];
$modnewsresult->test_request = ($responsearray['x_test_request'] == 'true') ? '1' : '0';
$modnewsresult->timeupdated = time();
/* Inserting value to mod_news_result table */
$ret1 = $DB->update_record("mod_news_result", $modnewsresult, false);


//if ($plugininstance->enrolperiod) {
//   $timestart = time();
//   $timeend   = $timestart + $plugin_instance->enrolperiod;
//} else {
//    $timestart = 0;
//    $timeend   = 0;
//}

///* Enrol User */
//$plugin->enrol_user($plugininstance, $user->id, $plugininstance->roleid, $timestart, $timeend);
echo '<script type="text/javascript">
     window.location.href="'.$CFG->wwwroot.'/mod/news/return.php?id='.$id.'";
     </script>';
die;
