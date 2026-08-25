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
 * English language strings for PersonalityFinder.
 *
 * @package    mod_personalityfinder
 * @copyright  2026 Johan Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'PersonalityFinder';
$string['modulename'] = 'PersonalityFinder';
$string['modulename_help'] = 'Use the PersonalityFinder activity to build a user-defined personality reflection instrument with attributes, two focus dimensions, general dimensions, and an optional matrix.';
$string['modulenameplural'] = 'PersonalityFinders';
$string['pluginadministration'] = 'PersonalityFinder administration';
$string['personalityfinder:addinstance'] = 'Add a new PersonalityFinder activity';
$string['personalityfinder:view'] = 'View PersonalityFinder activity';
$string['personalityfinder:manage'] = 'Manage PersonalityFinder instrument';
$string['privacy:metadata'] = 'PersonalityFinder stores respondent submissions and calculated reflection results when learners complete the activity.';

$string['activityintro'] = 'Reflect on personality attributes, focus dimensions, and general dimensions.';
$string['builder'] = 'Instrument builder';
$string['builderheading'] = 'PersonalityFinder instrument builder';
$string['builderintro'] = 'Use this early builder to edit the JSON configuration for this PersonalityFinder activity. Later versions will add visual editors for attributes, focus dimensions, general dimensions, and matrix text.';
$string['builderlink'] = 'Open instrument builder';
$string['configjson'] = 'Instrument JSON';
$string['configjson_help'] = 'The JSON configuration defines the attributes, focus dimensions, general dimensions, matrix, result text, and reflection prompts for this activity.';
$string['configjsoninvalid'] = 'The instrument JSON is not valid: {$a}';
$string['configjsonsaved'] = 'The instrument JSON has been saved.';
$string['configjsonreset'] = 'Load example JSON';
$string['configjsonresetconfirm'] = 'Load the example JSON into the editor. You must still save the form to keep it.';
$string['defaultinstrument'] = 'Example personality reflection instrument';
$string['disclaimer'] = 'This activity is intended for reflection and conversation. It is not a clinical, diagnostic, or formal psychometric assessment.';
$string['editinstrument'] = 'Edit instrument';
$string['focusdimensions'] = 'Focus dimensions';
$string['generaldimensions'] = 'General dimensions';
$string['attributes'] = 'Personality attributes';
$string['matrix'] = 'Matrix';
$string['nojsonyet'] = 'No instrument JSON has been saved yet. A default example configuration is shown below.';
$string['saveinstrument'] = 'Save instrument';
$string['viewactivity'] = 'View activity';
$string['instrumentpreview'] = 'Instrument preview';
$string['notreadyforresponses'] = 'This alpha scaffold does not yet collect respondent submissions. It provides the Moodle-compliant foundation, JSON configuration, and preview structure.';
$string['jsonsummary'] = 'JSON configuration summary';
$string['sectioncount'] = '{$a->count} item(s)';
$string['unknown'] = 'Unknown';
$string['returntocourse'] = 'Return to course';
$string['name'] = 'Name';
$string['intro'] = 'Description';
$string['exampleleft'] = 'Left pole';
$string['exampleright'] = 'Right pole';
$string['scale'] = 'Scale';
$string['items'] = 'Items';
$string['quadrants'] = 'Quadrants';
$string['reflectionprompts'] = 'Reflection prompts';
$string['errorreadingconfig'] = 'The saved instrument JSON could not be read. Please open the builder and correct the JSON.';
$string['instrumenttitle'] = 'Instrument title';
$string['jsondownloadnote'] = 'You can copy this JSON and reuse it in another PersonalityFinder activity.';
$string['cachedef_config'] = 'PersonalityFinder configuration cache';
$string['instrumentbuilderheading'] = 'PersonalityFinder Instrument Builder';
$string['instrumentbuilderintro'] = 'Use the Instrument Builder to create, edit, import, and export the PersonalityFinder JSON instrument.';
$string['instrumentbuilderwarning'] = 'Editing or replacing the instrument after respondents have started may affect existing attempts. For live use, create a new activity.';
$string['instrumentbuildernotavailable'] = 'Create the activity first, then return to Settings to open the Instrument Builder.';
$string['visualbuilderheading'] = 'Attributes';
$string['visualbuilderintro'] = 'Edit the respondent instruction and the list of personality attributes.';
$string['jsoneditorheading'] = 'Import / export JSON';
$string['jsoneditorintro'] = 'Use this section to import, export, back up, or troubleshoot the full instrument JSON. Ordinary editing should be done in the builder sections below.';
$string['importjsonfile'] = 'Import JSON file';
$string['importjsonfile_help'] = 'Upload a .json file that contains a complete PersonalityFinder instrument configuration. The file is validated before it replaces the current instrument JSON.';
$string['importjsonbutton'] = 'Validate and import JSON file';
$string['importjsonmissing'] = 'Please choose a JSON file to import.';
$string['importjsonsaved'] = 'The imported JSON file has been validated and saved.';
$string['jsonadvancednote'] = 'Advanced option: paste, inspect, validate, troubleshoot, or manually save the complete JSON below.';
$string['savejsonbutton'] = 'Save instrument JSON';
$string['validatejsonbutton'] = 'Validate only';
$string['validatejsonsuccess'] = 'The JSON is valid.';
$string['exportjsonbutton'] = 'Export instrument JSON';
$string['attributesbuilderheading'] = 'Attributes';
$string['attributesbuilderintro'] = 'Edit the attributes respondents may select. These attributes are user-defined and should reflect the language of the instrument creator.';
$string['attribute'] = 'Attribute';
$string['attributelabel'] = 'Attribute label';
$string['attributedescription'] = 'Description';
$string['attributecategory'] = 'Category';
$string['attributeenabled'] = 'Enabled';
$string['deleteattribute'] = 'Delete';
$string['deleteattributeconfirm'] = 'Delete cannot be undone. Are you sure?';
$string['saveattribute'] = 'Save';
$string['addattribute'] = 'Add attribute';
$string['newattribute'] = 'New attribute';
$string['noattributes'] = 'No attributes have been defined yet.';

