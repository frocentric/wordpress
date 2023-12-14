/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/src/OptimoleNotice/index.js":
/*!***********************************************!*\
  !*** ./assets/js/src/OptimoleNotice/index.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ OptimoleNotice)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./style.scss */ "./assets/js/src/OptimoleNotice/style.scss");
/* harmony import */ var _common_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../common/utils */ "./assets/js/src/common/utils.js");
/* harmony import */ var _common_useSettings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../common/useSettings */ "./assets/js/src/common/useSettings.js");






function OptimoleNotice(_ref) {
  let {
    stacked = false,
    noImage = false,
    type,
    onDismiss,
    onSuccess,
    initialStatus = null
  } = _ref;
  const {
    assets,
    title,
    email: initialEmail,
    option,
    optionKey,
    optimoleActivationUrl,
    optimoleApi,
    optimoleDash,
    nonce
  } = window.themeisleSDKPromotions;
  const [showForm, setShowForm] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [email, setEmail] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialEmail || '');
  const [dismissed, setDismissed] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [progress, setProgress] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialStatus);
  const [getOption, updateOption] = (0,_common_useSettings__WEBPACK_IMPORTED_MODULE_4__["default"])();

  const dismissNotice = async () => {
    setDismissed(true);
    const newValue = { ...option
    };
    newValue[type] = new Date().getTime() / 1000 | 0;
    window.themeisleSDKPromotions.option = newValue;
    await updateOption(optionKey, JSON.stringify(newValue));

    if (onDismiss) {
      onDismiss();
    }
  };

  const toggleForm = () => {
    setShowForm(!showForm);
  };

  const updateEmail = e => {
    setEmail(e.target.value);
  };

  const submitForm = async e => {
    e.preventDefault();
    setProgress('installing');
    await (0,_common_utils__WEBPACK_IMPORTED_MODULE_3__.installPluginOrTheme)('optimole-wp');
    setProgress('activating');
    await (0,_common_utils__WEBPACK_IMPORTED_MODULE_3__.activatePlugin)(optimoleActivationUrl);
    updateOption('themeisle_sdk_promotions_optimole_installed', !Boolean(getOption('themeisle_sdk_promotions_optimole_installed')));
    setProgress('connecting');

    try {
      await fetch(optimoleApi, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          'email': email
        })
      });

      if (onSuccess) {
        onSuccess();
      }

      setProgress('done');
    } catch (e) {
      setProgress('done');
    }
  };

  if (dismissed) {
    return null;
  }

  const form = () => {
    if (progress === 'done') {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
        className: "done"
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Awesome! You are all set!"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        icon: 'external',
        isPrimary: true,
        href: optimoleDash,
        target: "_blank"
      }, "Go to Optimole dashboard"));
    }

    if (progress) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
        className: "om-progress"
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
        className: "dashicons dashicons-update spin"
      }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, progress === 'installing' && 'Installing', progress === 'activating' && 'Activating', progress === 'connecting' && 'Connecting to API', "\u2026"));
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, "Enter your email address to create & connect your account"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
      onSubmit: submitForm
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
      defaultValue: email,
      type: "email",
      onChange: updateEmail,
      placeholder: "Email address"
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
      isPrimary: true,
      type: "submit"
    }, "Start using Optimole")));
  };

  const dismissButton = () => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    disabled: progress && progress !== 'done',
    onClick: dismissNotice,
    isLink: true,
    className: "om-notice-dismiss"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons-no-alt dashicons"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "screen-reader-text"
  }, "Dismiss this notice."));

  if (stacked) {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "ti-om-stack-wrap"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "om-stack-notice"
    }, dismissButton(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
      src: assets + '/optimole-logo.svg',
      alt: "Optimole logo"
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, "Get more with Optimole"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, type === 'om-editor' || type === 'om-image-block' ? 'Increase this page speed and SEO ranking by optimizing images with Optimole.' : 'Leverage Optimole\'s full integration with Elementor to automatically lazyload, resize, compress to AVIF/WebP and deliver from 400 locations around the globe!'), !showForm && 'done' !== progress && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
      isPrimary: true,
      onClick: toggleForm,
      className: "cta"
    }, "Get Started Free"), (showForm || 'done' === progress) && form(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("i", null, title)));
  }

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, dismissButton(), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "content"
  }, !noImage && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: assets + '/optimole-logo.svg',
    alt: "Optimole logo"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, title), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    className: "description"
  }, type === 'om-media' ? 'Save your server space by storing images to Optimole and deliver them optimized from 400 locations around the globe. Unlimited images, Unlimited traffic.' : 'This image looks to be too large and would affect your site speed, we recommend you to install Optimole to optimize your images.'), !showForm && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "actions"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    isPrimary: true,
    onClick: toggleForm
  }, "Get Started Free"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    isLink: true,
    target: "_blank",
    href: "https://wordpress.org/plugins/optimole-wp"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-external"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, "Learn more"))), showForm && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "form-wrap"
  }, form()))));
}

