<template>
  <section>
    <page-header>
      <template slot="languages">
        <div v-if="uri !== 'create' && settings.languages.length > 1">
          <div class="dropdown ml-3 text-uppercase">
            <a
                id="languageSelector"
                class="nav-link border-left"
                v-bind:class="{'border-right': !(settings.google_translate && languageSelected !== 'it')}"
                href="#"
                role="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >{{ languageSelected }}</a>
            <div class="dropdown-menu dropdown-menu-right" style="min-width: unset">
              <div v-for="(lang) in settings.languages">
                <div v-if="lang !== languageSelected">
                  <a href="#" class="dropdown-item" @click="changeLanguage(lang)"> {{ lang }} </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="settings.google_translate && languageSelected !== 'it' && uri !== 'create' && settings.languages.length > 1">
          <a href="#" class="btn btn-info btn-block font-weight-bold mt-0 mr-1" @click="showTranslationModal"> {{ trans.translate }} </a>
        </div>
      </template>

      <template slot="options">
        <div v-if="!creatingCategory" class="dropdown">
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
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <a href="#" class="dropdown-item text-danger" @click="showDeleteModal"> {{ trans.delete }} </a>
          </div>
        </div>
      </template>
    </page-header>

    <main class="py-4">
      <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
        <div v-if="isReady" class="my-3">
          <h3 class="mt-3">
            <router-link :to="{ name: 'portfolio_categories' }" class="text-decoration-none text-muted">{{
                trans.portfolio_categories
              }}
            </router-link>
            <span class="text-muted"> / </span>
            {{ title }}
          </h3>
          <p v-if="!creatingCategory" class="mt-2 text-secondary">
            {{ trans.last_updated }} {{ moment(category.updated_at).fromNow() }}
          </p>
        </div>

        <div v-if="isReady" class="mt-5 card shadow-lg">
          <div class="card-body">
            <div class="col-12">
              <div class="form-group row">
                <label class="font-weight-bold text-uppercase text-muted small">
                  {{ trans.name }}
                </label>
                <input
                    v-model="category.name[languageSelected]"
                    type="text"
                    name="name"
                    autofocus
                    autocomplete="off"
                    title="Name"
                    class="form-control border-0"
                    :placeholder="trans.give_your_portfolio_category_a_name"
                    @keyup.enter="saveCategory"
                />
              </div>

              <div v-if="languageSelected === 'it'" class="form-group row">
                <label class="font-weight-bold text-uppercase text-muted small">
                  {{ trans.slug }}
                </label>
                <input
                    v-model="category.slug"
                    type="text"
                    name="slug"
                    disabled
                    autocomplete="off"
                    title="Slug"
                    class="form-control border-0"
                    :class="invalidSlug.shouldShow ? 'is-invalid' : ''"
                    :placeholder="trans.give_your_portfolio_category_a_name_slug"
                />
                <span v-if="invalidSlug.shouldShow" class="invalid-feedback" role="alert">
                  <strong>{{ invalidSlug.error }}</strong>
                </span>
              </div>

              <div class="form-group row mt-4 mb-2">
                <div class="col-md px-0">
                  <a
                      href="#"
                      onclick="this.blur()"
                      class="btn btn-success btn-block font-weight-bold mt-0"
                      :class="shouldDisableButton ? 'disabled' : ''"
                      aria-label="Save"
                      @click.prevent="saveCategory"
                  >
                    {{ trans.save }}
                  </a>
                </div>
                <div class="col-md px-0">
                  <router-link
                      :to="{ name: 'portfolio_categories' }"
                      class="btn btn-link btn-block font-weight-bold text-muted text-decoration-none"
                  >
                    {{ trans.cancel }}
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h2 v-if="portfolios.length > 0" class="mt-5">{{ trans.portfolios }}</h2>

        <div v-if="portfolios.length > 0" class="mt-3 card shadow-lg">
          <div class="card-body p-0">
            <div :key="`${index}-${portfolio.id}`" v-for="(portfolio, index) in portfolios">
              <router-link
                  :to="{
                    name: 'edit-portfolio',
                    params: { id: portfolio.id },
                  }"
                  class="text-decoration-none"
              >
                <div
                    v-hover="{ class: `hover-bg` }"
                    class="d-flex p-3 align-items-center"
                    :class="{
                      'border-top': index !== 0,
                      'rounded-top': index === 0,
                      'rounded-bottom': index === portfolios.length - 1,
                    }"
                >
                  <div class="pl-2 col-md-6 col-sm-8 col-10">
                    <p class="mb-0 mt-2 lead font-weight-bold text-truncate">
                      {{ portfolio.title.it }}
                    </p>
                    <p class="text-secondary mb-2">
                      <span v-if="isPublished(portfolio.published_at)">
                        <span class="d-none d-md-inline"> {{ portfolio.read_time }} ― </span>
                        {{ trans.published }}
                        {{ moment(portfolio.published_at).format('MMM D, YYYY') }}
                      </span>
                      <span v-if="isDraft(portfolio.published_at)">
                        <span class="text-danger">{{ trans.draft }}</span>
                        <span class="d-none d-md-inline">
                          ― {{ trans.updated }}
                          {{ moment(portfolio.updated_at).fromNow() }}
                        </span>
                      </span>
                    </p>
                  </div>
                  <div class="ml-auto">
                    <div class="d-none d-md-inline">
                      <span class="text-secondary mr-3">
                        {{ suffixedNumber(portfolio.views_count) }}
                        {{ portfolio.views_count == 1 ? trans.view : trans.views }}
                      </span>
                      <span class="mr-3">
                        {{ trans.created }}
                        {{ moment(portfolio.created_at).format('MMM D, YYYY') }}
                      </span>
                    </div>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="25"
                        viewBox="0 0 24 24"
                        class="icon-cheveron-right-circle"
                    >
                      <circle cx="12" cy="12" r="10" style="fill: none"/>
                      <path
                          class="fill-light-gray"
                          d="M10.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                      />
                    </svg>
                  </div>
                </div>
              </router-link>
            </div>

            <infinite-loading spinner="spiral" @infinite="fetchPortfolios">
              <span slot="no-more"/>
              <div slot="no-results"/>
            </infinite-loading>
          </div>
        </div>
      </div>
    </main>

    <section v-if="isReady">
      <translation-modal
          ref="translationModal"
          :elem="category"
          :language-selected="languageSelected"
          @update="saveCategory"
      />
    </section>

    <delete-modal
        ref="deleteModal"
        :header="trans.delete"
        :message="trans.deleted_portfolio_category_are_gone_forever"
        @delete="deleteCategory"
    />
  </section>
