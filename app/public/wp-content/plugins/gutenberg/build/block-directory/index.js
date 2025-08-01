/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./packages/block-directory/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getDownloadableBlocks: () => (getDownloadableBlocks),
  getErrorNoticeForBlock: () => (getErrorNoticeForBlock),
  getErrorNotices: () => (getErrorNotices),
  getInstalledBlockTypes: () => (getInstalledBlockTypes),
  getNewBlockTypes: () => (getNewBlockTypes),
  getUnusedBlockTypes: () => (getUnusedBlockTypes),
  isInstalling: () => (isInstalling),
  isRequestingDownloadableBlocks: () => (isRequestingDownloadableBlocks)
});

// NAMESPACE OBJECT: ./packages/block-directory/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  addInstalledBlockType: () => (addInstalledBlockType),
  clearErrorNotice: () => (clearErrorNotice),
  fetchDownloadableBlocks: () => (fetchDownloadableBlocks),
  installBlockType: () => (installBlockType),
  receiveDownloadableBlocks: () => (receiveDownloadableBlocks),
  removeInstalledBlockType: () => (removeInstalledBlockType),
  setErrorNotice: () => (setErrorNotice),
  setIsInstalling: () => (setIsInstalling),
  uninstallBlockType: () => (uninstallBlockType)
});

// NAMESPACE OBJECT: ./packages/block-directory/build-module/store/resolvers.js
var resolvers_namespaceObject = {};
__webpack_require__.r(resolvers_namespaceObject);
__webpack_require__.d(resolvers_namespaceObject, {
  getDownloadableBlocks: () => (resolvers_getDownloadableBlocks)
});

;// external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","editor"]
const external_wp_editor_namespaceObject = window["wp"]["editor"];
;// ./packages/block-directory/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer returning an array of downloadable blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
const downloadableBlocks = (state = {}, action) => {
  switch (action.type) {
    case 'FETCH_DOWNLOADABLE_BLOCKS':
      return {
        ...state,
        [action.filterValue]: {
          isRequesting: true
        }
      };
    case 'RECEIVE_DOWNLOADABLE_BLOCKS':
      return {
        ...state,
        [action.filterValue]: {
          results: action.downloadableBlocks,
          isRequesting: false
        }
      };
  }
  return state;
};

/**
 * Reducer managing the installation and deletion of blocks.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
const blockManagement = (state = {
  installedBlockTypes: [],
  isInstalling: {}
}, action) => {
  switch (action.type) {
    case 'ADD_INSTALLED_BLOCK_TYPE':
      return {
        ...state,
        installedBlockTypes: [...state.installedBlockTypes, action.item]
      };
    case 'REMOVE_INSTALLED_BLOCK_TYPE':
      return {
        ...state,
        installedBlockTypes: state.installedBlockTypes.filter(blockType => blockType.name !== action.item.name)
      };
    case 'SET_INSTALLING_BLOCK':
      return {
        ...state,
        isInstalling: {
          ...state.isInstalling,
          [action.blockId]: action.isInstalling
        }
      };
  }
  return state;
};

/**
 * Reducer returning an object of error notices.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
const errorNotices = (state = {}, action) => {
  switch (action.type) {
    case 'SET_ERROR_NOTICE':
      return {
        ...state,
        [action.blockId]: {
          message: action.message,
          isFatal: action.isFatal
        }
      };
    case 'CLEAR_ERROR_NOTICE':
      const {
        [action.blockId]: blockId,
        ...restState
      } = state;
      return restState;
  }
  return state;
};
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  downloadableBlocks,
  blockManagement,
  errorNotices
}));

;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// ./packages/block-directory/build-module/store/selectors.js
/**
 * WordPress dependencies
 */


const EMPTY_ARRAY = [];

/**
 * Returns true if application is requesting for downloadable blocks.
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {boolean} Whether a request is in progress for the blocks list.
 */
function isRequestingDownloadableBlocks(state, filterValue) {
  var _state$downloadableBl;
  return (_state$downloadableBl = state.downloadableBlocks[filterValue]?.isRequesting) !== null && _state$downloadableBl !== void 0 ? _state$downloadableBl : false;
}

/**
 * Returns the available uninstalled blocks.
 *
 * @param {Object} state       Global application state.
 * @param {string} filterValue Search string.
 *
 * @return {Array} Downloadable blocks.
 */
function getDownloadableBlocks(state, filterValue) {
  var _state$downloadableBl2;
  return (_state$downloadableBl2 = state.downloadableBlocks[filterValue]?.results) !== null && _state$downloadableBl2 !== void 0 ? _state$downloadableBl2 : EMPTY_ARRAY;
}

/**
 * Returns the block types that have been installed on the server in this
 * session.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items
 */
function getInstalledBlockTypes(state) {
  return state.blockManagement.installedBlockTypes;
}

/**
 * Returns block types that have been installed on the server and used in the
 * current post.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */
const getNewBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (0,external_wp_data_namespaceObject.createSelector)(state => {
  const installedBlockTypes = getInstalledBlockTypes(state);
  if (!installedBlockTypes.length) {
    return EMPTY_ARRAY;
  }
  const {
    getBlockName,
    getClientIdsWithDescendants
  } = select(external_wp_blockEditor_namespaceObject.store);
  const installedBlockNames = installedBlockTypes.map(blockType => blockType.name);
  const foundBlockNames = getClientIdsWithDescendants().flatMap(clientId => {
    const blockName = getBlockName(clientId);
    return installedBlockNames.includes(blockName) ? blockName : [];
  });
  const newBlockTypes = installedBlockTypes.filter(blockType => foundBlockNames.includes(blockType.name));
  return newBlockTypes.length > 0 ? newBlockTypes : EMPTY_ARRAY;
}, state => [getInstalledBlockTypes(state), select(external_wp_blockEditor_namespaceObject.store).getClientIdsWithDescendants()]));

