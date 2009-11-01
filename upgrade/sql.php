<?php
if(!create_billing_options($db)) {
	echo("<tr>\n
					<td>UPDATED TABLE ".PRFX."BILLING_OPTIONS</td>\n
					<td><font color=\"red\"><b>Failed: </b> </font> ".$db->ErrorMsg() ."</td>\n
			</tr>\n");
			$error_flag = true;
} else {
	echo("<tr>\n
					<td>UPDATED TABLE ".PRFX."BILLING_OPTIONS</td>\n
					<td><font color=\"green\"><b>OK</b></font></td>\n
			<tr>\n");
}

##################################
# create_table_company				#
##################################
if(!create_table_company($db)){
	echo("<tr>\n
				<td>UPDATED TABLE ".PRFX."TABLE_COMPANY</td>\n
				<td><font color=\"red\"><b>Failed:</b></font> ". $db->ErrorMsg() ."</td>\n
			</tr>");
	$error_flag = true;	
} else {
	echo("<tr>\n
				<td>UPDATED TABLE ".PRFX."TABLE_COMPANY</td>\n
				<td><font color=\"green\"><b>OK</b></font></td>\n
			</tr>\n");
}
###############################
# UPDATE CUSTOMER TABLE				#
###############################
if(!create_table_customer($db)) {
	echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."TABLE_CUSTOMER</td>\n
			<td><font color=\"red\"><b>Failed:</b></font> ". $db->ErrorMsg() ."</td>\n
		</tr>\n");
	$error_flag = true;
} else {
	echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."TABLE_CUSTOMER</td>
			<td><font color=\"green\"><b>OK</b></font></td>\n
		</tr>\n");
}
###############################
# create_labor_rate				#
###############################
if(!create_labor_rate($db)) {
	echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."LABOR_RATE</td>\n
			<td><font color=\"red\"><b>Failed:</b></font> ". $db->ErrorMsg() ."</td>\n
		</tr>\n");
	$error_flag = true;
} else {
	echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."LABOR_RATE</td>
			<td><font color=\"green\"><b>OK</b></font></td>\n
		</tr>\n");
}
##################################
# create_setup							#
##################################
if(!create_setup($db) ) {
echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."SETUP</td>\n
			<td><font color=\"red\"><b>Failed:</b></font> ". $db->ErrorMsg() ."</td>\n
		</tr>\n");
	$error_flag = true;
} else {
	echo("<tr>\n
				<td>UPDATED TABLE ".PRFX."SETUP</td>\n
				<td><font color=\"green\"><b>OK</b></font></td>\n
			</tr>\n");
}


##################################
# create_acl								#
##################################
if(!create_acl($db) ) {
echo("<tr>\n
			<td>UPDATED TABLE ".PRFX."ACL</td>\n
			<td><font color=\"red\"><b>Failed:</b></font> ". $db->ErrorMsg() ."</td>\n
		</tr>\n");
	$error_flag = true;
} else {
	echo("<tr>\n
				<td>UPDATED TABLE ".PRFX."ACL</td>\n
				<td><font color=\"green\"><b>OK</b></font></td>\n
			</tr>\n");
}	
/* START SQL STUFF FOR UPGRADE */

function create_billing_options($db) {

		$q = "INSERT IGNORE INTO `".PRFX."CONFIG_BILLING_OPTIONS` VALUES (6,'deposit_billing','Direct Deposit',0)";
	
		if(!$rs = $db->execute($q) ) {
			return false;
		} else {
			return true;
		}
}

function create_table_company($db)
{
	$q="ALTER TABLE `".PRFX."TABLE_COMPANY` CHANGE `COMPANY_ADDRESS` `COMPANY_ADDRESS` varchar(100) NOT NULL default '';";

	$rs = $db->Execute($q);
		if(!$rs) {
			return false;
		} else {
			return true;
		}
}
function create_table_customer($db){

	$q="ALTER TABLE `".PRFX."TABLE_CUSTOMER` CHANGE `DISCOUNT` `DISCOUNT`  decimal(10,2) NOT NULL default '0.00';" ;

	$rs = $db->Execute($q);
		if(!$rs) {
			return false;
		} else {
			return true;
		}
}

function create_labor_rate($db) {

	$q="ALTER TABLE `".PRFX."TABLE_LABOR_RATE` ADD `LABOR_TYPE` varchar(20) collate latin1_general_ci default NULL, ADD `LABOR_MANUF` varchar(30) collate latin1_general_ci default NULL ;";

		$rs = $db->Execute($q);
	 
			return true;
			
			
		
}

function create_setup($db) {
   $q = "ALTER TABLE `".PRFX."SETUP`
  ADD `CHECK_PAYABLE` varchar(30) collate latin1_general_ci default NULL,
  ADD `DD_NAME` varchar(50) collate latin1_general_ci default NULL,
  ADD `DD_BANK` varchar(50) collate latin1_general_ci default NULL,
  ADD `DD_BSB` varchar(15) collate latin1_general_ci default NULL,
  ADD `DD_ACC` varchar(50) collate latin1_general_ci default NULL,
  ADD `DD_INS` varchar(200) collate latin1_general_ci default NULL,
  CHANGE `INVOICE_TAX` `INVOICE_TAX` decimal(3,1) NOT NULL default '0.00'; END";

    	$rs = $db->Execute($q);
	return true;
}

function create_acl($db) {
	$q = "INSERT IGNORE INTO `".PRFX."ACL` VALUES (66, 'billing:proc_deposit', 1, 1, 1, 1, 0)";

			$rs = $db->Execute($q);
			if(!$rs) {
				return false;
			} else {
				return true;
			}

}

?>
