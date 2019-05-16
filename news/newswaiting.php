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
 * Prints a particular instance of news
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_news
 * @copyright  2019 Xiaowu
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT);       // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);        // ... news instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('news', $id, 0, false, MUST_EXIST);               //模块信息
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);       //课程信息
    $news  = $DB->get_record('news', array('id' => $cm->instance), '*', MUST_EXIST);      //news插件信息
} else if ($n) {
    $news    = $DB->get_record('news', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $news->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('news', $news->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

global  $USER; //获取用户信息，根据用户信息查询角色信息
//获取支付网关的配置信息
$news_config = get_config('news');
$loginid=$news_config->loginid;
$transactionkey=$news_config->transactionkey;
$merchantmd5hash=$news_config->merchantmd5hash;
//获取支付的金额，货币
$amount = $news->amount;
$currency = $news->currency;
$invoice    = date(YmdHis);
$sequence    = rand(1, 1000);
$timeStamp    = time ();
$x_fp_hash = hash_hmac("md5", $loginid . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^"."USD", $transactionkey);

echo '
<form method="post" action="https://test.authorize.net/gateway/transact.dll">
        <input type="text" name="x_login" value='.$loginid.'>
        <input type="text" name="x_amount" value='.$amount.'>
        <input type="text" name="x_description" value="">
        <input type="text" name="x_invoice_num" value='.$invoice.'>
        <input type="text" name="x_fp_sequence" value='.$sequence.'>
        <input type="text" name="x_fp_timestamp" value='.$timeStamp.'>
        <input type="text" name="x_fp_hash" value='.$x_fp_hash.'>
        <input type="text" name="x_currency_code" value="USD">
        <input type="text" name="x_first_name" value="xiaoParent">
        <input type="text" name="x_last_name" value="wuParent">
        <input type="text" name="x_email" value="xiaowuq2@sunnet.us">
        <input type="text" name="x_phone" value="(211)212-1212">
        <input type="text" name="x_address" value="20180810">
        <input type="text" name="x_state" value="AL">
        <input type="text" name="x_city" value="20180810">
        <input type="text" name="x_zip" value="23233">
        <input type="text" name="x_tax" value="0">
        <input type="text" name="x_type" value="AUTH_CAPTURE">
        <input type="text" name="x_show_form" value="news_FORM">
        <input type="text" name="x_relay_response" value="true">
        <input type="text" name="x_relay_URL" value="'.$CFG->wwwroot.'/mod/news/newsresult.php">
        <input type="submit" class="btn tijiao" value="submit" />
    </form>';
$news_invoice= new stdClass();
$news_invoice->course_moduleid=$cm->id;
$news_invoice->newsid=$news->id;
$news_invoice->userid=$USER->id;
$news_invoice->invoice_num=$invoice;
$news_invoice->iscompletion=0;
$news_invoice->timecreated=time();
$news_invoice_id = $DB->insert_record('news_invoice', $news_invoice, $returnid=true, $bulk=false);