/**
 * Returns the block types that have been installed on the server but are not
 * used in the current post.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} Block type items.
 */
const getUnusedBlockTypes = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (0,external_wp_data_namespaceObject.createSelector)(state => {
  const installedBlockTypes = getInstalledBlockTypes(state);
  if (!installedBlockTypes.length) {
    return EMPTY_ARRAY;
  }
  const {
    getBlockName,
    getClientIdsWithDescendants
  } = select(external_wp_blockEditor_namespaceObject.store);
  const installedBlockNames = installedBlockTypes.map(blockType => blockType.name);
  const foundBlockNames = getClientIdsWithDescendants().flatMap(clientId => {
    const blockName = getBlockName(clientId);
    return installedBlockNames.includes(blockName) ? blockName : [];
  });
  const unusedBlockTypes = installedBlockTypes.filter(blockType => !foundBlockNames.includes(blockType.name));
  return unusedBlockTypes.length > 0 ? unusedBlockTypes : EMPTY_ARRAY;
}, state => [getInstalledBlockTypes(state), select(external_wp_blockEditor_namespaceObject.store).getClientIdsWithDescendants()]));

/**
 * Returns true if a block plugin install is in progress.
 *
 * @param {Object} state   Global application state.
 * @param {string} blockId Id of the block.
 *
 * @return {boolean} Whether this block is currently being installed.
 */
function isInstalling(state, blockId) {
  return state.blockManagement.isInstalling[blockId] || false;
}

/**
 * Returns all block error notices.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Object with error notices.
 */
function getErrorNotices(state) {
  return state.errorNotices;
}

/**
 * Returns the error notice for a given block.
 *
 * @param {Object} state   Global application state.
 * @param {string} blockId The ID of the block plugin. eg: my-block
 *
 * @return {string|boolean} The error text, or false if no error.
 */
function getErrorNoticeForBlock(state, blockId) {
  return state.errorNotices[blockId];
}

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// ./packages/block-directory/build-module/store/load-assets.js
/**
 * WordPress dependencies
 */


/**
 * Load an asset for a block.
 *
 * This function returns a Promise that will resolve once the asset is loaded,
 * or in the case of Stylesheets and Inline JavaScript, will resolve immediately.
 *
 * @param {HTMLElement} el A HTML Element asset to inject.
 *
 * @return {Promise} Promise which will resolve when the asset is loaded.
 */
const loadAsset = el => {
  return new Promise((resolve, reject) => {
    /*
     * Reconstruct the passed element, this is required as inserting the Node directly
     * won't always fire the required onload events, even if the asset wasn't already loaded.
     */
    const newNode = document.createElement(el.nodeName);
    ['id', 'rel', 'src', 'href', 'type'].forEach(attr => {
      if (el[attr]) {
        newNode[attr] = el[attr];
      }
    });

    // Append inline <script> contents.
    if (el.innerHTML) {
      newNode.appendChild(document.createTextNode(el.innerHTML));
    }
    newNode.onload = () => resolve(true);
    newNode.onerror = () => reject(new Error('Error loading asset.'));
    document.body.appendChild(newNode);

    // Resolve Stylesheets and Inline JavaScript immediately.
    if ('link' === newNode.nodeName.toLowerCase() || 'script' === newNode.nodeName.toLowerCase() && !newNode.src) {
      resolve();
    }
  });
};

/**
 * Load the asset files for a block
 */
async function loadAssets() {
  /*
   * Fetch the current URL (post-new.php, or post.php?post=1&action=edit) and compare the
   * JavaScript and CSS assets loaded between the pages. This imports the required assets
   * for the block into the current page while not requiring that we know them up-front.
   * In the future this can be improved by reliance upon block.json and/or a script-loader
   * dependency API.
   */
  const response = await external_wp_apiFetch_default()({
    url: document.location.href,
    parse: false
  });
  const data = await response.text();
  const doc = new window.DOMParser().parseFromString(data, 'text/html');
  const newAssets = Array.from(doc.querySelectorAll('link[rel="stylesheet"],script')).filter(asset => asset.id && !document.getElementById(asset.id));

  /*
   * Load each asset in order, as they may depend upon an earlier loaded script.
   * Stylesheets and Inline Scripts will resolve immediately upon insertion.
   */
  for (const newAsset of newAssets) {
    await loadAsset(newAsset);
  }
}

;// ./packages/block-directory/build-module/store/utils/get-plugin-url.js
/**
 * Get the plugin's direct API link out of a block-directory response.
 *
 * @param {Object} block The block object
 *
 * @return {string} The plugin URL, if exists.
 */
function getPluginUrl(block) {
  if (!block) {
    return false;
  }
  const link = block.links['wp:plugin'] || block.links.self;
  if (link && link.length) {
    return link[0].href;
  }
  return false;
}

;// ./packages/block-directory/build-module/store/actions.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



/**
 * Returns an action object used in signalling that the downloadable blocks
 * have been requested and are loading.
 *
 * @param {string} filterValue Search string.
 *
 * @return {Object} Action object.
 */
function fetchDownloadableBlocks(filterValue) {
  return {
    type: 'FETCH_DOWNLOADABLE_BLOCKS',
    filterValue
  };
}

/**
 * Returns an action object used in signalling that the downloadable blocks
 * have been updated.
 *
 * @param {Array}  downloadableBlocks Downloadable blocks.
 * @param {string} filterValue        Search string.
 *
 * @return {Object} Action object.
 */
