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
 * Index file.
 *
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_gugcat\grade_aggregation;

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/local/gugcat/locallib.php');
require_once($CFG->dirroot.'/local/gugcat/classes/form/calculationform.php');

$courseid = 3;//required_param('id', PARAM_INT);
$formtype = 0;//required_param('setting', PARAM_INT);
$categoryid = optional_param('categoryid', null, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);  

require_login($courseid);
$urlparams = array('id' => $courseid, 'setting' => $formtype, 'page' => $page);
$URL = new moodle_url('/local/gugcat/overview/compare/index.php', $urlparams);
is_null($categoryid) ? null : $URL->param('categoryid', $categoryid);
$indexurl = new moodle_url('/local/gugcat/index.php', array('id' => $courseid));

$PAGE->set_url($URL);
$PAGE->set_title(get_string('gugcat', 'local_gugcat'));
$PAGE->navbar->add(get_string('navname', 'local_gugcat'), $indexurl);

$PAGE->requires->css('/local/gugcat/styles/gugcat.css');
$PAGE->requires->js_call_amd('local_gugcat/main', 'init');
$course = get_course($courseid);

$coursecontext = context_course::instance($courseid);
$PAGE->set_context($coursecontext);
$PAGE->set_course($course);
$PAGE->set_heading($course->fullname);
require_capability('local/gugcat:view', $coursecontext);

$activities = local_gugcat::get_activities($courseid);
$mform = new calculationform(null, array('id' => $courseid, 'page' => $page, 'categoryid'=>$categoryid,
 'setting' => $formtype, 'assessments' => $activities));
if ($fromform = $mform->get_data()) {
    echo '<pre>';
    var_dump($fromform);
    // Get students based from groups
    $groupingids = array_column($activities, 'groupingid');
    $students = grade_aggregation::get_students_per_groups($groupingids, $courseid);
    $compareids = null;
    foreach($fromform->assessments as $id=>$value){
        var_dump($value);
        if(intval($value) == 1){
            $compareids .= $id.'_';
        }
    }
    $compareids = chop($compareids, '_');
    var_dump($compareids);
    echo '</pre>';
    // $compareids = "19_1";
    // Create comparison grade item
    // $gradeitemid = local_gugcat::add_grade_item($courseid, $fromform->cusgfname, null, $students, $compareids);

//     //params needed for logs
//     $params = array(
//         'context' => $coursecontext,
//         'other' => array(
//             'courseid' => $courseid,
//             'categoryid' => $categoryid,
//             'cnum' => $cnum,
//             'idnumber' => $student->idnumber,
//             'studentid' => $studentid,
//             'setting' => $formtype,
//             'page' => $page
//         )
//     );
//     if($formtype == OVERRIDE_GRADE_FORM){
//         $gradeitemid = local_gugcat::add_grade_item($courseid, get_string('aggregatedgrade', 'local_gugcat'), null);
//         local_gugcat::update_grade($studentid, $gradeitemid, $fromform->override, $fromform->notes, time());
//         //log of adjust course weight
//         $event = \local_gugcat\event\override_course_grade::create($params);
//         $event->trigger();
//     }else if($formtype == ADJUST_WEIGHT_FORM){
//         $weights = $fromform->weights;
//         $aggradeid = local_gugcat::get_grade_item_id($courseid, null, get_string('aggregatedgrade', 'local_gugcat'));
//         $DB->set_field('grade_grades', 'overridden', 0, array('itemid' => $aggradeid, 'userid'=>$studentid));
//         grade_aggregation::adjust_course_weight($weights, $courseid, $studentid, $fromform->notes);
//         //log of adjust course weight
//         $event = \local_gugcat\event\adjust_course_weight::create($params);
//         $event->trigger();
//     }
//     $url = new moodle_url('/local/gugcat/overview/index.php', array('id' => $courseid, 'page' => $page));
//     (!is_null($categoryid) && $categoryid != 0) ? $url->param('categoryid', $categoryid) : null;
    // redirect($PAGE->url);
    // exit;
}   

echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('local_gugcat');
echo $renderer->display_compare_form();
$mform->display();
echo $OUTPUT->footer();
