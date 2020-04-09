<?php if( !current_user_can( 'update_plugins' ))  wp_die( "You do not have permission to this feature. " ) ; ?>

<!-- Easy SSL Settings Tab -->

<div id="tab1" class="tab active">
<div class="container">
    <div class="row">

        <div class="col-md-7">
            <div class="row">
                <div class="col-md-3">
                    <img src="<?php  echo esc_html(plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . '/admin/images') ?>/EasySSL-Shield.png" width="150" >
                </div>
                <div class="col-md-7">

                    <!-- BEGIN errors check -->
                    <?php
                    if(  isset( $viewmodel['errors'] )){
                         if( !empty($viewmodel['errors'])) { ?>
                             <table class="table table-bordered table-condensed">
                             <?php
                                foreach($viewmodel['errors'] as $key => $value){ ?>
                                    <tr>
                                        <td class="alert-danger">
                                            <?php echo esc_html($value); ?>
                                        </td>
                                    </tr>
                                <?php
                                } ?>
                        </table>
                    <?php
                        }
                    } ?>
                    <!-- END errors check -->

                    <!-- BEGIN Detect Warning  -->
                    <table class="table table-bordered table-condensed">
                        <?php
                        if( !is_ssl() ) { ?>
                            <tr>
                                <td class="alert-danger">
                                    <li>SSL is not detected - You may need to install an SSL Certificate</li>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>

                    </table>
                    <!-- END Detect Warning  -->

                    <!-- BEGIN Detect Success  -->
                    <table class="table table-bordered table-condensed">
                        <tr><td class="alert-success">
                                        <?php
                                        printf(esc_html__("Did this plugin help?? Please %sWrite a Review%s to help other users who need SSL security", 'easy-ssl'), '<a href="#" id="essl_review">', '</a>') ;
                                        ?>
                        </td></tr>

                        <?php
                        if( is_ssl() ) {
                            ?>
                            <tr>
                                <td class="alert-success">
                                    <li>SSL is detected on your website!</li>
                                </td>
                            </tr>
                            <?php
                        }

                        if( isset( $viewmodel['settings']['essl']['htaccess_ssl'] )){
                            if( $viewmodel['settings']['essl']['htaccess_ssl'] ) { ?>
                                <tr>
                                    <td class="alert-success">
                                        <li>.htaccess Force SSL <strong>is</strong> enabled!</li>
                                    </td>
                                </tr>
                            <?php
                            }
                        }

                        if(isset( $viewmodel['settings']['essl']['webconfig_ssl'] )){
                            if( $viewmodel['settings']['essl']['webconfig_ssl'] ) { ?>
                                <tr>
                                    <td class="alert-success">
                                        <li>web.config Force SSL <strong>is</strong> enabled!</li>
                                    </td>
                                </tr>
                            <?php
                            }
                        }

                        if(isset( $viewmodel['settings']['essl']['wp_ssl'] )) {
                            if ( $viewmodel['settings']['essl']['wp_ssl'] ) { ?>
                                <tr>
                                    <td class="alert-success">
                                        <li>Force SSL <strong>is</strong> enabled!</li>
                                    </td>
                                </tr>
                            <?php
                            }
                        }

                        if(isset( $viewmodel['settings']['essl']['hsts'] )) {
                            if ( $viewmodel['settings']['essl']['hsts'] ) { ?>
                                <tr>
                                    <td class="alert-success">
                                        <li>HTTP Strict Transport Security (HSTS) <strong>is</strong> enabled!</li>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                    </table>
                    <!-- END Detect Success  -->

                </div>
            </div>

            <div class="row">
                <div class="switch col-md-9">
                    <h4>
                        <?php
                            esc_html_e( 'Force SSL HTTPS using Server Configuration (.htaccess / web.config )' , 'easy-ssl' );
                        ?>
                    </h4>

                    <p>
                        <?php
                            esc_html_e( 'Forcing HTTPS using .htaccess or web.config(Windows) is the best option if possible. *Fastest option' , 'easy-ssl' );
                        ?>
                    </p>

                    <?php
                    if(isset( $viewmodel['serverconfig']['errors'] )){
                        if( $viewmodel['serverconfig']['errors'] ) { ?>
                            <span style="color:red;">
                                <?php
                                    echo esc_html( $viewmodel['serverconfig']['errors'] );

                                    esc_html_e('You can still do an SSL redirect by clicking on the Toggle under "Free Settings" on this page, thene click Save Settings' , 'easy-ssl' );
                                ?>
                            </span>
                            <br/>

                    <?php
                        }
                    }
                    ?>

                    <form action="<?php echo get_admin_url(); ?>admin.php?page=essl_menu_page" method="POST">
                        <div class="row">
                            <div class="col-md-9">
                                <?php
                                    printf(esc_html__("%sCheck Server Configuration File / Setup%s", 'easy-ssl'), '<input type="submit" class="button-primary" value="Check Server Configuration File / Setup"', '/>') ;
                                ?>
                            </div>
                        </div>
                        <input type="hidden" name="controller" value="ESSL_Controller">
                        <input type="hidden" name="action" value="server_configuration">
                        <input type="hidden" name="essl_aiowz_sc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-sc-nonce' );?>" >
                    </form>

                        <?php
                        if(isset( $viewmodel['settings']['essl']['htaccess_ssl'] )){
                            if( $viewmodel['settings']['essl']['htaccess_ssl'] ){
                                $config_file_ssl = true;
                            }
                        }
                        if(isset( $viewmodel['settings']['essl']['webconfig_ssl'] )){
                            if( $viewmodel['settings']['essl']['webconfig_ssl'] ){
                                $config_file_ssl = true;
                            }
                        }
                        if(isset($viewmodel['serverconfig'])){
                            ?>
                            <h4><?php esc_html_e('Results', 'easy-ssl');?>:</h4>

                            <?php
                            if(isset($viewmodel['serverconfig']['output'])){
                                esc_html_e( 'Configuration Check Results:' , 'easy-ssl' ); ?>

                                <table class="table table-bordered table-condensed">

                                <?php
                                if(isset($viewmodel['serverconfig']['output']['revert'])){ ?>
                                    <tr>
                                        <td class="alert-warning">
                                <?php
                                    esc_html_e($viewmodel['serverconfig']['output']['revert']);
                                } ?>
                                        </td>
                                    </tr>
                                <?php
                                if(isset($viewmodel['serverconfig']['output']['warning'])){
                                    foreach($viewmodel['serverconfig']['output']['warning'] as $key => $value){ ?>
                                        <tr>
                                            <td class="alert-warning">
                                                <?php echo esc_html($value); ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }

                                if(isset($viewmodel['serverconfig']['output']['success'])){
                                    foreach($viewmodel['serverconfig']['output']['success'] as $key => $value){ ?>
                                        <tr>
                                            <td class="alert-success">
                                                <?php echo esc_html($value); ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                                </table>
                            <?php
                            }

                            if(isset($viewmodel['serverconfig']['windows_complete']) && isset($viewmodel['serverconfig']['apache_complete']) ) {
                                ?>
                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <td class="alert-danger">
                                            <li>
                                     <?php
                                     esc_html_e('Both Apache and Windows detected with both files: web.config and .htaccess . You will have to manually configure your Server configuration.', 'easy-ssl' );
                                ?>
                                            </li>
                                        </td>
                                    </tr>
                                </table>
                            <?php
                            }else{

                             if( ! isset( $config_file_ssl ) ){
                            ?>
                                <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
                                    <input type="hidden" name="controller" value="ESSL_Controller">
                                    <input type="hidden" name="action" value="write_configuration">
                                    <input type="hidden" name="essl_aiowz_wc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-wc-nonce' );?>" >
                                    <?php
                                    //We've succeeded and the user can enable Server Configuration writing and backup of any existing configuration
                                    if ( isset( $viewmodel['serverconfig']['windows_complete'])) {
                                        printf(esc_html__("%sSAFE BACKUP Existing web.config and Write New settings to FORCE HTTPS%s", 'easy-ssl'), '<button type="submit" class="btn btn-success" value="BACKUP Existing web.config and Write New settings to FORCE HTTPS">', '</button>');
                                    } elseif ( isset( $viewmodel['serverconfig']['apache_complete'])) {
                                        printf(esc_html__("%sSAFE BACKUP Existing .HTACCESS and Write New settings to FORCE HTTPS%s", 'easy-ssl'), '<button type="submit" class="btn btn-success success button-success" value="BACKUP Existing .HTACCESS and Write New settings to FORCE HTTPS">', '</button>');
                                    } else{
                                        printf(esc_html__("No configuration file found. You can follow instructions at : %shttps://www.easyssl.cc/ssl_server_configuration_instructions%s to setup .htaccess or web.config", 'easy-ssl'), '<a href="https://www.easyssl.cc/ssl_server_configuration_instructions">', '</a>');
                                    }?>

                                    <br/><br/>

                                    <?php
                                        echo esc_html( 'Your WordPress absolute path: ' . ABSPATH );
                                    ?>

                                    <br/>

                                    <?php
                                        echo esc_html( 'WordPress plugin backup url: ' . plugin_dir_url(ESSL_FILE) . ESSL_PLG_FOLDER_NAME . DS . 'backups' );
                                    ?>

                                 </form>
                                    <br><br>
                                <?php
                                }
                            }
                        }

                        if(isset($config_file_ssl)){
                            if(isset($viewmodel['settings']['essl_file'])){
                                if($viewmodel['settings']['essl_file']){?>

                                <br>
                                <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
                                    <input type="hidden" name="controller" value="ESSL_Controller">
                                    <input type="hidden" name="action" value="revert_configuration">
                                    <input type="hidden" name="essl_aiowz_rc_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-rc-nonce' );?>" >
                                    <?php
                                    printf(esc_html__("%sREVERT current config and restore my original config file%s", 'easy-ssl'), '<button type="submit" class="btn btn-danger" value="REVERT current config and restore my original config">', '</button>');
                                }
                            }
                        } ?>
                                </form>

                    <!-- https://support.plesk.com/hc/en-us/articles/115000254993-Unable-to-upload-files-in-WordPress-on-Plesk-server-The-uploaded-file-could-not-be-moved-to-wp-content-upload -->
                </div>
            </div>

            <h3>
            <?php
                esc_html_e( 'Free Settings', 'easy-ssl' );
            ?> </h3>

       <form action="<?php echo get_admin_url();?>admin.php?page=essl_menu_page" method="POST">
            <div class="row">
                <div class="switch col-md-9">
                    <label for="cmn-toggle-wpssl"><?php _e('Force SSL', 'easy-ssl'); ?></label>
                    <?php
                        $checked = '';
                        if(isset($viewmodel['settings']['essl']['wp_ssl'])){
                            if($viewmodel['settings']['essl']['wp_ssl']) {
                                $checked = 'checked';
                            }
                        }
                    ?>
                    <input id="cmn-toggle-wpssl" name="wp_ssl" class="cmn-toggle cmn-toggle-round" data-toggle="wp_ssl" type="checkbox" <?php esc_html_e($checked);?>>
                    <label for="cmn-toggle-wpssl"></label>
                    <p><?php echo __('Will do a SEO friendly redirect from HTTP to HTTPS on all pages/posts , everything regarding content' , 'easy-ssl');?></p>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="switch col-md-9">
                    <label for="cmn-toggle-hsts"><?php _e('Enable HTTP Strict Transport Security (HSTS)', 'easy-ssl'); ?></label>
                    <?php
                        $checked = '';
                        if(isset($viewmodel['settings']['essl']['hsts'])){
                            if($viewmodel['settings']['essl']['hsts']) {
                                $checked = 'checked';
                            }
                        }
                     ?>
                    <input id="cmn-toggle-hsts" name="hsts" class="cmn-toggle cmn-toggle-round" data-toggle="hsts" type="checkbox" <?php esc_html_e( $checked );?>>
                    <label for="cmn-toggle-hsts"></label>
                    <p><?php esc_html_e('HSTS is a web security policy mechanism that helps to protect websites against protocol downgrade attacks and cookie hijacking.', 'easy-ssl');?></p>
                    <p><?php esc_html_e('It allows web servers to declare that web browsers should interact with it using only HTTPS connections', 'easy-ssl');?></p>
                </div>
            </div>

           <input type="hidden" name="essl_aiowz_tkn" value="<?php echo wp_create_nonce( 'essl-csrf-index-nonce' );?>" >
           <input type="hidden" name="action" value="index">
           <input type="submit" class="button-primary" value="Save Settings">
       </form>

       <hr />

            <style type="text/css">
                #sidestuff{
                    background-color:#F0F0EE;
                }
            </style>
        </div>

        <div class="col-md-3" id="sidestuff">
            <div class="row">
                <div class="col-md-12 col-sm-6">
                    <h3>Tips:</h3>
                    <p>
                        If you are trying to Force HTTPS using .htaccess or web.config and you don't have an initial .htaccess or web.config
                        file in your main WordPress path, you can create one.
                    </p>

                    <p>
                        Alternatively if creating your own server config isn't an option you can just use "Free Settings" and click on Force
                        SSL and "Save Settings"
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
</div>