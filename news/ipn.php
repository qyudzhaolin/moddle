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
define('NO_DEBUG_DISPLAY', true);

require("../../config.php");
require_once("lib.php");

global $DB, $CFG;

if (empty($_POST) or !empty($_GET)) {
    print_error("Sorry, you can not use the script that way."); die;
}
$modnewsresult = new stdClass();
$postdata = array_map('utf8_encode', $_POST);
$modnewsresult->auth_json = json_encode($postdata);
$modnewsresult->timeupdated = time();

$ret1 = $DB->insert_record("mod_news_result", $modnewsresult, true);

echo '<script type="text/javascript">
     window.location.href="'.$CFG->wwwroot.'/mod/news/update.php?id='.$ret1.'";
     </script>';
die;
