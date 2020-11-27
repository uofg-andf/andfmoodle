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
 * A moodleform allowing the editing of the grade options for an individual grade item
 *
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/gugcat/locallib.php');
class addgradeform extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
 
       

        $mform = $this->_form; // Don't forget the underscore! 
        $mform->addElement('html', '<div class="mform-container">');

        $mform->addElement('select', 'reasons', 'Reason for additional grade', local_gugcat::$REASONS,['class' => 'mform-custom']); 
        $mform->setType('reasons', PARAM_NOTAGS); 
        $mform->setDefault('reasons', "Select Reason");   

        $mform->addElement('text', 'otherreason', 'Others', ['class' => 'mform-custom']); 
        $mform->setType('otherreason', PARAM_NOTAGS); 
        $mform->hideIf('otherreason', 'reasons', 'neq', 8); 
        $mform->setDefault('otherreason', "Please Specify");

        $mform->addElement('select', 'grade', 'Grade', local_gugcat::$GRADES, ['class' => 'mform-custom']); 
        $mform->setType('grade', PARAM_NOTAGS); 
        $mform->setDefault('grade', "Select Grade");
        $mform->addElement('html', '</div>');
        
        $this->add_action_buttons(false, get_string('confirmgrade', 'local_gugcat'), ['class' => 'float-right']);
        //hidden params
        $mform->addElement('hidden', 'studentid', $this->_customdata['studentid']);
        $mform->setType('studentid', PARAM_ACTION);
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_ACTION);
        $mform->addElement('hidden', 'activityid', $this->_customdata['activityid']);
        $mform->setType('activityid', PARAM_ACTION);
        
    }    
        
    function validation($data, $files) {
        return array();
    }
}