function receiveDownloadableBlocks(downloadableBlocks, filterValue) {
  return {
    type: 'RECEIVE_DOWNLOADABLE_BLOCKS',
    downloadableBlocks,
    filterValue
  };
}

/**
 * Action triggered to install a block plugin.
 *
 * @param {Object} block The block item returned by search.
 *
 * @return {boolean} Whether the block was successfully installed & loaded.
 */
const installBlockType = block => async ({
  registry,
  dispatch
}) => {
  const {
    id,
    name
  } = block;
  let success = false;
  dispatch.clearErrorNotice(id);
  try {
    dispatch.setIsInstalling(id, true);

    // If we have a wp:plugin link, the plugin is installed but inactive.
    const url = getPluginUrl(block);
    let links = {};
    if (url) {
      await external_wp_apiFetch_default()({
        method: 'PUT',
        url,
        data: {
          status: 'active'
        }
      });
    } else {
      const response = await external_wp_apiFetch_default()({
        method: 'POST',
        path: 'wp/v2/plugins',
        data: {
          slug: id,
          status: 'active'
        }
      });
      // Add the `self` link for newly-installed blocks.
      links = response._links;
    }
    dispatch.addInstalledBlockType({
      ...block,
      links: {
        ...block.links,
        ...links
      }
    });

    // Ensures that the block metadata is propagated to the editor when registered on the server.
    const metadataFields = ['api_version', 'title', 'category', 'parent', 'ancestor', 'icon', 'description', 'keywords', 'attributes', 'provides_context', 'uses_context', 'selectors', 'supports', 'styles', 'example', 'variations', 'allowed_blocks', 'block_hooks'];
    await external_wp_apiFetch_default()({
      path: (0,external_wp_url_namespaceObject.addQueryArgs)(`/wp/v2/block-types/${name}`, {
        _fields: metadataFields
      })
    })
    // Ignore when the block is not registered on the server.
    .catch(() => {}).then(response => {
      if (!response) {
        return;
      }
      (0,external_wp_blocks_namespaceObject.unstable__bootstrapServerSideBlockDefinitions)({
        [name]: Object.fromEntries(Object.entries(response).filter(([key]) => metadataFields.includes(key)))
      });
    });
    await loadAssets();
    const registeredBlocks = registry.select(external_wp_blocks_namespaceObject.store).getBlockTypes();
    if (!registeredBlocks.some(i => i.name === name)) {
      throw new Error((0,external_wp_i18n_namespaceObject.__)('Error registering block. Try reloading the page.'));
    }
    registry.dispatch(external_wp_notices_namespaceObject.store).createInfoNotice((0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %s is the block title.
    (0,external_wp_i18n_namespaceObject.__)('Block %s installed and added.'), block.title), {
      speak: true,
      type: 'snackbar'
    });
    success = true;
  } catch (error) {
    let message = error.message || (0,external_wp_i18n_namespaceObject.__)('An error occurred.');

    // Errors we throw are fatal.
    let isFatal = error instanceof Error;

    // Specific API errors that are fatal.
    const fatalAPIErrors = {
      folder_exists: (0,external_wp_i18n_namespaceObject.__)('This block is already installed. Try reloading the page.'),
      unable_to_connect_to_filesystem: (0,external_wp_i18n_namespaceObject.__)('Error installing block. You can reload the page and try again.')
    };
    if (fatalAPIErrors[error.code]) {
      isFatal = true;
      message = fatalAPIErrors[error.code];
    }
    dispatch.setErrorNotice(id, message, isFatal);
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(message, {
      speak: true,
      isDismissible: true
    });
  }
  dispatch.setIsInstalling(id, false);
  return success;
};

/**
 * Action triggered to uninstall a block plugin.
 *
 * @param {Object} block The blockType object.
 */
const uninstallBlockType = block => async ({
  registry,
  dispatch
}) => {
  try {
    const url = getPluginUrl(block);
    await external_wp_apiFetch_default()({
      method: 'PUT',
      url,
      data: {
        status: 'inactive'
      }
    });
    await external_wp_apiFetch_default()({
      method: 'DELETE',
      url
    });
    dispatch.removeInstalledBlockType(block);
  } catch (error) {
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(error.message || (0,external_wp_i18n_namespaceObject.__)('An error occurred.'));
  }
};

/**
 * Returns an action object used to add a block type to the "newly installed"
 * tracking list.
 *
 * @param {Object} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */
function addInstalledBlockType(item) {
  return {
    type: 'ADD_INSTALLED_BLOCK_TYPE',
    item
  };
}

/**
 * Returns an action object used to remove a block type from the "newly installed"
 * tracking list.
 *
 * @param {string} item The block item with the block id and name.
 *
 * @return {Object} Action object.
 */
function removeInstalledBlockType(item) {
  return {
    type: 'REMOVE_INSTALLED_BLOCK_TYPE',
    item
  };
}

/**
 * Returns an action object used to indicate install in progress.
 *
 * @param {string}  blockId
 * @param {boolean} isInstalling
 *
 * @return {Object} Action object.
 */
function setIsInstalling(blockId, isInstalling) {
  return {
    type: 'SET_INSTALLING_BLOCK',
    blockId,
    isInstalling
  };
}

/**
 * Sets an error notice to be displayed to the user for a given block.
 *
 * @param {string}  blockId The ID of the block plugin. eg: my-block
 * @param {string}  message The message shown in the notice.
 * @param {boolean} isFatal Whether the user can recover from the error.
 *
 * @return {Object} Action object.
 */
