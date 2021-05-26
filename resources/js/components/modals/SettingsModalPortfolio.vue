<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div
          class="modal-header d-flex align-items-center justify-content-between"
        >
          <h5 class="modal-title">{{ trans.general_settings }}</h5>

          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="24"
              class="icon-close-circle"
            >
              <circle cx="12" cy="12" r="10" class="fill-light-gray" />
              <path
                class="fill-bg"
                d="M13.41 12l2.83 2.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 1 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12z"
              />
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="languageSelected === 'it'" class="form-group row">
            <div class="col-12">
              <label
                for="slug"
                class="font-weight-bold text-uppercase text-muted small"
                >{{ trans.slug }}</label
              >
              <a
                v-tooltip="{ placement: 'right' }"
                v-if="portfolio.title.it"
                href="#"
                class="text-decoration-none"
                :title="trans.sync_with_post_title"
                @click.prevent="syncSlug()"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  class="icon-refresh"
                  width="25"
                >
                  <circle cx="12" cy="12" r="10" style="fill: none" />
                  <path
                    class="fill-light-gray"
                    d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"
                  />
                </svg>
              </a>
              <input
                v-model="portfolio.slug"
                id="slug"
                type="text"
                class="form-control border-0"
                name="slug"
                :title="trans.slug"
                :placeholder="trans.a_unique_slug"
                @input="update"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label
                for="settings"
                class="font-weight-bold text-uppercase text-muted small"
                >{{ trans.summary }}</label
              >
              <textarea
                v-model="portfolio.summary[languageSelected]"
                id="settings"
                rows="4"
                name="summary"
                style="resize: none"
                class="form-control resize-none border-0"
                :placeholder="trans.a_descriptive_summary"
                @input="update"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label class="font-weight-bold text-uppercase text-muted small">{{
                trans.portfolio_categories
              }}</label>
              <multiselect
                v-model="portfolio.portfolio_category"
                :options="portfolio_categories"
                :custom-label="customLabel"
                :placeholder="trans.select_some_portfolio_categories"
                :tag-placeholder="trans.add_a_new_portfolio_category"
                :multiple="true"
                :taggable="true"
                track-by="slug"
                style="cursor: pointer"
                @input="update"
                @tag="addPortfolioCategory"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label
                for="project_name"
                class="font-weight-bold text-uppercase text-muted small"
              >
                {{ trans.project_name }}
              </label>
              <input
                v-model="portfolio.info.project_name[languageSelected]"
                id="project_name"
                type="text"
                class="form-control border-0"
                name="project_name"
                :title="trans.project_name"
                :placeholder="trans.project_name_placeholder"
                @input="update"
              />
            </div>
          </div>
          <div class="form-group row">
            <div class="col-12">
              <label
                for="location"
                class="font-weight-bold text-uppercase text-muted small"
              >
                {{ trans.location }}
              </label>
              <input
                v-model="portfolio.info.location[languageSelected]"
                id="location"
                type="text"
                class="form-control border-0"
                name="project_name"
                :title="trans.location"
                :placeholder="trans.location_placeholder"
                @input="update"
              />
            </div>
          </div>
          <div v-if="languageSelected === 'it'" class="form-group row">
            <div class="col-12">
              <label
                for="year"
                class="font-weight-bold text-uppercase text-muted small"
              >
                {{ trans.year }}
              </label>
              <input
                v-model="portfolio.info.year"
                id="year"
                type="number"
                class="form-control border-0"
                name="project_name"
                :title="trans.year"
                :placeholder="trans.year_placeholder"
                @input="update"
              />
            </div>
          </div>
          <div v-if="languageSelected === 'it'" class="form-group row">
            <div class="col-12">
              <label
                for="company"
                class="font-weight-bold text-uppercase text-muted small"
              >
                {{ trans.company }}
              </label>
              <input
                v-model="portfolio.info.company"
                id="company"
                type="text"
                class="form-control border-0"
                name="project_name"
                :title="trans.company"
                :placeholder="trans.company_placeholder"
                @input="update"
              />
            </div>
          </div>
          <div v-if="languageSelected === 'it'" class="form-group row">
            <div class="col-12">
              <label
                for="company"
                class="font-weight-bold text-uppercase text-muted small"
              >
                {{ trans.company_url }}
              </label>
              <input
                v-model="portfolio.info.company_url"
                id="company-url"
                type="text"
                class="form-control border-0"
                :title="trans.company_url"
                :placeholder="trans.company_url_placeholder"
                @input="update"
              />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
            data-dismiss="modal"
          >
            {{ trans.done }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import Multiselect from "vue-multiselect";
import Tooltip from "../../directives/Tooltip";
import debounce from "lodash/debounce";
import strings from "../../mixins/strings";

export default {
  name: "settings-modal-portfolio",

  components: {
    Multiselect,
  },

  directives: {
    Tooltip,
  },

  mixins: [strings],

  props: {
    portfolio: {
      type: Object,
      required: true,
    },

    portfolio_categories: {
      type: Array,
      default: () => [],
    },

    languageSelected: {
      type: String,
      required: true,
    },
  },

  computed: {
    ...mapState(["settings"]),
    ...mapGetters({
      trans: "settings/trans",
    }),
  },

  methods: {
    customLabel({ name }) {
      if (name) {
        return name["it"];
      }
    },

    syncSlug() {
      this.$emit("sync-slug", strings.methods.slugify(this.portfolio.title.it));
      this.update();
    },

    addPortfolioCategory(string) {
      let portfolio_category = {
        name: {
          it: string,
          en: null,
          es: null,
          fr: null,
          de: null,
        },
        slug: strings.methods.slugify(string),
        user_id: this.settings.user.id,
      };

      this.$emit("add-portfolio-category", portfolio_category);
      this.$emit("add-category", portfolio_category);
      this.update();
    },

    update: debounce(function () {
      this.$emit("update-portfolio");
    }, 3000),
  },
};
</script>
