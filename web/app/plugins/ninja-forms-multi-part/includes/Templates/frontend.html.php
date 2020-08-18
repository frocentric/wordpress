<script id="tmpl-nf-mp-form-content" type="text/template">
	<div class="nf-mp-header"></div>
	<div class="nf-mp-body"></div>
	<div class="nf-mp-footer"></div>
</script>

<?php 
if ( ! version_compare( get_option( 'ninja_forms_version', '0' ), '3.0', '>' ) ) {
    /* 
     * If we're using the RC codebase of Ninja Forms, load the older, <% %> style templates.
     */
    require_once( NF_MultiPart::$dir . 'includes/Templates/frontend-old.html.php' );
    return false;
}
?>

<script id="tmpl-nf-mp-header" type="text/template">
	{{{ data.renderProgressBar() }}}
	{{{ data.renderBreadcrumbs() }}}
	{{{ data.renderPartTitle() }}}
</script>
<script id="tmpl-nf-mp-part-title" type="text/template">
	<h3>
		{{{ data.title }}}
	</h3>
</script>

<script id="tmpl-nf-mp-footer" type="text/template">
	{{{ data.renderNextPrevious() }}}
</script>

<script id="tmpl-nf-mp-next-previous" type="text/template">
	<ul class="nf-next-previous">
		<# if ( data.showPrevious ) { #>
		<li class="nf-previous-item">
			<input type="button" class="nf-previous" value="{{{ data.prevLabel }}}" />
		</li>
		<# } #>

		<# if ( data.showNext ) { #>
		<li class="nf-next-item">
			<input type="button" class="nf-next" value="{{{ data.nextLabel }}}" />
		</li>
		<# } #>
	</ul>
</script>

<script id="tmpl-nf-mp-breadcrumbs" type="text/template">
	<ul class="nf-breadcrumbs">
		<# _.each( data.parts, function( part, index ) { #>
		<li class="{{{ ( data.currentIndex == index ) ? 'active' : '' }}} {{{ ( part.errors ) ? 'errors' : '' }}}">
			<a href="#" class="nf-breadcrumb" data-index="{{{ index }}}">{{{ ( part.errors ) ? '' : '' }}} {{{ part.title }}}</a>
		</li>
		<# } ); #>
	</ul>
</script>

<script id="tmpl-nf-mp-progress-bar" type="text/template">
    <div class="nf-progress-container">
        <div class="nf-progress" style="width: {{{ data.percent }}}%;"></div>
    </div>
</script>