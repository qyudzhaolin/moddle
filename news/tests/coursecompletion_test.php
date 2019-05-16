<?php

class mod_news_testcase extends advanced_testcase {
    public function test_plugin_coursecompletion_test() {
        global $DB;defined('MOODLE_INTERNAL') || die();
        global $CFG,$USER;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'fullname'=>'news_coursecompletion_test',
            'enablecompletion'=>1,
            'completion'=>1
            ]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_news');
        $news= $generator->create_instance(array('course'=>$course->id,'completionnews'=>1));
        $cm         = get_coursemodule_from_id('news', $news->cmid, 0, false, MUST_EXIST);
        $oldCompletion= $cm->completion;
        $completion=new completion_info($course);
        $cm->completion=COMPLETION_COMPLETE;
        $completion->update_state($cm,COMPLETION_COMPLETE);
        $newCompletion= $cm->completion;

        $this->assertNotEquals($oldCompletion,$newCompletion);
    }
}


?>