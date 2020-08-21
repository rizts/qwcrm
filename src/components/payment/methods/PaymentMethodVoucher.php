<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

class PaymentMethodVoucher {
    
    private $app = null;
    private $VAR = null;  
    
    public function __construct() {
        
        // Set class variables
        $this->app = \Factory::getApplication();
        $this->VAR = &\CMSApplication::$VAR;        
        
        // Check the Voucher exists, get the voucher_id and set amount
        if(!$this->VAR['qpayment']['voucher_id'] = $this->app->components->voucher->getIdByVoucherCode($this->VAR['qpayment']['voucher_code'])) {
            Payment::$payment_valid = false;
            $this->app->system->variables->systemMessagesWrite('danger', _gettext("There is no Voucher with that code."));                   
        } else {                        
            $this->VAR['qpayment']['amount'] = $this->app->components->voucher->getRecord($this->VAR['qpayment']['voucher_id'], 'unit_net');
        }        
        
    }
    
    // Pre-Processing
    public function preProcess() {
        
        // Make sure the Voucher is valid and then pass the amount to the next process
        if(!$this->app->components->voucher->checkRecordAllowsRedeem($this->VAR['qpayment']['voucher_id'], $this->VAR['qpayment']['invoice_id'])) {
            Payment::$payment_valid = false;
            $this->app->system->variables->systemMessagesWrite('danger', _gettext("This Voucher is not valid or cannot be redeemed."));
            return false;                
        }
        
        return true;

    }

    // Processing
    public function process() {
        
        // Build additional_info column
        $this->VAR['qpayment']['additional_info'] = $this->app->components->payment->buildAdditionalInfoJson();    

        // Insert the payment with the calculated information
        $payment_id = $this->app->components->payment->insertRecord($this->VAR['qpayment']);
        if($payment_id) {
            
            Payment::$payment_processed = true;
            
            // Change the status of the Voucher to prevent further use
            $this->app->components->voucher->updateStatus($this->VAR['qpayment']['voucher_id'], 'redeemed', true);

            // Update the redeemed Voucher with the missing redemption information
            $this->app->components->voucher->updateRecordAsRedeemed($this->VAR['qpayment']['voucher_id'], $this->VAR['qpayment']['invoice_id'], $payment_id);
            
        }
        
        return;
        
    }
    
    // Post-Processing 
    public function postProcess() { 
        
        // Set success/failure message
        if(!Payment::$payment_processed) {
        
            $this->app->system->variables->systemMessagesWrite('danger', _gettext("Voucher was not applied successfully."));
        
        } else {            
            
            $this->app->system->variables->systemMessagesWrite('success', _gettext("Voucher applied successfully."));

        }
        
        return;
       
    }
    
}