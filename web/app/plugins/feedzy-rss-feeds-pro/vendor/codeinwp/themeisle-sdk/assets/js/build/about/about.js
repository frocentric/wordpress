/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/src/about/components/Header.js":
/*!**************************************************!*\
  !*** ./assets/js/src/about/components/Header.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Header)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

function Header(_ref) {
  let {
    pages = [],
    selected = ''
  } = _ref;
  const {
    currentProduct,
    logoUrl,
    strings,
    links
  } = window.tiSDKAboutData;

  const hasActiveClass = function () {
    let hash = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
    return hash === selected ? 'active' : '';
  };

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "head"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: logoUrl,
    alt: currentProduct.name
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "by ", (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "https://themeisle.com"
  }, "Themeisle")))), (links.length > 0 || pages.length > 0) && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    className: "nav"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: hasActiveClass()
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: window.location
  }, strings.aboutUs)), pages.map((page, index) => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: hasActiveClass(page.hash),
    key: index
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: page.hash
  }, page.name))), links.map((link, index) => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    key: index
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: link.url
  }, link.text))))));
}

/***/ }),

/***/ "./assets/js/src/about/components/Hero.js":
/*!************************************************!*\
  !*** ./assets/js/src/about/components/Hero.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Hero)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);



function Hero() {
  const {
    strings,
    teamImage,
    homeUrl,
    pageSlug
  } = window.tiSDKAboutData;
  const {
    heroHeader,
    heroTextFirst,
    heroTextSecond,
    teamImageCaption,
    newsHeading,
    emailPlaceholder,
    signMeUp
  } = strings;
  const [email, setEmail] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)('');
  const [loading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [hasSubscribed, setHasSubscribed] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  const submit = e => {
    var _fetch$then$then;

    e.preventDefault();
    setLoading(true);
    (_fetch$then$then = fetch('https://api.themeisle.com/tracking/subscribe', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json, */*;q=0.1',
        'Cache-Control': 'no-cache'
      },
      body: JSON.stringify({
        slug: 'about-us',
        site: homeUrl,
        from: pageSlug,
        email
      })
    }).then(r => r.json()).then(response => {
      setLoading(false);

      if ('success' === response.code) {
        setHasSubscribed(true);
      }
    })) === null || _fetch$then$then === void 0 ? void 0 : _fetch$then$then.catch(error => {
      setLoading(false);
    });
  };

  const updateEmail = e => {
    setEmail(e.target.value);
  };

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "story-card"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "body"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, heroHeader), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, heroTextFirst), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, heroTextSecond)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("figure", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: teamImage,
    alt: teamImageCaption
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("figcaption", null, teamImageCaption))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "footer"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, newsHeading), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
    onSubmit: submit
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    disabled: loading || hasSubscribed,
    type: "email",
    value: email,
    onChange: updateEmail,
    placeholder: emailPlaceholder
  }), !loading && !hasSubscribed && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    isPrimary: true,
    type: "submit"
  }, signMeUp), loading && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-update spin"
  }), hasSubscribed && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-yes-alt"
  })))));
}

/***/ }),

/***/ "./assets/js/src/about/components/ProductCard.js":
/*!*******************************************************!*\
  !*** ./assets/js/src/about/components/ProductCard.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ProductCard)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _common_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../common/utils */ "./assets/js/src/common/utils.js");




function ProductCard(_ref) {
  let {
    product,
    slug
  } = _ref;
  const {
    icon,
    name,
    description,
    status,
    premiumUrl,
    activationLink
  } = product;
  const {
    strings,
    canInstallPlugins
  } = window.tiSDKAboutData;
  const {
    installNow,
    installed,
    notInstalled,
    active,
    activate,
    learnMore
  } = strings;
  const isPremium = !!premiumUrl;
  const [productStatus, setProductStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(status);
  const [loading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  const runInstall = async () => {
    if (!canInstallPlugins) {
      return;
    }

    setLoading(true);
    await (0,_common_utils__WEBPACK_IMPORTED_MODULE_2__.installPluginOrTheme)(slug, slug === 'neve').then(res => {
      if (res.success) {
        setProductStatus('installed');
      }
    });
    setLoading(false);
  };

  const runActivate = async () => {
    if (!canInstallPlugins) {
      return;
    }

    setLoading(true);
    window.location.href = activationLink;
  };

  const buttonContent = () => {
    if (productStatus === 'not-installed' && isPremium) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        isLink: true,
        icon: 'external',
        href: premiumUrl,
        target: "_blank"
      }, learnMore);
    }

    if (productStatus === 'not-installed' && !isPremium) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        isPrimary: true,
        onClick: runInstall,
        disabled: loading || !canInstallPlugins
      }, installNow);
    }

    if (productStatus === 'installed') {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        isSecondary: true,
        onClick: runActivate,
        disabled: loading || !canInstallPlugins
      }, activate);
    }

    return null;
  };

  const wrappedButtonContent = !canInstallPlugins ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    text: `Ask your admin to enable ${name} on your site`,
    position: "top center"
  }, buttonContent()) : buttonContent();
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "product-card"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "header"
  }, icon && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: icon,
    alt: name
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, name)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "body"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    dangerouslySetInnerHTML: {
      __html: description
    }
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "footer"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "Status:", " ", (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: productStatus
  }, productStatus === 'installed' && installed, productStatus === 'not-installed' && notInstalled, productStatus === 'active' && active)), productStatus !== 'active' && !loading && wrappedButtonContent, loading && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-update spin"
  })));
}

