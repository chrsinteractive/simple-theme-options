<div class="wrap">
	<h2>Simple Tracking</h2>
	<div id="chrssto-form-container" class="chrsstro-form-section metabox-holder">

        <div class="chrssto-col-content">
            <form method="post" action="options.php">
                <?php
                submit_button();
                settings_fields( 'chrs_options' );
                do_settings_sections( 'simple_tracking' );
                submit_button();
                ?>
            </form>
        </div>
        <div class="chrssto-col-sidebar">
            <?php chrssto_info_box(); ?>
        </div>
	</div>
</div>
<div class="clear"></div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#chrssto-form-container .codeEditor').each( function(index, value) {
            wp.codeEditor.initialize(this, cm_settings);
        });
    });
</script>
