/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
  actions: () => (/* binding */ actions),
  addAction: () => (/* binding */ addAction),
  addFilter: () => (/* binding */ addFilter),
  applyFilters: () => (/* binding */ applyFilters),
  applyFiltersAsync: () => (/* binding */ applyFiltersAsync),
  createHooks: () => (/* reexport */ build_module_createHooks),
  currentAction: () => (/* binding */ currentAction),
  currentFilter: () => (/* binding */ currentFilter),
  defaultHooks: () => (/* binding */ defaultHooks),
  didAction: () => (/* binding */ didAction),
  didFilter: () => (/* binding */ didFilter),
  doAction: () => (/* binding */ doAction),
  doActionAsync: () => (/* binding */ doActionAsync),
  doingAction: () => (/* binding */ doingAction),
  doingFilter: () => (/* binding */ doingFilter),
  filters: () => (/* binding */ filters),
  hasAction: () => (/* binding */ hasAction),
  hasFilter: () => (/* binding */ hasFilter),
  removeAction: () => (/* binding */ removeAction),
  removeAllActions: () => (/* binding */ removeAllActions),
  removeAllFilters: () => (/* binding */ removeAllFilters),
  removeFilter: () => (/* binding */ removeFilter)
});

;// ./packages/hooks/build-module/validateNamespace.js
/**
 * Validate a namespace string.
 *
 * @param namespace The namespace to validate - should take the form
 *                  `vendor/plugin/function`.
 *
 * @return Whether the namespace is valid.
 */
function validateNamespace(namespace) {
  if ('string' !== typeof namespace || '' === namespace) {
    // eslint-disable-next-line no-console
    console.error('The namespace must be a non-empty string.');
    return false;
  }
  if (!/^[a-zA-Z][a-zA-Z0-9_.\-\/]*$/.test(namespace)) {
    // eslint-disable-next-line no-console
    console.error('The namespace can only contain numbers, letters, dashes, periods, underscores and slashes.');
    return false;
  }
  return true;
}
/* harmony default export */ const build_module_validateNamespace = (validateNamespace);

;// ./packages/hooks/build-module/validateHookName.js
/**
 * Validate a hookName string.
 *
 * @param hookName The hook name to validate. Should be a non empty string containing
 *                 only numbers, letters, dashes, periods and underscores. Also,
 *                 the hook name cannot begin with `__`.
 *
 * @return Whether the hook name is valid.
 */
function validateHookName(hookName) {
  if ('string' !== typeof hookName || '' === hookName) {
    // eslint-disable-next-line no-console
    console.error('The hook name must be a non-empty string.');
    return false;
  }
  if (/^__/.test(hookName)) {
    // eslint-disable-next-line no-console
    console.error('The hook name cannot begin with `__`.');
    return false;
  }
  if (!/^[a-zA-Z][a-zA-Z0-9_.-]*$/.test(hookName)) {
    // eslint-disable-next-line no-console
    console.error('The hook name can only contain numbers, letters, dashes, periods and underscores.');
    return false;
  }
  return true;
}
/* harmony default export */ const build_module_validateHookName = (validateHookName);

;// ./packages/hooks/build-module/createAddHook.js
/**
 * Internal dependencies
 */



/**
 *
 * Adds the hook to the appropriate hooks container.
 */

/**
 * Returns a function which, when invoked, will add a hook.
 *
 * @param hooks    Hooks instance.
 * @param storeKey
 *
 * @return  Function that adds a new hook.
 */
