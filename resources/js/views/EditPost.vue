<template>
  <section>
    <page-header>
      <template slot="status">
        <ul class="navbar-nav mr-auto flex-row float-right">
          <li class="text-muted font-weight-bold">
            <div class="border-left pl-3">
              <div v-if="!isSaving && !isSaved">
                <span v-if="isPublished(post.published_at)">{{
                  trans.published
                }}</span>
                <span v-if="isDraft(post.published_at)">{{ trans.draft }}</span>
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
            <router-link
              v-if="isPublished(post.published_at)"
              :to="{ name: 'post-stats', params: { id: uri } }"
              class="dropdown-item"
            >
              {{ trans.view_stats }}
            </router-link>
            <div
              v-if="isPublished(post.published_at)"
              class="dropdown-divider"
            />
            <a
              v-if="isDraft(post.published_at) && (isAdmin || isEditor)"
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
              v-if="isPublished(post.published_at)"
              href="#"
              class="dropdown-item"
              @click.prevent="convertToDraft"
            >
              {{ trans.convert_to_draft }}
            </a>
            <a
              v-if="!creatingPost"
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
            v-model="post.title[languageSelected]"
            :placeholder="trans.title"
            style="font-size: 42px"
            class="w-100 form-control-lg border-0 font-serif bg-transparent px-0"
            rows="1"
            @input.native="updatePost"
          />
        </div>

        <div class="form-group my-2">
          <quill-editor
            :key="post.id"
            :post="post"
            :language-selected="languageSelected"
            @update-post="savePost"
          />
        </div>
      </div>
    </main>

    <section v-if="isReady">
      <translation-modal
        ref="translationModal"
        :elem="post"
        :language-selected="languageSelected"
        @update="savePost"
      />
      <publish-modal
        ref="publishModal"
        :post="post"
        @publish="updatePublishedAt"
      />
      <settings-modal
        ref="settingsModal"
        :post="post"
        :tags="tags"
        :topics="topics"
        :language-selected="languageSelected"
        :errors="errors"
        @sync-slug="updateSlug"
        @add-tag="addTag"
        @add-post-tag="addPostTag"
        @add-post-topic="addPostTopic"
        @add-topic="addTopic"
        @update-post="savePost"
      />
      <featured-image-modal
        ref="featuredImageModal"
        :post="post"
        @update-featured-image="updateFeaturedImage"
        @remove-featured-image="removeFeaturedImage"
        @update-post="savePost"
      />
      <gallery-modal ref="galleryModal" :post="post" @update-post="savePost" />
      <seo-modal
        ref="seoModal"
        :post="post"
        :language-selected="languageSelected"
        @sync-title="updateMetaTitle"
        @sync-description="updateMetaDescription"
        @update-post="savePost"
      />
      <delete-modal
        ref="deleteModal"
        :header="trans.delete"
        :message="trans.deleted_posts_are_gone_forever"
        @delete="deletePost"
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
import SettingsModal from "../components/modals/SettingsModal";
import TranslationModal from "../components/modals/TranslationModal";
import Vue from "vue";
import VueTextAreaAutosize from "vue-textarea-autosize";
import debounce from "lodash/debounce";
import get from "lodash/get";
import isEmpty from "lodash/isEmpty";
import status from "../mixins/status";

Vue.use(VueTextAreaAutosize);