function setErrorNotice(blockId, message, isFatal = false) {
  return {
    type: 'SET_ERROR_NOTICE',
    blockId,
    message,
    isFatal
  };
}

/**
 * Sets the error notice to empty for specific block.
 *
 * @param {string} blockId The ID of the block plugin. eg: my-block
 *
 * @return {Object} Action object.
 */
function clearErrorNotice(blockId) {
  return {
    type: 'CLEAR_ERROR_NOTICE',
    blockId
  };
}

;// ./node_modules/tslib/tslib.es6.mjs
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol, Iterator */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g = Object.create((typeof Iterator === "function" ? Iterator : Object).prototype);
  return g.next = verb(0), g["throw"] = verb(1), g["return"] = verb(2), typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = Object.create((typeof AsyncIterator === "function" ? AsyncIterator : Object).prototype), verb("next"), verb("throw"), verb("return", awaitReturn), i[Symbol.asyncIterator] = function () { return this; }, i;
  function awaitReturn(f) { return function (v) { return Promise.resolve(v).then(f, reject); }; }
  function verb(n, f) { if (g[n]) { i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; if (f) i[n] = f(i[n]); } }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose, inner;
    if (async) {
      if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
      dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
      if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
      dispose = value[Symbol.dispose];
      if (async) inner = dispose;
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    if (inner) dispose = function() { try { inner.call(this); } catch (e) { return Promise.reject(e); } };
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  var r, s = 0;
  function next() {
    while (r = env.stack.pop()) {
      try {
        if (!r.async && s === 1) return s = 0, env.stack.push(r), Promise.resolve().then(next);
        if (r.dispose) {
          var result = r.dispose.call(r.value);
          if (r.async) return s |= 2, Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
        }
        else s |= 1;
      }
      catch (e) {
        fail(e);
      }
    }
    if (s === 1) return env.hasError ? Promise.reject(env.error) : Promise.resolve();
    if (env.hasError) throw env.error;
  }
  return next();
}

function __rewriteRelativeImportExtension(path, preserveJsx) {
  if (typeof path === "string" && /^\.\.?\//.test(path)) {
      return path.replace(/\.(tsx)$|((?:\.d)?)((?:\.[^./]+?)?)\.([cm]?)ts$/i, function (m, tsx, d, ext, cm) {
          return tsx ? preserveJsx ? ".jsx" : ".js" : d && (!ext || !cm) ? m : (d + ext + "." + cm.toLowerCase() + "js");
      });
  }
  return path;
}

/* harmony default export */ const tslib_es6 = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __esDecorate,
  __runInitializers,
  __propKey,
  __setFunctionName,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
  __rewriteRelativeImportExtension,
});

;// ./node_modules/lower-case/dist.es2015/index.js
/**
 * Source: ftp://ftp.unicode.org/Public/UCD/latest/ucd/SpecialCasing.txt
 */
var SUPPORTED_LOCALE = {
    tr: {
        regexp: /\u0130|\u0049|\u0049\u0307/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    az: {
        regexp: /\u0130/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    lt: {
        regexp: /\u0049|\u004A|\u012E|\u00CC|\u00CD|\u0128/g,
        map: {
            I: "\u0069\u0307",
            J: "\u006A\u0307",
            Į: "\u012F\u0307",
            Ì: "\u0069\u0307\u0300",
            Í: "\u0069\u0307\u0301",
            Ĩ: "\u0069\u0307\u0303",
        },
    },
};
/**
 * Localized lower case.
 */
function localeLowerCase(str, locale) {
    var lang = SUPPORTED_LOCALE[locale.toLowerCase()];
    if (lang)
        return lowerCase(str.replace(lang.regexp, function (m) { return lang.map[m]; }));
    return lowerCase(str);
}
/**
 * Lower case as a function.
 */
function lowerCase(str) {
    return str.toLowerCase();
}

;// ./node_modules/no-case/dist.es2015/index.js

// Support camel case ("camelCase" -> "camel Case" and "CAMELCase" -> "CAMEL Case").
var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
// Remove all non-word characters.
var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
/**
 * Normalize the string into something other libraries can manipulate easier.
 */
function noCase(input, options) {
    if (options === void 0) { options = {}; }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    // Trim the delimiter from around the output string.
    while (result.charAt(start) === "\0")
        start++;
    while (result.charAt(end - 1) === "\0")
        end--;
    // Transform each token independently.
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
}
/**
 * Replace `re` in the input string with the replacement value.
 */
function replace(input, re, value) {
    if (re instanceof RegExp)
        return input.replace(re, value);
    return re.reduce(function (input, re) { return input.replace(re, value); }, input);
}

;// ./node_modules/pascal-case/dist.es2015/index.js


function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
        return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
}
function dist_es2015_pascalCaseTransformMerge(input) {
    return input.charAt(0).toUpperCase() + input.slice(1).toLowerCase();
}
function pascalCase(input, options) {
    if (options === void 0) { options = {}; }
    return noCase(input, __assign({ delimiter: "", transform: pascalCaseTransform }, options));
}

;// ./node_modules/camel-case/dist.es2015/index.js


function camelCaseTransform(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return pascalCaseTransform(input, index);
}
function camelCaseTransformMerge(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return pascalCaseTransformMerge(input);
}
function camelCase(input, options) {
    if (options === void 0) { options = {}; }
    return pascalCase(input, __assign({ transform: camelCaseTransform }, options));
}

;// ./packages/block-directory/build-module/store/resolvers.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

const resolvers_getDownloadableBlocks = filterValue => async ({
  dispatch
}) => {
  if (!filterValue) {
    return;
  }
  try {
    dispatch(fetchDownloadableBlocks(filterValue));
    const results = await external_wp_apiFetch_default()({
      path: `wp/v2/block-directory/search?term=${filterValue}`
    });
    const blocks = results.map(result => Object.fromEntries(Object.entries(result).map(([key, value]) => [camelCase(key), value])));
    dispatch(receiveDownloadableBlocks(blocks, filterValue));
  } catch {
    dispatch(receiveDownloadableBlocks([], filterValue));
  }
};

;// ./packages/block-directory/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Module Constants
 */
const STORE_NAME = 'core/block-directory';

/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */
const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  resolvers: resolvers_namespaceObject
};

