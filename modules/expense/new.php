<?php

require(INCLUDES_DIR.'modules/workorder.php');

// Load PHP Language Translations
$langvals = gateway_xml2php();

$last_record_id = last_record_id_lookup($db);
$new_record_id = $last_record_id + 1;

// If details submitted insert record, if non submitted load new.tpl and populate values
    if((isset($VAR['submit'])) || (isset($VAR['submitandnew']))) {
        
        if($run != insert_new_expense($db,$VAR)){
            $smarty->assign('error_msg', 'Falied to insert Expense');
            $BuildPage .= $smarty->fetch('core'.SEP.'error.tpl');
            echo "expense insert error";

            } else {

                if (isset($VAR['submitandnew'])){

                     // Submit New Expense and reload page
                     force_page('expense', 'new&page_title=');
                     exit;

                } else {

                    // Submit and load Expense View Details
                    force_page('expense', 'expense_details&expense_id='.$new_record_id.'&page_title='.$langvals['expense_details_title']);
                    exit;

                 }
            }

} else {
            
    $smarty->assign('new_record_id', $new_record_id);
    $smarty->assign('tax_rate', tax_rate($db));
    $BuildPage .= $smarty->fetch('expense/new.tpl');

       }