<div id="resource_catalog" class="resource-catalog">
    <div class="wrapper-gutters">

        <div v-if="features.search_expand_button && (features.search || features.filters)" class="activate_search_btn_container">
            <button type="button" :aria-expanded="search_expanded ? 'true' : 'false'" aria-controls="search_and_filter_container" @click="toggleSearchExpanded">
                <span class="details-arrow">&#9658;</span>
                <span v-if="features.search">Search</span>
                <span v-if="features.search && features.filters"> &amp; </span>
                <span v-if="features.filters">Filter</span>
            </button>
        </div>

        <div v-show="search_expanded" id="search_and_filter_container" role="search">

            <input v-if="features.search" class="resource-searchbox" type="search" placeholder="Search..." v-model="search" @input="debounceFetchResources">

            <div v-if="features.filters" class="resource-filters">

                <div v-if="features.filter.categories" class="resource-filter resource-category-filter">
                    <label class="resource-filter-label" for="categories_p">Category</label>
                    <select class="solution_filter" name="categories_p" v-model="category_filter" @change="fetchResources">
                        <option selected value="all">All Categories</option>
                        <option disabled>---</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>

                <div v-if="features.filter.tags" class="resource-filter resource-category-filter">
                    <label class="resource-filter-label" for="tags_p">Tag</label>
                    <select class="tags_filter" name="tags_p" v-model="tag_filter" @change="fetchResources">
                        <option selected value="all">All Tags</option>
                        <option disabled>---</option>
                        <option v-for="tag in tags" :value="tag.id">{{ tag.name }}</option>
                    </select>
                </div>

                <div v-for="custom_taxonomy in custom_taxonomies" class="resource-filter resource-custom-filter">
                    <label v-if="features.custom_filters_labels" class="resource-filter-label" :for="custom_taxonomy.name">{{ custom_taxonomy.label }}</label>
                    <div v-if="features.custom_filters_descriptions" class="resource-filter-description">{{ custom_taxonomy.description }}</div>
                    <select :name="custom_taxonomy.name" v-model="custom_taxonomies_filters[custom_taxonomy.name]" @change="fetchResources">
                        <option selected value="all">{{ custom_taxonomy.labels.all_items }}</option>
                        <option disabled>---</option>
                        <option v-for="tax in custom_taxonomies_choices[custom_taxonomy.name]" :value="tax.id">{{ tax.name }}</option>
                    </select>
                </div>

                <div v-if="features.reset && (features.search || features.filters)" class="reset_btn">
                    <button type="button" @click="reset">
                        <span class="reset-icon">&#8635;</span>
                        <span class="reset-label">Reset</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div v-if="filtered || loading" class="resource-count">{{ loading ? 'Loading ' : resourceCount }} resources:</div>

    <section v-if="fetched.resources" class="resources">
        <article v-for="resource in resources" class="resource" :id="resource.slug">
            <h2 class="resource-title" v-html="resource.title.rendered"></h2>
            <section class="resource-cats-and-tags">
                <div v-for="category in resource.categories" :class="['resource-category', categorySlug(category)]" :title="categoryName(category)">
                    <span class="first-letter" :title="categoryName(category)">{{ categoryName(category).charAt(0) }}</span>
                    <span class="category-name sr-only">{{ categoryName(category) }}</span>
                </div>
                <div v-for="audience in resource.resource_audiences" :class="['resource-audience', audienceSlug(audience)]">{{ audienceName(audience) }}</div>
                <div v-for="tag in resource.tags" :class="['resource-tag', tagSlug(tag)]">{{ tagName(tag) }}</div>
                <div v-for="length in resource.resource_lengths" :class="['resource-length', lengthSlug(length)]">{{ lengthName(length) }}</div>
                <template v-for="custom_taxonomy in custom_taxonomies" :key="custom_taxonomy.name">
                    <div v-for="tax in resource[custom_taxonomy.name]" class="resource-tag">{{ taxonomyName(tax, custom_taxonomy.name) }}</div>
                </template>
            </section>
            <div class="resource-excerpt" v-if="resource.excerpt" v-html="resource.excerpt.rendered"></div>
            <button v-if="resource.content" :aria-controls="`${resource.slug}_content`" :aria-expanded="(resource.content && resource.content.protected) ? 'true' : 'false'" @click="resource.content.protected = !resource.content.protected">
                <span class="details-arrow">&#9658;</span> Details
            </button>
            <a v-if="resource.resource_link" :href="resource.resource_link" target="_blank" @click="analyticsCaptureOutboundLink(resource.resource_link)">
                <button class="resource-link">Get &#9658;</button>
            </a>
            <section :id="`${resource.slug}_content`" class="resource-content" v-show="resource.content && resource.content.protected" v-html="resource.content.rendered"></section>
        </article>
    </section>

</div>