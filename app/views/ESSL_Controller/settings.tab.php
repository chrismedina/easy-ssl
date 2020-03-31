<!-- Easy SSL Settings Tab -->

<div id="tab1" class="tab active">
<div class="container">
    <div class="row">

        <div class="col-md-7">

            <div class="row">

                <div class="col-md-3">

                            <img src="<?php  echo plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/images' ?>/EasySSL-Shield.png" width="150" >

                </div>
                <div class="col-md-7">
                    <table class="table table-bordered table-condensed">
                        <tr><td class="alert-success">
                                        <?php
                                        printf(__("Did this plugin help?? Please %sWrite a Review%s to help other users who need SSL security", 'easy-ssl'), '<a href="#" id="essl_review">', '</a>') ;
                                        ?>
                            </td></tr>
                        <?php

                        if(is_ssl()) echo '<tr><td class="alert-success"><li>SSL is detected on your website!</li></td></tr>';
                        if(isset($viewmodel['settings']['essl']['htaccess_ssl'])){ if($viewmodel['settings']['essl']['htaccess_ssl']) echo '<tr><td class="alert-success"><li>.htaccess Force SSL <strong>is</strong> enabled!</li></td></tr>'; }
                        if(isset($viewmodel['settings']['essl']['webconfig_ssl'])){ if($viewmodel['settings']['essl']['webconfig_ssl']) echo '<tr><td class="alert-success"><li>web.config Force SSL <strong>is</strong> enabled!</li></td></tr>'; }
                        if(isset($viewmodel['settings']['essl']['wp_ssl'])){ if($viewmodel['settings']['essl']['wp_ssl']) echo '<tr><td class="alert-success"><li>Force SSL <strong>is</strong> enabled!</li></td></tr>'; }
                        if(isset($viewmodel['settings']['essl']['301'])){ if($viewmodel['settings']['essl']['301']) echo '<tr><td class="alert-success"><li>Standard 301 HTTP to HTTPS <strong>is</strong> enabled!</li></td></tr>'; }
                        if(isset($viewmodel['settings']['essl']['hsts'])){ if($viewmodel['settings']['essl']['hsts']) echo '<tr><td class="alert-success"><li>HTTP Strict Transport Security (HSTS) <strong>is</strong> enabled!</li></td></tr>'; }
                        ?>
                    </table>
                    <table class="table table-bordered table-condensed">
                        <?php
                        if(!is_ssl()) echo '<tr><td class="alert-danger"><li>SSL is not detected - You may need to install an SSL Certificate</li></td></tr>';
                        ?>
                            <tr><td class="alert-warning"><li>Secure Cookies not enabled. </li></td></tr>
                            <tr><td class="alert-warning"><li>301 <strong>ALL</strong> HTTP to HTTPS history in Google (fixes 404 errors so you don't lose traffic)</li></td></tr>

                    </table>
                </div>
            </div>

            <div class="row">
                <div class="switch col-md-9">
                    <?php
                    echo "<h4>" . __( 'Force SSL HTTPS using Server Configuration (.htaccess / web.config )', 'easy-ssl' ) . "</h4>";
                    ?>
                    <p><?php echo __('Forcing HTTPS using .htaccess or web.config(Windows) is the best option if possible. *Fastest option', 'easy-ssl');?></p>

                    <?php
                    if(isset($viewmodel['serverconfig']['errors'])){
                        if($viewmodel['serverconfig']['errors']) {
                            echo '<span style="color:red;">' . $viewmodel['serverconfig']['errors'] . '<br>';
                            echo __('You can still do an SSL redirect by clicking on the Toggle under "Free Settings" on this page, thene click Save Settings');
                            echo '</span><br>';

                            //ToDo:  Give example of some .htaccess and web.config settings to create manually

                        }
                    }
                    ?>

                    <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
                        <div class="row">
                            <div class="col-md-9">
                                <?php
                                printf(__("%sCheck Server Configuration File / Setup%s", 'easy-ssl'), '<input type="submit" class="button-primary" value="Check Server Configuration File / Setup"', '/>') ;
                                ?>
                            </div>
                        </div>
                        <input type="hidden" name="controller" value="ESSL_Controller">
                        <input type="hidden" name="action" value="serverconfiguration">
                        <input type="hidden" name="essl_aiowz_sc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-sc-nonce' );?>" >
                    </form>

                        <?php
                        if(isset($viewmodel['settings']['essl']['htaccess_ssl'])){
                            if($viewmodel['settings']['essl']['htaccess_ssl']){
                                $config_file_ssl = true;
                            }
                        }
                        if(isset($viewmodel['settings']['essl']['webconfig_ssl'])){
                            if($viewmodel['settings']['essl']['webconfig_ssl']){
                                $config_file_ssl = true;
                            }
                        }
                        if(isset($viewmodel['serverconfig'])){
                            ?>
                            <h4><?php _e('Results');?>:</h4>

                            <?php
                            if(isset($viewmodel['serverconfig']['output'])){
                                echo _e('Configuration Check Results:');
                                echo $viewmodel['serverconfig']['output'];
                            }

                            if(isset($viewmodel['serverconfig']['windows_complete']) && isset($viewmodel['serverconfig']['apache_complete']) ) {
                                echo '<table class="table table-bordered table-condensed">';
                                echo '<tr> <td class="alert-danger"> <li>' . __('Both Apache and Windows detected with both files: web.config and .htaccess . You will have to manually configure your Server configuration.', 'easy-ssl' ) . ' </li> </td></tr>';
                                echo '</table>';
                            }else{

                                if( ! isset($config_file_ssl) ){
                            ?>
                            <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
                                <input type="hidden" name="controller" value="ESSL_Controller">
                                <input type="hidden" name="action" value="writeconfiguration">
                                <input type="hidden" name="essl_aiowz_wc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-wc-nonce' );?>" >
                                <?php
                                //We've succeeded and the user can enable Server Configuration writing and backup of any existing configuration
                                if (isset($viewmodel['serverconfig']['windows_complete'])) {
                                    //_e('**Safe to proceed. Click the button below to begin forcing HTTPS using a Server Config file:');
                                    //echo '<br><br>';
                                    printf(__("%sSAFE BACKUP Existing web.config and Write New settings to FORCE HTTPS%s", 'easy-ssl'), '<button type="submit" class="btn btn-success" value="BACKUP Existing web.config and Write New settings to FORCE HTTPS">', '</button>');
                                } elseif (isset($viewmodel['serverconfig']['apache_complete'])) {
                                    //_e('**Safe to proceed. Click the button below to begin forcing HTTPS using a Server Config file:');
                                    //echo '<br><br>';
                                    printf(__("%sSAFE BACKUP Existing .HTACCESS and Write New settings to FORCE HTTPS%s", 'easy-ssl'), '<button type="submit" class="btn btn-success success button-success" value="BACKUP Existing .HTACCESS and Write New settings to FORCE HTTPS">', '</button>');
                                }

                                echo '<br><br>';
                                _e("Your WordPress absolute path: " . ABSPATH . '<br>');
                                _e("WordPress plugin backup url: " . plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . DS . 'backups' . '<br>');
                                echo '</form><br>';
                                }
                            }
                        }

                        if(isset($config_file_ssl)){
                                if(isset($viewmodel['settings']['essl']['file_copied_to'])){
                                ?>
                                <br>
                                <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
                                    <input type="hidden" name="controller" value="ESSL_Controller">
                                    <input type="hidden" name="action" value="revertconfiguration">
                                    <input type="hidden" name="essl_aiowz_rc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-rc-nonce' );?>" >
                                    <?php
                                    printf(__("%sREVERT current config and restore my original config file%s", 'easy-ssl'), '<button type="submit" class="btn btn-danger" value="REVERT current config and restore my original config">', '</button>');
                                    }
                        } ?>
                                </form>

                    <!-- https://support.plesk.com/hc/en-us/articles/115000254993-Unable-to-upload-files-in-WordPress-on-Plesk-server-The-uploaded-file-could-not-be-moved-to-wp-content-upload -->
                </div>
            </div>

            <?php
            // header
                echo "<h3>" . __( 'Free Settings', 'easy-ssl' ) . "</h3>";
            ?>

       <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
            <div class="row">
                <div class="switch col-md-9">
                    <label for="cmn-toggle-wpssl"><?php _e('Force SSL', 'easy-ssl'); ?></label>
                    <input id="cmn-toggle-wpssl" name="wp_ssl" class="cmn-toggle cmn-toggle-round" data-value="<?php $checked = ''; if(isset($viewmodel['settings']['essl']['wp_ssl'])){  if($viewmodel['settings']['essl']['wp_ssl']) { echo $viewmodel['settings']['essl']['wp_ssl']; $checked = 'checked';  } else { echo 'off';  } }  ?>" data-toggle="wp_ssl" type="checkbox" onClick="toggle(this)" <?php echo $checked;?>>
                    <label for="cmn-toggle-wpssl"></label>
                    <p><?php echo __('Will do a SEO friendly redirect from HTTP to HTTPS on all pages/posts , everything regarding content' , 'easy-ssl');?></p>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="switch col-md-9">
                    <label for="cmn-toggle-hsts"><?php _e('Enable HTTP Strict Transport Security (HSTS)', 'easy-ssl'); ?></label>
                    <input id="cmn-toggle-hsts" name="hsts" class="cmn-toggle cmn-toggle-round" data-value="<?php $checked = '';  if(isset($viewmodel['settings']['essl']['hsts'])){ if($viewmodel['settings']['essl']['hsts']) {echo $viewmodel['settings']['essl']['hsts']; $checked = 'checked'; } else { echo 'off'; } } ?>" data-toggle="hsts" type="checkbox" onClick="toggle(this)" <?php echo $checked;?>>
                    <label for="cmn-toggle-hsts"></label>
                    <p><?php echo __('HSTS is a web security policy mechanism that helps to protect websites against protocol downgrade attacks and cookie hijacking.', 'easy-ssl');?></p>
                    <p><?php echo __('It allows web servers to declare that web browsers should interact with it using only HTTPS connections', 'easy-ssl');?></p>
                </div>
            </div>

           <input type="hidden" name="essl_aiowz_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-index-nonce' );?>" >
           <input type="hidden" name="action" value="index">
           <input type="submit" class="button-primary" value="Save Settings">
           </form>
           <hr />


            <div class="row">
                <div class="col-md-9">
                    <div id="settings_result"></div>
                </div>
            </div>



            <hr />
            <!-- PRO FEATURES -->
            <?php
            if(isset($viewmodel['settings']['transient']['essl_pro'])) {
                if (!empty($viewmodel['settings']['transient']['essl_pro'])) echo $viewmodel['settings']['transient']['essl_pro'];
            }
            ?>

            <?php
            if(isset($viewmodel['settings']['transient']['essl_general'])) {
                if (!empty($viewmodel['settings']['transient']['essl_general'])) echo $viewmodel['settings']['transient']['essl_general'];
            }
            ?>
        </div>

        <?php
        if(isset($viewmodel['settings']['transient']['essl_easyssl']))
        {
            if(!empty($viewmodel['settings']['transient']['essl_easyssl']))  echo $viewmodel['settings']['transient']['essl_easyssl'];
        }else{
            //ToDo offer other premium feature. For now leave this alone

        ?>

        <?php
        }
        ?>

        <div class="col-md-3" id="sidestuff">
            <div class="row">
                <div class="col-md-12 col-sm-6">
                    <h3>Tips:</h3>
                    <p>
                        If you are trying to Force HTTPS using .htaccess or web.config and you don't have an initial .htaccess or web.config file in your main WordPress path, you can create one.
                    </p>

                    <p>
                        Alternatively if creating your own server config isn't an option you can just use "Free Settings" and click on Force SSL and "Save Settings"
                    </p>
                </div>
            </div>
        </div>


        <style type="text/css">
            #sidestuff{
                background-color:#F0F0EE;
            }
        </style>
        </div>
    </div>

</div>
