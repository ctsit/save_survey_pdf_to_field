# End-of-Life Notice

This project is officially end-of-life. The Vanderbilt University Medical Center team incorporated the functionality of this module into REDCap 10.6.0 on 2020-11-30. They are part of the survey option “Save a PDF of completed survey response to a File Upload field”. We recommend all users of the module reconfigure their REDCap projects to use the features in REDCap core. Release 1.1.3 of this module will be the last version released. Users are welcome to fork this module for their own use, but UF CTS-IT will no longer be providing support or updates.

# Save Survey PDF to a Field

This REDCap module generates a PDF of a survey upon completion and saves it to a REDCap file upload field. It is configured at the project level with source instrument/target field pairs. In the event of save errors, the PDF is sent to a backup email address.

## Prerequisites
- REDCap >= 8.0.0 (for versions < 8.0.0, [REDCap Modules](https://github.com/vanderbilt/redcap-external-modules) is required).


## System-level Installation
- Clone this repo into to `<redcap-root>/modules/save_survey_pdf_to_field_v<module_version_number>`.
- Go to **Control Center > Manage External Modules** and enable _Save Survey PDF to a Field_.
- Still in **Control Center > Manage External Modules**, configure the module with a 'From Address' for any emails this module sends out. Optionally set an email address that will be CC'd in any such emails. Emails will only be sent if the module fails to save a PDF of a survey.


## Project-level Installation

Before you can configure this module, you will need to create the surveys and fields needed for configuration. This module can save PDFs for as many surveys as desired, but each survey is saved as its own PDF. The PDFs must be saved to file upload fields in a non-survey instrument. On that instrument create a file upload field for each survey that needs to be saved.

Should you need to save multiple revisions of a survey PDF for a single record\_id, use the Online Designer to copy the file upload field as many times as you need. This module will use the basename of the original field name and append '\_1', '\_2', '\_3', etc. looking for additional fields into which it can save the PDF. If your original field name has the format `field_name_1`, the module will increment the number at the end of field name by `1` and look for `field_name_2`, `field_name_3`, etc. until it finds an empty file upload field.


With the surveys and file upload fields created, you can proceed with module configuration.

- For each project on which you want to use this module, go to the project home page, click on **Manage External Modules** link, and then enable _Save Survey PDF to a Field_ for that project.
- Configure the module with the an email address that should receive generated PDFs for this project should this module be unable to save the PDF.
- Also configure the module with the names of surveys for which you want to save PDFs and the field names in which those surveys should be stored. Survey names and file names are entered in pairs to allow as many different surveys to be saved as needed.


## Using the module

A series of file upload fields to receive saved PDFs, if configured, would look like this in the Online Designer.

![Upload fields in the Online Designer](img/upload_fields_in_online_designer.png)

Once the first PDF has been saved, those same fields would look like this in the Data Entry form

![Upload fields in the Data Entry page](img/upload_fields_in_data_entry.png)


## In case of errors

If the module cannot save the PDF it will attempt to send an email with the PDF attached to the email address configured on the project.  It will use the _From Address_ configured at the system level for the module. It will also log an error to the projects REDCap log.  Should the _email_ fail, that will also be logged.  Those log events look like this:

![Errors in the REDCap project log](img/errors_in_the_log.png)