function createAddHook(hooks, storeKey) {
  return function addHook(hookName, namespace, callback, priority = 10) {
    const hooksStore = hooks[storeKey];
    if (!build_module_validateHookName(hookName)) {
      return;
    }
    if (!build_module_validateNamespace(namespace)) {
      return;
    }
    if ('function' !== typeof callback) {
      // eslint-disable-next-line no-console
      console.error('The hook callback must be a function.');
      return;
    }

    // Validate numeric priority
    if ('number' !== typeof priority) {
      // eslint-disable-next-line no-console
      console.error('If specified, the hook priority must be a number.');
      return;
    }
    const handler = {
      callback,
      priority,
      namespace
    };
    if (hooksStore[hookName]) {
      // Find the correct insert index of the new hook.
      const handlers = hooksStore[hookName].handlers;
      let i;
      for (i = handlers.length; i > 0; i--) {
        if (priority >= handlers[i - 1].priority) {
          break;
        }
      }
      if (i === handlers.length) {
        // If append, operate via direct assignment.
        handlers[i] = handler;
      } else {
        // Otherwise, insert before index via splice.
        handlers.splice(i, 0, handler);
      }

      // We may also be currently executing this hook.  If the callback
      // we're adding would come after the current callback, there's no
      // problem; otherwise we need to increase the execution index of
      // any other runs by 1 to account for the added element.
      hooksStore.__current.forEach(hookInfo => {
        if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
          hookInfo.currentIndex++;
        }
      });
    } else {
      // This is the first hook of its type.
      hooksStore[hookName] = {
        handlers: [handler],
        runs: 0
      };
    }
    if (hookName !== 'hookAdded') {
      hooks.doAction('hookAdded', hookName, namespace, callback, priority);
    }
  };
}
/* harmony default export */ const build_module_createAddHook = (createAddHook);

;// ./packages/hooks/build-module/createRemoveHook.js
/**
 * Internal dependencies
 */



/**
 * Removes the specified callback (or all callbacks) from the hook with a given hookName
 * and namespace.
 */

/**
 * Returns a function which, when invoked, will remove a specified hook or all
 * hooks by the given name.
 *
 * @param hooks             Hooks instance.
 * @param storeKey
 * @param [removeAll=false] Whether to remove all callbacks for a hookName,
 *                          without regard to namespace. Used to create
 *                          `removeAll*` functions.
 *
 * @return Function that removes hooks.
 */
function createRemoveHook(hooks, storeKey, removeAll = false) {
  return function removeHook(hookName, namespace) {
    const hooksStore = hooks[storeKey];
    if (!build_module_validateHookName(hookName)) {
      return;
    }
    if (!removeAll && !build_module_validateNamespace(namespace)) {
      return;
    }

    // Bail if no hooks exist by this name.
    if (!hooksStore[hookName]) {
      return 0;
    }
    let handlersRemoved = 0;
    if (removeAll) {
      handlersRemoved = hooksStore[hookName].handlers.length;
      hooksStore[hookName] = {
        runs: hooksStore[hookName].runs,
        handlers: []
      };
    } else {
      // Try to find the specified callback to remove.
      const handlers = hooksStore[hookName].handlers;
      for (let i = handlers.length - 1; i >= 0; i--) {
        if (handlers[i].namespace === namespace) {
          handlers.splice(i, 1);
          handlersRemoved++;
          // This callback may also be part of a hook that is
          // currently executing.  If the callback we're removing
          // comes after the current callback, there's no problem;
          // otherwise we need to decrease the execution index of any
          // other runs by 1 to account for the removed element.
          hooksStore.__current.forEach(hookInfo => {
            if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
              hookInfo.currentIndex--;
            }
          });
        }
      }
    }
    if (hookName !== 'hookRemoved') {
      hooks.doAction('hookRemoved', hookName, namespace);
    }
    return handlersRemoved;
  };
}
/* harmony default export */ const build_module_createRemoveHook = (createRemoveHook);

;// ./packages/hooks/build-module/createHasHook.js
/**
 * Internal dependencies
 */

/**
 *
 * Returns whether any handlers are attached for the given hookName and optional namespace.
 */

/**
 * Returns a function which, when invoked, will return whether any handlers are
 * attached to a particular hook.
 *
 * @param hooks    Hooks instance.
 * @param storeKey
 *
 * @return  Function that returns whether any handlers are
 *                   attached to a particular hook and optional namespace.
 */
