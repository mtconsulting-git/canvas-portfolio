<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div ref="modal" class="modal-dialog" role="document">
      <div class="modal-content">
        <div
          class="modal-header d-flex align-items-center justify-content-between"
        >
          <h5 class="modal-title">{{ trans.draft_convert }}</h5>

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
        <div class="modal-footer">
          <div class="row w-100">
            <div class="col-lg order-lg-last px-0">
              <a
                href="#"
                class="btn btn-success btn-block font-weight-bold mt-0"
                data-dismiss="modal"
                @click="draft"
              >
                {{ trans.draft_convert }}
              </a>
            </div>

            <div class="col-lg order-lg-first px-0">
              <button
                type="button"
                class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
                data-dismiss="modal"
              >
                {{ trans.cancel }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import status from "../../mixins/status";

export default {
  name: "draft-modal",

  mixins: [status],

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      components: {},
      result: "",
    };
  },

  computed: {
    ...mapState(["settings"]),
    ...mapGetters({
      trans: "settings/trans",
    }),
  },

  methods: {
    draft() {
      this.$emit("draft", null);
    },
  },
};
</script>
