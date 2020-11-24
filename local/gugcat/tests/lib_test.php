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
 * Gugcat library phpunit tests.
 *
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/local/gugcat/lib.php');

class local_gugcat_lib_testcase extends basic_testcase {
    public function test_local_gugcat_extend_navigation_course(){
        $node = new navigation_node(array('text'=>"test"));
        $node2 = new navigation_node(array('text'=>"test"));
        $url = new moodle_url('/local/gugcat/index.php');
        $gugcat = get_string('navname', 'local_gugcat');
        $icon = new pix_icon('my-media', '', 'local_mymedia');
        $node2->add($gugcat, $url, navigation_node::TYPE_CONTAINER, $gugcat, 'gugcat', $icon);
        local_gugcat_extend_navigation_course($node, null, null);
        $this->assertEquals($node2, $node);
    }

    public function test_local_gugcat_extend_navigation(){
        $node = new navigation_node(array('text'=>"test"));
        $node2 = new navigation_node(array('text'=>"test"));
        $url = new moodle_url('/local/gugcat/index.php');
        $gugcat = get_string('navname', 'local_gugcat');
        $icon = new pix_icon('my-media', '', 'local_mymedia');
        $node2->add($gugcat, $url, navigation_node::TYPE_CONTAINER, $gugcat, 'gugcat', $icon);
        local_gugcat_extend_navigation_course($node, null, null);
        $this->assertEquals($node2, $node);
    }
}