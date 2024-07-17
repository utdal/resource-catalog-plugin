if (window.location.hostname.endsWith('test')) {
  Vue.config.devtools = true;
}

document.addEventListener('DOMContentLoaded', () => {

let resource_catalog_app = new Vue({
  el: '#resource_catalog',

  data: {
    base_url: window.location.origin,
    resources: [],
    categories: [],
    tags: [],
    custom_taxonomies: [],
    category_filter: 'all',
    tag_filter: 'all',
    custom_taxonomies_filters: {},
    custom_taxonomies_choices: {},
    search: '',
    loading: true,
    search_timeout: undefined,
    search_expanded: false,
    features: {
      initial_load: true,
      search_expand_button: true,
      search: true,
      search_tags: true,
      search_categories: true,
      search_custom_filters: true,
      custom_filters_labels: true,
      custom_filters_descriptions: true,
      reset: true,
      filters: true,
      filter: {
        tags: true,
        categories: true,
        custom_taxonomies: false,
      },
      outbound_analytics: true,
      featured_image: true,
      excerpt: true,
      content: true,
      content_expand_button: true,
    },
    order: {
      resources: {by: 'title', how: 'asc'},
    },
    fetched: {
      resources: false,
      categories: false,
      tags: false,
      custom: false,
    },
  },

  mounted() {
    this.setOptions();
    this.fetchCategories();
    this.fetchTags();
    this.fetchCustomTaxonomies();
    if (this.features.initial_load) this.fetchResources();
  },

  computed: {

    resourceCount() {
      return this.resources.length;
    },

    filtered() {
      return this.search.length > 0 ||
        this.category_filter !== 'all' ||
        this.tag_filter !== 'all' ||
        this.customTaxonomyFiltered;
    },

    customTaxonomyFiltered() {
      return Boolean(Object.values(this.custom_taxonomies_filters).find(selected => selected !== 'all'));
    }

  },

  methods: {

    setOptions() {
      let validate_url = (url) => {
        try { new URL(url) } catch(_) { return false; }
        return url;
      };

      let set_custom_taxonomies = (taxonomies) => {
        for (let taxonomy of taxonomies) {
          this.custom_taxonomies.push({
            'name': taxonomy.name,
            'label': taxonomy.singular_label,
            'labels': taxonomy.labels,
            'description': taxonomy.description,
            'rest_base': taxonomy.rest_base || taxonomy.name,
          });
          this.$set(this.custom_taxonomies_filters, taxonomy.name, 'all');
        }
      };

      if (typeof resource_catalog_options === 'object') {
        let options = resource_catalog_options;
        let order_choices = ['asc', 'desc'];
        let orderby_choices = ['date', 'id', 'modified', 'parent', 'slug', 'title'];

        if ('site_url' in options) this.base_url = validate_url(options.site_url) || this.base_url;
        if ('search_expand_button' in options) this.features.search_expand_button = Boolean(options.search_expand_button);
        if ('search' in options) this.features.search = Boolean(options.search);
        if ('search_tags' in options) this.features.search_tags = Boolean(options.search_tags);
        if ('search_categories' in options) this.features.search_categories = Boolean(options.search_categories);
        if ('search_custom_filters' in options) this.features.search_custom_filters = Boolean(options.search_custom_filters);
        if ('custom_filters_labels' in options) this.features.custom_filters_labels = Boolean(options.custom_filters_labels);
        if ('custom_filters_descriptions' in options) this.features.custom_filters_descriptions = Boolean(options.custom_filters_descriptions);
        if ('reset' in options) this.features.reset = Boolean(options.reset);
        if ('show_all' in options) this.features.initial_load = Boolean(options.show_all);
        if ('tags_filter' in options) this.features.filter.tags = Boolean(options.tags_filter);
        if ('categories_filter' in options) this.features.filter.categories = Boolean(options.categories_filter);
        if ('custom_filters' in options) this.features.filter.custom_taxonomies = Boolean(options.custom_filters);
        if (this.features.filter.custom_taxonomies && options.custom_filters instanceof Array) set_custom_taxonomies(options.custom_filters);
        if ('filters' in options) this.features.filters = Boolean(options.filters);
        if ('outbound_analytics' in options) this.features.outbound_analytics = Boolean(options.outbound_analytics);
        if ('order' in options) this.order.resources.how = order_choices.includes(options.order) ? options.order : 'asc';
        if ('orderby' in options) this.order.resources.by = orderby_choices.includes(options.orderby) ? options.orderby : 'title';
        if ('search_expanded' in options) this.search_expanded = this.features.search_expand_button ? Boolean(options.search_expanded) : true;
        if ('featured_image' in options) this.features.featured_image = Boolean(options.featured_image);
        if ('excerpt' in options) this.features.excerpt = Boolean(options.excerpt);
        if ('content' in options) this.features.content = Boolean(options.content);
        if ('content_expand_button' in options) this.features.content_expand_button = Boolean(options.content_expand_button);
      }
    },

    fetchFromWordPress(url, propertyName, page = 1, union = false, sort = false, parentPropertyName) {
      this.loading = true;
      let totalPages;
      let params = new URLSearchParams(url.search);
      params.set('page', page);
      params.set('per_page', 50);
      url.search = params;

      fetch(url)
        .then((response) => {
          totalPages = response.headers.get('X-WP-TotalPages') || 1;
          return response.json();
        })
        .then((data) => {
          this[propertyName] = union ? _.unionBy(this[propertyName], data, 'id') : data;
          this.$set(this.fetched, propertyName, true);
          if (parentPropertyName) {
            let subProperty = {};
            subProperty[propertyName] = this[propertyName];
            this[parentPropertyName] = Object.assign(this[parentPropertyName], subProperty);
          }
          this.loading = false;
          if (page < totalPages) {
            this.fetchFromWordPress(url, propertyName, page+1, true);
          } else if (sort) {
            this.sort(propertyName);
          }
        });
    },

    fetchResources() {
      let params = {};
      let url = new URL(this.base_url + '/wp-json/wp/v2/resource');

      params.orderby = this.order.resources.by;
      params.order = this.order.resources.how;

      if (this.features.featured_image) {
        params._embed = "wp:featuredmedia";
      }
      if (this.tag_filter !== 'all') {
        params.tags = this.tag_filter;
      }
      if (this.category_filter !== 'all') {
        params.categories = this.category_filter;
      }
      if (this.search) {
        params.search = this.search;
      }

      for (const custom_filter in this.custom_taxonomies_filters) {
        if (this.custom_taxonomies_filters[custom_filter] !== 'all') {
          params[custom_filter] = this.custom_taxonomies_filters[custom_filter];
        }
      }

      url.search = new URLSearchParams(params);

      this.$set(this.fetched, 'resources', false);
      this.fetchFromWordPress(url, 'resources');

      // Search also searches tags, categories, and custom taxonomies
      if (this.search) {
        delete params.search;
        let searched_tags = this.searchFetchedTags(this.search);
        let searched_cats = this.searchFetchedCategories(this.search);

        if (this.features.search_tags && this.tag_filter === 'all' && searched_tags.length > 0) {
          params.tags = searched_tags.map(tag => tag.id).join(',');
          url.search = new URLSearchParams(params);
          this.fetchFromWordPress(url, 'resources', 1, true, true);
          delete params.tags;
        }

        if (this.features.search_categories && this.category_filter === 'all' && searched_cats.length > 0) {
          params.categories = searched_cats.map(cat => cat.id).join(',');
          url.search = new URLSearchParams(params);
          this.fetchFromWordPress(url, 'resources', 1, true, true);
        }

        if (this.features.search_custom_filters && !this.customTaxonomyFiltered) {
          let searched_taxes = this.searchFetchedCustomTaxonomies(this.search);
          for (const [custom_filter, tax_choices] of Object.entries(searched_taxes)) {
            if (tax_choices.length > 0) {
              params[custom_filter] = tax_choices.map(tax => tax.id).join(',');
              url.search = new URLSearchParams(params);
              this.fetchFromWordPress(url, 'resources', 1, true, true);
            }
          }
        }
      }
    },

    fetchCustomTaxonomies() {
      for (let taxonomy of this.custom_taxonomies) {
        this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/' + taxonomy.rest_base), taxonomy.name, 1, false, false, 'custom_taxonomies_choices');
      }
    },

    fetchCategories() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/categories'), 'categories');
    },

    fetchTags() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/tags'), 'tags');
    },

    analyticsCaptureOutboundLink(url) {
      if (this.outbound_analytics && (typeof ga === 'function')) {
        ga('send', 'event', 'outbound', 'click', url, {
          'transport': 'beacon',
          'hitCallback': () => { console.log('Outbound link clicked: ' + url) }
        });
      }

      return true;
    },

    debounceFetchResources() {
      if (this.search_timeout) clearTimeout(this.search_timeout);
      this.search_timeout = setTimeout(() => {
        this.fetchResources();
      }, 350);
    },

    sort(propertyName) {
      if (propertyName in this.order) {
        this[propertyName] = _.orderBy(this[propertyName], property => {
          let orderby = this.order[propertyName].by;
          return (orderby === 'title') ? property.title.rendered.toLowerCase() : property[orderby];
        }, this.order[propertyName].how);
      }
    },

    searchFetchedTags(query) {
      return this.tags.filter(tag => tag.name.toLowerCase().includes(query.toLowerCase()));
    },

    searchFetchedCategories(query) {
      return this.categories.filter(cat => cat.name.toLowerCase().includes(query.toLowerCase()));
    },

    searchFetchedCustomTaxonomies(query) {
      let matched_taxonomies = {};
      for (const [tax_name, tax_choices] of Object.entries(this.custom_taxonomies_choices)) {
        matched_taxonomies[tax_name] = tax_choices.filter(tax => tax.name.toLowerCase().includes(query.toLowerCase()));
      }
      return matched_taxonomies;
    },

    getPropertyValue(id, propertyName, attributeName) {
      let found_property = this[propertyName].find(property => property.id === id);

      return (found_property && attributeName in found_property) ? found_property[attributeName] : id;
    },

    categoryName(id) {
      return this.getPropertyValue(id, 'categories', 'name');
    },

    categorySlug(id) {
      return this.getPropertyValue(id, 'categories', 'slug');
    },

    tagName(id) {
      return this.getPropertyValue(id, 'tags', 'name');
    },

    tagSlug(id) {
      return this.getPropertyValue(id, 'tags', 'slug');
    },

    lengthName(id) {
      return this.getPropertyValue(id, 'lengths', 'name');
    },

    lengthSlug(id) {
      return this.getPropertyValue(id, 'lengths', 'slug');
    },

    audienceName(id) {
      return this.getPropertyValue(id, 'audiences', 'name');
    },

    audienceSlug(id) {
      return this.getPropertyValue(id, 'audiences', 'slug');
    },

    taxonomyName(id, taxonomy) {
      let found_property = this.custom_taxonomies_choices[taxonomy].find(property => property.id === id);

      return (found_property && 'name' in found_property) ? found_property.name : id;
    },

    toggleSearchExpanded() {
      this.search_expanded = !this.search_expanded;
    },

    toggleResourceContent(resource) {
      this.resources.find(property => property.id === resource.id).shown = true;
    },

    resourceContentShown(resource) {
      return resource.content && resource.content.protected;
    },

    reset() {
      this.category_filter = 'all';
      this.tag_filter = 'all';
      this.search = '';
      for (const custom_filter in this.custom_taxonomies_filters) {
        this.custom_taxonomies_filters[custom_filter] = 'all';
      }
      if (this.features.initial_load) {
        this.fetchResources();
      } else {
        this.resources = [];
      }
    },

  },

});

});