$string['attributeprompt'] = 'Respondent instruction';
$string['attributeprompt_help'] = 'This text tells respondents what to do with the attribute list. For example: Please read through the list below and mark the personality attributes that you have and how others may describe your personality.';
$string['defaultattributeprompt'] = 'Please read through the list below and mark the personality attributes that you have and how others may describe your personality.';

$string['twodimensionsheading'] = 'Two personality dimensions';
$string['twodimensionsintro'] = 'Define the two bipolar dimensions that will form the main measurement section of the instrument. Respondents will rate themselves between paired statements, and their scores on these two dimensions will later be plotted on a 2x2 matrix.';
$string['twodimensionsprompt'] = 'Instructions shown to respondents';
$string['twodimensionsprompt_help'] = 'This text tells respondents how to complete the two personality dimensions section. For example: Please rate where you naturally fall between each pair of statements. There are no right or wrong answers.';
$string['defaulttwodimensionsprompt'] = 'Please rate where you naturally fall between each pair of statements. There are no right or wrong answers.';
$string['focusscalepoints'] = 'Scale points';
$string['focusscalepoints_help'] = 'Choose how many numerical anchors respondents will see for the semantic differential items. The same scale is used for both personality dimensions. The minimum is 2 and the maximum is 8.';
$string['focusscalepreviewnote'] = 'Respondents will see the same scale for both dimensions.';
$string['scalepreview'] = 'Scale preview';
$string['dimensionnumber'] = 'Dimension {$a}';
$string['leftpoleextreme'] = 'Left pole (extreme)';
$string['rightpoleextreme'] = 'Right pole (extreme)';
$string['dimensiondescription'] = 'Description';
$string['semanticdifferentialsfor'] = 'Semantic differentials for Dimension {$a}';
$string['leftstatement'] = 'Left statement';
$string['rightstatement'] = 'Right statement';
$string['actions'] = 'Actions';
$string['addsemanticdifferential'] = 'Add semantic differential';
$string['nosemanticdifferentials'] = 'No semantic differentials have been defined yet.';
$string['savedimension'] = 'Save dimension';
$string['deletesemanticconfirm'] = 'Delete cannot be undone. Are you sure?';
$string['newleftstatement'] = 'New left statement';
$string['newrightstatement'] = 'New right statement';
$string['defaultleftstatement'] = 'Left statement';
$string['defaultrightstatement'] = 'Right statement';
$string['twodimensionsmatrixnote'] = 'After respondents complete the instrument, their scores on these two dimensions will be plotted on a 2x2 matrix. The meanings of each quadrant can be defined in the matrix interpretation section.';
$string['prepopulatedfocusdimension'] = 'Pre-populated from the earlier focus dimension.';
$string['matrixinterpretationheading'] = 'Matrix interpretation';
$string['matrixinterpretationintro'] = 'Define the meaning of the four quadrants created by the two personality dimensions. Dimension 1 is displayed on the horizontal axis. Dimension 2 is displayed on the vertical axis, with its right pole at the top. Use reflection prompts to invite thoughtful consideration rather than diagnosis.';
$string['matrixenabled'] = 'Enable matrix interpretation';
$string['savematrix'] = 'Save matrix';
$string['quadrantnumber'] = 'Quadrant {$a}';
$string['quadrantlabel'] = 'Quadrant label';
$string['quadrantsummary'] = 'Summary';
$string['quadrantassignments'] = 'Possible assignments or roles';
$string['quadrantassignments_help'] = 'Enter one possible assignment, role, placement, or practical suggestion per line. The wording should remain reflective and non-diagnostic.';
$string['quadrantprompt'] = 'Reflection prompt(s)';
$string['generalpersonalitydimensionsheading'] = 'General personality dimensions';
$string['generalpersonalitydimensionsintro'] = 'Define the additional personality dimensions shown after the two main personality dimensions. The first two rows are calculated from the earlier two-dimension exercise and normalised to this section\'s scale.';
$string['generaldimensionsprompt'] = 'Instructions shown to respondents';
$string['generaldimensionsprompt_help'] = 'This text tells respondents how to complete the general personality dimensions section.';
$string['defaultgeneraldimensionsprompt'] = 'A further way to describe your personality is to rate yourself on each of the following additional dimensions.';
$string['generalscalepoints'] = 'How many anchors on the rating scale?';
$string['generalscalepoints_help'] = 'Choose the number of points respondents will see for each general personality dimension. The first two dimensions are calculated from the two personality dimensions exercise and normalised to this scale. The minimum is 2 and the maximum is 8.';
$string['generaldimensionsbuilderheading'] = 'Dimensions';
$string['generaldimensionsbuilderintro'] = 'The first two dimensions come from your Two personality dimensions exercise. Additional dimensions can be added, edited, or removed.';
$string['calculateddimension'] = 'Calculated';
$string['calculateddimensionexplain'] = 'This score is calculated from the responses in the Two personality dimensions exercise. Respondents will not rate this dimension again in this section.';
$string['calculateddimensionsnote'] = 'The first two dimensions are calculated from the totals in the Two personality dimensions exercise and normalised to the scale you choose above. Learners will not rate these again in this section.';
$string['addgeneraldimension'] = 'Add new dimension';
$string['savegeneraldimensions'] = 'Save general dimensions';
$string['savegeneraldimension'] = 'Save dimension';
$string['deletegeneraldimensionconfirm'] = 'Delete cannot be undone. Are you sure?';
$string['newgeneralleftlabel'] = 'Left pole';
$string['newgeneralrightlabel'] = 'Right pole';
$string['submitresponses'] = 'Submit responses';
$string['attributemyself'] = 'I describe myself this way';
$string['attributeothers'] = 'Others may describe me this way';
$string['responsesaved'] = 'Your PersonalityFinder responses have been saved.';
$string['yourresults'] = 'Your reflection summary';
$string['startactivity'] = 'Complete activity';
$string['resubmitactivity'] = 'Update my responses';
$string['redoactivity'] = 'Redo this reflection';
$string['redoactivityconfirm'] = 'This will permanently clear your saved PersonalityFinder response and report for this activity. You will need to complete the reflection again from the beginning. Continue?';
$string['redoactivityhelp'] = 'To redo this reflection, first clear the saved response. The form will then reopen from the beginning.';
$string['previousresponsecleared'] = 'Your previous PersonalityFinder response has been cleared. Please complete the reflection again.';
$string['calculatedfocusdimension'] = 'Calculated from your Two personality dimensions responses';
$string['matrixresult'] = 'Matrix result';
$string['selectedattributes'] = 'Selected attributes';
$string['selectedselfattributes'] = 'Attributes I selected for myself';
$string['selectedotherattributes'] = 'Attributes I think others may see';
$string['noattributesselected'] = 'No attributes selected in this category.';
$string['focusdimensionresults'] = 'Two personality dimension results';
$string['generaldimensionresults'] = 'General personality dimension results';
$string['scoreoutof'] = '{$a->score} / {$a->max}';