/**
 * Store definition for the block directory namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store);

;// ./packages/block-directory/build-module/components/auto-block-uninstaller/index.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function AutoBlockUninstaller() {
  const {
    uninstallBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const shouldRemoveBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isAutosavingPost,
      isSavingPost
    } = select(external_wp_editor_namespaceObject.store);
    return isSavingPost() && !isAutosavingPost();
  }, []);
  const unusedBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getUnusedBlockTypes(), []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (shouldRemoveBlockTypes && unusedBlockTypes.length) {
      unusedBlockTypes.forEach(blockType => {
        uninstallBlockType(blockType);
        (0,external_wp_blocks_namespaceObject.unregisterBlockType)(blockType.name);
      });
    }
  }, [shouldRemoveBlockTypes]);
  return null;
}

;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// ./packages/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/**
 * External dependencies
 */

/**
 * Return an SVG icon.
 *
 * @param props The component props.
 *
 * @return Icon component
 */
/* harmony default export */ const icon = ((0,external_wp_element_namespaceObject.forwardRef)(({
  icon,
  size = 24,
  ...props
}, ref) => {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}));

;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./packages/icons/build-module/library/star-filled.js
/**
 * WordPress dependencies
 */


const starFilled = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
  })
});
/* harmony default export */ const star_filled = (starFilled);

;// ./packages/icons/build-module/library/star-half.js
/**
 * WordPress dependencies
 */


const starHalf = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222L4.753 9.882a.25.25 0 01.14-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z"
  })
});
/* harmony default export */ const star_half = (starHalf);

;// ./packages/icons/build-module/library/star-empty.js
/**
 * WordPress dependencies
 */


const starEmpty = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    fillRule: "evenodd",
    d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
    clipRule: "evenodd"
  })
});
/* harmony default export */ const star_empty = (starEmpty);

;// ./packages/block-directory/build-module/components/block-ratings/stars.js
/**
 * WordPress dependencies
 */



function Stars({
  rating
}) {
  const stars = Math.round(rating / 0.5) * 0.5;
  const fullStarCount = Math.floor(rating);
  const halfStarCount = Math.ceil(rating - fullStarCount);
  const emptyStarCount = 5 - (fullStarCount + halfStarCount);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("span", {
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %s: number of stars. */
    (0,external_wp_i18n_namespaceObject.__)('%s out of 5 stars'), stars),
    children: [Array.from({
      length: fullStarCount
    }).map((_, i) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(icon, {
      className: "block-directory-block-ratings__star-full",
      icon: star_filled,
      size: 16
    }, `full_stars_${i}`)), Array.from({
      length: halfStarCount
    }).map((_, i) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(icon, {
      className: "block-directory-block-ratings__star-half-full",
      icon: star_half,
      size: 16
    }, `half_stars_${i}`)), Array.from({
      length: emptyStarCount
    }).map((_, i) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(icon, {
      className: "block-directory-block-ratings__star-empty",
      icon: star_empty,
      size: 16
    }, `empty_stars_${i}`))]
  });
}
/* harmony default export */ const stars = (Stars);

;// ./packages/block-directory/build-module/components/block-ratings/index.js
/**
 * Internal dependencies
 */


const BlockRatings = ({
  rating
}) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
  className: "block-directory-block-ratings",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(stars, {
    rating: rating
  })
});
/* harmony default export */ const block_ratings = (BlockRatings);

;// ./packages/block-directory/build-module/components/downloadable-block-icon/index.js
/**
 * WordPress dependencies
 */


function DownloadableBlockIcon({
  icon
}) {
  const className = 'block-directory-downloadable-block-icon';
  return icon.match(/\.(jpeg|jpg|gif|png|svg)(?:\?.*)?$/) !== null ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("img", {
    className: className,
    src: icon,
    alt: ""
  }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    className: className,
    icon: icon,
    showColors: true
  });
}
/* harmony default export */ const downloadable_block_icon = (DownloadableBlockIcon);

;// ./packages/block-directory/build-module/components/downloadable-block-notice/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


const DownloadableBlockNotice = ({
  block
}) => {
  const errorNotice = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getErrorNoticeForBlock(block.id), [block]);
  if (!errorNotice) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    className: "block-directory-downloadable-block-notice",
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
      className: "block-directory-downloadable-block-notice__content",
      children: [errorNotice.message, errorNotice.isFatal ? ' ' + (0,external_wp_i18n_namespaceObject.__)('Try reloading the page.') : null]
    })
  });
};
/* harmony default export */ const downloadable_block_notice = (DownloadableBlockNotice);

;// ./packages/block-directory/build-module/components/downloadable-block-list-item/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */





// Return the appropriate block item label, given the block data and status.