/***/ }),

/***/ "./assets/js/src/common/useSettings.js":
/*!*********************************************!*\
  !*** ./assets/js/src/common/useSettings.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api */ "@wordpress/api");
/* harmony import */ var _wordpress_api__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/**
 * WordPress dependencies.
 */



/**
 * useSettings Hook.
 *
 * useSettings hook to get/update WordPress' settings database.
 *
 * Setting field needs to be registered to REST for this function to work.
 *
 * This hook works similar to get_option and update_option in PHP just without the option for a default value.
 * For notificiations to work, you need to add a Snackbar section to your React codebase if it isn't being
 * used inside the block editor.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/packages/editor/src/components/editor-snackbars/index.js
 * @author  Hardeep Asrani <hardeepasrani@gmail.com>
 * @version 1.1
 *
 */

const useSettings = () => {
  const {
    createNotice
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.dispatch)('core/notices');
  const [settings, setSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)({});
  const [status, setStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useState)('loading');

  const getSettings = () => {
    _wordpress_api__WEBPACK_IMPORTED_MODULE_0___default().loadPromise.then(async () => {
      try {
        const settings = new (_wordpress_api__WEBPACK_IMPORTED_MODULE_0___default().models.Settings)();
        const response = await settings.fetch();
        setSettings(response);
      } catch (error) {
        setStatus('error');
      } finally {
        setStatus('loaded');
      }
    });
  };

  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useEffect)(() => {
    getSettings();
  }, []);

  const getOption = option => {
    return settings === null || settings === void 0 ? void 0 : settings[option];
  };

  const updateOption = function (option, value) {
    let success = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'Settings saved.';
    setStatus('saving');
    const save = new (_wordpress_api__WEBPACK_IMPORTED_MODULE_0___default().models.Settings)({
      [option]: value
    }).save();
    save.success((response, status) => {
      if ('success' === status) {
        setStatus('loaded');
        createNotice('success', success, {
          isDismissible: true,
          type: 'snackbar'
        });
      }

      if ('error' === status) {
        setStatus('error');
        createNotice('error', 'An unknown error occurred.', {
          isDismissible: true,
          type: 'snackbar'
        });
      }

      getSettings();
    });
    save.error(response => {
      setStatus('error');
      createNotice('error', response.responseJSON.message ? response.responseJSON.message : 'An unknown error occurred.', {
        isDismissible: true,
        type: 'snackbar'
      });
    });
  };

  return [getOption, updateOption, status];
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (useSettings);

/***/ }),

/***/ "./assets/js/src/common/utils.js":
/*!***************************************!*\
  !*** ./assets/js/src/common/utils.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "activatePlugin": () => (/* binding */ activatePlugin),
/* harmony export */   "getBlocksByType": () => (/* binding */ getBlocksByType),
/* harmony export */   "installPluginOrTheme": () => (/* binding */ installPluginOrTheme)
/* harmony export */ });
const installPluginOrTheme = function (slug) {
  let theme = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  return new Promise(resolve => {
    wp.updates.ajax(theme === true ? 'install-theme' : 'install-plugin', {
      slug,
      success: () => {
        resolve({
          success: true
        });
      },
      error: err => {
        resolve({
          success: false,
          code: err.errorCode
        });
      }
    });
  });
};

const activatePlugin = url => {
  return new Promise(resolve => {
    jQuery.get(url).done(() => {
      resolve({
        success: true
      });
    }).fail(() => {
      resolve({
        success: false
      });
    });
  });
};

