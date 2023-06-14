<?php
/**
 * @file
 * Provides ExternalModule class for Multi-DET module.
 */

namespace SaveSurveyPdfToFieldModule\ExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;

require_once "utility.php";
define("ATTEMPT_LIMIT", 30);

/**
 * ExternalModule class for save_survey_pdf_to_field.
 */
class ExternalModule extends AbstractExternalModule {

    /**
     * @inheritdoc.
     */
    function hook_survey_complete($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {

      global $Proj, $redcap_version;

      $source_instruments = AbstractExternalModule::getProjectSetting('ssptf_source_instrument');

      //check if instrument is the same one set in config
      // NOTE: array_keys($foo, NULL) throws an error in PHP 8+
      if (is_null($instrument)) {
        $indices == FALSE;
      } else {
        $indices = array_keys($source_instruments, $instrument);
      }

      //abort hook if not
      if($indices === FALSE) {
        return 0;
      }

      // Iterate along dictionary of targeted keys
      foreach ($indices as $target => $index) {

          //get target upload field from config
          $target_fields = AbstractExternalModule::getProjectSetting('ssptf_target_upload_field');
          $target_upload_field = $target_fields[$index];

          //Determine target field name
          $matches = array();
          $target_upload_field_name = $target_upload_field;
          if (preg_match('#^(.+)_([0-9]+)$#', $target_upload_field, $matches)) {
              $target_upload_field_name = $matches[1];
              $index = (int)$matches[2];
          }

          //check if we can write to $target_upload_field_name else check other base name variations
          $writable = false;
          $extension = '';
          for($count = $index; $count <= ATTEMPT_LIMIT + 1; $count++) {
              $field = $target_upload_field_name . $extension;
              if(isset($Proj->metadata[$field]) && $Proj->metadata[$field]['element_type'] == 'file' && !fieldHasValue($project_id, $record, $field, $event_id))
              {
                  $target_upload_field_name = $field;
                  $writable = true;
                  break;
              }

              $extension = "_" . $count;
          }

          //make pdf and store it in a temp directory
          $path_to_temp_file = makePDF($project_id, $record, $instrument, $event_id, $repeat_instance);

          if ($writable) {
              //upload pdf into designated upload field
              $doc_id = uploadPdfToEdocs($path_to_temp_file, $instrument);
              setUploadField($project_id, $record, $event_id, $target_upload_field_name, $doc_id);

              REDCap::logEvent("save_survey_pdf_to_field", "save_survey_pdf_to_field uploaded a new PDF to a field.\n$target_upload_field_name = $doc_id", null, $record, $event_id, $project_id);
          } else {
              //log failure
              REDCap::logEvent("save_survey_pdf_to_field alert", "save_survey_pdf_to_field failed to save a PDF from the '$instrument' instrument to the '$target_upload_field_name' field.", null, $record, $event_id, $project_id);

              //send error email
              $receiver_addr = AbstractExternalModule::getProjectSetting('ssptf_receiver_address');
              $sender_addr = AbstractExternalModule::getSystemSetting('ssptf_sender_address');
              $cc = AbstractExternalModule::getSystemSetting('ssptf_cc');
              $subject = "ERROR: PDF of REDCap instrument could not be saved.";
              $url = APP_PATH_WEBROOT_FULL . 'redcap_v' . $redcap_version . "/DataEntry/record_home.php?pid=" . $project_id . "&id=" . $record . "&arm=" . getArm();
              $body = "ERROR: REDCap failed to save a PDF of an instrument for this
                  research subject: " . "<a href=\"". $url . "\">" . $url . "</a>" .
                  " That document is attached to this message. Please review this REDCap
                  project's configuration, make changes as needed,and upload this PDF to
                  this research subject's record.";
              $sent = sendEmail($receiver_addr, $sender_addr, $cc, $subject, $body, $path_to_temp_file);

              //notify user if email failed to send
              if (!$sent) {
                  REDCap::logEvent("save_survey_pdf_to_field alert", "save_survey_pdf_to_field could not send email containing the PDF from the '$instrument' instrument.", null, $record, $event_id, $project_id);
              } else {
                  REDCap::logEvent("save_survey_pdf_to_field alert", "save_survey_pdf_to_field sent an email containing the PDF from the '$instrument' instrument.", null, $record, $event_id, $project_id);
              }
          }

          unlink($path_to_temp_file);
      }
    }
}
