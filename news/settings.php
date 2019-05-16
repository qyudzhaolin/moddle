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
 * Folder module admin settings and defaults
/**
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $plugName = 'news';

    $settings->add(new admin_setting_heading('news_settings', '',
    get_string('newsname_help', 'news')));
    //Authorize.net login ID
    $settings->add(new admin_setting_configtext($plugName.'/loginid',
        get_string("login_id","news"),
        get_string("login_id_help","news"),'',PARAM_TEXT));

    //Authorize.net transaction key
    $settings->add(new admin_setting_configtext($plugName.'/transactionkey',
        get_string("transaction_key","news"),
        get_string("transaction_key_help","news"),'',PARAM_TEXT));

    //Authorize.net merchant MD5 hash key
    $settings->add(new admin_setting_configtext($plugName.'/merchantmd5hash',
        get_string("md5_hash_key","news"),
        get_string("md5_hash_key_help","news"),'',PARAM_TEXT));

    //Check for production mode
    $settings->add(new admin_setting_configcheckbox($plugName.'/checkproductionmode',
        get_string('production_mode', 'news'),'',
        0));

    ////Notify students
    //$settings->add(new admin_setting_configcheckbox($plugName.'/mailstudents',
    //    get_string('notify_students', 'news'),'',
    //    0));

    ////Notify teachers
    //$settings->add(new admin_setting_configcheckbox($plugName.'/mailteachers',
    //    get_string('notify_teachers', 'news'),'',
    //    0));

    ////Notify admin
    //$settings->add(new admin_setting_configcheckbox($plugName.'/mailadmins',
    //    get_string('notify_admin', 'news'),'',
    //    0));

    //Notify Enable
    $settings->add(new admin_setting_configcheckbox($plugName.'/sendnotifications',
        get_string('sendnotifications', 'news'),'',
        0));

    //default email
    $settings->add(new admin_setting_confightmleditor($plugName.'/emailcontent',
    get_string('emailcontentnotice', 'news'),
    '', '', PARAM_RAW));


    $settings->add(new admin_setting_configtext($plugName.'/name',
        get_string("name","news"),
        '','',PARAM_FLOAT));
    //cost
    $settings->add(new admin_setting_configtext($plugName.'/cost',
        get_string("cost","news"),
       '','',PARAM_FLOAT));

         var_dump('这是settings设置页面');
    //Currency
    $currencies = array('AUD' => 'Australian Dollar','USD'=> 'US Dollar', 'CAD'=> 'Canadian Dollar', 'EUR'=>'Enro', 'GBP'=>'British Pound Sterling', 'NZD'=>'New Zealand Dollar');
    $settings->add(new admin_setting_configselect($plugName.'/currency', get_string('currency', 'news'),
        '', 'USD', $currencies));

    //Default role assignment
    if (!during_initial_install()) {
        $options = get_default_enrol_roles(context_system::instance());
        $student = get_archetype_roles('student');
        $student = reset($student);
        $settings->add(new admin_setting_configselect(
        $plugName.'/roleid',
        get_string('defaultrole', 'news'),
        get_string('defaultrole_help', 'news'),
        $student->id,
        $options
    ));


    }
}