const flatRecursively = (r, a) => {
  const b = {};
  Object.keys(a).forEach(function (k) {
    if ('innerBlocks' !== k) {
      b[k] = a[k];
    }
  });
  r.push(b);

  if (Array.isArray(a.innerBlocks)) {
    b.innerBlocks = a.innerBlocks.map(i => {
      return i.id;
    });
    return a.innerBlocks.reduce(flatRecursively, r);
  }

  return r;
};
/**
 * Get blocks by type.
 *
 * @param {Array} blocks blocks array.
 * @param {string} type type of block looking for.
 *
 * @return {Array} array of blocks of {type} in page
 */


const getBlocksByType = (blocks, type) => blocks.reduce(flatRecursively, []).filter(a => type === a.name);



/***/ }),

/***/ "./assets/js/src/index.js":
/*!********************************!*\
  !*** ./assets/js/src/index.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _otter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./otter.js */ "./assets/js/src/otter.js");
/* harmony import */ var _optimole_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./optimole.js */ "./assets/js/src/optimole.js");
/* harmony import */ var _rop_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./rop.js */ "./assets/js/src/rop.js");
/* harmony import */ var _neve_fse_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./neve-fse.js */ "./assets/js/src/neve-fse.js");





/***/ }),

/***/ "./assets/js/src/neve-fse.js":
/*!***********************************!*\
  !*** ./assets/js/src/neve-fse.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _common_useSettings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./common/useSettings */ "./assets/js/src/common/useSettings.js");





const NeveFSENotice = _ref => {
  let {
    onDismiss = () => {}
  } = _ref;
  const [getOption, updateOption] = (0,_common_useSettings__WEBPACK_IMPORTED_MODULE_2__["default"])();

  const dismissNotice = async () => {
    const newValue = { ...window.themeisleSDKPromotions.option
    };
    newValue['neve-fse-themes-popular'] = new Date().getTime() / 1000 | 0;
    window.themeisleSDKPromotions.option = newValue;
    await updateOption(window.themeisleSDKPromotions.optionKey, JSON.stringify(newValue));

    if (onDismiss) {
      onDismiss();
    }
  };

  const {
    neveFSEMoreUrl
  } = window.themeisleSDKPromotions;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    onClick: dismissNotice,
    className: "notice-dismiss"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "screen-reader-text"
  }, "Dismiss this notice.")), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Meet ", (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("b", null, "Neve FSE"), " from the makers of ", window.themeisleSDKPromotions.product, ". A theme that makes full site editing on WordPress straightforward and user-friendly."), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "neve-fse-notice-actions"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "link",
    target: "_blank",
    href: neveFSEMoreUrl
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-external"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, "Learn more"))));
};

class NeveFSE {
  constructor() {
    const {
      showPromotion,
      debug
    } = window.themeisleSDKPromotions;
    this.promo = showPromotion;
    this.debug = debug === '1';
    this.domRef = null;
    this.run();
  }

  run() {
    if (window.themeisleSDKPromotions.option['neve-fse-themes-popular']) {
      return;
    }

    const root = document.querySelector('#ti-neve-fse-notice');

    if (!root) {
      return;
    }

    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(NeveFSENotice, {
      onDismiss: () => {
        root.style.display = 'none';
      }
    }), root);
  }

}

new NeveFSE();

/***/ }),

/***/ "./assets/js/src/optimole.js":
/*!***********************************!*\
  !*** ./assets/js/src/optimole.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/plugins */ "@wordpress/plugins");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/edit-post */ "@wordpress/edit-post");
/* harmony import */ var _wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _common_utils__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./common/utils */ "./assets/js/src/common/utils.js");
/* harmony import */ var _OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./OptimoleNotice */ "./assets/js/src/OptimoleNotice/index.js");











const TiSdkMoleEditorPromo = () => {
  const [show, setShow] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(true);

  const hide = () => {
    setShow(false);
  };

  const {
    getBlocks
  } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_3__.useSelect)(select => {
    const {
      getBlocks
    } = select('core/block-editor');
    return {
      getBlocks
    };
  });
  const imageBlocksCount = (0,_common_utils__WEBPACK_IMPORTED_MODULE_7__.getBlocksByType)(getBlocks(), 'core/image').length;

  if (imageBlocksCount < 2) {
    return null;
  }

  const classes = `ti-sdk-optimole-post-publish ${show ? '' : 'hidden'}`;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_edit_post__WEBPACK_IMPORTED_MODULE_2__.PluginPostPublishPanel, {
    className: classes
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__["default"], {
    stacked: true,
    type: "om-editor",
    onDismiss: hide
  }));
};

