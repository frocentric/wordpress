<script id="nf-tmpl-mp-layout" type="text/template">
	<div id="nf-mp-main-content"></div>
	<div id="nf-mp-drawer"></div>
</script>

<script id="nf-tmpl-mp-main-content-fields-empty" type="text/template">
	<div class="nf-fields-empty">
		<h3><?php _e( 'Add form fields', 'ninja-forms' ); ?></h3>
		<p><?php _e( 'Get started by adding your first form field.', 'ninja-forms' ); ?> <a class="nf-open-drawer" title="Add New Field" href="#" data-drawerid="addField"><?php _e( 'Just click here and select the fields you want.', 'ninja-forms' ); ?></a>
	</div>
</script>
	
<script id="nf-tmpl-mp-drawer-layout" type="text/template">
	<div class="nf-mp-drawer-scroll-previous nf-mp-drawer-scroll" style="">
		<i class="fa fa-chevron-left" aria-hidden="true"></i>
	</div>

	<div id="nf-mp-drawer-viewport" class="als-viewport"></div>

	<div class="nf-mp-drawer-scroll-next nf-mp-drawer-scroll" style="">
		<i class="fa fa-chevron-right" aria-hidden="true"></i>
	</div>
</script>

<?php 
if ( ! version_compare( get_option( 'ninja_forms_version', '0' ), '3.0', '>' ) ) {
    /* 
     * If we're using the RC codebase of Ninja Forms, load the older, <% %> style templates.
     */
    require_once( NF_MultiPart::$dir . 'includes/Templates/builder-old.html.php' );
    return false;
}
?>

<script id="nf-tmpl-mp-gutter-left" type="text/template">
	<# if ( data.hasPrevious() ) { #>
	<i class="fa fa-chevron-circle-left" aria-hidden="true"></i>
	<# } #>
</script>


<script id="nf-tmpl-mp-gutter-right" type="text/template">
	<# if ( data.hasNext() ) { #>
	<i class="fa fa-chevron-circle-right next" aria-hidden="true"></i>
	<# } else if ( data.hasContent() ) { #>
	<i class="fa fa-plus-circle new" aria-hidden="true"></i>
	<#} #>
</script>

<script id="nf-tmpl-mp-drawer-item" type="text/template">
	{{{ data.title }}}
</script>

<script id="nf-tmpl-mp-form-title" type="text/template">
	<h2>{{{ data.renderFormTitle() }}} - <i class="fa fa-cog" aria-hidden="true"></i> {{{ data.renderPartTitle() }}}</h2>
</script>