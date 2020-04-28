if (window.location.hostname.endsWith('test')) {
  Vue.config.devtools = true;
}

let resource_catalog_app = new Vue({
  el: '#resource_catalog',

  data: {
    base_url: window.location.origin,
    resources: [],
    audiences: [],
    lengths: [],
    programs: [],
    categories: [],
    tags: [],
    audience_filter: 'all',
    length_filter: 'all',
    program_filter: 'all',
    category_filter: 'all',
    tag_filter: 'all',
    search: '',
    loading: true,
    search_timeout: undefined,
    search_expanded: false,
    features: {
      show_all: true,
      search_expand_button: true,
      search: true,
      filters: true,
      filter: {
        audiences: true,
        lengths: true,
        tags: true,
        categories: true,
        programs: true,
      },
    },
    fetched: {
      resources: false,
      audiences: false,
      lengths: false,
      programs: false,
      categories: false,
      tags: false,
    },
  },

  mounted() {
    this.setOptions();
    this.fetchAudiences();
    this.fetchLengths();
    this.fetchPrograms();
    this.fetchCategories();
    this.fetchTags();
    if (this.features.initial_load) this.fetchResources();
  },

  computed: {

    resourceCount() {
      return this.resources.length;
    },

    filtered() {
      return this.search.length > 0 ||
        this.audience_filter !== 'all' ||
        this.length_filter !== 'all' ||
        this.program_filter !== 'all' ||
        this.category_filter !== 'all' ||
        this.tag_filter !== 'all';
    },

  },

  methods: {

    setOptions() {
      let validate_url = (url) => {
        try { new URL(url) } catch(_) { return false; }
        return url;
      };

      if (typeof resource_catalog_options === 'object') {
        let options = resource_catalog_options;
        if ('site_url' in options) this.base_url = validate_url(options.site_url) || this.base_url;
        if ('search_expand_button' in options) this.features.search_expand_button = Boolean(options.search_expand_button);
        if ('search' in options) this.features.search = Boolean(options.search);
        if ('show_all' in options) this.features.initial_load = Boolean(options.show_all);
        if ('audiences_filter' in options) this.features.filter.audiences = Boolean(options.audiences_filter);
        if ('lengths_filter' in options) this.features.filter.lengths = Boolean(options.lengths_filter);
        if ('tags_filter' in options) this.features.filter.tags = Boolean(options.tags_filter);
        if ('categories_filter' in options) this.features.filter.categories = Boolean(options.categories_filter);
        if ('programs_filter' in options) this.features.filter.programs = Boolean(options.programs_filter);
        if ('filters' in options) this.features.filters = Boolean(options.filters);
        if ('search_expanded' in options) this.search_expanded = this.features.search_expand_button ? Boolean(options.search_expanded) : true;
      }
    },

    fetchFromWordPress(url, propertyName, page = 1, union = false) {
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
          this.loading = false;
          if (page < totalPages) {
            this.fetchFromWordPress(url, propertyName, page+1, true);
          }
        });
    },

    fetchResources() {
      let params = {};
      let url = new URL(this.base_url + '/wp-json/wp/v2/resource');

      if (this.audience_filter !== 'all') {
        params.resource_audiences = this.audience_filter;
      }
      if (this.length_filter !== 'all') {
        params.resource_lengths = this.length_filter;
      }
      if (this.program_filter !== 'all') {
        params.resource_programs = this.program_filter;
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

      url.search = new URLSearchParams(params);

      this.$set(this.fetched, 'resources', false);
      this.fetchFromWordPress(url, 'resources');
    },

    fetchAudiences() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/resource_audiences'), 'audiences');
    },

    fetchLengths() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/resource_lengths'), 'lengths');
    },

    fetchPrograms() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/resource_programs'), 'programs');
    },

    fetchCategories() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/categories'), 'categories');
    },

    fetchTags() {
      this.fetchFromWordPress(new URL(this.base_url + '/wp-json/wp/v2/tags'), 'tags');
    },

    debounceFetchResources() {
      if (this.search_timeout) clearTimeout(this.search_timeout);
      this.search_timeout = setTimeout(() => {
        this.fetchResources();
      }, 350);
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

    toggleSearchExpanded() {
      this.search_expanded = !this.search_expanded;
    },

    toggleResourceContent(resource) {
      this.resources.find(property => property.id === resource.id).shown = true;
    },

    resourceContentShown(resource) {
      return resource.content && resource.content.protected;
    }

  },

});