class Optimole {
  constructor() {
    const {
      showPromotion,
      debug
    } = window.themeisleSDKPromotions;
    this.promo = showPromotion;
    this.debug = debug === '1';
    this.domRef = null;
    this.run();
  }

  run() {
    if (this.debug) {
      this.runAll();
      return;
    }

    switch (this.promo) {
      case 'om-attachment':
        this.runAttachmentPromo();
        break;

      case 'om-media':
        this.runMediaPromo();
        break;

      case 'om-editor':
        this.runEditorPromo();
        break;

      case 'om-image-block':
        this.runImageBlockPromo();
        break;

      case 'om-elementor':
        this.runElementorPromo();
        break;
    }
  }

  runAttachmentPromo() {
    wp.media.view.Attachment.Details.prototype.on("ready", () => {
      setTimeout(() => {
        this.removeAttachmentPromo();
        this.addAttachmentPromo();
      }, 100);
    });
    wp.media.view.Modal.prototype.on("close", () => {
      setTimeout(this.removeAttachmentPromo, 100);
    });
  }

  runMediaPromo() {
    if (window.themeisleSDKPromotions.option['om-media']) {
      return;
    }

    const root = document.querySelector('#ti-optml-notice');

    if (!root) {
      return;
    }

    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__["default"], {
      type: "om-media",
      onDismiss: () => {
        root.style.opacity = 0;
      }
    }), root);
  }

  runImageBlockPromo() {
    if (window.themeisleSDKPromotions.option['om-image-block']) {
      return;
    }

    let showNotice = true;
    let initialStatus = null;
    const withImagePromo = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_4__.createHigherOrderComponent)(BlockEdit => {
      return props => {
        if ('core/image' === props.name && showNotice) {
          return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, props), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__["default"], {
            stacked: true,
            type: "om-image-block",
            initialStatus: initialStatus,
            onDismiss: () => {
              showNotice = false;
            },
            onSuccess: () => {
              initialStatus = 'done';
            }
          })));
        }

        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, props);
      };
    }, 'withImagePromo');
    (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.addFilter)('editor.BlockEdit', 'optimole-promo/image-promo', withImagePromo, 99);
  }

  runEditorPromo() {
    if (window.themeisleSDKPromotions.option['om-editor']) {
      return;
    }

    (0,_wordpress_plugins__WEBPACK_IMPORTED_MODULE_1__.registerPlugin)('optimole-promo', {
      render: TiSdkMoleEditorPromo
    });
  }

  runElementorPromo() {
    if (!window.elementor) {
      return;
    }

    const self = this;
    elementor.on("preview:loaded", () => {
      elementor.panel.currentView.on("set:page:editor", details => {
        if (self.domRef) {
          (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.unmountComponentAtNode)(self.domRef);
        }

        if (!details.activeSection) {
          return;
        }

        if (details.activeSection !== 'section_image') {
          return;
        }

        self.runElementorActions(self);
      });
    });
  }

  addAttachmentPromo() {
    if (this.domRef) {
      (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.unmountComponentAtNode)(this.domRef);
    }

    if (window.themeisleSDKPromotions.option['om-attachment']) {
      return;
    }

    const mount = document.querySelector('#ti-optml-notice-helper');

    if (!mount) {
      return;
    }

    this.domRef = mount;
    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "notice notice-info ti-sdk-om-notice",
      style: {
        margin: 0
      }
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__["default"], {
      noImage: true,
      type: "om-attachment",
      onDismiss: () => {
        mount.style.opacity = 0;
      }
    })), mount);
  }

  removeAttachmentPromo() {
    const mount = document.querySelector('#ti-optml-notice-helper');

    if (!mount) {
      return;
    }

    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.unmountComponentAtNode)(mount);
  }

  runElementorActions(self) {
    if (window.themeisleSDKPromotions.option['om-elementor']) {
      return;
    }

    const controlsWrap = document.querySelector('#elementor-panel__editor__help');
    const mountPoint = document.createElement('div');
    mountPoint.id = 'ti-optml-notice';
    self.domRef = mountPoint;

    if (controlsWrap) {
      controlsWrap.parentNode.insertBefore(mountPoint, controlsWrap);
      (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_OptimoleNotice__WEBPACK_IMPORTED_MODULE_8__["default"], {
        stacked: true,
        type: "om-elementor",
        onDismiss: () => {
          mountPoint.style.opacity = 0;
        }
      }), mountPoint);
    }
  }

  runAll() {
    this.runAttachmentPromo();
    this.runMediaPromo();
    this.runEditorPromo();
    this.runImageBlockPromo();
    this.runElementorPromo();
  }

}