export default {
  name: "edit-post",

  components: {
    TranslationModal,
    PublishModal,
    FeaturedImageModal,
    GalleryModal,
    DeleteModal,
    QuillEditor,
    PageHeader,
    SeoModal,
    SettingsModal,
  },

  mixins: [status],

  data() {
    return {
      uri: this.$route.params.id || "create",
      post: {
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
        tags: [],
        topic: [],
      },
      tags: [],
      topics: [],
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

    creatingPost() {
      return this.$route.name === "create-post";
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
        await Promise.all([this.fetchPost()]);
        this.isReady = true;
        NProgress.done();
      }
    },
  },

  async created() {
    await Promise.all([this.fetchPost()]);
    this.isReady = true;
    NProgress.done();
  },

  methods: {
    fetchPost() {
      return this.request()
        .get(`/api/posts/${this.uri}`)
        .then(({ data }) => {
          this.post.id = data.post.id;
          this.post.title.it = get(data.post.title, "it", "");
          this.post.title.en = get(data.post.title, "en", "");
          this.post.title.es = get(data.post.title, "es", "");
          this.post.title.fr = get(data.post.title, "fr", "");
          this.post.title.de = get(data.post.title, "de", "");
          this.post.slug = get(data.post, "slug", "");
          this.post.summary.it = get(data.post.summary, "it", "");
          this.post.summary.en = get(data.post.summary, "en", "");
          this.post.summary.es = get(data.post.summary, "es", "");
          this.post.summary.fr = get(data.post.summary, "fr", "");
          this.post.summary.de = get(data.post.summary, "de", "");
          this.post.body.it = get(data.post.body, "it", "");
          this.post.body.en = get(data.post.body, "en", "");
          this.post.body.es = get(data.post.body, "es", "");
          this.post.body.fr = get(data.post.body, "fr", "");
          this.post.body.de = get(data.post.body, "de", "");
          this.post.published_at = get(data.post, "published_at", "");
          this.post.featured_image = get(data.post, "featured_image", "");
          this.post.featured_image_caption = get(
            data.post,
            "featured_image_caption",
            ""
          );
          this.post.gallery_images = get(data.post, "gallery_images", []);
          this.post.meta.description.it = get(
            data.post.meta,
            "description.it",
            ""
          );
          this.post.meta.description.en = get(
            data.post.meta,
            "description.en",
            ""
          );
          this.post.meta.description.es = get(
            data.post.meta,
            "description.es",
            ""
          );
          this.post.meta.description.fr = get(
            data.post.meta,
            "description.fr",
            ""
          );
          this.post.meta.description.de = get(
            data.post.meta,
            "description.de",
            ""
          );
          this.post.meta.title.it = get(data.post.meta, "title.it", "");
          this.post.meta.title.en = get(data.post.meta, "title.en", "");
          this.post.meta.title.es = get(data.post.meta, "title.es", "");
          this.post.meta.title.fr = get(data.post.meta, "title.fr", "");
          this.post.meta.title.de = get(data.post.meta, "title.de", "");
          this.post.meta.canonical_link.it = get(
            data.post.meta,
            "canonical_link.it",
            ""
          );
          this.post.meta.canonical_link.en = get(
            data.post.meta,
            "canonical_link.en",
            ""
          );
          this.post.meta.canonical_link.es = get(
            data.post.meta,
            "canonical_link.es",
            ""
          );
          this.post.meta.canonical_link.fr = get(
            data.post.meta,
            "canonical_link.fr",
            ""
          );
          this.post.meta.canonical_link.de = get(
            data.post.meta,
            "canonical_link.de",
            ""
          );
          this.post.tags = get(data.post, "tags", []);
          this.post.topic = get(data.post, "topic", []);

          this.tags = get(data, "tags", []);
          this.topics = get(data, "topics", []);

          NProgress.inc();
        })
        .catch(() => {
          this.$router.push({ name: "posts" });
        });
    },

    convertToDraft() {
      this.post.published_at = null;
      this.savePost();
    },

    updatePublishedAt(date) {
      this.post.published_at = date;
      this.savePost();
    },

    updateSlug(slug) {
      this.post.slug = slug;
    },

    addTag(tag) {
      this.tags.push(tag);
    },

    addTopic(topic) {
      this.topics.push([topic]);
    },

    addPostTag(tag) {
      this.post.tags.push(tag);
      this.savePost();
    },

    addPostTopic(topic) {
      this.post.topic = [topic];
      this.savePost();
    },

    updateFeaturedImage(path) {
      this.post.featured_image = path;
    },

    removeFeaturedImage() {
      this.post.featured_image = null;
      this.post.featured_image_caption = null;
    },

    updateMetaTitle(title) {
      this.post.meta.title[this.languageSelected] = title;
    },

    updateMetaDescription(description) {
      this.post.meta.description[this.languageSelected] = description;
    },

    updatePost: debounce(function () {
      this.savePost();
    }, 3000),

    async savePost() {
      this.errors = [];
      this.isSaving = true;
      this.isSaved = false;
      this.post.title[this.languageSelected] =
        this.post.title[this.languageSelected] || "Title";

      await this.request()
        .post(`/api/posts/${this.post.id}`, this.post)
        .then(({ data }) => {
          this.isSaving = false;
          this.isSaved = true;
          this.post = data;

          // TODO: Check if searchable data is changing
          this.$store.dispatch("search/buildIndex", true);
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
        });

      if (isEmpty(this.errors) && this.creatingPost) {
        await this.$router.push({
          name: "edit-post",
          params: { id: this.post.id },
        });
        NProgress.done();
      }

      setTimeout(() => {
        this.isSaved = false;
        this.isSaving = false;
      }, 3000);
    },

    async deletePost() {
      await this.request()
        .delete(`/api/posts/${this.post.id}`)
        .then(() => {
          this.$store.dispatch("search/buildIndex", true);
          this.$toasted.show(this.trans.success, {
            className: "bg-success",
          });
        });

      $(this.$refs.deleteModal.$el).modal("hide");

      await this.$router.push({ name: "posts" });
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
      $(this.$refs.settingsModal.$el).modal("show");
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
