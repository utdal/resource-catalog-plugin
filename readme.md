# Resource Catalog

A WordPress plugin to manage and display resources.

This provides a Resources custom post type, with a link custom field. It also provides a dynamic searchable and filterable front-end catalog display via a shortcode.

## Shortcode

Type the shortcode `[resource_catalog]` to show your catalog on a page.

### Shortcode attributes

Below is a list of shortcode attributes that you can specify on the shortcode, along with their default values:

Example usage: `[resource_catalog search_expand_button="false" search_expanded="true"]`

- `site_url=""` : specify the base API URL for pulling in resources, i.e "{site_url}/wp-json/wp/v2"
- `search_expand_button="true"` : show/hide the search expand button
- `search="true"` : show/hide the search box
- `reset="true"` : show/hide the reset button
- `filters="true"` : show/hide all filters
- `tags_filter="true"` : show/hide the tags filter
- `categories_filter="true"` : show/hide the categories filter
- `custom_filters=""` : comma-separated slugs of CPTUI custom taxonomies for which to show filters
- `custom_filters_labels="true"` : show/hide the custom filters labels
- `custom_filters_descriptions="true"` : show/hide the custom filters descriptions
- `search_expanded="false"` : expand the search & filters by default
- `search_tags=true`: if a search exactly matches a tag, show all resources with that tag instead of searching content
- `search_categories=true`: if a search exactly matches a category, show all resources with that category instead of searching content
- `search_custom_filters=true`: if a search exactly matches a custom taxonomy, show all resources with that custom taxonomy instead of searching content
- `orderby="title"` : order the displayed resources by the specified attribute (see Wordpress REST API docs for allowable values)
- `order="asc"` : order the displayed resources as 'asc' or 'desc'
- `show_all="true"` : load and show all resources on page load
- `featured_image="true"` : show featured images of resources
- `excerpt="true"`: show the excerpts of resources
- `content="true"`: show the content of resources
- `content_expand_button="true"`: hide the content behind a "Details" toggle button
- `outbound_analytics="false"` : capture outbound resource link click events in Google Analytics

## Custom Link Field

Resources have a custom link field that you can point to any URL. This will be displayed as a "Get" button on the resource.

## Custom Taxonomies

If you have the Custom Post Type UI (CPTUI) plugin activated, you can add custom taxonomies to your resources and include those taxonomies as filters on the front-end catalog display. See the shortcode attribute `custom_filters`.