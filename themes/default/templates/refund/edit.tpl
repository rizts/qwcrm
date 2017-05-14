<!-- edit.tpl -->
<script src="{$theme_js_dir}tinymce/tinymce.min.js"></script>
<script src="{$theme_js_dir}editor-config.js"></script>
<link rel="stylesheet" href="{$theme_js_dir}jscal2/css/jscal2.css" />
<link rel="stylesheet" href="{$theme_js_dir}jscal2/css/steel/steel.css" />
<script src="{$theme_js_dir}jscal2/jscal2.js"></script>
<script src="{$theme_js_dir}jscal2/unicode-letter.js"></script>
<script>{include file="`$theme_js_dir_finc`jscal2/language.js"}</script>

<table width="100%" border="0" cellpadding="20" cellspacing="0">
    <tr>
        <td>
            <table width="700" cellpadding="5" cellspacing="0" border="0" >
                <tr>
                    <td class="menuhead2" width="80%">&nbsp;{$translate_refund_edit_title}</td>
                    <td class="menuhead2" width="20%" align="right" valign="middle">
                        <a>
                            <img src="{$theme_images_dir}icons/16x16/help.gif" alt="" border="0" onMouseOver="ddrivetip('<b>{$translate_refund_edit_help_title|nl2br|regex_replace:"/[\r\t\n]/":" "}</b><hr><p>{$translate_refund_edit_help_content|nl2br|regex_replace:"/[\r\t\n]/":" "}</p>');" onMouseOut="hideddrivetip();" onClick="window.location;">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td class="menutd2" colspan="2">
                        <table width="100%" class="olotable" cellpadding="5" cellspacing="0" border="0" >
                            <tr>
                                <td width="100%" valign="top" >
                                    <table class="menutable" width="100%" border="0" cellpadding="0" cellspacing="0" >
                                        <tr>
                                            <td>                                          
                                                <table width="100%" cellpadding="2" cellspacing="2" border="0">  
                                                    
                                                    <form action="index.php?page=refund:edit" method="POST" name="edit_refund" id="edit_refund" autocomplete="off">                                                        
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_id}</b></td>
                                                            <td colspan="3"><input name="refund_id" type="hidden" value="{$refund_details.REFUND_ID}"/>{$refund_details.REFUND_ID}</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_payee}</b><span style="color: #ff0000"> *</span></td>
                                                            <td colspan="3"><input name="refundPayee" class="olotd5" size="50" value="{$refund_details.REFUND_PAYEE}" type="text" maxlength="50" required onkeydown="return onlyAlphaNumeric(event);"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_date}</b><span style="color: #ff0000"> *</span></td>
                                                            <td>
                                                                <input id="refundDate" name="refundDate" class="olotd5" size="10" value="{$refund_details.REFUND_DATE|date_format:$date_format}" type="text" maxlength="10" pattern="{literal}^[0-9]{1,2}(\/|-)[0-9]{1,2}(\/|-)[0-9]{2,2}([0-9]{2,2})?${/literal}" required onkeydown="return onlyDate(event);">
                                                                <input id="refundDate_button" type="button" value="+">                                                                    
                                                                <script>                                                                    
                                                                    Calendar.setup( {
                                                                        trigger     : "refundDate_button",
                                                                        inputField  : "refundDate",
                                                                        dateFormat  : "{$date_format}"                                                                                            
                                                                    } );                                                                    
                                                                </script>                                                                    
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_type}</b><span style="color: #ff0000"> *</span></td>
                                                            <td>
                                                                <select id="refundType" name="refundType" class="olotd5" col="30" style="width: 150px" value="{$refund_details.REFUND_TYPE}"/>
                                                                    <option value="1"{if $refund_details.REFUND_TYPE== '1'} selected{/if}>{$translate_refund_type_1}</option>
                                                                    <option value="2"{if $refund_details.REFUND_TYPE== '2'} selected{/if}>{$translate_refund_type_2}</option>
                                                                    <option value="3"{if $refund_details.REFUND_TYPE== '3'} selected{/if}>{$translate_refund_type_3}</option>
                                                                    <option value="4"{if $refund_details.REFUND_TYPE== '4'} selected{/if}>{$translate_refund_type_4}</option>
                                                                    <option value="5"{if $refund_details.REFUND_TYPE== '5'} selected{/if}>{$translate_refund_type_5}</option>                                                                        
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_payment_method}</b><span style="color: #ff0000"> *</span></td>
                                                            <td>
                                                                <select id="refundPaymentMethod" name="refundPaymentMethod" class="olotd5" style="width: 150px" value="{$refund_details.REFUND_PAYMENT_METHOD}"/>
                                                                    <option value="1"{if $refund_details.REFUND_METHOD == '1'} selected{/if}>{$translate_refund_payment_method_1}</option>
                                                                    <option value="2"{if $refund_details.REFUND_METHOD == '2'} selected{/if}>{$translate_refund_payment_method_2}</option>
                                                                    <option value="3"{if $refund_details.REFUND_METHOD == '3'} selected{/if}>{$translate_refund_payment_method_3}</option>
                                                                    <option value="4"{if $refund_details.REFUND_METHOD == '4'} selected{/if}>{$translate_refund_payment_method_4}</option>
                                                                    <option value="5"{if $refund_details.REFUND_METHOD == '5'} selected{/if}>{$translate_refund_payment_method_5}</option>
                                                                    <option value="6"{if $refund_details.REFUND_METHOD == '6'} selected{/if}>{$translate_refund_payment_method_6}</option>
                                                                    <option value="7"{if $refund_details.REFUND_METHOD == '7'} selected{/if}>{$translate_refund_payment_method_7}</option>
                                                                    <option value="8"{if $refund_details.REFUND_METHOD == '8'} selected{/if}>{$translate_refund_payment_method_8}</option>
                                                                    <option value="9"{if $refund_details.REFUND_METHOD == '9'} selected{/if}>{$translate_refund_payment_method_9}</option>
                                                                    <option value="10"{if $refund_details.REFUND_METHOD == '10'} selected{/if}>{$translate_refund_payment_method_10}</option>
                                                                    <option value="11"{if $refund_details.REFUND_METHOD == '11'} selected{/if}>{$translate_refund_payment_method_11}</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_net_amount}</b></td>
                                                            <td><input name="refundNetAmount" class="olotd5" size="10" value="{$refund_details.REFUND_NET_AMOUNT}" type="text" maxlength="10" pattern="{literal}^[0-9]{1,7}(.[0-9]{0,2})?${/literal}" onkeydown="return onlyNumbersPeriod(event);"></b></a></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><span style="color: #ff0000"></span><b>{$translate_refund_tax_rate}</b></td>
                                                            <td><input name="refundTaxRate" class="olotd5" size="5" value="{$refund_details.REFUND_TAX_RATE}" type="text" maxlength="5" pattern="{literal}^[0-9]{0,2}(\.[0-9]{0,2})?${/literal}" required onkeydown="return onlyNumbersPeriod(event);"/><b>%</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_tax_amount}</b></td>
                                                            <td><input name="refundTaxAmount" class="olotd5" size="10" value="{$refund_details.REFUND_TAX_AMOUNT}" type="text" maxlength="10" pattern="{literal}^[0-9]{1,7}(.[0-9]{0,2})?${/literal}" onkeydown="return onlyNumbersPeriod(event);"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_gross_amount}</b><span style="color: #ff0000"> *</span></td>
                                                            <td><input name="refundGrossAmount" class="olotd5" size="10" value="{$refund_details.REFUND_GROSS_AMOUNT}" type="text" maxlength="10" pattern="{literal}^[0-9]{1,7}(.[0-9]{0,2})?${/literal}" required onkeydown="return onlyNumbersPeriod(event);"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_notes}</b></td>
                                                            <td><textarea class="olotd5" name="refundNotes" cols="50" rows="15">{$refund_details.REFUND_NOTES}</textarea></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="right"><b>{$translate_refund_items}</b><span style="color: #ff0000"> *</span></td>
                                                            <td><textarea class="olotd5 mceCheckForContent" name="refundItems" cols="50" rows="15">{$refund_details.REFUND_ITEMS}</textarea></td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td><input class="olotd5" name="submit" type="submit" value="{$translate_refund_update_button}" /></td>
                                                        </tr>
                                                    </form>
                                                    
                                                    <!-- This script sets the dropdown Refund Type to the correct item -->
                                                    <script>dropdown_select_edit_type("{$refund_details.REFUND_TYPE}");</script>

                                                    <!-- This script sets the dropdown Refund Type to the correct item -->
                                                    <script>dropdown_select_edit_payment_method("{$refund_details.REFUND_PAYMENT_METHOD}");</script>
                                                        
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>