$string['allowresubmit'] = 'Allow learners to update their response';
$string['allowresubmit_helptext'] = 'When enabled, a learner may reopen the activity and replace their previous response. When disabled, the first saved response is locked for that learner.';
$string['resubmissionnotallowed'] = 'Your response has been saved and cannot be changed in this activity.';
$string['youarehere'] = 'You are here';
$string['matrixpositionheading'] = 'Your approximate position';
$string['latestresponse'] = 'Latest response';
$string['privacy:metadata:personalityfinder_responses'] = 'Stores respondent submissions and calculated reflection results for PersonalityFinder activities.';
$string['privacy:metadata:personalityfinder_responses:personalityfinderid'] = 'The PersonalityFinder activity instance.';
$string['privacy:metadata:personalityfinder_responses:userid'] = 'The user who submitted the response.';
$string['privacy:metadata:personalityfinder_responses:responsesjson'] = 'The respondent’s raw selected responses stored as JSON.';
$string['privacy:metadata:personalityfinder_responses:resultsjson'] = 'The calculated reflection results stored as JSON.';
$string['privacy:metadata:personalityfinder_responses:timecreated'] = 'The time the response was first created.';
$string['privacy:metadata:personalityfinder_responses:timemodified'] = 'The time the response was last updated.';
$string['possibleassignments'] = 'Possible assignments';
$string['matrixresultintro'] = 'Your scores are plotted on the two dimensions below. The intersection shows your primary style for this instrument.';
$string['matrixresultdisclaimer'] = 'These styles are not fixed labels. They are lenses to help you reflect on your natural tendencies and how you may use them wisely.';
$string['primarystyle'] = 'Your primary style';
$string['scores'] = 'Scores';
$string['howtoreadthis'] = 'How to read this';
$string['matrixpositionbasedonscores'] = 'Your position is based on your two calculated dimension scores.';
$string['matrixreadhorizontal'] = 'The horizontal axis shows where the first dimension tends to lie.';
$string['matrixreadvertical'] = 'The vertical axis shows where the second dimension tends to lie.';
$string['scalenote'] = 'Scale note:';
$string['matrixscalenote'] = 'Scores start at 1 and end at the maximum scale value. The centre is halfway between the two middle values, for example 2.5 on a 1–4 scale.';


