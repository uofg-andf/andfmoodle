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
 *
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_gugcat;

use grade_grade;
use local_gugcat;
use stdClass;

defined('MOODLE_INTERNAL') || die();
require_once('gcat_item.php');

 /**
 * Grade capture class.
 */

class grade_aggregation{

     /**
     * Returns rows for grade aggreation table
     *
     * @param mixed $course
     * @param mixed $module
     * @param mixed $students
     */
    public static function get_rows($course, $modules, $students){
        global $DB;
        $rows = array();
        $gradebook = array();
        foreach ($modules as $mod) {
            $mod->scaleid = $mod->gradeitem->scaleid;
            $mod->gradeitemid = $mod->gradeitem->id;
            $grades = new stdClass();

            //get provisional grades
            $prvgrdid = local_gugcat::set_prv_grade_id($course->id, $mod);
            $sort = 'id';
            $fields = 'userid, itemid, id, rawgrade, finalgrade, timemodified';
            $grades->provisional = $DB->get_records(GRADE_GRADES, array('itemid' => $prvgrdid), $sort, $fields);
            //get grades from gradebook
            $gbgrades = grade_get_grades($course->id, 'mod', $mod->modname, $mod->instance, array_keys($students));
            $grades->gradebook = isset($gbgrades->items[0]) ? $gbgrades->items[0]->grades : null;
            $mod->grades = $grades;
            array_push($gradebook, $mod);
        }

        $i = 1;
        foreach ($students as $student) {
            $gradecaptureitem = new gcat_item();
            $gradecaptureitem->cnum = $i;
            $gradecaptureitem->studentno = $student->id;
            $gradecaptureitem->surname = $student->lastname;
            $gradecaptureitem->forename = $student->firstname;
            $gradecaptureitem->grades = array();
            $floatweight = 0;
            foreach ($gradebook as $item) {
                $grades = $item->grades;
                $pg = isset($grades->provisional[$student->id]) ? $grades->provisional[$student->id] : null;
                $gb = isset($grades->gradebook[$student->id]) ? $grades->gradebook[$student->id] : null;
                $grd = (isset($pg) && !is_null($pg->finalgrade)) ? $pg->finalgrade 
                : (isset($pg) && !is_null($pg->rawgrade) ? $pg->rawgrade 
                : ((isset($gb) && !is_null($gb->grade)) ? $gb->grade : null));                
                $scaleid = $item->scaleid;
                if (is_null($scaleid) && local_gugcat::is_grademax22($item->gradeitem->gradetype, $item->gradeitem->grademax)){
                    $scaleid = local_gugcat::get_gcat_scaleid();
                }
                local_gugcat::set_grade_scale($scaleid);
                $grade = is_null($grd) ? get_string('nograderecorded', 'local_gugcat') : local_gugcat::convert_grade($grd);
                if(!is_null($grd) && $grade !== NON_SUBMISSION_AC && $grade !== MEDICAL_EXEMPTION_AC){
                    $gg = new grade_grade(array('userid'=>$student->id, 'itemid'=>$item->gradeitemid), true);
                    $floatweight += (float)$gg->get_aggregationweight();
                }
                $gradecaptureitem->aggregatedgrade = in_array(get_string('nograderecorded', 'local_gugcat'), $gradecaptureitem->grades) ? get_string('missinggrade', 'local_gugcat') : null;
                $gradecaptureitem->nonsubmission = ($grade === NON_SUBMISSION_AC) ? true : false;
                $gradecaptureitem->medicalexemption = ($grade === MEDICAL_EXEMPTION_AC) ? true : false;
                array_push($gradecaptureitem->grades, $grade);
            }
            $gradecaptureitem->completed = round((float)$floatweight * 100 ) . '%';
            array_push($rows, $gradecaptureitem);
            $i++;
        }
        return $rows;
    }
}