function getDownloadableBlockLabel({
  title,
  rating,
  ratingCount
}, {
  hasNotice,
  isInstalled,
  isInstalling
}) {
  const stars = Math.round(rating / 0.5) * 0.5;
  if (!isInstalled && hasNotice) {
    /* translators: %s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Retry installing %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }
  if (isInstalled) {
    /* translators: %s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Add %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }
  if (isInstalling) {
    /* translators: %s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Installing %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }

  // No ratings yet, just use the title.
  if (ratingCount < 1) {
    /* translators: %s: block title */
    return (0,external_wp_i18n_namespaceObject.sprintf)('Install %s.', (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title));
  }
  return (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: 1: block title, 2: average rating, 3: total ratings count. */
  (0,external_wp_i18n_namespaceObject._n)('Install %1$s. %2$s stars with %3$s review.', 'Install %1$s. %2$s stars with %3$s reviews.', ratingCount), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title), stars, ratingCount);
}
function DownloadableBlockListItem({
  item,
  onClick
}) {
  const {
    author,
    description,
    icon,
    rating,
    title
  } = item;
  // getBlockType returns a block object if this block exists, or null if not.
  const isInstalled = !!(0,external_wp_blocks_namespaceObject.getBlockType)(item.name);
  const {
    hasNotice,
    isInstalling,
    isInstallable
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getErrorNoticeForBlock,
      isInstalling: isBlockInstalling
    } = select(store);
    const notice = getErrorNoticeForBlock(item.id);
    const hasFatal = notice && notice.isFatal;
    return {
      hasNotice: !!notice,
      isInstalling: isBlockInstalling(item.id),
      isInstallable: !hasFatal
    };
  }, [item]);
  let statusText = '';
  if (isInstalled) {
    statusText = (0,external_wp_i18n_namespaceObject.__)('Installed!');
  } else if (isInstalling) {
    statusText = (0,external_wp_i18n_namespaceObject.__)('Installing…');
  }
  const itemLabel = getDownloadableBlockLabel(item, {
    hasNotice,
    isInstalled,
    isInstalling
  });
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Tooltip, {
    placement: "top",
    text: itemLabel,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Composite.Item, {
      className: dist_clsx('block-directory-downloadable-block-list-item', isInstalling && 'is-installing'),
      accessibleWhenDisabled: true,
      disabled: isInstalling || !isInstallable,
      onClick: event => {
        event.preventDefault();
        onClick();
      },
      "aria-label": itemLabel,
      type: "button",
      role: "option",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
        className: "block-directory-downloadable-block-list-item__icon",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(downloadable_block_icon, {
          icon: icon,
          title: title
        }), isInstalling ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
          className: "block-directory-downloadable-block-list-item__spinner",
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
        }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(block_ratings, {
          rating: rating
        })]
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("span", {
        className: "block-directory-downloadable-block-list-item__details",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
          className: "block-directory-downloadable-block-list-item__title",
          children: (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.sprintf)(/* translators: 1: block title. 2: author name. */
          (0,external_wp_i18n_namespaceObject.__)('%1$s <span>by %2$s</span>'), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title), author), {
            span: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
              className: "block-directory-downloadable-block-list-item__author"
            })
          })
        }), hasNotice ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(downloadable_block_notice, {
          block: item
        }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
          children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("span", {
            className: "block-directory-downloadable-block-list-item__desc",
            children: !!statusText ? statusText : (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(description)
          }), isInstallable && !(isInstalled || isInstalling) && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.VisuallyHidden, {
            children: (0,external_wp_i18n_namespaceObject.__)('Install block')
          })]
        })]
      })]
    })
  });
}
/* harmony default export */ const downloadable_block_list_item = (DownloadableBlockListItem);

;// ./packages/block-directory/build-module/components/downloadable-blocks-list/index.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const noop = () => {};
function DownloadableBlocksList({
  items,
  onHover = noop,
  onSelect
}) {
  const {
    installBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  if (!items.length) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Composite, {
    role: "listbox",
    className: "block-directory-downloadable-blocks-list",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Blocks available for install'),
    children: items.map(item => {
      return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(downloadable_block_list_item, {
        onClick: () => {
          // Check if the block is registered (`getBlockType`
          // will return an object). If so, insert the block.
          // This prevents installing existing plugins.
          if ((0,external_wp_blocks_namespaceObject.getBlockType)(item.name)) {
            onSelect(item);
          } else {
            installBlockType(item).then(success => {
              if (success) {
                onSelect(item);
              }
            });
          }
          onHover(null);
        },
        onHover: onHover,
        item: item
      }, item.id);
    })
  });
}
/* harmony default export */ const downloadable_blocks_list = (DownloadableBlocksList);

;// external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// ./packages/block-directory/build-module/components/downloadable-blocks-panel/inserter-panel.js
/**
 * WordPress dependencies
 */




function DownloadableBlocksInserterPanel({
  children,
  downloadableItems,
  hasLocalBlocks
}) {
  const count = downloadableItems.length;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %d: number of available blocks. */
    (0,external_wp_i18n_namespaceObject._n)('%d additional block is available to install.', '%d additional blocks are available to install.', count), count));
  }, [count]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [!hasLocalBlocks && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      className: "block-directory-downloadable-blocks-panel__no-local",
      children: (0,external_wp_i18n_namespaceObject.__)('No results available from your installed blocks.')
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "block-editor-inserter__quick-inserter-separator"
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
      className: "block-directory-downloadable-blocks-panel",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
        className: "block-directory-downloadable-blocks-panel__header",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", {
          className: "block-directory-downloadable-blocks-panel__title",
          children: (0,external_wp_i18n_namespaceObject.__)('Available to install')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "block-directory-downloadable-blocks-panel__description",
          children: (0,external_wp_i18n_namespaceObject.__)('Select a block to install and add it to your post.')
        })]
      }), children]
    })]
  });
}
/* harmony default export */ const inserter_panel = (DownloadableBlocksInserterPanel);

