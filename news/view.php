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
 * news plugin.
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT);       // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);        // ... news instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('news', $id, 0, false, MUST_EXIST);               //module information
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);       //course information
    $news  = $DB->get_record('news', array('id' => $cm->instance), '*', MUST_EXIST);      //news plugin information
} else if ($n) {
    $news    = $DB->get_record('news', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $news->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('news', $news->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/news/view.php', array('id' => $cm->id));
$title = $course->shortname . ': ' . format_string($news->name);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$output = $PAGE->get_renderer('mod_news');
echo $OUTPUT->header();
// Conditions to show the intro can change to look for own settings or whatever.
//if ($news->intro) {
//    echo $OUTPUT->box(format_module_intro('news', $news, $cm->id), 'generalbox mod_introbox', 'newsintro');
//}

global  $USER;
$news_config = get_config('news');
$loginid=$news_config->loginid;
$transactionkey=$news_config->transactionkey;
$merchantmd5hash=$news_config->merchantmd5hash;
$amount = $news->amount;
$currency = $news->currency;
$description = $news->intro;
$label = "Pay Now";

if ($news_config->checkproductionmode == 1) {
    $url = "https://secure.authorize.net/gateway/transact.dll";
    $testmode = "false";
} else {
    $url = "https://test.authorize.net/gateway/transact.dll";
    $testmode = "true";
}

$invoice = date('YmdHis');
$_SESSION['sequence'] = $sequence = rand(1, 1000);
$_SESSION['timestamp'] = $timestamp = time();

if ( phpversion() >= '5.1.2' ) {
    if ($news_config->checkproductionmode == 1) {
        $fingerprint = hash_hmac("md5"
                       , $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^" . $news->currency
                       , $transactionkey);
    } else {
        $fingerprint = hash_hmac("md5", $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^", $transactionkey);
    }
} else {
    if ($news_config->checkproductionmode == 1) {
        $fingerprint = bin2hex(mhash(MHASH_MD5
                       , $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^" . $news->currency
                       , $transactionkey));
    } else {
        $fingerprint = bin2hex(mhash(MHASH_MD5
                       , $loginid . "^" . $sequence . "^" . $timestamp . "^" . $amount . "^"
                       , $transactionkey));
    }
}

$newsresult=$DB->get_record('mod_news_result', array('courseid' => $course->id,'userid' => $USER->id,'instanceid' => $id));
?>
<div align="center">
    <p>This activity requires a news for entry.</p>
    <p>
        <b>
            <?php echo $news->name; ?>
        </b>
    </p>
    <p>
        <b>
            <?php echo get_string("cost").": {$news->currency} {$news->amount}"; ?>
        </b>
    </p>
    <p>&nbsp;</p>
    <p>
        <img alt="Authorize.net" src="<?php echo $CFG->wwwroot; ?>/mod/news/pix/news-logo.jpg" />
    </p>
    <p>&nbsp;</p>
    <p>
        <form method="post" action="<?php echo $url; ?>">
            <input type="hidden" name="x_login" value="<?php echo $loginid; ?>" />
            <input type="hidden" name="x_amount" value="<?php echo $amount; ?>" />
            <?php
            if ($news_config->checkproductionmode == 1) {
            ?>
            <input type="hidden" name="x_currency_code" value="<?php echo $news->currency; ?>" />
            <?php
            }
            ?>
            <input type="hidden" name="x_cust_id" value="<?php echo $news->course.'-'.$USER->id.'-'.$id; ?>" />
            <input type="hidden" name="x_description" value="<?php echo $news->name; ?>" />
            <input type="hidden" name="x_invoice_num" value="<?php echo $invoice; ?>" />
            <input type="hidden" name="x_fp_sequence" value="<?php echo $sequence; ?>" />
            <input type="hidden" name="x_fp_timestamp" value="<?php echo $timestamp; ?>" />
            <input type="hidden" name="x_fp_hash" value="<?php echo $fingerprint; ?>" />
            <input type="hidden" name="x_test_request" value="<?php echo $testmode; ?>" />
            <input type="hidden" name="x_email_customer" value="true" />

            <input type="hidden" name="x_first_name" value="<?php echo $USER->firstname ?>" />
            <input type="hidden" name="x_last_name" value="<?php echo $USER->lastname ?>" />
            <input type="hidden" name="x_email" value="<?php echo $USER->email ?>" />
            <input type="hidden" name="x_address" value="<?php echo $USER->address ?>" />
            <input type="hidden" name="x_phone" value="" />
            <input type="hidden" name="x_city" value="<?php echo $USER->city ?>" />
            <input type="hidden" name="x_country" value="<?php echo $USER->country ?>" />


            <input type="hidden" name="x_relay_response" value="TRUE" />
            <input type="hidden" name="x_relay_url" value="<?php echo $CFG->wwwroot; ?>/mod/news/ipn.php" />

            <input type="hidden" name="x_show_form" value="news_FORM" />
            <?php
            if (!empty($newsresult) && $newsresult->response_code==1) {?>
            <p style="font-weight:bold;">news was successful.</p>
            <?php
            }else{
            ?>
            <input type="submit" id="sub_button" value="Pay Now" />
            <?php }?> 

        </form>
    </p>
</div>
<style type="text/css">
#sub_button{
  background: url("<?php echo $CFG->wwwroot; ?>/mod/news/pix/paynow.png") no-repeat scroll 0 0 transparent;
  color: #000000;
  cursor: pointer;
  font-weight: bold;
  height: 20px;
  padding-bottom: 2px;
  width: 300px;
  height: 110px;
}
</style>
<?php
echo $OUTPUT->footer();
?>