new Optimole();

/***/ }),

/***/ "./assets/js/src/otter.js":
/*!********************************!*\
  !*** ./assets/js/src/otter.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _common_useSettings_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./common/useSettings.js */ "./assets/js/src/common/useSettings.js");
/* harmony import */ var _common_utils_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./common/utils.js */ "./assets/js/src/common/utils.js");









const style = {
  button: {
    display: 'flex',
    justifyContent: 'center',
    width: '100%'
  },
  image: {
    padding: '20px 0'
  },
  skip: {
    container: {
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center'
    },
    button: {
      fontSize: '9px'
    },
    poweredby: {
      fontSize: '9px',
      textTransform: 'uppercase'
    }
  }
};
const upsells = {
  'blocks-css': {
    title: 'Custom CSS',
    description: 'Enable Otter Blocks to add Custom CSS for this block.',
    image: 'css.jpg'
  },
  'blocks-animation': {
    title: 'Animations',
    description: 'Enable Otter Blocks to add Animations for this block.',
    image: 'animation.jpg'
  },
  'blocks-conditions': {
    title: 'Visibility Conditions',
    description: 'Enable Otter Blocks to add Visibility Conditions for this block.',
    image: 'conditions.jpg'
  }
};

const Footer = _ref => {
  let {
    onClick
  } = _ref;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    style: style.skip.container
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
    style: style.skip.button,
    variant: "tertiary",
    onClick: onClick
  }, "Skip for now"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    style: style.skip.poweredby
  }, "Recommended by ", window.themeisleSDKPromotions.product));
};

const withInspectorControls = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__.createHigherOrderComponent)(BlockEdit => {
  return props => {
    if (props.isSelected && Boolean(window.themeisleSDKPromotions.showPromotion)) {
      const [isLoading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
      const [installStatus, setInstallStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)('default');
      const [hasSkipped, setHasSkipped] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
      const [getOption, updateOption, status] = (0,_common_useSettings_js__WEBPACK_IMPORTED_MODULE_6__["default"])();

      const install = async () => {
        setLoading(true);
        await (0,_common_utils_js__WEBPACK_IMPORTED_MODULE_7__.installPluginOrTheme)('otter-blocks');
        updateOption('themeisle_sdk_promotions_otter_installed', !Boolean(getOption('themeisle_sdk_promotions_otter_installed')));
        await (0,_common_utils_js__WEBPACK_IMPORTED_MODULE_7__.activatePlugin)(window.themeisleSDKPromotions.otterActivationUrl);
        setLoading(false);
        setInstallStatus('installed');
      };

      const Install = () => {
        if ('installed' === installStatus) {
          return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("strong", null, "Awesome! Refresh the page to see Otter Blocks in action."));
        }

        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.Button, {
          variant: "secondary",
          onClick: install,
          isBusy: isLoading,
          style: style.button
        }, "Install & Activate Otter Blocks");
      };

      const onSkip = () => {
        const option = { ...window.themeisleSDKPromotions.option
        };
        option[window.themeisleSDKPromotions.showPromotion] = new Date().getTime() / 1000 | 0;
        updateOption('themeisle_sdk_promotions', JSON.stringify(option));
        window.themeisleSDKPromotions.showPromotion = false;
      };

      (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
        if (hasSkipped) {
          onSkip();
        }
      }, [hasSkipped]);

      if (hasSkipped) {
        return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, props);
      }

      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, props), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.InspectorControls, null, Object.keys(upsells).map(key => {
        if (key === window.themeisleSDKPromotions.showPromotion) {
          const upsell = upsells[key];
          return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, {
            key: key,
            title: upsell.title,
            initialOpen: false
          }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, upsell.description), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Install, null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
            style: style.image,
            src: window.themeisleSDKPromotions.assets + upsell.image
          }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Footer, {
            onClick: () => setHasSkipped(true)
          }));
        }
      })));
    }

    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockEdit, props);
  };
}, 'withInspectorControl');