$string['downloadreflectionpdf'] = 'Download reflection PDF';
$string['reflectionsummarypdf'] = 'Personality reflection summary';
$string['reflectionfilename'] = 'reflection-summary';
$string['generatedfor'] = 'Generated for';
$string['generatedon'] = 'Generated on';
$string['submittedon'] = 'Submitted on';
$string['reflectionpdfdisclaimer'] = 'This document is a reflection summary, not a diagnosis, selection report, or formal psychometric assessment. Use it as a conversation aid and a prompt for thoughtful self-reflection.';
$string['noresultstodownload'] = 'There is no saved reflection to download yet.';
$string['quadrantreflectionoverview'] = 'Quadrant reflection overview';

$string['scaleanchorposition'] = 'Scale position {$a}';
$string['leanleft'] = 'Leans left';
$string['leanright'] = 'Leans right';
$string['reflectioncloudheading'] = 'Personality reflection cloud';
$string['reflectioncloudintro'] = 'These words are drawn from your general personality dimensions. Larger words show the poles you leaned toward more strongly in this reflection.';
$string['reflectioncloudnote'] = 'This is a visual reflection aid, not a diagnosis. It helps you notice which words may currently describe your preferences most strongly.';
$string['attributenotapplicable'] = 'Not applicable to me';
$string['attributecardsintro'] = 'You reflected the following personality attributes. Attributes marked not applicable, or not selected by you, are not shown here.';
$string['attributeselfbadge'] = 'I selected this';
$string['attributeothersbadge'] = 'Others may see this in you as well';
$string['attributesharedbadge'] = 'Shared reflection';

$string['showdescription'] = 'Show description';
$string['hidedescription'] = 'Hide description';
