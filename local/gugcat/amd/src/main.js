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
 * AMD module for local_gugcat.
 * 
 * @package    local_gugcat
 * @copyright  2020
 * @author     Accenture
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str' ], function($, Str) {

    //Returns boolean on check of the current url and match it to the path params
    const checkCurrentUrl = function(path) {
        var url = window.location.pathname;
        return url.match(path);
    }

    const update_reason_inputs = (val) =>{
        var list = document.querySelectorAll('.input-reason');
        list.forEach(input => input.value = val);
    }

    const onChangeListeners = (event) =>{
        var urlParams = new URLSearchParams(window.location.search);
        var categories = document.getElementById('select-category');
        var activities = document.getElementById('select-activity');
        var grade_reason = document.getElementById('select-grade-reason');
        var input_reason = document.getElementById('input-reason');
        var mform_grade_reason = document.getElementById('id_reasons');
        switch (event.target) {
            case categories:
                urlParams.delete("activityid");
                if(categories.value === 'null'){
                    urlParams.delete("categoryid");
                }else{
                    urlParams.set("categoryid", categories.value);
                }
                if(checkCurrentUrl("gugcat/add")){
                    urlParams.delete("studentid");
                    var url = window.location.pathname;
                    url = url.replace("gugcat/add", "gugcat")+"?"+urlParams;
                    window.location.replace(url);
                    return;
                }
                window.location.search = urlParams;
                break;
            case activities:
                urlParams.set("activityid", activities.value);
                window.location.search = urlParams;
                break;
            case grade_reason:
                var selected = event.target.value;    
                update_reason_inputs(selected);
                if(selected === 'Other'){
                    update_reason_inputs('');
                    input_reason.style.display = 'block';
                }else{
                    input_reason.style.display = 'none';
                }
                break;
            case mform_grade_reason:
                var selectedOption = event.target.value;
                var mformReason = document.getElementById('id_otherreason');
                mformReason.value = selectedOption;
                if(selectedOption = '8'){
                    mformReason.value = '';
                    mformReason.required = true;
                }
                else
                    mformReason.required = false;
                mformReason.focus();
                break;
            default:
                break;
        }
    }

    const onClickListeners = (event) =>{
        var btn_saveadd = document.getElementById('btn-saveadd');
        var btn_release = document.getElementById('btn-release');
        var btn_import = document.getElementById('btn-import');
        var btn_coursegradeform = document.getElementById('btn-coursegradeform');
        var btn_finalrelease = document.getElementById('btn-finalrelease');
        var import_submit = document.getElementById('importgrades-submit');
        var gcat_tbl_form = document.getElementById('multigradesform');
        switch (event.target) {
            case btn_saveadd:
                btn_saveadd.classList.toggle('togglebtn');
                btn_saveadd.style.display = 'none';
                Str.get_string('saveallnewgrade', 'local_gugcat').then(function(langString) {
                    btn_saveadd.style.display = 'inline-block';
                    if(btn_saveadd.classList.contains('togglebtn')){
                        btn_saveadd.textContent = langString;
                    } else {
                        gcat_tbl_form.submit();
                    }
                });
                $(".togglemultigrd").show();
                break;
            case btn_release:
                document.getElementById('release-submit').click();
                break;
            case btn_import:
                if(!$(".gradeitems").text().includes("Moodle Grade[Date]")){
                    Str.get_string('confirmimport', 'local_gugcat').then(function(msg) {
                        var confirmation = confirm(msg);
                        if(confirmation == true) {
                            import_submit.click();
                        }
                    });
                }else{
                    import_submit.click();
                }
                break;
            case btn_coursegradeform:
                var inputarr = document.querySelectorAll('.input-percent');
                if(inputarr.length > 0){
                    inputarr.forEach(div => {
                        var invalid = div.querySelector('input.is-invalid');
                        if(invalid != null){
                            div.querySelector('div.felement').classList.add('no-after');
                        }else{
                            div.querySelector('div.felement').classList.remove('no-after');
                        }
                    });
                }
                break;
            case btn_finalrelease:
                Str.get_string('confirmfinalrelease', 'local_gugcat').then(function(msg) {
                    if(confirm(msg) == true) {
                        document.getElementById('finalrelease-submit').click();
                    }
                });
                break;
            default:
                break;
        }
    }

    return {
        init: function() {
            const GCAT = document.querySelector('.gcat-container');
            if(GCAT){
                GCAT.addEventListener('change', onChangeListeners);
                GCAT.addEventListener('click', onClickListeners);
    
                var input_reason = document.getElementById('input-reason');
                if(input_reason){
                    input_reason.addEventListener('input', (e) => {
                        update_reason_inputs(e.target.value);
                    });
                }

                var input_percentarr = document.querySelectorAll('.input-percent');
                if(input_percentarr.length > 0){
                    input_percentarr.forEach(div => {
                        var input = div.querySelector('input');
                        input.addEventListener('focus', (e) => {
                            var val = e.target.value;
                            if(val !== "" && val.match(/^[0-9]+$/) === null){
                                div.querySelector('div.felement').classList.add('no-after');
                            }else{
                                div.querySelector('div.felement').classList.remove('no-after');
                            }
                        });
                        input.addEventListener('blur', (e) => {
                            var val = e.target.value;
                            if(val !== "" && val.match(/^[0-9]+$/) === null){
                                div.querySelector('div.felement').classList.add('no-after');
                            }else{
                                div.querySelector('div.felement').classList.remove('no-after');
                            }
                        });
                    });
                    
                }

                //Show 'grade discrepancy' when grade discrepancy exist
                var grdDiscExist = document.querySelectorAll('td .grade-discrepancy');
                if (grdDiscExist.length > 0) {
                    document.getElementById('btn-grddisc').style.display = 'inline-block';
                }

                // Hide elements different pages
                if(checkCurrentUrl("gugcat/overview")){
                    document.querySelector('#btn-overviewtab').classList.add('active');
                    document.querySelector('#btn-assessmenttab').classList.remove('active');
                    var mformNotes = document.getElementById('id_notes');
                    if(mformNotes){
                        (async () => {
                            mformNotes.placeholder = await Str.get_string('specifyreason', 'local_gugcat');
                            mformNotes.required = true;
                        })();
                    }
                }else if(checkCurrentUrl("gugcat/index")){
                    document.getElementById('btn-release').style.display = 'block';
                    var nodeArr = Array.from(document.querySelectorAll('.gradeitems'));
                    if(nodeArr.find(node => node.innerHTML !== 'Moodle Grade<br>[Date]')){
                        document.getElementById('btn-saveadd').style.display = 'inline-block';
                        var btnnewgrade = document.querySelectorAll('.addnewgrade');
                        btnnewgrade.forEach(e => {
                            e.style.display = 'block';
                        });

                        var hideshowgrade = document.querySelectorAll('.hide-show-grade');
                        hideshowgrade.forEach(element => {
                            element.style.display = 'block';
                        });
                    }
                }else if(checkCurrentUrl("gugcat/add") || checkCurrentUrl("gugcat/edit")){
                    //Add placeholder
                    var mformReason = document.getElementById('id_otherreason');
                    var mformNotes = document.getElementById('id_notes');
                    (async () => {
                        mformReason.placeholder = await Str.get_string('pleasespecify', 'local_gugcat');
                        mformNotes.placeholder = await Str.get_string('specifyreason', 'local_gugcat');
                        mformNotes.required = true;
                    })();
                }
            }
        }
    };
});
