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
 * A moodleform allowing the editing of the grade options for all students in aggregation
 *
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_gugcat\grade_aggregation;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot . '/local/gugcat/locallib.php');
class calculationform extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore! 

        $assessments = $this->_customdata['assessments'];
        $mform->addElement('html', '<div class="mform-container">');
        if($this->_customdata['setting'] == '0'){
            $mform->addElement('text', 'cusgfname', get_string('enteraggrename', 'local_gugcat')); 
            $mform->setType('cusgfname', PARAM_NOTAGS); 
            $mform->addRule('cusgfname', null, 'required', null, 'client');
            $mform->addElement('static', 'selectcompare', get_string('selectassessmentscompare', 'local_gugcat')); 
            $mform->setType('selectcompare', PARAM_NOTAGS); 
    
            foreach($assessments as $asmt){
                $mform->addElement('advcheckbox', "assessments[$asmt->gradeitemid]", $asmt->name);
            }
            $mform->addElement('advcheckbox', "assessments[0]", 'Assessment A');
            $mform->addElement('advcheckbox', "assessments[1]", 'Assessment B');
    
            
            $mform->addElement('select', 'displaygrade', get_string('display', 'local_gugcat'), array('Best Grade', ' Simple Mean', 'Weighted Mean'), ['class' => 'mform-custom-select']); 
            $mform->setType('displaygrade', PARAM_NOTAGS); 
            $mform->setDefault('displaygrade', 'Best Grade');
            $attributes = array(
                'type' => 'number',
                'maxlength' => '3',
                'minlength' => '1',
                'size' => '6',
                'pattern' => '[0-9]+'
            );
            $mform->addElement('text', 'countbestgrade', get_string('displaybestgrade', 'local_gugcat'), $attributes);
            $mform->setType('countbestgrade', PARAM_INT);
            $mform->addRule('countbestgrade', null, 'numeric', null, 'client');
            $mform->addRule('countbestgrade', get_string('errorfieldnumbers', 'local_gugcat'), 'regex', '/^[0-9]+$/', 'client');
            $mform->addRule('countbestgrade', get_string('errorfieldnumbers', 'local_gugcat'), 'regex', '/^[0-9]+$/', 'server');
            $mform->setDefault('countbestgrade', '0');
            $mform->addElement('static', 'space', ''); 
            $mform->addElement('advcheckbox', 'displaytocat', get_string('continuedisplay', 'local_gugcat'));
    
    
            $mform->addElement('html', '</div>');
            
            $mform->addElement('submit', 'submit', get_string('next', 'local_gugcat'), ['class' => 'btn-coursegradeform']);
        }else{
            $attributes = array(
                'class' => 'input-cb input-percent',
                'type' => 'number',
                'maxlength' => '3',
                'minlength' => '1',
                'size' => '6',
                'pattern' => '[0-9]+'
            );
            $mform->addElement('static', 'selectgradesaggregate', get_string('selectgradesaggregate', 'local_gugcat')); 
            
            foreach($assessments as $asmt){
                $attributes = array(
                    'class' => 'input-cb input-percent',
                    'type' => 'number',
                    'maxlength' => '3',
                    'minlength' => '1',
                    'size' => '6',
                    'pattern' => '[0-9]+',
                    'data-weight' => '60',
                    'data-check' => false
                );
                $mform->addElement('text', "weights[$asmt->gradeitemid]", $asmt->name, $attributes);
                $mform->setType('weights['.$asmt->gradeitemid.']', PARAM_INT);
                $mform->setDefault('weights['.$asmt->gradeitemid.']', '0');
            }
            $mform->addElement('text', "weights[0]", 'Assessment A', $attributes);
            $mform->setType('weights[0]', PARAM_INT);
            
            $mform->addElement('static', 'totalweight', get_string('totalweight', 'local_gugcat'), '100%'); 
            $mform->setType('totalweight', PARAM_NOTAGS); 

            $mform->addElement('html', '</div>');
            $mform->addElement('button', 'adjustoverride', get_string('back', 'local_gugcat'), ['id' => 'btn-coursegradeform', 'class' => 'btn-coursegradeform']);
            $mform->addElement('submit', 'submit', get_string('savechanges', 'local_gugcat'), ['class' => 'btn-coursegradeform']);
        }
        // hidden params
        $mform->addElement('hidden', 'id', $this->_customdata['id']);
        $mform->setType('id', PARAM_ACTION);
        $mform->addElement('hidden', 'setting', $this->_customdata['setting']);
        $mform->setType('setting', PARAM_ACTION);
        $mform->addElement('hidden', 'categoryid', $this->_customdata['categoryid']);
        $mform->setType('categoryid', PARAM_ACTION);
        $mform->addElement('hidden', 'page', $this->_customdata['page']);
        $mform->setType('page', PARAM_ACTION);
        
    function validation($data, $files) {
        return array();
        }
    }
}
