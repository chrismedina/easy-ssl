<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('.tabs .tab-links a').on('click', function(e)  {
            var currentAttrValue = jQuery(this).attr('href');

            // Show/Hide Tabs
            jQuery('.tabs ' + currentAttrValue).show().siblings().hide();

            // Change/remove current tab to active
            jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

            e.preventDefault();
        });
    });
</script>

<div class="wrap">
    <div class="main">

        <?php
            if( array_key_exists( 'notification', $viewmodel ) ) {
                echo '<div class="updated"><p><strong>' . __($viewmodel['notification'], 'menu-test') . '</strong></p></div>';
            }?>
        <h3>
        <?php
            esc_html_e( 'Easy SSL', 'easy-ssl' );
        ?>
            </h3>

        <div class="tabs standard">
            <ul class="tab-links">
                <li class="active"><a href="#tab1">Easy SSL Settings</a></li>
                <!--<li><a href="#tab2">Image Size / SEO Settings</a></li>-->

            <?php
                //Create tab links dynamically
                if( array_key_exists( 'tabs', $viewmodel )) {
                    foreach ( $viewmodel["tabs"] as $tab ) {
                        $i++;

                        //Default active tab is the first tab
                        if($i==1){ ?>
                            <li class="active">
                            <?php
                        } else{?>
                            <li>
                            <?php
                            } ?>

                        <a href="#tab<?php echo esc_html($i);?>">
                            <?php echo esc_html($tab["title"]);?>
                        </a></li>
                <?php
                    }
                }
            ?>
            </ul>

            <div class="tab-content">

                <!--Tabs -->
                 <?php
                    require_once('settings.tab.php');
                ?>

            </div>

        </div>

    </div><!--end .main-->
</div>
