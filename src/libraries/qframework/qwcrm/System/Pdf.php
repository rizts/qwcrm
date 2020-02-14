<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

class Pdf extends System {
    
    /*
     * Local holder for the mPDF object
     */
    private $mpdf;

    /*
     * This is wrapper function whilst Pdf class is not autoloaded, when it is this should go in the contructore
     */
    private function getMpdf($pdf_template) {
               
        // mPDF Defines
        //define('_MPDF_TEMP_PATH', '../../common/tempfiles');         // Folders for temporary files
        //define('_MPDF_RRFONTDATAPATH', '../../common/tempfiles');    // if you wish to use a different folder for temporary files you should define this constant
        
        // Set mPDF configuration
        // https://mpdf.github.io/configuration/configuration-v7-x.html + All variables can be changed at runtime ($this->mpdf->SetTitle() etc...) or set in the constructor array ($mpdfConfig), see this link
        // Constructor Defaults are here: https://github.com/mpdf/mpdf/blob/development/src/Config/ConfigVariables.php + https://mpdf.github.io/reference/mpdf-functions/construct.html
        // Full defaults D:\websites\htdocs\projects\qwcrm\src\libraries\vendor\mpdf\mpdf\src\Config\ConfigVariables.php
        // $constructor from D:\websites\htdocs\projects\qwcrm\src\libraries\vendor\mpdf\mpdf\src\Mpdf.php
        // Debugging notes - https://mpdf.github.io/troubleshooting/corrupt-pdf-file.html
        /*Default Constructor which is merged with the user supplied one
         *$constructor = [
			'mode' => '',
			'format' => 'A4',
			'default_font_size' => 0,
			'default_font' => '',
			'margin_left' => 15,
			'margin_right' => 15,
			'margin_top' => 16,
			'margin_bottom' => 16,
			'margin_header' => 9,
			'margin_footer' => 9,
			'orientation' => 'P',
		];*/
        
        $mpdfConfig = [
            'debug' => true,
		];
                
        // Initialize mPDF    
        $this->mpdf = new \Mpdf\Mpdf($mpdfConfig);
                
        // Not needed when using full page import, should take it from the page - does not like parsing the header? not HTML5 compliant
        //$this->mpdf->SetTitle('My Title');

        // mPDF now supports setting curlAllowUnsafeSslRequests (prevents red crosses where images should be, when using https with old ROOT CA Store)
        $this->mpdf->curlAllowUnsafeSslRequests = true;
        
        // Build the PDF       
        try
        {            
            $this->mpdf->WriteHTML($pdf_template);
        }        
        // Note: safer fully qualified exception name used for catch
        catch (\Mpdf\MpdfException $e)
        {   
            
            // Process the exception, log, print etc.
            $message = $e->getMessage();
            
            //$this->app->system->variables->systemMessagesWrite('danger', _gettext("The PDF has failed to build successfully."));
            $this->app->system->variables->systemMessagesWrite('danger', _gettext("There is most likely an issue with the printing template."). ' <strong>`'.\CMSApplication::$VAR['component'].':'.\CMSApplication::$VAR['page_tpl'].'`</strong>');                
            $this->app->system->variables->systemMessagesWrite('danger', $message);
                            
            return false;
        }
        
        return true;
        
    }
    
    // Output a PDF in the browser
    public function mpdf_output_in_browser($pdf_filename, $pdf_template) {
        
        // Intialise mPDF
        if($this->getMpdf($pdf_template))
        {
            // Output the PDF to the browser
            $this->mpdf->Output($pdf_filename.'.pdf', 'I');     
            
            // I think this exit prevents issues
            die();
        
        } else {         
            // Load 404 page with the error/system messages            
            die($this->app->system->page->load_page('get_payload', 'core', '404', ''));
            //$this->app->system->page->force_error_page('file', __FILE__, __FUNCTION__, '', '', _gettext("Could not open the Setup Log to save the record."));
        }

    }

    // Return a PDF in a variable
    public function mpdf_output_as_variable($pdf_filename, $pdf_template) {
        
        // Intialise mPDF
        if($this->getMpdf($pdf_template))
        {
            // Return the PDF as a string
            return $this->mpdf->Output($pdf_filename.'.pdf', 'S');  
            
        } else {
            
            // Clear any onscreen notifications        
            $this->app->system->general->ajax_clear_onscreen_notifications();
        
            // Load error page with the messages via ajax // Output the system message to the browser (if allowed)
            $this->app->system->general->ajax_output_system_messages_onscreen();
            return false;
        }

        

    }
    
}