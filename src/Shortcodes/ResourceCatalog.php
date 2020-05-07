<?php

namespace UTDallasResourceCatalog\Shortcodes;

class ResourceCatalog extends Shortcode
{
    /** @var string Shortcode name. */
    public $name = 'resource_catalog';

    /** @var string The path to the views */
    public $view_path = \UTDallasResourceCatalog\PLUGIN_PATH . '/views';

    /** @var array Default shortcode attributes. */
    public $default_attributes = [
        'site_url' => '',
        'search_expand_button' => true,
        'search' => true,
        'reset' => true,
        'filters' => true,
        'audiences_filter' => true,
        'lengths_filter' => true,
        'tags_filter' => true,
        'categories_filter' => true,
        'programs_filter' => true,
        'search_expanded' => false,
        'order' => 'asc',
        'orderby' => 'title',
        'show_all' => true,
        'outbound_analytics' => false,
    ];

    /** @var array Filters to apply to the shortcode attributes. */
    public $attribute_filters = [
        'site_url' => FILTER_VALIDATE_URL,
        'search_expand_button' => FILTER_VALIDATE_BOOLEAN,
        'search' => FILTER_VALIDATE_BOOLEAN,
        'reset' => FILTER_VALIDATE_BOOLEAN,
        'filters' => FILTER_VALIDATE_BOOLEAN,
        'audiences_filter' => FILTER_VALIDATE_BOOLEAN,
        'lengths_filter' => FILTER_VALIDATE_BOOLEAN,
        'tags_filter' => FILTER_VALIDATE_BOOLEAN,
        'categories_filter' => FILTER_VALIDATE_BOOLEAN,
        'programs_filter' => FILTER_VALIDATE_BOOLEAN,
        'search_expanded' => FILTER_VALIDATE_BOOLEAN,
        'order' => FILTER_CALLBACK,
        'orderby' => FILTER_CALLBACK,
        'show_all' => FILTER_VALIDATE_BOOLEAN,
        'outbound_analytics' => FILTER_VALIDATE_BOOLEAN,
    ];

    /** @var array Whitelist values for shortcode attributes */
    public $attribute_whitelist = [
        'order' => [
            'asc',
            'desc',
        ],
        'orderby' => [
            'author',
            'date',
            'id',
            'include',
            'modified',
            'parent',
            'relevance',
            'slug',
            'include_slugs',
            'title'
        ],
    ];

    /**
     * Shortcode class constructor.
     */
    public function __construct()
    {
        $this->default_attributes['site_url'] = get_bloginfo('url');

        $this->attribute_filter_options['order'] = ['options' => [$this, 'whitelistOrder']];
        $this->attribute_filter_options['orderby'] = ['options' => [$this, 'whitelistOrderBy']];

        parent::__construct();
    }

    /**
     * Render the shortcode
     *
     * @return string
     */
    public function render()
    {
        wp_enqueue_style('resource_catalog_css');
        wp_enqueue_script('resource_catalog_js');
        wp_localize_script('resource_catalog_js', 'resource_catalog_options', $this->attributes);

        ob_start();

        include("{$this->view_path}/catalog.php");

        return ob_get_clean();
    }

    /**
     * Filters only allowable orders
     *
     * @param string $order
     * @return string
     */
    public function whitelistOrder($order)
    {
        return in_array($order, $this->attribute_whitelist['order'], true) ? $order : $this->default_attributes['order'];
    }

    /**
     * Filters only allowable orderBys
     *
     * @param string $orderby
     * @return string
     */
    public function whitelistOrderBy($orderby)
    {
        return in_array($orderby, $this->attribute_whitelist['orderby'], true) ? $orderby : $this->default_attributes['orderby'];
    }
}