</template>

<script>
import {mapGetters, mapState} from 'vuex';
import $ from 'jquery';
import DeleteModal from '../components/modals/DeleteModal';
import TranslationModal from "../components/modals/TranslationModal";
import Hover from '../directives/Hover';
import InfiniteLoading from 'vue-infinite-loading';
import NProgress from 'nprogress';
import PageHeader from '../components/PageHeader';
import isEmpty from 'lodash/isEmpty';
import status from '../mixins/status';
import strings from '../mixins/strings';
import get from "lodash/get";

export default {
  name: 'edit-portfolio_category',

  components: {
    TranslationModal,
    DeleteModal,
    InfiniteLoading,
    PageHeader,
  },

  directives: {
    Hover,
  },

  mixins: [status, strings],

  data() {
    return {
      uri: this.$route.params.id || 'create',
      category: {
        id: null,
        name: {
          it: null,
          en: null,
          es: null,
          fr: null,
          de: null,
        }
      },
      page: 1,
      errors: [],
      portfolios: [],
      isReady: false,
      languageSelected: 'it',
    };
  },

  computed: {
    ...mapState(['settings']),
    ...mapGetters({
      trans: 'settings/trans',
    }),

    creatingCategory() {
      return this.$route.name === 'create-portfolio_category';
    },

    shouldDisableButton() {
      return isEmpty(this.category.name.it);
    },

    title() {
      if (this.creatingCategory) {
        return this.category.name.it || this.trans.new_portfolio_category;
      } else {
        return this.category.name.it || this.trans.edit_portfolio_category;
      }
    },

    invalidSlug() {
      if (!isEmpty(this.errors.slug) && this.errors.slug.length > 0) {
        return {
          error: this.errors.slug[0],
          shouldShow: true,
        };
      }

      return {
        error: null,
        shouldShow: false,
      };
    },
  },

  watch: {
    'category.name.it'(val) {
      this.category.slug = !isEmpty(val) ? this.slugify(val) : '';
    },

    async $route(to) {
      if (this.uri === 'create' && to.params.id === this.category.id) {
        this.uri = to.params.id;
      }

      if (this.uri !== to.params.id) {
        this.isReady = false;
        this.uri = to.params.id;
        this.page = 1;
        this.portfolios = [];
        await Promise.all([this.fetchCategory(), this.fetchPortfolios()]);
        this.isReady = true;
        NProgress.done();
      }
    },
  },

  async created() {
    await Promise.all([this.fetchCategory(), this.fetchPortfolios()]);
    this.isReady = true;
    NProgress.done();
  },

  methods: {
    fetchCategory() {
      return this.request()
          .get(`/api/portfolio_categories/${this.uri}`)
          .then(({data}) => {
            this.category.id = data.id;
            this.category.name.it = get(data.name, 'it', '');
            this.category.name.en = get(data.name, 'en', '');
            this.category.name.es = get(data.name, 'es', '');
            this.category.name.fr = get(data.name, 'fr', '');
            this.category.name.de = get(data.name, 'de', '');

            NProgress.inc();
          })
          .catch(() => {
            this.$router.push({name: 'portfolio_categories'});
          });
    },

    fetchPortfolios($state) {
      return this.request()
          .get(`/api/portfolio_categories/${this.uri}/portfolios`, {
            params: {
              page: this.page,
            },
          })
          .then(({data}) => {
            if (!isEmpty(data) && !isEmpty(data.data)) {
              this.page += 1;
              this.portfolios.push(...data.data);
              $state.loaded();
            } else {
              $state.complete();
            }

            if (isEmpty($state)) {
              NProgress.inc();
            }
          })
          .catch(() => {
            NProgress.done();
          });
    },

    async saveCategory() {
      this.errors = [];

      await this.request()
          .post(`/api/portfolio_categories/${this.category.id}`, this.category)
          .then(({data}) => {
            this.category = data;
            this.$store.dispatch('search/buildIndex', true);
            this.$toasted.show(this.trans.saved, {
              className: 'bg-success',
            });
          })
          .catch((error) => {
            this.errors = error.response.data.errors;
          });

      if (isEmpty(this.errors) && this.creatingCategory) {
        await this.$router.push({name: 'edit-portfolio_category', params: {id: this.category.id}});
        NProgress.done();
      }
    },

    async deleteCategory() {
      await this.request()
          .delete(`/api/portfolio_categories/${this.category.id}`)
          .then(() => {
            this.$store.dispatch('search/buildIndex', true);
            this.$toasted.show(this.trans.success, {
              className: 'bg-success',
            });
          });

      $(this.$refs.deleteModal.$el).modal('hide');

      await this.$router.push({name: 'portfolio_categories'});
    },

    changeLanguage(lang) {
      this.languageSelected = lang;
    },

    showTranslationModal() {
      $(this.$refs.translationModal.$el).modal('show');
    },

    showDeleteModal() {
      $(this.$refs.deleteModal.$el).modal('show');
    },
  },
};
</script>
