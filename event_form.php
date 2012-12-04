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
 * The mform for creating and editing a calendar event
 *
 * @copyright 2009 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

 /**
  * Always include formslib
  */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating and editing a calendar
 *
 * @copyright 2009 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_form extends moodleform {
    /**
     * The form definition
     */
    function definition () {
        global $CFG, $USER, $OUTPUT;
        $mform = $this->_form;
        $event_content ='';
        $newevent = (empty($this->_customdata->event) || empty($this->_customdata->event->id));
        $repeatedevents = (!empty($this->_customdata->event->eventrepeats) && $this->_customdata->event->eventrepeats>0);
        $hasduration = (!empty($this->_customdata->hasduration) && $this->_customdata->hasduration);

        if ($newevent) {
            
            $event_content .= html_writer::start_tag('form', array('class'=>'mform', 'id'=>'mform1', 'accept-charset'=>'utf-8', 'method'=>'post', 'action'=>'event.php', 'autocomplete'=>'off'));
            
            $event_content .= html_writer::start_tag('div id=group1'); 
            
            $event_content .= html_writer::start_tag('label', array('for'=>'name');
            $event_content .= get_string('eventname','calendar');
            $event_content .= html_writer::end_tag('label');
            $event_content .= html_writer::tag('input', array('id'=>'name','name'=>'name','type'=>'text', 'size'=>'50'));
            //$mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50"');//added by abdul
            
            //$mform->addRule('name', get_string('required'), 'required'); //added by abdul
            //$mform->setType('name', PARAM_TEXT); //added by abdul
            
            $event_content .= html_writer::start_tag('label', array('for'=>'eventtype');
            $event_content .= get_string('eventkind', 'calendar');
            $event_content .= html_writer::end_tag('label');
            $event_content .= html_writer::start_tag('select', array('id'=>'eventtype','name'=>'eventtype'));
            
            $eventtypes = $this->_customdata->eventtypes;
            $options = array();
            if (!empty($eventtypes->user)) {
                $event_content .= html_writer::start_tag('option', array('value'=>get_string('user')));
                $event_content .= get_string('user');
                $event_content .= html_writer::end_tag('option');
                //$options['user'] = get_string('user');
            }
            if (!empty($eventtypes->groups) && is_array($eventtypes->groups)) {
                $event_content .= html_writer::start_tag('option', array('value'=>get_string('group')));
                $event_content .= get_string('group');
                $event_content .= html_writer::end_tag('option');
                //$options['group'] = get_string('group');
            }
            if (!empty($eventtypes->courses)) {
                $event_content .= html_writer::start_tag('option', array('value'=>get_string('course')));
                $event_content .= get_string('course');
                $event_content .= html_writer::end_tag('option');
                //$options['course'] = get_string('course');
            }
            if (!empty($eventtypes->site)) {
                $event_content .= html_writer::start_tag('option', array('value'=>get_string('site')));
                $event_content .= get_string('site');
                $event_content .= html_writer::end_tag('option');
                //$options['site'] = get_string('site');
            }
            $event_content .= html_writer::end_tag('select');
            
            
            //$mform->addElement('select', 'eventtype', get_string('eventkind', 'calendar'), $options);
            //$mform->addRule('eventtype', get_string('required'), 'required');
            
            if (!empty($eventtypes->groups) && is_array($eventtypes->groups)) {
                $event_content .= html_writer::start_tag('label', array('for'=>'groupid'));
                $event_content .= get_string('typegroup', 'calendar');
                $event_content .= html_writer::start_tag('select', array('id'=>'groupid', 'name'=>'groupid'));
                
                // re-visit for groups
                //$mform->addElement('select', 'groupid', get_string('typegroup', 'calendar'), $groupoptions);
                //$mform->disabledIf('groupid', 'eventtype', 'noteq', 'group');
                
                $groupoptions = array();
                foreach ($eventtypes->groups as $group) {
                    $event_content .= html_writer::start_tag('option', array('value'=>$group->name));
                    $event_content .= $group->name;
                    $event_content .= html_writer::end_tag('option');
                    //$groupoptions[$group->id] = $group->name;
                }
                
                $event_content .= html_writer::end_tag('select');
            }
        }

        // Add some hidden fields
        $event_content .= html_writer::tag('input', array('name'=>'id', 'type'=>'hidden', 'value'=>'0'));
        $event_content .= html_writer::tag('input', array('name'=>'courseid','type'=>'hidden'));
        $event_content .= html_writer::tag('input', array('name'=>'userid','type'=>'hidden', 'value'=>$USER->id));
        $event_content .= html_writer::tag('input', array('name'=>'modulename','type'=>'hidden'));
        $event_content .= html_writer::tag('input', array('name'=>'instance','type'=>'hidden', 'value'=>'0'));
        $event_content .= html_writer::tag('input', array('name'=>'action','type'=>'hidden'));
        
        //$mform->addElement('hidden', 'id');
        //$mform->setType('id', PARAM_INT);
        //$mform->setDefault('id', 0);

        //$mform->addElement('hidden', 'courseid');
        //$mform->setType('courseid', PARAM_INT);

        //$mform->addElement('hidden', 'userid');
        //$mform->setType('userid', PARAM_INT);
        //$mform->setDefault('userid', $USER->id);

        //$mform->addElement('hidden', 'modulename');
        //$mform->setType('modulename', PARAM_INT);
        //$mform->setDefault('modulename', '');

        //$mform->addElement('hidden', 'instance');
        //$mform->setType('instance', PARAM_INT);
        //$mform->setDefault('instance', 0);

        //$mform->addElement('hidden', 'action');
        //$mform->setType('action', PARAM_INT);

        // Normal fields
        
        /*$mform->addElement('text', 'name', get_string('eventname','calendar'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);*///orginal
        
        //$event_content .= html_writer::tag('input', array('name'=>'action','type'=>'hidden'));
        
        $event_content .= $mform->addElement('date_time_selector', 'timestart', get_string('date')); //added by abdul
        $event_content .= $mform->addRule('timestart', get_string('required'), 'required'); //added by abdul
        
        $event_content .= $mform->addElement('editor', 'description', get_string('eventdescription','calendar'), null, $this->_customdata->event->editoroptions);
        $event_content .= $mform->setType('description', PARAM_RAW);

        /*$mform->addElement('date_time_selector', 'timestart', get_string('date'));
        $mform->addRule('timestart', get_string('required'), 'required');*/// orginal

        $event_content .= $mform->addElement('radio', 'duration', get_string('eventduration', 'calendar'), get_string('durationnone', 'calendar'), 0);

        $event_content .= $mform->addElement('radio', 'duration', null, get_string('durationuntil', 'calendar'), 1);
        $event_content .= $mform->addElement('date_time_selector', 'timedurationuntil', '&nbsp;');
        $event_content .= $mform->disabledIf('timedurationuntil','duration','noteq', 1);

        $event_content .= $mform->addElement('radio', 'duration', null, get_string('durationminutes', 'calendar'), 2);
        $event_content .= $mform->addElement('text', 'timedurationminutes', null);
        $event_content .= $mform->setType('timedurationminutes', PARAM_INT);
        $event_content .= $mform->disabledIf('timedurationminutes','duration','noteq', 2);

        $event_content .= $mform->setDefault('duration', ($hasduration)?1:0);

        if ($newevent) {

            $event_content .= $mform->addElement('checkbox', 'repeat', get_string('repeatevent', 'calendar'), null, 'repeat');
            $event_content .= $mform->addElement('text', 'repeats', get_string('repeatweeksl', 'calendar'), 'maxlength="10" size="10"');
            $event_content .= $mform->setType('repeats', PARAM_INT);
            $event_content .= $mform->setDefault('repeats', 1);
            $event_content .= $mform->disabledIf('repeats','repeat','notchecked');

        } else if ($repeatedevents) {

            $event_content .= $mform->addElement('hidden', 'repeatid');
            $event_content .= $mform->setType('repeatid', PARAM_INT);

            $event_content .= $mform->addElement('header', 'repeatedevents', get_string('repeatedevents', 'calendar'));
            $event_content .= $mform->addElement('radio', 'repeateditall', null, get_string('repeateditall', 'calendar', $this->_customdata->event->eventrepeats), 1);
            $event_content .= $mform->addElement('radio', 'repeateditall', null, get_string('repeateditthis', 'calendar'), 0);

            $event_content .= $mform->setDefault('repeateditall', 1);

        }

        $this->add_action_buttons(false, get_string('savechanges'));
        
        return $event_content;
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     * @return array
     */
    function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        if ($data['courseid'] > 0) {
            if ($course = $DB->get_record('course', array('id'=>$data['courseid']))) {
                if ($data['timestart'] < $course->startdate) {
                    $errors['timestart'] = get_string('errorbeforecoursestart', 'calendar');
                }
            } else {
                $errors['courseid'] = get_string('invalidcourse', 'error');
            }

        }

        if ($data['duration'] == 1 && $data['timestart'] > $data['timedurationuntil']) {
            $errors['timedurationuntil'] = get_string('invalidtimedurationuntil', 'calendar');
        } else if ($data['duration'] == 2 && (trim($data['timedurationminutes']) == '' || $data['timedurationminutes'] < 1)) {
            $errors['timedurationminutes'] = get_string('invalidtimedurationminutes', 'calendar');
        }

        return $errors;
    }

}
