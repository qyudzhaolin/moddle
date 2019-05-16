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
 * English strings for news
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_news
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'news';
$string['modulenameplural'] = 'newss';
$string['modulename_help'] = 'Add this module to include news to the activity.';
$string['news:addinstance'] = 'Add news';
$string['news:submit'] = 'Submit news';
$string['news:view'] = 'View news';
$string['newsfieldset'] = 'news fieldset';
$string['newsname'] = 'news name';
$string['amount'] = 'news amount';
$string['currency'] = 'Currency';
$string['newsname_help'] = 'This is the content of the help tooltip associated with the newsname field. Markdown syntax is supported.';
$string['news'] = 'news';
$string['pluginadministration'] = 'news administration';
$string['pluginname'] = 'news';
$string['completionnews'] = 'news is required';
$string['completionnewslabel'] = 'Yes';
$string['completionnews_help']='Pay after activity is completed.';
$string['enablenotifications']='Enable email notifications';
$string['emailcontent']='Content of the email notification';
$string['sendnotifications']='Send notification';

$string['congratulationstext'] = 'news was successful.';
$string['newsfullmessage'] = '{$a->user} has paid.';
$string['newssubject'] = 'news subject';
$string['newsthanks']='Thank you for your news.';
$string['newssorry']='Your news did not go through.';
$string['messageprovider:notification']='news notification';

//settings
$string['login_id'] = 'Authorize.net login ID';
$string['login_id_help'] = 'Copy API Login ID from merchant account and paste here.';
$string['transaction_key'] = 'Authorize.net transaction key';
$string['transaction_key_help'] = 'Copy API Transaction Key from merchant account and paste here.';
$string['md5_hash_key'] = 'Authorize.net merchant MD5 hash key';
$string['md5_hash_key_help'] = 'Copy secret MD5 Hash from merchant account and paste here.';
$string['production_mode'] = 'Check for production mode';
$string['notify_students'] = 'Notify students';
$string['notify_teachers'] = 'Notify teachers';
$string['notify_admin'] = 'Notify admin';
$string['email_content'] = 'Please enter default email content';
$string['cost'] = 'Pay cost';
$string['currency'] = 'Pay currency';
$string['role'] = 'Default role assignment';
$string['role_help'] = 'Select role which should be assigned to users during Authorize.net news.';
$string['emailcontentnotice'] = 'Please enter default email content.';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_help'] = 'Select role which should be assigned to users during Authorize.net enrolments.';