if (!(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_4__.select)('core/edit-site')) {
  (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.addFilter)('editor.BlockEdit', 'themeisle-sdk/with-inspector-controls', withInspectorControls);
}

/***/ }),

/***/ "./assets/js/src/rop.js":
/*!******************************!*\
  !*** ./assets/js/src/rop.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _common_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./common/utils */ "./assets/js/src/common/utils.js");
/* harmony import */ var _common_useSettings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./common/useSettings */ "./assets/js/src/common/useSettings.js");






const ROPNotice = _ref => {
  let {
    onDismiss = () => {}
  } = _ref;
  const [status, setStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [getOption, updateOption] = (0,_common_useSettings__WEBPACK_IMPORTED_MODULE_3__["default"])();

  const dismissNotice = async () => {
    const newValue = { ...window.themeisleSDKPromotions.option
    };
    newValue['rop-posts'] = new Date().getTime() / 1000 | 0;
    window.themeisleSDKPromotions.option = newValue;
    await updateOption(window.themeisleSDKPromotions.optionKey, JSON.stringify(newValue));

    if (onDismiss) {
      onDismiss();
    }
  };

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    disabled: 'installing' === status,
    onClick: dismissNotice,
    variant: "link",
    className: "om-notice-dismiss"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons-no-alt dashicons"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "screen-reader-text"
  }, "Dismiss this notice.")), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Boost your content's reach effortlessly! Introducing ", (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("b", null, "Revive Old Posts"), ", a cutting-edge plugin from the makers of ", window.themeisleSDKPromotions.product, ". Seamlessly auto-share old & new content across social media, driving traffic like never before."), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "rop-notice-actions"
  }, 'installed' !== status ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "primary",
    isBusy: 'installing' === status,
    onClick: async () => {
      setStatus('installing');
      await (0,_common_utils__WEBPACK_IMPORTED_MODULE_2__.installPluginOrTheme)('tweet-old-post');
      await (0,_common_utils__WEBPACK_IMPORTED_MODULE_2__.activatePlugin)(window.themeisleSDKPromotions.ropActivationUrl);
      updateOption('themeisle_sdk_promotions_rop_installed', !Boolean(getOption('themeisle_sdk_promotions_rop_installed')));
      setStatus('installed');
    }
  }, "Install & Activate") : (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "primary",
    href: window.themeisleSDKPromotions.ropDash
  }, "Visit Dashboard"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "link",
    target: "_blank",
    href: "https://wordpress.org/plugins/tweet-old-post/"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-external"
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, "Learn more"))));
};

class ROP {
  constructor() {
    const {
      showPromotion,
      debug
    } = window.themeisleSDKPromotions;
    this.promo = showPromotion;
    this.debug = debug === '1';
    this.domRef = null;
    this.run();
  }

  run() {
    if (window.themeisleSDKPromotions.option['rop-posts']) {
      return;
    }

    const root = document.querySelector('#ti-rop-notice');

    if (!root) {
      return;
    }

    (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ROPNotice, {
      onDismiss: () => {
        root.style.display = 'none';
      }
    }), root);
  }

}

new ROP();

/***/ }),

/***/ "./assets/js/src/OptimoleNotice/style.scss":
/*!*************************************************!*\
  !*** ./assets/js/src/OptimoleNotice/style.scss ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/api":
/*!*****************************!*\
  !*** external ["wp","api"] ***!
  \*****************************/
/***/ ((module) => {

module.exports = window["wp"]["api"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/edit-post":
/*!**********************************!*\
  !*** external ["wp","editPost"] ***!
  \**********************************/
/***/ ((module) => {

module.exports = window["wp"]["editPost"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/***/ ((module) => {

module.exports = window["wp"]["hooks"];

/***/ }),

/***/ "@wordpress/plugins":
/*!*********************************!*\
  !*** external ["wp","plugins"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["plugins"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkthemeisle_sdk"] = self["webpackChunkthemeisle_sdk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], () => (__webpack_require__("./assets/js/src/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map