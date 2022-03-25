<div class="wrap">
	<h2>Simple Theme Options</h2>
	<div id="theme-options-form-container" class="metabox-holder">
		<form method="post" action="options.php">
        <?php
        submit_button();
        settings_fields( 'chrs_options' );
        do_settings_sections( 'theme_options' );
        submit_button();
        ?>
		</form>
	</div>
</div>
<div class="clear"></div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#theme-options-form-container .codeEditor').each( function(index, value) {
            wp.codeEditor.initialize(this, cm_settings);
        });
    });
</script>