/***/ }),

/***/ "./assets/js/src/about/components/ProductCards.js":
/*!********************************************************!*\
  !*** ./assets/js/src/about/components/ProductCards.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ProductCards)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ProductCard__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ProductCard */ "./assets/js/src/about/components/ProductCard.js");


function ProductCards() {
  const {
    products
  } = window.tiSDKAboutData;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "product-cards"
  }, Object.keys(products).map((key, index) => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_ProductCard__WEBPACK_IMPORTED_MODULE_1__["default"], {
    key: key,
    slug: key,
    product: products[key]
  }))));
}

/***/ }),

/***/ "./assets/js/src/about/components/ProductPage.js":
/*!*******************************************************!*\
  !*** ./assets/js/src/about/components/ProductPage.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ProductPage)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _pages_Otter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./pages/Otter */ "./assets/js/src/about/components/pages/Otter.js");


const pagesMap = {
  'otter-page': _pages_Otter__WEBPACK_IMPORTED_MODULE_1__["default"]
};

function Page(props) {
  const CurrentPage = pagesMap[props.id];
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CurrentPage, {
    page: props.page
  });
}

function ProductPage(_ref) {
  let {
    page = {}
  } = _ref;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: 'product-page' + (page && page.product ? ' ' + page.product : '')
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Page, {
    id: page.id,
    page: page
  }));
}

/***/ }),

/***/ "./assets/js/src/about/components/pages/Otter.js":
/*!*******************************************************!*\
  !*** ./assets/js/src/about/components/pages/Otter.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Otter)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _common_utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../common/utils */ "./assets/js/src/common/utils.js");




