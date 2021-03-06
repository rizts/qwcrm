<!-- update.tpl -->
{*
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
*}
<table width="100%" border="0" cellpadding="20" cellspacing="0">
    <tr>
        <td>
            <table width="700" cellpadding="5" cellspacing="0" border="0"> 
                
                
                <!-- Header -->
                <tr>
                    <td class="menuhead2" width="80%">{t}Update Status{/t}</td>
                    <td class="menuhead2" width="20%" align="right" valign="middle">
                        <a>
                            <img src="{$theme_images_dir}icons/16x16/help.gif" border="0" onMouseOver="ddrivetip('<div><strong>{t escape=js}ADMINISTRATOR_UPDATE_HELP_TITLE{/t}</strong></div><hr><div>{t escape=js}ADMINISTRATOR_UPDATE_HELP_CONTENT{/t}</div>');" onMouseOut="hideddrivetip();">
                        </a>
                    </td>
                </tr>
                
                <!-- Main Content -->
                <tr>
                    <td class="menutd2" colspan="2">
                        <table width="100%" class="olotable" cellpadding="5" cellspacing="0" border="0">
                                                        
                            <!-- Default Page content -->
                            {if !$update_response}                                
                                <tr>
                                    <td colspan="2">
                                        <p>{t}This page will allow you to check for updates to QWcrm.{/t}</p>
                                        <p><b>{t}Current Version{/t}:</b> {$current_version}</p>
                                    </td>                                    
                                </tr>                                
                            {/if}
                            
                            <!-- Update Response --> 
                            
                            {if $update_response}                                 
                            
                                {if $update_response == 'no_response'}
                                    <tr>
                                        <td colspan="2">
                                            <p>{t}There has been no response from the QWcrm update server. You can manually check for updates at{/t}: <a href="https://quantumwarp.com" target="_blank">QuantumWarp.com</a></p>
                                        </td>
                                    </tr>                                    
                                {else}
                                    <tr>
                                        <td colspan="2">
                                            <div>{$update_response.message}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="%50" align="left">
                                            <b>{t}Current Version{/t}:</b> {$current_version}<br>
                                        </td>
                                        <td width="%50" align="left">
                                            <b>{t}Latest Version{/t}:</b> {$update_response.version}<br>
                                            <b>{t}Release Date{/t}:</b> {$update_response.release_date}<br>
                                            {if $version_compare == 1}
                                                <b>{t}Download Link{/t}:</b> <a href="{$update_response.downloadurl}" target="_blank">{$update_response.downloadurl}</a>
                                            {/if}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" colspan="2">                                        
                                            {if $version_compare == 1}                                                
                                                <span style="color: red;"><strong>{t}An update is available.{/t}</strong></span><br>                                           
                                                {t}Please download and once you unpack the file read the README for further instructions.{/t}                                                
                                            {else}
                                                <span style="color: green;"><strong>{t}No Updates Available, you have the latest version.{/t}</strong></span>
                                            {/if}                                    
                                            <br>
                                        </td>
                                    </tr>
                                {/if}
                                
                            {/if}
                            
                            <!-- Submit Button -->
                            
                            {if !$update_response}  
                                <tr>
                                    <td>
                                        <form method="post" action="index.php?component=administrator&page_tpl=update"> 
                                            <button class="olotd5" type="submit" name="submit" value="check_for_update">{t}Check for Update{/t}</button>
                                        </form>
                                    </td>
                                </tr>
                            {/if}
                            
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>