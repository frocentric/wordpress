!function(){var t={703:function(t,e,n){"use strict";var r=n(414);function o(){}function i(){}i.resetWarningCache=o,t.exports=function(){function t(t,e,n,o,i,a){if(a!==r){var s=new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use PropTypes.checkPropTypes() to call them. Read more at http://fb.me/use-check-prop-types");throw s.name="Invariant Violation",s}}function e(){return t}t.isRequired=t;var n={array:t,bigint:t,bool:t,func:t,number:t,object:t,string:t,symbol:t,any:t,arrayOf:e,element:t,elementType:t,instanceOf:e,node:t,objectOf:e,oneOf:e,oneOfType:e,shape:e,exact:e,checkPropTypes:i,resetWarningCache:o};return n.PropTypes=n,n}},697:function(t,e,n){t.exports=n(703)()},414:function(t){"use strict";t.exports="SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED"}},e={};function n(r){var o=e[r];if(void 0!==o)return o.exports;var i=e[r]={exports:{}};return t[r](i,i.exports,n),i.exports}n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,{a:e}),e},n.d=function(t,e){for(var r in e)n.o(e,r)&&!n.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:e[r]})},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},function(){"use strict";var t,e,r,o,i,a,s,c,u,l=window.wp.domReady,f=n.n(l),h=window.wp.element,m=window.wp.i18n,p=window.wp.components;"undefined"!=typeof nf_user_management_data&&(t=void 0!==nf_user_management_data.siteUrl?nf_user_management_data.siteUrl:"",e=void 0!==nf_user_management_data.adminUrl?nf_user_management_data.adminUrl:"",r=void 0!==nf_user_management_data.restUrl?nf_user_management_data.restUrl:"",o=void 0!==nf_user_management_data.ajaxUrl?nf_user_management_data.ajaxUrl:"",i=void 0!==nf_user_management_data.token?nf_user_management_data.token:"",a=void 0!==nf_user_management_data.settings?nf_user_management_data.settings:{},s=void 0!==nf_user_management_data.roles?nf_user_management_data.roles:{},c=void 0!==nf_user_management_data.roles_menu?nf_user_management_data.roles_menu:[],u=void 0!==nf_user_management_data.display_status?nf_user_management_data.display_status:[]),a=void 0!==a&&a.length>0?JSON.parse(a):{};var d=["view_own_submissions","view_others_submissions","edit_own_submissions","edit_others_submissions"];d.forEach((function(t){void 0===a[t]&&(a[t]=[])}));var y={siteUrl:t,adminUrl:e,restUrl:r,ajaxUrl:o,token:i,settings:a,roles:s,settings_types:d,roles_select_options_default_state:c,display_state:u},v=window.wp.apiFetch,_=n.n(v),g=n(697),w=n.n(g);function b(t){return b="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},b(t)}function E(){E=function(){return e};var t,e={},n=Object.prototype,r=n.hasOwnProperty,o=Object.defineProperty||function(t,e,n){t[e]=n.value},i="function"==typeof Symbol?Symbol:{},a=i.iterator||"@@iterator",s=i.asyncIterator||"@@asyncIterator",c=i.toStringTag||"@@toStringTag";function u(t,e,n){return Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{u({},"")}catch(t){u=function(t,e,n){return t[e]=n}}function l(t,e,n,r){var i=e&&e.prototype instanceof v?e:v,a=Object.create(i.prototype),s=new P(r||[]);return o(a,"_invoke",{value:S(t,n,s)}),a}function f(t,e,n){try{return{type:"normal",arg:t.call(e,n)}}catch(t){return{type:"throw",arg:t}}}e.wrap=l;var h="suspendedStart",m="suspendedYield",p="executing",d="completed",y={};function v(){}function _(){}function g(){}var w={};u(w,a,(function(){return this}));var x=Object.getPrototypeOf,O=x&&x(x(I([])));O&&O!==n&&r.call(O,a)&&(w=O);var L=g.prototype=v.prototype=Object.create(w);function j(t){["next","throw","return"].forEach((function(e){u(t,e,(function(t){return this._invoke(e,t)}))}))}function R(t,e){function n(o,i,a,s){var c=f(t[o],t,i);if("throw"!==c.type){var u=c.arg,l=u.value;return l&&"object"==b(l)&&r.call(l,"__await")?e.resolve(l.__await).then((function(t){n("next",t,a,s)}),(function(t){n("throw",t,a,s)})):e.resolve(l).then((function(t){u.value=t,a(u)}),(function(t){return n("throw",t,a,s)}))}s(c.arg)}var i;o(this,"_invoke",{value:function(t,r){function o(){return new e((function(e,o){n(t,r,e,o)}))}return i=i?i.then(o,o):o()}})}function S(e,n,r){var o=h;return function(i,a){if(o===p)throw new Error("Generator is already running");if(o===d){if("throw"===i)throw a;return{value:t,done:!0}}for(r.method=i,r.arg=a;;){var s=r.delegate;if(s){var c=N(s,r);if(c){if(c===y)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(o===h)throw o=d,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);o=p;var u=f(e,n,r);if("normal"===u.type){if(o=r.done?d:m,u.arg===y)continue;return{value:u.arg,done:r.done}}"throw"===u.type&&(o=d,r.method="throw",r.arg=u.arg)}}}function N(e,n){var r=n.method,o=e.iterator[r];if(o===t)return n.delegate=null,"throw"===r&&e.iterator.return&&(n.method="return",n.arg=t,N(e,n),"throw"===n.method)||"return"!==r&&(n.method="throw",n.arg=new TypeError("The iterator does not provide a '"+r+"' method")),y;var i=f(o,e.iterator,n.arg);if("throw"===i.type)return n.method="throw",n.arg=i.arg,n.delegate=null,y;var a=i.arg;return a?a.done?(n[e.resultName]=a.value,n.next=e.nextLoc,"return"!==n.method&&(n.method="next",n.arg=t),n.delegate=null,y):a:(n.method="throw",n.arg=new TypeError("iterator result is not an object"),n.delegate=null,y)}function k(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function T(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function P(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(k,this),this.reset(!0)}function I(e){if(e||""===e){var n=e[a];if(n)return n.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var o=-1,i=function n(){for(;++o<e.length;)if(r.call(e,o))return n.value=e[o],n.done=!1,n;return n.value=t,n.done=!0,n};return i.next=i}}throw new TypeError(b(e)+" is not iterable")}return _.prototype=g,o(L,"constructor",{value:g,configurable:!0}),o(g,"constructor",{value:_,configurable:!0}),_.displayName=u(g,c,"GeneratorFunction"),e.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===_||"GeneratorFunction"===(e.displayName||e.name))},e.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,g):(t.__proto__=g,u(t,c,"GeneratorFunction")),t.prototype=Object.create(L),t},e.awrap=function(t){return{__await:t}},j(R.prototype),u(R.prototype,s,(function(){return this})),e.AsyncIterator=R,e.async=function(t,n,r,o,i){void 0===i&&(i=Promise);var a=new R(l(t,n,r,o),i);return e.isGeneratorFunction(n)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},j(L),u(L,c,"Generator"),u(L,a,(function(){return this})),u(L,"toString",(function(){return"[object Generator]"})),e.keys=function(t){var e=Object(t),n=[];for(var r in e)n.push(r);return n.reverse(),function t(){for(;n.length;){var r=n.pop();if(r in e)return t.value=r,t.done=!1,t}return t.done=!0,t}},e.values=I,P.prototype={constructor:P,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=t,this.done=!1,this.delegate=null,this.method="next",this.arg=t,this.tryEntries.forEach(T),!e)for(var n in this)"t"===n.charAt(0)&&r.call(this,n)&&!isNaN(+n.slice(1))&&(this[n]=t)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var n=this;function o(r,o){return s.type="throw",s.arg=e,n.next=r,o&&(n.method="next",n.arg=t),!!o}for(var i=this.tryEntries.length-1;i>=0;--i){var a=this.tryEntries[i],s=a.completion;if("root"===a.tryLoc)return o("end");if(a.tryLoc<=this.prev){var c=r.call(a,"catchLoc"),u=r.call(a,"finallyLoc");if(c&&u){if(this.prev<a.catchLoc)return o(a.catchLoc,!0);if(this.prev<a.finallyLoc)return o(a.finallyLoc)}else if(c){if(this.prev<a.catchLoc)return o(a.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return o(a.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n];if(o.tryLoc<=this.prev&&r.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,y):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),y},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.finallyLoc===t)return this.complete(n.completion,n.afterLoc),T(n),y}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.tryLoc===t){var r=n.completion;if("throw"===r.type){var o=r.arg;T(n)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,n,r){return this.delegate={iterator:I(e),resultName:n,nextLoc:r},"next"===this.method&&(this.arg=t),y}},e}function x(t,e,n,r,o,i,a){try{var s=t[i](a),c=s.value}catch(t){return void n(t)}s.done?e(c):Promise.resolve(c).then(r,o)}function O(t){return function(){var e=this,n=arguments;return new Promise((function(r,o){var i=t.apply(e,n);function a(t){x(i,r,o,a,s,"next",t)}function s(t){x(i,r,o,a,s,"throw",t)}a(void 0)}))}}_().use(_().createNonceMiddleware(y.token));var L=function(){var t=O(E().mark((function t(e,n){var r,o,i,a;return E().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return r=n.setIsSaving,o=n.setIsSaved,i=n.setIsError,a=n.setCurrentSettings,t.next=3,_()({url:y.restUrl.concat("nf-user-management/save-submissions-access-settings"),method:"POST",data:{settings:JSON.stringify(e)}}).then((function(t){t=JSON.parse(t),r(!1),t.status?(a(e),o(!0)):i(!0)})).catch((function(t){console.log("Deletion cancelled: "+t.message),r(!1),i(!0)}));case 3:case"end":return t.stop()}}),t)})));return function(_x,e){return t.apply(this,arguments)}}();L.propTypes={settings:w().object,functions:w().func};var j=function(){var t=O(E().mark((function t(){return E().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,_()({url:y.restUrl.concat("nf-user-management/get-submissions-access-settings-meta"),method:"GET"}).then((function(t){return t})).catch((function(t){console.log("Deletion cancelled: "+t.message)}));case 2:return t.abrupt("return",t.sent);case 3:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}(),R=function(t){var e=t.text;return React.createElement("div",{className:"nf-processing"},React.createElement("div",{className:"nf-processing-content"},React.createElement("span",{className:"nf-processing-content-in"},React.createElement("h4",null,e&&React.createElement("span",{className:"nf-loading-text"},e)),React.createElement(p.Spinner,null))))};function S(t){return S="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},S(t)}function N(){N=function(){return e};var t,e={},n=Object.prototype,r=n.hasOwnProperty,o=Object.defineProperty||function(t,e,n){t[e]=n.value},i="function"==typeof Symbol?Symbol:{},a=i.iterator||"@@iterator",s=i.asyncIterator||"@@asyncIterator",c=i.toStringTag||"@@toStringTag";function u(t,e,n){return Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{u({},"")}catch(t){u=function(t,e,n){return t[e]=n}}function l(t,e,n,r){var i=e&&e.prototype instanceof v?e:v,a=Object.create(i.prototype),s=new P(r||[]);return o(a,"_invoke",{value:j(t,n,s)}),a}function f(t,e,n){try{return{type:"normal",arg:t.call(e,n)}}catch(t){return{type:"throw",arg:t}}}e.wrap=l;var h="suspendedStart",m="suspendedYield",p="executing",d="completed",y={};function v(){}function _(){}function g(){}var w={};u(w,a,(function(){return this}));var b=Object.getPrototypeOf,E=b&&b(b(I([])));E&&E!==n&&r.call(E,a)&&(w=E);var x=g.prototype=v.prototype=Object.create(w);function O(t){["next","throw","return"].forEach((function(e){u(t,e,(function(t){return this._invoke(e,t)}))}))}function L(t,e){function n(o,i,a,s){var c=f(t[o],t,i);if("throw"!==c.type){var u=c.arg,l=u.value;return l&&"object"==S(l)&&r.call(l,"__await")?e.resolve(l.__await).then((function(t){n("next",t,a,s)}),(function(t){n("throw",t,a,s)})):e.resolve(l).then((function(t){u.value=t,a(u)}),(function(t){return n("throw",t,a,s)}))}s(c.arg)}var i;o(this,"_invoke",{value:function(t,r){function o(){return new e((function(e,o){n(t,r,e,o)}))}return i=i?i.then(o,o):o()}})}function j(e,n,r){var o=h;return function(i,a){if(o===p)throw new Error("Generator is already running");if(o===d){if("throw"===i)throw a;return{value:t,done:!0}}for(r.method=i,r.arg=a;;){var s=r.delegate;if(s){var c=R(s,r);if(c){if(c===y)continue;return c}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(o===h)throw o=d,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);o=p;var u=f(e,n,r);if("normal"===u.type){if(o=r.done?d:m,u.arg===y)continue;return{value:u.arg,done:r.done}}"throw"===u.type&&(o=d,r.method="throw",r.arg=u.arg)}}}function R(e,n){var r=n.method,o=e.iterator[r];if(o===t)return n.delegate=null,"throw"===r&&e.iterator.return&&(n.method="return",n.arg=t,R(e,n),"throw"===n.method)||"return"!==r&&(n.method="throw",n.arg=new TypeError("The iterator does not provide a '"+r+"' method")),y;var i=f(o,e.iterator,n.arg);if("throw"===i.type)return n.method="throw",n.arg=i.arg,n.delegate=null,y;var a=i.arg;return a?a.done?(n[e.resultName]=a.value,n.next=e.nextLoc,"return"!==n.method&&(n.method="next",n.arg=t),n.delegate=null,y):a:(n.method="throw",n.arg=new TypeError("iterator result is not an object"),n.delegate=null,y)}function k(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function T(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function P(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(k,this),this.reset(!0)}function I(e){if(e||""===e){var n=e[a];if(n)return n.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var o=-1,i=function n(){for(;++o<e.length;)if(r.call(e,o))return n.value=e[o],n.done=!1,n;return n.value=t,n.done=!0,n};return i.next=i}}throw new TypeError(S(e)+" is not iterable")}return _.prototype=g,o(x,"constructor",{value:g,configurable:!0}),o(g,"constructor",{value:_,configurable:!0}),_.displayName=u(g,c,"GeneratorFunction"),e.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===_||"GeneratorFunction"===(e.displayName||e.name))},e.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,g):(t.__proto__=g,u(t,c,"GeneratorFunction")),t.prototype=Object.create(x),t},e.awrap=function(t){return{__await:t}},O(L.prototype),u(L.prototype,s,(function(){return this})),e.AsyncIterator=L,e.async=function(t,n,r,o,i){void 0===i&&(i=Promise);var a=new L(l(t,n,r,o),i);return e.isGeneratorFunction(n)?a:a.next().then((function(t){return t.done?t.value:a.next()}))},O(x),u(x,c,"Generator"),u(x,a,(function(){return this})),u(x,"toString",(function(){return"[object Generator]"})),e.keys=function(t){var e=Object(t),n=[];for(var r in e)n.push(r);return n.reverse(),function t(){for(;n.length;){var r=n.pop();if(r in e)return t.value=r,t.done=!1,t}return t.done=!0,t}},e.values=I,P.prototype={constructor:P,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=t,this.done=!1,this.delegate=null,this.method="next",this.arg=t,this.tryEntries.forEach(T),!e)for(var n in this)"t"===n.charAt(0)&&r.call(this,n)&&!isNaN(+n.slice(1))&&(this[n]=t)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var n=this;function o(r,o){return s.type="throw",s.arg=e,n.next=r,o&&(n.method="next",n.arg=t),!!o}for(var i=this.tryEntries.length-1;i>=0;--i){var a=this.tryEntries[i],s=a.completion;if("root"===a.tryLoc)return o("end");if(a.tryLoc<=this.prev){var c=r.call(a,"catchLoc"),u=r.call(a,"finallyLoc");if(c&&u){if(this.prev<a.catchLoc)return o(a.catchLoc,!0);if(this.prev<a.finallyLoc)return o(a.finallyLoc)}else if(c){if(this.prev<a.catchLoc)return o(a.catchLoc,!0)}else{if(!u)throw new Error("try statement without catch or finally");if(this.prev<a.finallyLoc)return o(a.finallyLoc)}}}},abrupt:function(t,e){for(var n=this.tryEntries.length-1;n>=0;--n){var o=this.tryEntries[n];if(o.tryLoc<=this.prev&&r.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===t||"continue"===t)&&i.tryLoc<=e&&e<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=t,a.arg=e,i?(this.method="next",this.next=i.finallyLoc,y):this.complete(a)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),y},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.finallyLoc===t)return this.complete(n.completion,n.afterLoc),T(n),y}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var n=this.tryEntries[e];if(n.tryLoc===t){var r=n.completion;if("throw"===r.type){var o=r.arg;T(n)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,n,r){return this.delegate={iterator:I(e),resultName:n,nextLoc:r},"next"===this.method&&(this.arg=t),y}},e}function k(t,e,n,r,o,i,a){try{var s=t[i](a),c=s.value}catch(t){return void n(t)}s.done?e(c):Promise.resolve(c).then(r,o)}function T(t){return function(){var e=this,n=arguments;return new Promise((function(r,o){var i=t.apply(e,n);function a(t){k(i,r,o,a,s,"next",t)}function s(t){k(i,r,o,a,s,"throw",t)}a(void 0)}))}}function P(t,e){return function(t){if(Array.isArray(t))return t}(t)||function(t,e){var n=null==t?null:"undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(null!=n){var r,o,i,a,s=[],c=!0,u=!1;try{if(i=(n=n.call(t)).next,0===e){if(Object(n)!==n)return;c=!1}else for(;!(c=(r=i.call(n)).done)&&(s.push(r.value),s.length!==e);c=!0);}catch(t){u=!0,o=t}finally{try{if(!c&&null!=n.return&&(a=n.return(),Object(a)!==a))return}finally{if(u)throw o}}return s}}(t,e)||function(t,e){if(t){if("string"==typeof t)return I(t,e);var n=Object.prototype.toString.call(t).slice(8,-1);return"Object"===n&&t.constructor&&(n=t.constructor.name),"Map"===n||"Set"===n?Array.from(t):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?I(t,e):void 0}}(t,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function I(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,r=new Array(e);n<e;n++)r[n]=t[n];return r}R.propTypes={text:w().string};var F=function(t){var e=t.settings,n=t.roles_select_options_default_state,r=t.display_state,o=P((0,h.useState)(e),2),i=o[0],a=o[1],s=P((0,h.useState)(!1),2),c=s[0],u=s[1],l=P((0,h.useState)(!1),2),f=l[0],d=l[1],y=P((0,h.useState)(!1),2),v=y[0],_=y[1],g=P((0,h.useState)({state:!1,text:""}),2),w=g[0],b=g[1],E=P((0,h.useState)(n),2),x=E[0],O=E[1],S=P((0,h.useState)(r),2),k=S[0],I=S[1];(0,h.useEffect)((function(){T(N().mark((function t(){var e,n,r,o;return N().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,j();case 2:e=t.sent,n=JSON.parse(e),r=n.select_options_status,o=n.display_settings_status,O(r),I(o);case 8:case"end":return t.stop()}}),t)})))()}),[f]);var F=function(t,e,n){b({state:!1,text:""}),d(!1),u(!0);var r=i[e]&&i[e].indexOf(t)>=0&&"add"===n,o=i[e]&&i[e].indexOf(t)<0&&"dismiss"===n;if(r)b({state:!0,text:"Role Already granted permission."}),u(!1);else if(o)b({state:!0,text:"Role Not Listed, cannot be dismissed."}),u(!1);else{var a=i;"add"===n?(a[e].push(t),a=A(a,t,e)):"dismiss"===n&&(a=A(a,t,e))[e]&&a[e].splice(a[e].indexOf(t),1),a&&G(a)}},G=function(){var t=T(N().mark((function t(e){var n;return N().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return n={setIsSaving:u,setIsSaved:d,setIsError:_,setCurrentSettings:a},t.next=3,L(e,n);case 3:case"end":return t.stop()}}),t)})));return function(_x){return t.apply(this,arguments)}}(),A=function(t,e,n){return n.startsWith("edit_")&&(n.endsWith("_own_submissions")&&i.view_own_submissions.indexOf(e)<0?(t.view_own_submissions.push(e),b({status:!0,text:(0,m.__)("This role was also added to View Own Submissions list.","ninja-forms-user-management")})):n.endsWith("_others_submissions")&&i.view_others_submissions.indexOf(e)<0&&(t.view_others_submissions.push(e),b({status:!0,text:(0,m.__)("This role was also added to View Others Submissions list.","ninja-forms-user-management")}))),n.endsWith("_others_submissions")&&(n.startsWith("view")&&i.view_own_submissions.indexOf(e)<0?(t.view_own_submissions.push(e),b({status:!0,text:(0,m.__)("This role was also added to View Own Submissions list.","ninja-forms-user-management")})):n.startsWith("edit")&&i.edit_own_submissions.indexOf(e)<0&&(t.edit_own_submissions.push(e),b({status:!0,text:(0,m.__)("This role was also added to View Others Submissions list.","ninja-forms-user-management")}))),t},D=React.createElement(React.Fragment,null,React.createElement(p.CardHeader,null,React.createElement("h3",null,(0,m.__)("Use these settings to configure users level of access to submissions.","ninja-forms-user-management"))),React.createElement(p.CardBody,null,React.createElement("div",{className:"nf-user-management-settings-actions-status"},w.status&&React.createElement(p.Notice,{status:"notice",isDismissible:"true",onDismiss:function(){return b({state:!1,text:""})},children:w.text}),v&&React.createElement(p.Notice,{status:"error",isDismissible:"true",onDismiss:function(){return _(!1)},children:(0,m.__)("An error occured, please try again or refresh page.","ninja-form-user-management")}),f&&React.createElement(p.Notice,{status:"success",isDismissible:"true",onDismiss:function(){return d(!1)},children:(0,m.__)("Settings saved successfully","ninja-form-user-management")})),React.createElement("h3",null,(0,m.__)("View submissions","ninja-forms-user-management")),React.createElement(p.Flex,{align:"flex-start"},React.createElement(p.FlexBlock,null,React.createElement("h4",null,(0,m.__)("Owner","ninja-forms-user-management")),React.createElement(U,{type:"view_own_submissions",options:x.view_own_submissions,action:F}),React.createElement(C,{type:"view_own_submissions",settings:k.view_own_submissions,action:F}),i.view_own_submissions&&i.view_own_submissions.length>0&&React.createElement("p",null,(0,m.__)("Can view own submissions","ninja-forms-user-management"))),React.createElement(p.FlexBlock,null,React.createElement("h4",null,(0,m.__)("Others","ninja-forms-user-managemen")),React.createElement(U,{type:"view_others_submissions",options:x.view_others_submissions,action:F}),React.createElement(C,{type:"view_others_submissions",settings:k.view_others_submissions,action:F}),i.view_others_submissions&&i.view_others_submissions.length>0&&React.createElement("p",null,(0,m.__)("Can view others submissions","ninja-forms-user-management")))),React.createElement("hr",null),React.createElement("h3",null,(0,m.__)("Edit submissions","ninja-forms-user-management")),React.createElement(p.Flex,{align:"flex-start"},React.createElement(p.FlexBlock,null,React.createElement("h4",null,(0,m.__)("Owner","ninja-forms-user-managemen")),React.createElement(U,{type:"edit_own_submissions",options:x.edit_own_submissions,action:F}),React.createElement(C,{type:"edit_own_submissions",settings:k.edit_own_submissions,action:F}),i.edit_own_submissions&&i.edit_own_submissions.length>0&&React.createElement("p",null,(0,m.__)("Can edit own submissions","ninja-forms-user-management"))),React.createElement(p.FlexBlock,null,React.createElement("h4",null,(0,m.__)("Others","ninja-forms-user-managemen")),React.createElement(U,{type:"edit_others_submissions",options:x.edit_others_submissions,action:F}),React.createElement(C,{type:"edit_others_submissions",settings:k.edit_others_submissions,action:F}),i.edit_others_submissions&&i.edit_others_submissions.length>0&&React.createElement("p",null,(0,m.__)("Can edit others submissions","ninja-forms-user-management"))))));return React.createElement(p.Card,{className:"widget"},c&&React.createElement(R,{text:(0,m.__)("Saving settings","ninja-form-user-management")}),c?React.createElement(p.Disabled,{className:"nf-is-saving"},D):D)},U=function(t){var e=t.type,n=t.action,r=t.options;return React.createElement(p.SelectControl,{options:r,onChange:function(t){n(t,e,"add")}})};U.propTypes={type:w().string,action:w().func,options:w().array};var C=function(t){var e=t.type,n=t.settings,r=t.action;return React.createElement(React.Fragment,null,n.map((function(t){return React.createElement("span",{className:"nf-user-access-role-element",key:t.value},React.createElement(p.Button,{className:"nf-dismiss-role-access-icon",icon:"dismiss",iconSize:12,onClick:function(){return r(t.value,e,"dismiss")},disabled:t.disabled}),t.label)})))};C.propTypes={type:w().string,action:w().func,settings:w().array},f()((function(){G.observe(document.getElementById("ninja-forms-dashboard"),{subtree:!0,childList:!0})}));var G=new MutationObserver((function(t){t.forEach((function(t){t.addedNodes.forEach((function(t){"nf-user-access-settings-anchor"==t.id&&A()}))}))})),A=function(){var t=(0,h.createElement)(F,y),e=document.getElementById("nf-user-access-settings-anchor");h.createRoot?(0,h.createRoot)(e).render(t):(0,h.render)(t,e)}}()}();