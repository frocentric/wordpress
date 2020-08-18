<script id="nf-tmpl-mp-gutter-left" type="text/template">
	<% if ( hasPrevious() ) { %>
	<i class="fa fa-chevron-circle-left" aria-hidden="true"></i>
	<% } %>
</script>

<script id="nf-tmpl-mp-gutter-right" type="text/template">
	<% if ( hasNext() ) { %>
	<i class="fa fa-chevron-circle-right next" aria-hidden="true"></i>
	<% } else if ( hasContent() ) { %>
	<i class="fa fa-plus-circle new" aria-hidden="true"></i>
	<% } %>
</script>