;// ./packages/block-directory/build-module/components/downloadable-blocks-panel/no-results.js
/**
 * WordPress dependencies
 */



function DownloadableBlocksNoResults() {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "block-editor-inserter__no-results",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
        children: (0,external_wp_i18n_namespaceObject.__)('No results found.')
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "block-editor-inserter__tips",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Tip, {
        children: [(0,external_wp_i18n_namespaceObject.__)('Interested in creating your own block?'), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("br", {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.ExternalLink, {
          href: "https://developer.wordpress.org/block-editor/",
          children: [(0,external_wp_i18n_namespaceObject.__)('Get started here'), "."]
        })]
      })
    })]
  });
}
/* harmony default export */ const no_results = (DownloadableBlocksNoResults);

;// ./packages/block-directory/build-module/components/downloadable-blocks-panel/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





const downloadable_blocks_panel_EMPTY_ARRAY = [];
const useDownloadableBlocks = filterValue => (0,external_wp_data_namespaceObject.useSelect)(select => {
  const {
    getDownloadableBlocks,
    isRequestingDownloadableBlocks,
    getInstalledBlockTypes
  } = select(store);
  const hasPermission = select(external_wp_coreData_namespaceObject.store).canUser('read', 'block-directory/search');
  let downloadableBlocks = downloadable_blocks_panel_EMPTY_ARRAY;
  if (hasPermission) {
    downloadableBlocks = getDownloadableBlocks(filterValue);

    // Filter out blocks that are already installed.
    const installedBlockTypes = getInstalledBlockTypes();
    const installableBlocks = downloadableBlocks.filter(({
      name
    }) => {
      // Check if the block has just been installed, in which case it
      // should still show in the list to avoid suddenly disappearing.
      // `installedBlockTypes` only returns blocks stored in state
      // immediately after installation, not all installed blocks.
      const isJustInstalled = installedBlockTypes.some(blockType => blockType.name === name);
      const isPreviouslyInstalled = (0,external_wp_blocks_namespaceObject.getBlockType)(name);
      return isJustInstalled || !isPreviouslyInstalled;
    });

    // Keep identity of the `downloadableBlocks` array if nothing was filtered out
    if (installableBlocks.length !== downloadableBlocks.length) {
      downloadableBlocks = installableBlocks;
    }

    // Return identical empty array when there are no blocks
    if (downloadableBlocks.length === 0) {
      downloadableBlocks = downloadable_blocks_panel_EMPTY_ARRAY;
    }
  }
  return {
    hasPermission,
    downloadableBlocks,
    isLoading: isRequestingDownloadableBlocks(filterValue)
  };
}, [filterValue]);
function DownloadableBlocksPanel({
  onSelect,
  onHover,
  hasLocalBlocks,
  isTyping,
  filterValue
}) {
  const {
    hasPermission,
    downloadableBlocks,
    isLoading
  } = useDownloadableBlocks(filterValue);
  if (hasPermission === undefined || isLoading || isTyping) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
      children: [hasPermission && !hasLocalBlocks && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
          className: "block-directory-downloadable-blocks-panel__no-local",
          children: (0,external_wp_i18n_namespaceObject.__)('No results available from your installed blocks.')
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
          className: "block-editor-inserter__quick-inserter-separator"
        })]
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
        className: "block-directory-downloadable-blocks-panel has-blocks-loading",
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
      })]
    });
  }
  if (false === hasPermission) {
    if (!hasLocalBlocks) {
      return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(no_results, {});
    }
    return null;
  }
  if (downloadableBlocks.length === 0) {
    return hasLocalBlocks ? null : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(no_results, {});
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(inserter_panel, {
    downloadableItems: downloadableBlocks,
    hasLocalBlocks: hasLocalBlocks,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(downloadable_blocks_list, {
      items: downloadableBlocks,
      onSelect: onSelect,
      onHover: onHover
    })
  });
}

;// ./packages/block-directory/build-module/plugins/inserter-menu-downloadable-blocks-panel/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function InserterMenuDownloadableBlocksPanel() {
  const [debouncedFilterValue, setFilterValue] = (0,external_wp_element_namespaceObject.useState)('');
  const debouncedSetFilterValue = (0,external_wp_compose_namespaceObject.debounce)(setFilterValue, 400);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.__unstableInserterMenuExtension, {
    children: ({
      onSelect,
      onHover,
      filterValue,
      hasItems
    }) => {
      if (debouncedFilterValue !== filterValue) {
        debouncedSetFilterValue(filterValue);
      }
      if (!debouncedFilterValue) {
        return null;
      }
      return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(DownloadableBlocksPanel, {
        onSelect: onSelect,
        onHover: onHover,
        filterValue: debouncedFilterValue,
        hasLocalBlocks: hasItems,
        isTyping: filterValue !== debouncedFilterValue
      });
    }
  });
}
/* harmony default export */ const inserter_menu_downloadable_blocks_panel = (InserterMenuDownloadableBlocksPanel);

;// ./packages/block-directory/build-module/components/compact-list/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function CompactList({
  items
}) {
  if (!items.length) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("ul", {
    className: "block-directory-compact-list",
    children: items.map(({
      icon,
      id,
      title,
      author
    }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("li", {
      className: "block-directory-compact-list__item",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(downloadable_block_icon, {
        icon: icon,
        title: title
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
        className: "block-directory-compact-list__item-details",
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
          className: "block-directory-compact-list__item-title",
          children: title
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
          className: "block-directory-compact-list__item-author",
          children: (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %s: Name of the block author. */
          (0,external_wp_i18n_namespaceObject.__)('By %s'), author)
        })]
      })]
    }, id))
  });
}

