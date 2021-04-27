<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div
          class="modal-header d-flex align-items-center justify-content-between"
        >
          <h5 class="modal-title">{{ trans.translation_modal_title }}</h5>

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
          <div class="col-12 p-1 mb-0">
            <p class="text-justify">
              {{ trans.translation_modal_text }}
              <br />
              <br />
              {{ trans.translation_modal_attention }}
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <div class="col-6 m-0">
            <button
              type="button"
              class="btn btn-danger btn-block font-weight-bold text-decoration-none"
              data-dismiss="modal"
            >
              {{ trans.cancel }}
            </button>
          </div>
          <div class="col-6 m-0">
            <button
              type="button"
              class="btn btn-success btn-block font-weight-bold text-decoration-none"
              data-dismiss="modal"
              @click.prevent="translate"
            >
              {{ trans.translate }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Tooltip from "../../directives/Tooltip";
import debounce from "lodash/debounce";
import get from "lodash/get";

export default {
  name: "translation-modal",

  directives: {
    Tooltip,
  },

  props: {
    elem: {
      type: Object,
      required: true,
    },

    languageSelected: {
      type: String,
      required: true,
    },
  },

  computed: {
    ...mapGetters({
      trans: "settings/trans",
    }),
  },

  methods: {
    async translate() {
      let content = {
        language: this.languageSelected,
      };

      if (this.elem.title) {
        //post, portfolio
        content["title"] = this.elem.title.it;
        if (this.elem.summary) {
          content["summary"] = this.elem.summary.it;
        }
        if (this.elem.body) {
          content["body"] = this.elem.body.it;
        }

        if (this.elem.info) {
          //portfolio
          content["info"] = {};
          if (this.elem.info.project_name) {
            content["info"]["project_name"] = this.elem.info.project_name.it;
          }
          if (this.elem.info.location) {
            content["info"]["location"] = this.elem.info.location.it;
          }
        }

        content["meta"] = {};
        if (this.elem.meta.description) {
          content["meta"]["description"] = this.elem.meta.description.it;
        }
        if (this.elem.meta.title) {
          content["meta"]["title"] = this.elem.meta.title.it;
        }
        if (this.elem.meta.canonical_link) {
          content["meta"]["canonical_link"] = this.elem.meta.canonical_link.it;
        }
      } else if (this.elem.name) {
        //category, tag, topic
        content["name"] = this.elem.name.it;
      }

      await this.request()
        .post(`/api/translation`, content)
        .then(({ data }) => {
          if (data.title) {
            //post and portfolio
            this.elem.title[this.languageSelected] = data.title;
            this.elem.summary[this.languageSelected] = data.summary;
            this.elem.body[this.languageSelected] = data.body;

            if (this.elem.info) {
              //portfolio
              this.elem.info.project_name[this.languageSelected] = get(
                data.info,
                "project_name"
              );
              this.elem.info.location[this.languageSelected] = get(
                data.info,
                "location"
              );
            }

            this.elem.meta.description[this.languageSelected] = get(
              data.meta,
              "description"
            );
            this.elem.meta.title[this.languageSelected] = get(
              data.meta,
              "title"
            );
            this.elem.meta.canonical_link[this.languageSelected] = get(
              data.meta,
              "canonical_link"
            );
          } else if (data.name) {
            //category, tag, topic
            this.elem.name[this.languageSelected] = data.name;
          }
        });

      this.update();
    },

    update: debounce(function () {
      this.$emit("update");
    }),
  },
};
</script>
