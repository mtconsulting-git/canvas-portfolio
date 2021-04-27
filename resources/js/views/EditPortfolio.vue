<template>
  <section>
    <page-header>
      <template slot="status">
        <ul class="navbar-nav mr-auto flex-row float-right">
          <li class="text-muted font-weight-bold">
            <div class="border-left pl-3">
              <div v-if="!isSaving && !isSaved">
                <span v-if="isPublished(portfolio.published_at)">{{
                  trans.published
                }}</span>
                <span v-if="isDraft(portfolio.published_at)">{{
                  trans.draft
                }}</span>
              </div>
              <span v-if="isSaving">{{ trans.saving }}</span>
              <span v-if="isSaved" class="text-success">{{ trans.saved }}</span>
            </div>
          </li>
        </ul>
      </template>

      <template slot="languages">
        <div v-if="uri !== 'create' && settings.languages.length > 1">
          <div class="dropdown ml-3 text-uppercase">
            <a
              id="languageSelector"
              class="nav-link border-left"
              v-bind:class="{
                'border-right': !(
                  settings.google_translate && languageSelected !== 'it'
                ),
              }"
              href="#"
              role="button"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              >{{ languageSelected }}</a
            >
            <div
              class="dropdown-menu dropdown-menu-right"
              style="min-width: unset"
            >
              <div v-for="lang in settings.languages">
                <div v-if="lang !== languageSelected">
                  <a
                    href="#"
                    class="dropdown-item"
                    @click="changeLanguage(lang)"
                  >
                    {{ lang }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="
            settings.google_translate &&
            languageSelected !== 'it' &&
            uri !== 'create' &&
            settings.languages.length > 1
          "
        >
          <a
            href="#"
            class="btn btn-info btn-block font-weight-bold mt-0 mr-1"
            @click="showTranslationModal"
          >
            {{ trans.translate }}
          </a>
        </div>
      </template>

      <template slot="options">
        <div class="dropdown">
          <a
            id="navbarDropdown"
            class="nav-link pr-0"
            href="#"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="25"
              class="icon-dots-horizontal"
            >
              <path
                class="fill-light-gray"
                fill-rule="evenodd"
                d="M5 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm7 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"
              />
            </svg>
          </a>

          <div class="dropdown-menu dropdown-menu-right">
            <div
              v-if="isPublished(portfolio.published_at)"
              class="dropdown-divider"
            />
            <a
              v-if="isDraft(portfolio.published_at) && (isAdmin || isEditor)"
              href="#"
              class="dropdown-item"
              @click="showPublishModal"
            >
              {{ trans.publish }}
            </a>
            <a href="#" class="dropdown-item" @click="showSettingsModal">
              {{ trans.general_settings }}
            </a>
            <a href="#" class="dropdown-item" @click="showFeaturedImageModal">
              {{ trans.featured_image }}
            </a>
            <a href="#" class="dropdown-item" @click="showGalleryModal">
              {{ trans.gallery }}
            </a>
            <a href="#" class="dropdown-item" @click="showSeoModal">
              {{ trans.seo_settings }}
            </a>
            <a
              v-if="isPublished(portfolio.published_at)"
              href="#"
              class="dropdown-item"
              @click.prevent="convertToDraft"
            >
              {{ trans.convert_to_draft }}
            </a>
            <a
              v-if="!creatingPortfolio"
              href="#"
              class="dropdown-item text-danger"
              @click="showDeleteModal"
            >
              {{ trans.delete }}
            </a>
          </div>
        </div>
      </template>
    </page-header>

    <main v-if="isReady" class="py-4">
      <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
        <div class="form-group my-3">
          <textarea-autosize
            v-model="portfolio.title[languageSelected]"
            :placeholder="trans.title"
            style="font-size: 42px"
            class="w-100 form-control-lg border-0 font-serif bg-transparent px-0"
            rows="1"
            @input.native="updatePortfolio"
          />
        </div>

        <div class="form-group my-2">
          <quill-editor
            :key="portfolio.id"
            :post="portfolio"
            :language-selected="languageSelected"
            @update-post="savePortfolio"
          />
        </div>
      </div>
    </main>

    <section v-if="isReady">
      <translation-modal
        ref="translationModal"
        :elem="portfolio"
        :language-selected="languageSelected"
        @update="savePortfolio"
      />
      <publish-modal
        ref="publishModal"
        :post="portfolio"
        @publish="updatePublishedAt"
      />
      <settings-modal-portfolio
        ref="settingsModalPortfolio"
        :portfolio="portfolio"
        :portfolio_categories="portfolio_categories"
        :language-selected="languageSelected"
        :errors="errors"
        @sync-slug="updateSlug"
        @add-category="addCategory"
        @add-portfolio-category="addPortfolioCategory"
        @update-portfolio="savePortfolio"
      />
      <featured-image-modal
        ref="featuredImageModal"
        :post="portfolio"
        @update-featured-image="updateFeaturedImage"
        @remove-featured-image="removeFeaturedImage"
        @update-post="savePortfolio"
      />
      <gallery-modal
        ref="galleryModal"
        :post="portfolio"
        @update-post="savePortfolio"
      />
      <seo-modal
        ref="seoModal"
        :post="portfolio"
        :language-selected="languageSelected"
        @sync-title="updateMetaTitle"
        @sync-description="updateMetaDescription"
        @update-post="savePortfolio"
      />
      <delete-modal
        ref="deleteModal"
        :header="trans.delete"
        :message="trans.deleted_portfolios_are_gone_forever"
        @delete="deletePortfolio"
      />
    </section>
  </section>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import $ from "jquery";
import DeleteModal from "../components/modals/DeleteModal";
import FeaturedImageModal from "../components/modals/FeaturedImageModal";
import GalleryModal from "../components/modals/GalleryModal";
import NProgress from "nprogress";
import PageHeader from "../components/PageHeader";
import PublishModal from "../components/modals/PublishModal";
import QuillEditor from "../components/editor/QuillEditor";
import SeoModal from "../components/modals/SeoModal";
import SettingsModalPortfolio from "../components/modals/SettingsModalPortfolio";
import TranslationModal from "../components/modals/TranslationModal";
import Vue from "vue";
import VueTextAreaAutosize from "vue-textarea-autosize";
import debounce from "lodash/debounce";
import get from "lodash/get";
import isEmpty from "lodash/isEmpty";
import status from "../mixins/status";

Vue.use(VueTextAreaAutosize);

export default {
  name: "edit-portfolio",

  components: {
    TranslationModal,
    PublishModal,
    FeaturedImageModal,
    GalleryModal,
    DeleteModal,
    QuillEditor,
    PageHeader,
    SeoModal,
    SettingsModalPortfolio,
  },

  mixins: [status],

  data() {
    return {
      uri: this.$route.params.id || "create",
      portfolio: {
        id: null,
        title: {
          it: null,
          en: null,
          es: null,
          fr: null,
          de: null,
        },
        slug: null,
        summary: {
          it: null,
          en: null,
          es: null,
          fr: null,
          de: null,
        },
        body: {
          it: null,
          en: null,
          es: null,
          fr: null,
          de: null,
        },
        info: {
          project_name: {
            it: null,
            en: null,
            es: null,
            fr: null,
            de: null,
          },
          location: {
            it: null,
            en: null,
            es: null,
            fr: null,
            de: null,
          },
          year: null,
          company: null,
        },
        published_at: null,
        featured_image: null,
        featured_image_caption: null,
        gallery_images: [],
        meta: {
          description: {
            it: null,
            en: null,
            es: null,
            fr: null,
            de: null,
          },
          title: {
            it: null,
            en: null,
            es: null,
            fr: null,
            de: null,
          },
          canonical_link: {
            it: null,
            en: null,
            es: null,
            fr: null,
            de: null,
          },
        },
        portfolio_category: [],
      },
      portfolio_categories: [],
      isSaving: false,
      isSaved: false,
      errors: [],
      isReady: false,
      languageSelected: "it",
    };
  },

  computed: {
    ...mapState(["settings"]),
    ...mapGetters({
      trans: "settings/trans",
      isAdmin: "settings/isAdmin",
      isEditor: "settings/isEditor",
    }),

    creatingPortfolio() {
      return this.$route.name === "create-portfolio";
    },
  },

  watch: {
    async $route(to) {
      if (this.uri === "create" && to.params.id === this.id) {
        this.uri = to.params.id;
      }

      if (this.uri !== to.params.id) {
        this.isReady = false;
        this.uri = to.params.id;
        await Promise.all([this.fetchPortfolio()]);
        this.isReady = true;
        NProgress.done();
      }
    },
  },

  async created() {
    await Promise.all([this.fetchPortfolio()]);
    this.isReady = true;
    NProgress.done();
  },

  methods: {
    fetchPortfolio() {
      return this.request()
        .get(`/api/portfolios/${this.uri}`)
        .then(({ data }) => {
          this.portfolio.id = data.portfolio.id;
          this.portfolio.title.it = get(data.portfolio.title, "it", "");
          this.portfolio.title.en = get(data.portfolio.title, "en", "");
          this.portfolio.title.es = get(data.portfolio.title, "es", "");
          this.portfolio.title.fr = get(data.portfolio.title, "fr", "");
          this.portfolio.title.de = get(data.portfolio.title, "de", "");
          this.portfolio.slug = get(data.portfolio, "slug", "");
          this.portfolio.summary.it = get(data.portfolio.summary, "it", "");
          this.portfolio.summary.en = get(data.portfolio.summary, "en", "");
          this.portfolio.summary.es = get(data.portfolio.summary, "es", "");
          this.portfolio.summary.fr = get(data.portfolio.summary, "fr", "");
          this.portfolio.summary.de = get(data.portfolio.summary, "de", "");
          this.portfolio.body.it = get(data.portfolio.body, "it", "");
          this.portfolio.body.en = get(data.portfolio.body, "en", "");
          this.portfolio.body.es = get(data.portfolio.body, "es", "");
          this.portfolio.body.fr = get(data.portfolio.body, "fr", "");
          this.portfolio.body.de = get(data.portfolio.body, "de", "");
          this.portfolio.info.project_name.it = get(
            data.portfolio.info,
            "project_name.it",
            ""
          );
          this.portfolio.info.project_name.en = get(
            data.portfolio.info,
            "project_name.en",
            ""
          );
          this.portfolio.info.project_name.es = get(
            data.portfolio.info,
            "project_name.es",
            ""
          );
          this.portfolio.info.project_name.fr = get(
            data.portfolio.info,
            "project_name.fr",
            ""
          );
          this.portfolio.info.project_name.de = get(
            data.portfolio.info,
            "project_name.de",
            ""
          );
          this.portfolio.info.location.it = get(
            data.portfolio.info,
            "location.it",
            ""
          );
          this.portfolio.info.location.en = get(
            data.portfolio.info,
            "location.en",
            ""
          );
          this.portfolio.info.location.es = get(
            data.portfolio.info,
            "location.es",
            ""
          );
          this.portfolio.info.location.fr = get(
            data.portfolio.info,
            "location.fr",
            ""
          );
          this.portfolio.info.location.de = get(
            data.portfolio.info,
            "location.de",
            ""
          );
          this.portfolio.info.year = get(data.portfolio.info, "year", "");
          this.portfolio.info.company = get(data.portfolio.info, "company", "");
          this.portfolio.published_at = get(data.portfolio, "published_at", "");
          this.portfolio.featured_image = get(
            data.portfolio,
            "featured_image",
            ""
          );
          this.portfolio.featured_image_caption = get(
            data.portfolio,
            "featured_image_caption",
            ""
          );
          this.portfolio.gallery_images = get(
            data.portfolio,
            "gallery_images",
            []
          );
          this.portfolio.meta.description.it = get(
            data.portfolio.meta,
            "description.it",
            ""
          );
          this.portfolio.meta.description.en = get(
            data.portfolio.meta,
            "description.en",
            ""
          );
          this.portfolio.meta.description.es = get(
            data.portfolio.meta,
            "description.es",
            ""
          );
          this.portfolio.meta.description.fr = get(
            data.portfolio.meta,
            "description.fr",
            ""
          );
          this.portfolio.meta.description.de = get(
            data.portfolio.meta,
            "description.de",
            ""
          );
          this.portfolio.meta.title.it = get(
            data.portfolio.meta,
            "title.it",
            ""
          );
          this.portfolio.meta.title.en = get(
            data.portfolio.meta,
            "title.en",
            ""
          );
          this.portfolio.meta.title.es = get(
            data.portfolio.meta,
            "title.es",
            ""
          );
          this.portfolio.meta.title.fr = get(
            data.portfolio.meta,
            "title.fr",
            ""
          );
          this.portfolio.meta.title.de = get(
            data.portfolio.meta,
            "title.de",
            ""
          );
          this.portfolio.meta.canonical_link.it = get(
            data.portfolio.meta,
            "canonical_link.it",
            ""
          );
          this.portfolio.meta.canonical_link.en = get(
            data.portfolio.meta,
            "canonical_link.en",
            ""
          );
          this.portfolio.meta.canonical_link.es = get(
            data.portfolio.meta,
            "canonical_link.es",
            ""
          );
          this.portfolio.meta.canonical_link.fr = get(
            data.portfolio.meta,
            "canonical_link.fr",
            ""
          );
          this.portfolio.meta.canonical_link.de = get(
            data.portfolio.meta,
            "canonical_link.de",
            ""
          );
          this.portfolio.portfolio_category = get(
            data.portfolio,
            "portfolio_category",
            []
          );

          this.portfolio_categories = get(data, "portfolio_categories", []);

          NProgress.inc();
        })
        .catch(() => {
          this.$router.push({ name: "portfolios" });
        });
    },

    convertToDraft() {
      this.portfolio.published_at = null;
      this.savePortfolio();
    },

    updatePublishedAt(date) {
      this.portfolio.published_at = date;
      this.savePortfolio();
    },

    updateSlug(slug) {
      this.portfolio.slug = slug;
    },

    addCategory(portfolio_category) {
      this.portfolio_categories.push(portfolio_category);
    },

    addPortfolioCategory(portfolio_category) {
      this.portfolio.portfolio_category.push(portfolio_category);
      this.savePortfolio();
    },

    updateFeaturedImage(path) {
      this.portfolio.featured_image = path;
    },

    removeFeaturedImage() {
      this.portfolio.featured_image = null;
      this.portfolio.featured_image_caption = null;
    },

    updateMetaTitle(title) {
      this.portfolio.meta.title[this.languageSelected] = title;
    },

    updateMetaDescription(description) {
      this.portfolio.meta.description[this.languageSelected] = description;
    },

    updatePortfolio: debounce(function () {
      this.savePortfolio();
    }, 3000),

    async savePortfolio() {
      this.errors = [];
      this.isSaving = true;
      this.isSaved = false;
      this.portfolio.title[this.languageSelected] =
        this.portfolio.title[this.languageSelected] || "Title";

      await this.request()
        .post(`/api/portfolios/${this.portfolio.id}`, this.portfolio)
        .then(({ data }) => {
          this.isSaving = false;
          this.isSaved = true;
          this.portfolio = data;

          // TODO: Check if searchable data is changing
          this.$store.dispatch("search/buildIndex", true);
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        });

      if (isEmpty(this.errors) && this.creatingPortfolio) {
        await this.$router.push({
          name: "edit-portfolio",
          params: { id: this.portfolio.id },
        });
        NProgress.done();
      }

      setTimeout(() => {
        this.isSaved = false;
        this.isSaving = false;
      }, 3000);
    },

    async deletePortfolio() {
      await this.request()
        .delete(`/api/portfolios/${this.portfolio.id}`)
        .then(() => {
          this.$store.dispatch("search/buildIndex", true);
          this.$toasted.show(this.trans.success, {
            className: "bg-success",
          });
        });

      $(this.$refs.deleteModal.$el).modal("hide");

      await this.$router.push({ name: "portfolios" });
    },

    changeLanguage(lang) {
      this.languageSelected = lang;
    },

    showTranslationModal() {
      $(this.$refs.translationModal.$el).modal("show");
    },

    showPublishModal() {
      $(this.$refs.publishModal.$el).modal("show");
    },

    showSettingsModal() {
      $(this.$refs.settingsModalPortfolio.$el).modal("show");
    },

    showFeaturedImageModal() {
      $(this.$refs.featuredImageModal.$el).modal("show");
    },

    showGalleryModal() {
      $(this.$refs.galleryModal.$el).modal("show");
    },

    showSeoModal() {
      $(this.$refs.seoModal.$el).modal("show");
    },

    showDeleteModal() {
      $(this.$refs.deleteModal.$el).modal("show");
    },
  },
};
</script>
