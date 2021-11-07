/*eslint-disable */
/* jscs:disable */

function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass);
}

function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; };
    return _setPrototypeOf(o, p);
}

define([
    "jquery",
    "knockout",
    "mage/translate",
    "Magento_PageBuilder/js/widget-initializer",
    "mageUtils",
    "underscore",
    "Magento_PageBuilder/js/config",
    "Magento_PageBuilder/js/content-type-menu/hide-show-option",
    "Magento_PageBuilder/js/content-type/style-registry",
    "Magento_PageBuilder/js/utils/object",
    "Magento_PageBuilder/js/content-type/preview"
], function (
    _jquery,
    _knockout,
    _translate,
    _widgetInitializer,
    _mageUtils,
    _underscore,
    _config,
    _hideShowOption,
    _styleRegistry,
    _object,
    _preview
) {

  /**
   * @api
   */
  var Preview = /*#__PURE__*/function (_preview2) {
    "use strict";

    _inheritsLoose(Preview, _preview2);

    /**
     * @inheritdoc
     */
    function Preview(contentType, config, observableUpdater) {
      var _this;

      _this = _preview2.call(this, contentType, config, observableUpdater) || this;
      _this.displayingWidgetPreview = _knockout.observable(false);
      _this.loading = _knockout.observable(false);
      _this.messages = {
        NOT_SELECTED: (0, _translate)("Widget isn't configured"),
        UNKNOWN_ERROR: (0, _translate)("An unknown error occurred. Please try again.")
      };
      _this.placeholderText = _knockout.observable(_this.messages.NOT_SELECTED);
      return _this;
    }
    /**
     * Return an array of options
     *
     * @returns {OptionsInterface}
     */
    var _proto = Preview.prototype;

    _proto.retrieveOptions = function retrieveOptions() {
      var options = _preview2.prototype.retrieveOptions.call(this);

      options.hideShow = new _hideShowOption({
        preview: this,
        icon: _hideShowOption.showIcon,
        title: _hideShowOption.showText,
        action: this.onOptionVisibilityToggle,
        classes: ["hide-show-content-type"],
        sort: 40
      });
      return options;
    }
    /**
     * Runs the widget initializer for each configured widget
     */
    ;
    _proto.initializeWidgets = function initializeWidgets(element) {
      if (element) {
        this.element = element;
        (0, _widgetInitializer)({
          config: _config.getConfig("widgets"),
          breakpoints: _config.getConfig("breakpoints"),
          currentViewport: _config.getConfig("viewport")
        }, element);
      }
    }
    /**
     * Updates the view state using the data provided
     * @param {DataObject} data
     */
    ;
    _proto.processData = function processData(data) {
      // Only load if something changed
      this.displayPreviewPlaceholder(data, "html");

      if (data.template.length !== 0) {
        this.processRequest(data, "html", "title");
      }
    }
    /**
     * @inheritdoc
     */
    ;
    _proto.afterObservablesUpdated = function afterObservablesUpdated() {
      _preview2.prototype.afterObservablesUpdated.call(this);

      var data = this.contentType.dataStore.getState(); // Only load if something changed

      this.processData(data);
    }
    /**
     * Display preview placeholder
     *
     * @param {DataObject} data
     * @param {string} identifierName
     */
    ;
    _proto.displayPreviewPlaceholder = function displayPreviewPlaceholder(data, identifierName) {
      var identifier = (0, _object.get)(data, identifierName); // Only load if something changed

      if (this.lastIdentifier === identifier && this.lastTemplate === data.template) {
        // The mass converter will have transformed the HTML property into a directive
        if (this.lastRenderedHtml) {
          this.data.main.html(this.lastRenderedHtml);
          this.showWidgetPreview(true);
          this.initializeWidgets(this.element);
        }
      } else {
        this.showWidgetPreview(false);
        this.placeholderText("");
      }

      if (!identifier || identifier && identifier.toString().length === 0 || data.template.length === 0) {
        this.showWidgetPreview(false);
        this.placeholderText(this.messages.NOT_SELECTED);
        return;
      }
    }
    /**
     *
     * @param {DataObject} data
     * @param {string} identifierName
     * @param {string} labelKey
     */
    ;
    _proto.processRequest = function processRequest(data, identifierName, labelKey) {
      var _this2 = this;

      var url = _config.getConfig("preview_url");

      var identifier = (0, _object.get)(data, identifierName);
      var requestConfig = {
        // Prevent caching
        method: "POST",
        data: {
          role: this.config.name,
          identifier: identifier,
          directive: this.data.main.html()
        }
      };
      this.loading(true); // Retrieve a state object representing the widget from the preview controller and process it on the stage

      _jquery.ajax(url, requestConfig) // The state object will contain the widget name and either html or a message why there isn't any.
      .done(function (response) {
        // Empty content means something bad happened in the controller that didn't trigger a 5xx
        if (typeof response.data !== "object") {
          _this2.showWidgetPreview(false);

          _this2.placeholderText(_this2.messages.UNKNOWN_ERROR);

          return;
        } // Update the stage content type label with the real widget title if provided


        _this2.displayLabel(response.data[labelKey] ? response.data[labelKey] : _this2.config.label);

        var content = "";

        if (response.data.content) {
          _this2.showWidgetPreview(true);

          content = _this2.processContent(response.data.content);

          _this2.data.main.html(content);

          _this2.initializeWidgets(_this2.element);
        } else if (response.data.error) {
          _this2.showWidgetPreview(false);

          _this2.placeholderText(response.data.error);
        }

        _this2.lastIdentifier = parseInt(identifier.toString(), 10);
        _this2.lastTemplate = data.template.toString();
        _this2.lastRenderedHtml = content;
      }).fail(function () {
        _this2.showWidgetPreview(false);

        _this2.placeholderText(_this2.messages.UNKNOWN_ERROR);
      }).always(function () {
        _this2.loading(false);
      });
    }
    /**
     * Toggle display of widget preview.  If showing widget preview, add hidden mode to PB preview.
     * @param {boolean} isShow
     */
    ;
    _proto.showWidgetPreview = function showWidgetPreview(isShow) {
      this.displayingWidgetPreview(isShow);
    }
    /**
     * Adapt content to view it on stage.
     *
     * @param content
     */
    ;
    _proto.processContent = function processContent(content) {
      var processedContent = this.processBackgroundImages(content);
      processedContent = this.processBreakpointStyles(processedContent);
      return processedContent;
    }
    /**
     * Generate styles for background images.
     *
     * @param {string} content
     * @return string
     */
    ;
    _proto.processBackgroundImages = function processBackgroundImages(content) {
      var document = new DOMParser().parseFromString(content, "text/html");
      var elements = document.querySelectorAll("[data-background-images]");
      var styleWidget = document.createElement("style");

      var viewports = _config.getConfig("viewports");

      elements.forEach(function (element) {
        var rawAttrValue = element.getAttribute("data-background-images").replace(/\\(.)/mg, "$1");
        var attrValue = JSON.parse(rawAttrValue);

        var elementClass = "background-image-" + _mageUtils.uniqueid(13);

        var rules = "";
        Object.keys(attrValue).forEach(function (imageName) {
          var imageUrl = attrValue[imageName];
          var viewportName = imageName.replace("_image", "");

          if (viewports[viewportName].stage && imageUrl) {
            rules += "." + viewportName + "-viewport ." + elementClass + " {\n                            background-image: url(\"" + imageUrl + "\");\n                        }";
          }
        });

        if (rules.length) {
          styleWidget.append(rules);
          element.classList.add(elementClass);
        }
      });

      if (elements.length && styleWidget.innerText.length) {
        document.body.append(styleWidget);
        content = document.head.innerHTML + document.body.innerHTML;
      }

      return content;
    }
    /**
     * Replace media queries with viewport classes.
     *
     * @param {string} content
     * @return string
     */
    ;

    _proto.processBreakpointStyles = function processBreakpointStyles(content) {
      var document = new DOMParser().parseFromString(content, "text/html");
      var styleWidgets = document.querySelectorAll("style");
      var mediaStyleWidget = document.createElement("style");

      var viewports = _config.getConfig("viewports");

      styleWidgets.forEach(function (styleWidget) {
        var cssRules = styleWidget.sheet.cssRules;
        Array.from(cssRules).forEach(function (rule) {
          var mediaScope = rule instanceof CSSMediaRule && _underscore.findKey(viewports, function (viewport) {
            return rule.conditionText === viewport.media;
          });

          if (mediaScope) {
            Array.from(rule.cssRules).forEach(function (mediaRule, index) {
              if (mediaRule.selectorText.indexOf(_styleRegistry.pbStyleAttribute) !== -1) {
                var searchPattern = new RegExp(_config.getConfig("bodyId") + " ", "g");
                var replaceValue = _config.getConfig("bodyId") + " ." + mediaScope + "-viewport ";
                var selector = mediaRule.selectorText.replace(searchPattern, replaceValue);
                mediaStyleWidget.append(selector + " {" + mediaRule.style.cssText + "}");
              }
            });
          }
        });
      });

      if (mediaStyleWidget.innerText.length) {
        document.body.append(mediaStyleWidget);
        content = document.head.innerHTML + document.body.innerHTML;
      }

      return content;
    };

    return Preview;
  }(_preview);

  return Preview;
});
//# sourceMappingURL=preview.js.map
