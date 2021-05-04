<template>
  <section>
    <page-header />
    <main class="py-4">
      <!-- Blog Posts stats -->
      <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12">
        <div
          class="d-flex justify-content-between mt-2 mb-4 align-items-center"
          v-if="settings.blogEnabled"
        >
          <div>
            <h3 class="mt-2">{{ trans.stats }}</h3>
            <p class="mt-2 text-secondary">
              {{ trans.click_to_see_insights }}
            </p>
          </div>

          <select
            v-model="scope"
            id="scope"
            v-if="isReady && isAdmin && hasPublishedPosts"
            name="scope"
            class="ml-auto w-auto custom-select border-0 bg-light"
            @change="changeScope"
          >
            <option value="user">{{ trans.your_stats }}</option>
            <option value="all">{{ trans.all_stats }}</option>
          </select>
        </div>

        <div v-if="isReady && hasPublishedPosts && settings.blogEnabled">
          <div class="card-deck mt-4 pt-2">
            <div class="card shadow-lg">
              <div
                class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
              >
                <p class="font-weight-bold text-muted small text-uppercase">
                  {{ trans.views }}
                </p>
                <p>
                  <span
                    class="badge badge-pill badge-success p-2 font-weight-bold"
                  >
                    {{ trans.last_thirty_days }}
                  </span>
                </p>
              </div>
              <div class="card-body pt-0 pb-2">
                <p class="card-text display-4">
                  {{ suffixedNumber(data.totalViews) }}
                </p>
              </div>
            </div>
            <div class="card shadow-lg">
              <div
                class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
              >
                <p class="font-weight-bold text-muted small text-uppercase">
                  {{ trans.visitors }}
                </p>
                <p>
                  <span
                    class="badge badge-pill badge-primary p-2 font-weight-bold"
                    >{{ trans.last_thirty_days }}</span
                  >
                </p>
              </div>
              <div class="card-body pt-0 pb-2">
                <p class="card-text display-4">
                  {{ suffixedNumber(data.totalVisits) }}
                </p>
              </div>
            </div>
          </div>

          <line-chart
            :views="plotViewPoints"
            :visits="plotVisitPoints"
            class="mt-5"
            id-name="blog-stats"
          />

          <div class="mt-5 card shadow-lg">
            <div class="card-body p-0">
              <div :key="`${index}-${post.id}`" v-for="(post, index) in posts">
                <router-link
                  :to="{
                    name: 'post-stats',
                    params: { id: post.id },
                  }"
                  class="text-decoration-none"
                >
                  <div
                    v-hover="{ class: `hover-bg` }"
                    class="d-flex p-3 align-items-center"
                    :class="{
                      'border-top': index !== 0,
                      'rounded-top': index === 0,
                      'rounded-bottom': index === posts.length - 1,
                    }"
                  >
                    <div class="pl-2 col-md-6 col-sm-8 col-10">
                      <p class="text-truncate lead font-weight-bold mt-2 mb-0">
                        {{ post.title.it }}
                      </p>
                      <p class="text-secondary mb-2">
                        <span class="d-none d-md-inline">
                          {{ post.read_time }} ―
                        </span>
                        {{ trans.published }}
                        {{ moment(post.published_at).format("MMM D, YYYY") }}
                      </p>
                    </div>
                    <div class="ml-auto">
                      <div class="d-none d-md-inline">
                        <span class="text-muted mr-3"
                          >{{ suffixedNumber(post.views_count) }}
                          {{ trans.views }}</span
                        >
                        <span class="mr-3"
                          >{{ trans.created }}
                          {{
                            moment(post.created_at).format("MMM D, YYYY")
                          }}</span
                        >
                      </div>

                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="25"
                        viewBox="0 0 24 24"
                        class="icon-cheveron-right-circle"
                      >
                        <circle cx="12" cy="12" r="10" style="fill: none" />
                        <path
                          class="fill-light-gray"
                          d="M10.3 8.7a1 1 0 0 1 1.4-1.4l4 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-1.4-1.4l3.29-3.3-3.3-3.3z"
                        />
                      </svg>
                    </div>
                  </div>
                </router-link>
              </div>

              <infinite-loading spinner="spiral" @infinite="fetchPosts">
                <span slot="no-more" />
                <div slot="no-results" />
              </infinite-loading>
            </div>
          </div>
        </div>

        <div
          v-if="isReady && !hasPublishedPosts && settings.blogEnabled"
          class="card shadow mt-5"
        >
          <div class="card-body p-0">
            <div class="my-5">
              <p class="lead text-center text-muted mt-5">
                {{ trans.you_have_no_published_posts }}
              </p>
              <p class="lead text-center text-muted mt-1">
                {{ trans.stats_are_made_available }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- Portfolio stats -->
      <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12 pt-5">
        <div
            class="d-flex justify-content-between mt-2 mb-4 align-items-center"
            v-if="settings.portfolioEnabled"
        >
          <div>
            <h3 class="mt-2">{{ trans.stats_portfolios }}</h3>
            <p class="mt-2 text-secondary">
              {{ trans.click_to_see_insights }}
            </p>
          </div>

          <select
              v-model="scope"
              id="scope-portfolio"
              v-if="isReady && isAdmin && hasPublishedPortfolios"
              name="scope"
              class="ml-auto w-auto custom-select border-0 bg-light"
              @change="changePortfolioScope"
          >
            <option value="user">{{ trans.your_stats }}</option>
            <option value="all">{{ trans.all_stats }}</option>
          </select>
        </div>

        <div v-if="isReady && hasPublishedPortfolios && settings.portfolioEnabled">
          <div class="card-deck mt-4 pt-2">
            <div class="card shadow-lg">
              <div
                  class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
              >
                <p class="font-weight-bold text-muted small text-uppercase">
                  {{ trans.views }}
                </p>
                <p>
                  <span
                      class="badge badge-pill badge-success p-2 font-weight-bold"
                  >
                    {{ trans.last_thirty_days }}
                  </span>
                </p>
              </div>
              <div class="card-body pt-0 pb-2">
                <p class="card-text display-4">
                  {{ suffixedNumber(portfolioData.totalViews) }}
                </p>
              </div>
            </div>
            <div class="card shadow-lg">
              <div
                  class="card-header pb-0 bg-transparent d-flex justify-content-between align-middle border-0"
              >
                <p class="font-weight-bold text-muted small text-uppercase">
                  {{ trans.visitors }}
                </p>
                <p>
                  <span
                      class="badge badge-pill badge-primary p-2 font-weight-bold"
                  >{{ trans.last_thirty_days }}</span
                  >
                </p>
              </div>
              <div class="card-body pt-0 pb-2">
                <p class="card-text display-4">
                  {{ suffixedNumber(portfolioData.totalVisits) }}
                </p>
              </div>
            </div>
          </div>

          <line-chart
              :views="plotViewPointsPortfolio"
              :visits="plotVisitPointsPortfolio"
              id-name="portfolio-stats"
              class="mt-5"
          />

          <div class="mt-5 card shadow-lg">
            <div class="card-body p-0">
              <div :key="`${index}-${portfolio.id}`" v-for="(portfolio, index) in portfolios">
                <router-link
                    :to="{
                    name: 'portfolios-stats',
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
                      'rounded-bottom': index === posts.length - 1
                    }"
                  >
                    <div class="pl-2 col-md-6 col-sm-8 col-10">
                      <p class="text-truncate lead font-weight-bold mt-2 mb-0">
                        {{ portfolio.title.it }}
                      </p>
                      <p class="text-secondary mb-2">
                        <span class="d-none d-md-inline">
                          {{ portfolio.read_time }} ―
                        </span>
                        {{ trans.published }}
                        {{ moment(portfolio.published_at).format("MMM D, YYYY") }}
                      </p>
                    </div>
                    <div class="ml-auto">
                      <div class="d-none d-md-inline">
                        <span class="text-muted mr-3"
                        >{{ suffixedNumber(portfolio.views_count) }}
                          {{ trans.views }}</span
                        >
                        <span class="mr-3"
                        >{{ trans.created }}
                          {{
                            moment(portfolio.created_at).format("MMM D, YYYY")
                          }}</span
                        >
                      </div>

                      <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="25"
                          viewBox="0 0 24 24"
                          class="icon-cheveron-right-circle"
                      >
                        <circle cx="12" cy="12" r="10" style="fill: none" />
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
                <span slot="no-more" />
                <div slot="no-results" />
              </infinite-loading>
            </div>
          </div>
        </div>

        <div
            v-if="isReady && !hasPublishedPortfolios && settings.portfolioEnabled"
            class="card shadow mt-5"
        >
          <div class="card-body p-0">
            <div class="my-5">
              <p class="lead text-center text-muted mt-5">
                {{ trans.you_have_no_published_portfolios }}
              </p>
              <p class="lead text-center text-muted mt-1">
                {{ trans.stats_are_made_available_portfolios }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </section>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import Hover from "../directives/Hover";
import InfiniteLoading from "vue-infinite-loading";
import LineChart from "../components/LineChart";
import NProgress from "nprogress";
import PageHeader from "../components/PageHeader";
import isEmpty from "lodash/isEmpty";
import strings from "../mixins/strings";

export default {
  name: "all-stats",

  components: {
    LineChart,
    InfiniteLoading,
    PageHeader,
  },

  directives: {
    Hover,
  },

  mixins: [strings],

  data() {
    return {
      // vars for retrieve blog data
      page: 1,
      posts: [],
      data: null,
      // vars for retrieve portfolio data
      portfolioPage: 1,
      portfolios: [],
      portfolioData: null,

      scope: "user",
      infiniteId: +new Date(),
      isReady: false,
    };
  },

  computed: {
    ...mapState({
      settings: "settings",
    }),
    ...mapGetters({
      isAdmin: "settings/isAdmin",
      trans: "settings/trans",
    }),

    hasPublishedPosts() {
      return this.posts.length > 0;
    },

    hasPublishedPortfolios() {
      return this.portfolios.length > 0;
    },

    plotViewPoints() {
      return JSON.parse(this.data.traffic.views);
    },

    plotVisitPoints() {
      return JSON.parse(this.data.traffic.visits);
    },

    plotViewPointsPortfolio() {
      return JSON.parse(this.portfolioData.traffic.views);
    },

    plotVisitPointsPortfolio() {
      return JSON.parse(this.portfolioData.traffic.visits);
    },
  },

  async created() {
    if (this.settings.blogEnabled) await Promise.all([this.fetchStats(), this.fetchPosts()]);
    if (this.settings.portfolioEnabled) await Promise.all([this.fetchPortfolioStats(), this.fetchPortfolios()]);

    this.isReady = true;
    NProgress.done();
  },

  methods: {
    fetchStats() {
      return this.request()
        .get("/api/blog/stats", {
          params: {
            scope: this.scope,
          },
        })
        .then(({ data }) => {
          this.data = data;
          NProgress.inc();
        })
        .catch(() => {
          NProgress.done();
        });
    },
    fetchPortfolioStats() {
      return this.request()
          .get("/api/portfolio/stats", {
            params: {
              scope: this.scope,
            },
          })
          .then(({ data }) => {
            this.portfolioData = data;
            NProgress.inc();
          })
          .catch(() => {
            NProgress.done();
          });
    },

    fetchPosts($state) {
      return this.request()
        .get("/api/posts", {
          params: {
            scope: this.scope,
            page: this.page,
          },
        })
        .then(({ data }) => {
          if (!isEmpty(data) && !isEmpty(data.posts.data)) {
            this.page += 1;
            this.posts.push(...data.posts.data);

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

    fetchPortfolios($state) {
      return this.request()
          .get("/api/portfolios", {
            params: {
              scope: this.scope,
              page: this.portfolioPage,
            },
          })
          .then(({ data }) => {
            if (!isEmpty(data) && !isEmpty(data.portfolios.data)) {
              this.portfolioPage += 1;
              this.portfolios.push(...data.portfolios.data);

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

    async changeScope() {
      this.isReady = false;

      this.data = null;
      this.page = 1;
      this.posts = [];

      await Promise.all([this.fetchStats(), this.fetchPosts()]);

      this.infiniteId += 1;
      this.isReady = true;
      NProgress.done();
    },

    async changePortfolioScope() {
      this.isReady = false;

      this.portfolioData = null
      this.portfolioPage = 1
      this.portfolios = []

      await Promise.all([this.fetchPortfolioStats(), this.fetchPortfolios()]);

      this.infiniteId += 1;
      this.isReady = true;
      NProgress.done();
    },
  },
};
</script>

<style scoped lang="scss">
@import "../../sass/utilities/variables";

.badge-success {
  background-color: $green-500;
  color: darken($green, 20%);
}

.badge-primary {
  background-color: $blue-500;
  color: darken($blue, 35%);
}
</style>
