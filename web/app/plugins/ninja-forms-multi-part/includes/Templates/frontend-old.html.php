<script id="tmpl-nf-mp-header" type="text/template">
	<%= renderProgressBar() %>
	<%= renderBreadcrumbs() %>
	<h3>
		<%= title %>
	</h3>
</script>

<script id="tmpl-nf-mp-footer" type="text/template">
	<%= renderNextPrevious() %>
</script>

<script id="tmpl-nf-mp-next-previous" type="text/template">
	<ul class="nf-next-previous">
		<% if ( showPrevious ) { %>
		<li class="nf-previous-item">
			<input type="button" class="nf-previous" value="<%= previousLabel %>" />
		</li>
		<% } %>

		<% if ( showNext ) { %>
		<li class="nf-next-item">
			<input type="button" class="nf-next" value="<%= nextLabel %>" />
		</li>
		<% } %>
	</ul>
</script>

<script id="tmpl-nf-mp-breadcrumbs" type="text/template">
	<ul class="nf-breadcrumbs">
		<% _.each( parts, function( part, index ) { %>
		<li class="<%= ( currentIndex == index ) ? 'active' : '' %> <%= ( part.errors ) ? 'errors' : '' %>">
			<a href="#" class="nf-breadcrumb" data-index="<%= index %>"><%= ( part.errors ) ? '' : '' %> <%= part.title %></a>
		</li>
		<% } ); %>
	</ul>
</script>

<script id="tmpl-nf-mp-progress-bar" type="text/template">
	<progress style="width:100%" value="<%= percent %>" max="100"><%= percent %> %</progress>
</script>