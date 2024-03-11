"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/salutarium-core"],{

/***/ "./resources/assets/js/app-core.js":
/*!*****************************************!*\
  !*** ./resources/assets/js/app-core.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue */ "./node_modules/vue/dist/vue.esm.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _app_helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./app-helpers */ "./resources/assets/js/app-helpers.js");
/**
 * Main imports.
 */



/**
 * Helper functions.
 */


/**
 * Vue prototype.
 */
vue__WEBPACK_IMPORTED_MODULE_2__["default"].prototype.$http = (axios__WEBPACK_IMPORTED_MODULE_0___default());

/**
 * Window assignation.
 */
window.Vue = vue__WEBPACK_IMPORTED_MODULE_2__["default"];
window.eventBus = new vue__WEBPACK_IMPORTED_MODULE_2__["default"]();
window.axios = (axios__WEBPACK_IMPORTED_MODULE_0___default());

// TODO once every package is migrated to laravel-mix 6, this can be removed safely (jquery will be injected when needed)
window.jQuery = window.$ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
window.BootstrapSass = __webpack_require__(/*! bootstrap-sass */ "./node_modules/bootstrap-sass/assets/javascripts/bootstrap.js");
window.lazySize = __webpack_require__(/*! lazysizes */ "./node_modules/lazysizes/lazysizes.js");
window.getBaseUrl = _app_helpers__WEBPACK_IMPORTED_MODULE_1__.getBaseUrl;
window.isMobile = _app_helpers__WEBPACK_IMPORTED_MODULE_1__.isMobile;
window.loadDynamicScript = _app_helpers__WEBPACK_IMPORTED_MODULE_1__.loadDynamicScript;
window.showAlert = _app_helpers__WEBPACK_IMPORTED_MODULE_1__.showAlert;

/**
 * Dynamic loading for mobile.
 */
// $(function() {
//     /**
//      * Base url.
//      */
//     let baseUrl = getBaseUrl();
//
//     /**
//      * Velocity JS path. Just make sure if you are renaming
//      * file then update this path also for mobile.
//      */
//     let jSPath = 'themes/salutarium/assets/js/salutarium.js';
//
//     loadDynamicScript(`${baseUrl}/${jSPath}`, () => {});
// });

/***/ }),

/***/ "./resources/assets/js/app-helpers.js":
/*!********************************************!*\
  !*** ./resources/assets/js/app-helpers.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBaseUrl: () => (/* binding */ getBaseUrl),
/* harmony export */   isMobile: () => (/* binding */ isMobile),
/* harmony export */   loadDynamicScript: () => (/* binding */ loadDynamicScript),
/* harmony export */   removeTrailingSlash: () => (/* binding */ removeTrailingSlash),
/* harmony export */   showAlert: () => (/* binding */ showAlert)
/* harmony export */ });
function getBaseUrl() {
  return document.querySelector('meta[name="base-url"]').content;
}
function isMobile() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i | /mobi/i.test(navigator.userAgent);
}
function loadDynamicScript(src, onScriptLoaded) {
  var dynamicScript = document.createElement('script');
  dynamicScript.setAttribute('src', src);
  document.body.appendChild(dynamicScript);
  dynamicScript.addEventListener('load', onScriptLoaded, false);
}
function showAlert(messageType, messageLabel, message) {
  if (messageType && message !== '') {
    var alertId = Math.floor(Math.random() * 1000);
    var html = "<div class=\"alert ".concat(messageType, " alert-dismissible\" id=\"").concat(alertId, "\">\n            <a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a>\n                <strong>").concat(messageLabel ? messageLabel + '!' : '', " </strong> ").concat(message, ".\n        </div>");
    $('#alert-container').append(html).ready(function () {
      window.setTimeout(function () {
        $("#alert-container #".concat(alertId)).remove();
      }, 5000);
    });
  }
}
function removeTrailingSlash(site) {
  return site.replace(/\/$/, '');
}

/***/ }),

/***/ "./resources/assets/sass/app.scss":
/*!****************************************!*\
  !*** ./resources/assets/sass/app.scss ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["js/components","css/salutarium"], () => (__webpack_exec__("./resources/assets/js/app-core.js"), __webpack_exec__("./resources/assets/sass/app.scss")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);