function Otter(_ref) {
  let {
    page = {}
  } = _ref;
  const {
    products
  } = window.tiSDKAboutData;
  const {
    strings,
    plugin
  } = page;
  const product = page && page.product ? page.product : '';
  const icon = product && products[product] && products[product].icon ? products[product].icon : null;
  const [testimonial, setTestimonial] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(strings.testimonials.users[0]);
  const [productStatus, setProductStatus] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(plugin.status);
  const [loading, setLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const loadingText = 'In Progress';

  const runInstall = async () => {
    setLoading(true);
    await (0,_common_utils__WEBPACK_IMPORTED_MODULE_2__.installPluginOrTheme)(product, false).then(res => {
      if (res.success) {
        setProductStatus('installed');
        runActivate();
      }
    });
  };

  const runActivate = async () => {
    setLoading(true);
    window.location.href = plugin.activationLink;
  };

  const toggleTestimonial = index => {
    const user = strings.testimonials.users[index];
    const testimonial = document.getElementById('ts_' + index);
    testimonial.scrollIntoView({
      behavior: 'smooth'
    });
    setTestimonial(user);
  };

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "hero"
  }, icon && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    className: "logo",
    src: icon,
    alt: page.name || ''
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "label"
  }, "Neve + Otter = New Possibilities \uD83E\uDD1D"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h1", null, strings.heading), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.text), (productStatus === 'not-installed' || productStatus === 'installed') && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "primary",
    disabled: loading,
    className: 'otter-button' + (loading ? ' is-loading' : ''),
    onClick: productStatus === 'not-installed' ? runInstall : runActivate
  }, loading ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-update spin"
  }), loadingText) : strings.buttons.install_otter_free)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-3-highlights"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, strings.features.advancedTitle), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.features.advancedDesc)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, strings.features.fastTitle), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.features.fastDesc)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, strings.features.mobileTitle), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.features.mobileDesc))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-2-highlights"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: strings.details.s1Image,
    alt: strings.details.s1Title
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, strings.details.s1Title), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.details.s1Text))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-2-highlights"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, strings.details.s2Title), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.details.s2Text)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: strings.details.s2Image,
    alt: strings.details.s1Title
  }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-2-highlights"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: strings.details.s3Image,
    alt: strings.details.s1Title
  })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, strings.details.s3Title), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, strings.details.s3Text))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-2-highlights",
    style: {
      backgroundColor: '#F7F7F7',
      borderBottom: 'none',
      borderBottomRightRadius: '8px',
      borderBottomLeftRadius: '8px'
    }
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, strings.testimonials.heading), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "button-row"
  }, (productStatus === 'not-installed' || productStatus === 'installed') && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    variant: "primary",
    disabled: loading,
    className: 'otter-button' + (loading ? ' is-loading' : ''),
    onClick: productStatus === 'not-installed' ? runInstall : runActivate
  }, loading ? (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "dashicons dashicons-update spin"
  }), loadingText) : strings.buttons.install_now), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: "components-button otter-button is-secondary",
    href: strings.buttons.learn_more_link,
    target: "_blank",
    rel: "external noreferrer noopener"
  }, strings.buttons.learn_more))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "testimonials"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    id: "testimonial-container",
    className: "testimonial-container"
  }, strings.testimonials.users.map((user, index) => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: "testimonial",
    id: 'ts_' + index,
    key: 'ts_' + index
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, "\"", user.text, "\""), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: user.avatar,
    alt: user.name
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("h3", null, user.name)))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "testimonial-nav"
  }, strings.testimonials.users.map((user, index) => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    className: 'testimonial-button' + (user.name === testimonial.name ? ' active' : ''),
    key: 'button_' + index,
    onClick: () => toggleTestimonial(index)
  })))))));
}
;

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

/***/ "./assets/js/src/about/about.scss":
/*!****************************************!*\
  !*** ./assets/js/src/about/about.scss ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

module.exports = window["wp"]["element"];

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
/************************************************************************/
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!**************************************!*\
  !*** ./assets/js/src/about/about.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _about_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./about.scss */ "./assets/js/src/about/about.scss");
/* harmony import */ var _components_Header__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/Header */ "./assets/js/src/about/components/Header.js");
/* harmony import */ var _components_Hero__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/Hero */ "./assets/js/src/about/components/Hero.js");
/* harmony import */ var _components_ProductCards__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/ProductCards */ "./assets/js/src/about/components/ProductCards.js");
/* harmony import */ var _components_ProductPage__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/ProductPage */ "./assets/js/src/about/components/ProductPage.js");








const getTabHash = () => {
  let hash = window.location.hash;

  if ('string' !== typeof window.location.hash) {
    return null;
  }

  return hash;
};

function About() {
  const {
    productPages
  } = window.tiSDKAboutData;
  const pages = productPages ? Object.keys(productPages).map(key => {
    const result = productPages[key];
    result.id = key;
    return result;
  }) : [];
  const [hash, setHash] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(getTabHash());

  const setTabToCurrentHash = () => {
    const hash = getTabHash();

    if (null === hash) {
      return;
    }

    setHash(hash);
  };

  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    setTabToCurrentHash();
    window.addEventListener('hashchange', setTabToCurrentHash);
    return () => {
      window.removeEventListener('hashchange', setTabToCurrentHash);
    };
  }, []);
  const isHashInPages = pages.filter(page => {
    return page.hash === hash;
  });

  if (isHashInPages.length > 0) {
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "ti-about"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_Header__WEBPACK_IMPORTED_MODULE_2__["default"], {
      pages: pages,
      selected: hash
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_ProductPage__WEBPACK_IMPORTED_MODULE_5__["default"], {
      page: isHashInPages[0]
    }));
  }

  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "ti-about"
  }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_Header__WEBPACK_IMPORTED_MODULE_2__["default"], {
    pages: pages
  }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_Hero__WEBPACK_IMPORTED_MODULE_3__["default"], null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_ProductCards__WEBPACK_IMPORTED_MODULE_4__["default"], null));
}

document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('#ti-sdk-about');

  if (!root) {
    return;
  }

  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(About, null), root);
});
})();

/******/ })()
;
//# sourceMappingURL=about.js.map