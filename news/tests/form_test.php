<?php
defined('MOODLE_INTERNAL') || die();
  global $CFG;
  //require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

class mod_news_testcase extends advanced_testcase {

	public function test_plugin_news_form() {
        global $DB;
        $this->resetAfterTest();
        $old_newscount=$DB->count_records('news');
        $news=new stdClass();
        $news->course=3;
        $news->name='test';
        $news->intro='test intro';
        $news->introformat='1';
        $news->timecreated=time();
        $news->timemodified=time();
        $news->amount=1;
        $news->currency='USD';
        $news->completionnews=1;
        $news->sendstudentnotifications=0;
        $news->emailcontent='email content';
        $DB->insert_record('news',$news);
        $newscount=$DB->count_records('news');
        $this->assertEquals($newscount, $old_newscount+1);
    }
}


?>