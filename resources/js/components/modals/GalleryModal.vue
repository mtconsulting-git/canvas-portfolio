<template>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div ref="modal" class="modal-dialog" role="document">
      <div class="modal-content">
        <div
          class="modal-header d-flex align-items-center justify-content-between"
        >
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            @click.prevent="closeModal"
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
        <div class="modal-body pb-0">
          <file-pond
            ref="pond_gallery"
            name="GalleryImages"
            :max-file-size="settings.maxUpload"
            :icon-remove="getRemoveIcon"
            :icon-retry="getRetryIcon"
            :label-idle="getPlaceholderLabel"
            accepted-file-types="image/*"
            :server="getServerOptions"
            :allowRevert="noImagesInGallery"
            :allowProcess="false"
            :allow-multiple="true"
            :files="selectedImagesForPond"
            @processfile="updateFilePond"
            @removefile="updateFilePond"
          />

          <div v-if="!noImagesInGallery">
            <div v-for="image in post.gallery_images">
              <div class="selected-image-gallery">
                <button
                  type="button"
                  class="close"
                  @click.prevent="deleteImageFromGallery(image)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="24"
                    class="icon-trash"
                  >
                    <path
                      class="fill-light-gray"
                      d="M5 5h14l-.89 15.12a2 2 0 0 1-2 1.88H7.9a2 2 0 0 1-2-1.88L5 5zm5 5a1 1 0 0 0-1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0-1-1zm4 0a1 1 0 0 0-1 1v6a1 1 0 0 0 2 0v-6a1 1 0 0 0-1-1z"
                    />
                    <path
                      class="fill-light-gray"
                      d="M8.59 4l1.7-1.7A1 1 0 0 1 11 2h2a1 1 0 0 1 .7.3L15.42 4H19a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2h3.59z"
                    />
                  </svg>
                </button>
                <img :src="image" class="w-100 rounded mb-3" alt="" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            class="btn btn-link btn-block text-muted font-weight-bold text-decoration-none"
            data-dismiss="modal"
            @click="update"
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
import InfiniteLoading from "vue-infinite-loading";
import debounce from "lodash/debounce";
import vueFilePond from "vue-filepond";
import $ from "jquery";

import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import "filepond/dist/filepond.min.css";

import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImageExifOrientation from "filepond-plugin-image-exif-orientation";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginImageValidateSize from "filepond-plugin-image-validate-size";

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImagePreview,
  FilePondPluginImageValidateSize,
  FilePondPluginFileValidateSize,
  FilePondPluginImageExifOrientation
);

export default {
  name: "gallery-modal",

  components: {
    InfiniteLoading,
    FilePond,
  },

  props: {
    post: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      noImagesInGallery: true,
      selectedImagesForPond: [],
      galleryModalClasses: ["modal-xl", "modal-dialog-scrollable"],
    };
  },

  computed: {
    ...mapState(["settings"]),
    ...mapGetters({
      trans: "settings/trans",
    }),

    getServerOptions() {
      return {
        url: `${this.settings.path}/api/uploads`,
        headers: {
          "X-CSRF-TOKEN": document.head.querySelector('meta[name="csrf-token"]')
            .content,
        },
      };
    },

    getRetryIcon() {
      return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon-refresh" width="26"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M8.52 7.11a5.98 5.98 0 0 1 8.98 2.5 1 1 0 1 1-1.83.8 4 4 0 0 0-5.7-1.86l.74.74A1 1 0 0 1 10 11H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1.7-.7l.82.81zm5.51 8.34l-.74-.74A1 1 0 0 1 14 13h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1.7.7l-.82-.81A5.98 5.98 0 0 1 6.5 14.4a1 1 0 1 1 1.83-.8 4 4 0 0 0 5.7 1.85z"/></svg>';
    },

    getRemoveIcon() {
      return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" class="icon-close-circle"><circle style="fill:none" cx="12" cy="12" r="10"/><path style="fill:white" d="M13.41 12l2.83 2.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 1 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12z"/></svg>';
    },

    getPlaceholderLabel() {
      return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="35" class="icon-cloud-upload mr-3"><path class="fill-dark-gray" d="M18 14.97c0-.76-.3-1.51-.88-2.1l-3-3a3 3 0 0 0-4.24 0l-3 3A3 3 0 0 0 6 15a4 4 0 0 1-.99-7.88 5.5 5.5 0 0 1 10.86-.82A4.49 4.49 0 0 1 22 10.5a4.5 4.5 0 0 1-4 4.47z"/><path class="fill-dark-gray" d="M11 14.41V21a1 1 0 0 0 2 0v-6.59l1.3 1.3a1 1 0 0 0 1.4-1.42l-3-3a1 1 0 0 0-1.4 0l-3 3a1 1 0 0 0 1.4 1.42l1.3-1.3z"/></svg> Drop files or click here to upload';
    },
  },

  mounted() {
    if (this.post.gallery_images.length > 0) {
      this.noImagesInGallery = false;
    }
  },

  methods: {
    updateFilePond() {
      let old = this.post.gallery_images;
      this.post.gallery_images = [];
      let arr = document.getElementsByName("GalleryImages");
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].value) {
          this.post.gallery_images.push(arr[i].value);
        }
      }
      if (!this.noImagesInGallery) {
        for (let i = 0; i < old.length; i++) {
          if (this.post.gallery_images.indexOf(old[i]) === -1) {
            this.post.gallery_images.push(old[i]);
          }
        }
        let totalFiles = $(".filepond--item").length;
        let completedFiles = $(
          '.filepond--item[data-filepond-item-state="processing-complete"]'
        ).length;
        if (completedFiles === totalFiles) {
          this.selectedImagesForPond = [];
        }
      }
    },

    deleteImageFromGallery(image) {
      let index = this.post.gallery_images.indexOf(image);
      if (index > -1) {
        this.post.gallery_images.splice(index, 1);
      }
      if (this.post.gallery_images.length === 0) {
        this.noImagesInGallery = true;
      }
    },

    closeModal() {
      this.update();
      this.$refs.modal.hide;
    },

    update: debounce(function () {
      this.$emit("update-post");
    }, 3000),
  },
};
</script>