function createHasHook(hooks, storeKey) {
  return function hasHook(hookName, namespace) {
    const hooksStore = hooks[storeKey];

    // Use the namespace if provided.
    if ('undefined' !== typeof namespace) {
      return hookName in hooksStore && hooksStore[hookName].handlers.some(hook => hook.namespace === namespace);
    }
    return hookName in hooksStore;
  };
}
/* harmony default export */ const build_module_createHasHook = (createHasHook);

;// ./packages/hooks/build-module/createRunHook.js
/**
 * Internal dependencies
 */

/**
 * Returns a function which, when invoked, will execute all callbacks
 * registered to a hook of the specified type, optionally returning the final
 * value of the call chain.
 *
 * @param hooks          Hooks instance.
 * @param storeKey
 * @param returnFirstArg Whether each hook callback is expected to return its first argument.
 * @param async          Whether the hook callback should be run asynchronously
 *
 * @return Function that runs hook callbacks.
 */
function createRunHook(hooks, storeKey, returnFirstArg, async) {
  return function runHook(hookName, ...args) {
    const hooksStore = hooks[storeKey];
    if (!hooksStore[hookName]) {
      hooksStore[hookName] = {
        handlers: [],
        runs: 0
      };
    }
    hooksStore[hookName].runs++;
    const handlers = hooksStore[hookName].handlers;

    // The following code is stripped from production builds.
    if (false) {}
    if (!handlers || !handlers.length) {
      return returnFirstArg ? args[0] : undefined;
    }
    const hookInfo = {
      name: hookName,
      currentIndex: 0
    };
    async function asyncRunner() {
      try {
        hooksStore.__current.add(hookInfo);
        let result = returnFirstArg ? args[0] : undefined;
        while (hookInfo.currentIndex < handlers.length) {
          const handler = handlers[hookInfo.currentIndex];
          result = await handler.callback.apply(null, args);
          if (returnFirstArg) {
            args[0] = result;
          }
          hookInfo.currentIndex++;
        }
        return returnFirstArg ? result : undefined;
      } finally {
        hooksStore.__current.delete(hookInfo);
      }
    }
    function syncRunner() {
      try {
        hooksStore.__current.add(hookInfo);
        let result = returnFirstArg ? args[0] : undefined;
        while (hookInfo.currentIndex < handlers.length) {
          const handler = handlers[hookInfo.currentIndex];
          result = handler.callback.apply(null, args);
          if (returnFirstArg) {
            args[0] = result;
          }
          hookInfo.currentIndex++;
        }
        return returnFirstArg ? result : undefined;
      } finally {
        hooksStore.__current.delete(hookInfo);
      }
    }
    return (async ? asyncRunner : syncRunner)();
  };
}
/* harmony default export */ const build_module_createRunHook = (createRunHook);

;// ./packages/hooks/build-module/createCurrentHook.js
/**
 * Internal dependencies
 */

/**
 * Returns a function which, when invoked, will return the name of the
 * currently running hook, or `null` if no hook of the given type is currently
 * running.
 *
 * @param hooks    Hooks instance.
 * @param storeKey
 *
 * @return Function that returns the current hook name or null.
 */
function createCurrentHook(hooks, storeKey) {
  return function currentHook() {
    var _currentArray$at$name;
    const hooksStore = hooks[storeKey];
    const currentArray = Array.from(hooksStore.__current);
    return (_currentArray$at$name = currentArray.at(-1)?.name) !== null && _currentArray$at$name !== void 0 ? _currentArray$at$name : null;
  };
}
/* harmony default export */ const build_module_createCurrentHook = (createCurrentHook);

;// ./packages/hooks/build-module/createDoingHook.js
/**
 * Internal dependencies
 */

/**
 * Returns whether a hook is currently being executed.
 *
 */

/**
 * Returns a function which, when invoked, will return whether a hook is
 * currently being executed.
 *
 * @param hooks    Hooks instance.
 * @param storeKey
 *
 * @return Function that returns whether a hook is currently
 *                     being executed.
 */
