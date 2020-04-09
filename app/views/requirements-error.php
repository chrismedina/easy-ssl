<div class="error">
	<p>EasySSL Sync error: Your environment doesn't meet all of the system requirements listed below.</p>

	<ul class="ul-disc">
        <?php
        if(PHP_VERSION < ESSL_REQUIRED_PHP_VERSION ){
            ?>
            <li><strong>PHP <?php echo esc_html(ESSL_REQUIRED_PHP_VERSION); ?>+</strong>
                <em>(You're running version <?php echo PHP_VERSION; ?>)</em>
            </li>

        <?php } ?>

        <?php
        if($wp_version < ESSL_REQUIRED_WP_VERSION) {
            ?>
            <li><strong>WordPress <?php esc_html(ESSL_REQUIRED_WP_VERSION); ?>+</strong>
                <em>(You're running version <?php echo esc_html($wp_version); ?>)</em>
            </li>
        <?php
        }
        ?>

	</ul>

	<p>If you need to upgrade your version of PHP you can ask your hosting company for assistance, and if you need help upgrading WordPress you can refer to <a href="http://codex.wordpress.org/Upgrading_WordPress">the Codex</a>.</p>
</div>