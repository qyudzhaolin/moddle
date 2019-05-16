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
 * mod_news data generator.
 *
 * @package    mod_news
 * @category   test
 */

defined('MOODLE_INTERNAL') || die();

/**
 * mod_news data generator class.
 *
 * @package    mod_news
 * @category   test
 */
class mod_news_generator extends testing_module_generator {
    public function create_instance($record = null, array $options = null) {
        global $CFG;

        // Add default values for news.
        $record = (array)$record + array(
            'name' => 'news name',
            'intro' => 'intro test',
            'introformat' => 1,
            'timecreated' => time(),
            'timemodified' => time(),
            'grade' => 100,
            'amount' => 1,
            'currency' => 'USD',
            'completionnews' => 1,
        );

        return parent::create_instance($record, (array)$options);
    }
}