function createDoingHook(hooks, storeKey) {
  return function doingHook(hookName) {
    const hooksStore = hooks[storeKey];

    // If the hookName was not passed, check for any current hook.
    if ('undefined' === typeof hookName) {
      return hooksStore.__current.size > 0;
    }

    // Find if the `hookName` hook is in `__current`.
    return Array.from(hooksStore.__current).some(hook => hook.name === hookName);
  };
}
/* harmony default export */ const build_module_createDoingHook = (createDoingHook);

;// ./packages/hooks/build-module/createDidHook.js
/**
 * Internal dependencies
 */


/**
 *
 * Returns the number of times an action has been fired.
 *
 */

/**
 * Returns a function which, when invoked, will return the number of times a
 * hook has been called.
 *
 * @param hooks    Hooks instance.
 * @param storeKey
 *
 * @return  Function that returns a hook's call count.
 */
function createDidHook(hooks, storeKey) {
  return function didHook(hookName) {
    const hooksStore = hooks[storeKey];
    if (!build_module_validateHookName(hookName)) {
      return;
    }
    return hooksStore[hookName] && hooksStore[hookName].runs ? hooksStore[hookName].runs : 0;
  };
}
/* harmony default export */ const build_module_createDidHook = (createDidHook);

;// ./packages/hooks/build-module/createHooks.js
/**
 * Internal dependencies
 */







/**
 * Internal class for constructing hooks. Use `createHooks()` function
 *
 * Note, it is necessary to expose this class to make its type public.
 *
 * @private
 */
class _Hooks {
  constructor() {
    this.actions = Object.create(null);
    this.actions.__current = new Set();
    this.filters = Object.create(null);
    this.filters.__current = new Set();
    this.addAction = build_module_createAddHook(this, 'actions');
    this.addFilter = build_module_createAddHook(this, 'filters');
    this.removeAction = build_module_createRemoveHook(this, 'actions');
    this.removeFilter = build_module_createRemoveHook(this, 'filters');
    this.hasAction = build_module_createHasHook(this, 'actions');
    this.hasFilter = build_module_createHasHook(this, 'filters');
    this.removeAllActions = build_module_createRemoveHook(this, 'actions', true);
    this.removeAllFilters = build_module_createRemoveHook(this, 'filters', true);
    this.doAction = build_module_createRunHook(this, 'actions', false, false);
    this.doActionAsync = build_module_createRunHook(this, 'actions', false, true);
    this.applyFilters = build_module_createRunHook(this, 'filters', true, false);
    this.applyFiltersAsync = build_module_createRunHook(this, 'filters', true, true);
    this.currentAction = build_module_createCurrentHook(this, 'actions');
    this.currentFilter = build_module_createCurrentHook(this, 'filters');
    this.doingAction = build_module_createDoingHook(this, 'actions');
    this.doingFilter = build_module_createDoingHook(this, 'filters');
    this.didAction = build_module_createDidHook(this, 'actions');
    this.didFilter = build_module_createDidHook(this, 'filters');
  }
}
/**
 * Returns an instance of the hooks object.
 *
 * @return A Hooks instance.
 */
function createHooks() {
  return new _Hooks();
}
/* harmony default export */ const build_module_createHooks = (createHooks);

;// ./packages/hooks/build-module/index.js
/**
 * Internal dependencies
 */


const defaultHooks = build_module_createHooks();
const {
  addAction,
  addFilter,
  removeAction,
  removeFilter,
  hasAction,
  hasFilter,
  removeAllActions,
  removeAllFilters,
  doAction,
  doActionAsync,
  applyFilters,
  applyFiltersAsync,
  currentAction,
  currentFilter,
  doingAction,
  doingFilter,
  didAction,
  didFilter,
  actions,
  filters
} = defaultHooks;


(window.wp = window.wp || {}).hooks = __webpack_exports__;
/******/ })()
;