;// ./packages/block-directory/build-module/plugins/installed-blocks-pre-publish-panel/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function InstalledBlocksPrePublishPanel() {
  const newBlockTypes = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getNewBlockTypes(), []);
  if (!newBlockTypes.length) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_editor_namespaceObject.PluginPrePublishPanel, {
    title: (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %d: number of blocks (number).
    (0,external_wp_i18n_namespaceObject._n)('Added: %d block', 'Added: %d blocks', newBlockTypes.length), newBlockTypes.length),
    initialOpen: true,
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      className: "installed-blocks-pre-publish-panel__copy",
      children: (0,external_wp_i18n_namespaceObject._n)('The following block has been added to your site.', 'The following blocks have been added to your site.', newBlockTypes.length)
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(CompactList, {
      items: newBlockTypes
    })]
  });
}

;// ./packages/block-directory/build-module/plugins/get-install-missing/install-button.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function InstallButton({
  attributes,
  block,
  clientId
}) {
  const isInstallingBlock = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).isInstalling(block.id), [block.id]);
  const {
    installBlockType
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    replaceBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
    __next40pxDefaultSize: true,
    onClick: () => installBlockType(block).then(success => {
      if (success) {
        const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(block.name);
        const [originalBlock] = (0,external_wp_blocks_namespaceObject.parse)(attributes.originalContent);
        if (originalBlock && blockType) {
          replaceBlock(clientId, (0,external_wp_blocks_namespaceObject.createBlock)(blockType.name, originalBlock.attributes, originalBlock.innerBlocks));
        }
      }
    }),
    accessibleWhenDisabled: true,
    disabled: isInstallingBlock,
    isBusy: isInstallingBlock,
    variant: "primary",
    children: (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %s: block name */
    (0,external_wp_i18n_namespaceObject.__)('Install %s'), block.title)
  });
}

;// ./packages/block-directory/build-module/plugins/get-install-missing/index.js
/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */



const getInstallMissing = OriginalComponent => props => {
  const {
    originalName
  } = props.attributes;
  // Disable reason: This is a valid component, but it's mistaken for a callback.
  // eslint-disable-next-line react-hooks/rules-of-hooks
  const {
    block,
    hasPermission
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getDownloadableBlocks
    } = select(store);
    const blocks = getDownloadableBlocks('block:' + originalName).filter(({
      name
    }) => originalName === name);
    return {
      hasPermission: select(external_wp_coreData_namespaceObject.store).canUser('read', 'block-directory/search'),
      block: blocks.length && blocks[0]
    };
  }, [originalName]);

  // The user can't install blocks, or the block isn't available for download.
  if (!hasPermission || !block) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(OriginalComponent, {
      ...props
    });
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ModifiedWarning, {
    ...props,
    originalBlock: block
  });
};
const ModifiedWarning = ({
  originalBlock,
  ...props
}) => {
  const {
    originalName,
    originalUndelimitedContent,
    clientId
  } = props.attributes;
  const {
    replaceBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const convertToHTML = () => {
    replaceBlock(props.clientId, (0,external_wp_blocks_namespaceObject.createBlock)('core/html', {
      content: originalUndelimitedContent
    }));
  };
  const hasContent = !!originalUndelimitedContent;
  const hasHTMLBlock = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      canInsertBlockType,
      getBlockRootClientId
    } = select(external_wp_blockEditor_namespaceObject.store);
    return canInsertBlockType('core/html', getBlockRootClientId(clientId));
  }, [clientId]);
  let messageHTML = (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %s: block name */
  (0,external_wp_i18n_namespaceObject.__)('Your site doesn’t include support for the %s block. You can try installing the block or remove it entirely.'), originalBlock.title || originalName);
  const actions = [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(InstallButton, {
    block: originalBlock,
    attributes: props.attributes,
    clientId: props.clientId
  }, "install")];
  if (hasContent && hasHTMLBlock) {
    messageHTML = (0,external_wp_i18n_namespaceObject.sprintf)(/* translators: %s: block name */
    (0,external_wp_i18n_namespaceObject.__)('Your site doesn’t include support for the %s block. You can try installing the block, convert it to a Custom HTML block, or remove it entirely.'), originalBlock.title || originalName);
    actions.push(/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
      __next40pxDefaultSize: true,
      onClick: convertToHTML,
      variant: "tertiary",
      children: (0,external_wp_i18n_namespaceObject.__)('Keep as HTML')
    }, "convert"));
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    ...(0,external_wp_blockEditor_namespaceObject.useBlockProps)(),
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.Warning, {
      actions: actions,
      children: messageHTML
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.RawHTML, {
      children: originalUndelimitedContent
    })]
  });
};
/* harmony default export */ const get_install_missing = (getInstallMissing);

;// ./packages/block-directory/build-module/plugins/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */





(0,external_wp_plugins_namespaceObject.registerPlugin)('block-directory', {
  // The icon is explicitly set to undefined to prevent PluginPrePublishPanel
  // from rendering the fallback icon pluginIcon.
  icon: undefined,
  render() {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(AutoBlockUninstaller, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(inserter_menu_downloadable_blocks_panel, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(InstalledBlocksPrePublishPanel, {})]
    });
  }
});
(0,external_wp_hooks_namespaceObject.addFilter)('blocks.registerBlockType', 'block-directory/fallback', (settings, name) => {
  if (name !== 'core/missing') {
    return settings;
  }
  settings.edit = get_install_missing(settings.edit);
  return settings;
});

;// ./packages/block-directory/build-module/index.js
/**
 * Internal dependencies
 */



(window.wp = window.wp || {}).blockDirectory = __webpack_exports__;
/******/ })()
;