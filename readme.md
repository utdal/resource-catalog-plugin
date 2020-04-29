# Resource Catalog

A WordPress plugin to manage and display resources.

This provides a Resources custom post type, with custom taxonomies Audiences, Lengths, and Programs, and a link custom field. It also provides a dynamic searchable and filterable front-end catalog display via a shortcode.

## Shortcode

Type the shortcode `[resource_catalog]` to show your catalog on a page.

### Shortcode attributes

Below is a list of shortcode attributes that you can specify on the shortcode, along with their default values:

Example usage: `[resource_catalog search_expand_button="false" search_expanded="true"]`

- `search_expand_button="true"` : show/hide the search expand button
- `search="true"` : show/hide the search box
- `reset="true"` : show/hide the reset button
- `filters="true"` : show/hide all filters
- `audiences_filter="true"` : show/hide the audiences filter
- `lengths_filter="true"` : show/hide the lengths filter
- `tags_filter="true"` : show/hide the tags filter
- `categories_filter="true"` : show/hide the categories filter
- `programs_filter="true"` : show/hide the programs filter
- `search_expanded="false"` : expand the search & filters by default
- `orderby="title"` : order the displayed resources by the specified attribute (see Wordpress REST API docs for allowable values)
- `order="asc"` : order the displayed resources as 'asc' or 'desc'
- `show_all="true"` : load and show all resources on page load