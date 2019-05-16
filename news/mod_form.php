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
 * The main news configuration form
 *
/**
 * @package    news
 * @copyright  SunNet Solutions 2019
 * @license
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_news
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_news_mod_form extends moodleform_mod {
    public static $datefieldoptions = array('optional' => true);
    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        //$_amount = optional_param('amount', 0, PARAM_TEXT);

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('newsname', 'news'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'newsname', 'news');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $news_config = get_config('news');
        $amount = get_config('news','cost');
        $currency = $news_config->currency;

        //amount
        $mform->addElement('text', 'amount', get_string('amount', 'news'),array('size' => '64'));
        $mform->addRule('amount', null, 'numeric', null, 'client');
        $mform->setType('amount', PARAM_TEXT);
        $mform->setDefault('amount', $amount);
        //currency
        $currencies = array('AUD' => 'Australian Dollar', 'USD'=> 'US Dollar', 'CAD'=> 'Canadian Dollar', 'EUR'=>'Enro', 'GBP'=>'British Pound Sterling', 'NZD'=>'New Zealand Dollar');
        $mform->addElement('select', 'currency', get_string('currency', 'news'), $currencies);
        $mform->setDefault('currency', $currency);

        //send notifications
        $sendnotifications = $news_config->sendnotifications;
        $mform->addElement('advcheckbox', 'sendnotifications', get_string('enablenotifications', 'news'));
        $mform->setType('sendnotifications', PARAM_INT);
        $mform->setDefault('sendnotifications', $sendnotifications);

        //email content
        $mform->addElement('htmleditor', 'emailcontent', get_string('emailcontent', 'news'),array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => false, 'context' => $this->context, 'subdirs' => false));
        $mform->setType('emailcontent', PARAM_RAW);
        $mform->setDefault('emailcontent', $news_config->emailcontent);

        // -------------------------------------------------------------------------------
        $mform->addElement('header', 'timing', get_string('timing', 'quiz'));

        // Open and close dates.
        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'),
                self::$datefieldoptions);
        $mform->addHelpButton('timeopen', 'quizopenclose', 'quiz');

        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'),
                self::$datefieldoptions);

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Add custom completion news rules.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform = $this->_form;

        $group = [
            $mform->createElement('checkbox', 'completionnews', ' ', get_string('completionnewslabel', 'news')),
            //$mform->createElement('text', 'completionnews', ' ', ['size' => 3]),
        ];
        $mform->setType('completionnews', PARAM_BOOL);
        $mform->addGroup($group, 'completionnewsgroup', get_string('completionnews','news'), [' '], false);
        $mform->disabledIf('completionnews', 'completion', 'ne', COMPLETION_TRACKING_AUTOMATIC);
        $mform->addHelpButton('completionnewsgroup', 'completionnews', 'news');

        return ['completionnewsgroup'];
    }

    function completion_rule_enabled($data) {
        return ($data['completionnews'] != 0);
    }
}
