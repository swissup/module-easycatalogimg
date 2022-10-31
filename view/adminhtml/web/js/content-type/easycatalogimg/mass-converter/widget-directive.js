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
    "Magento_PageBuilder/js/mass-converter/widget-directive-abstract",
    "Magento_PageBuilder/js/utils/object"
    ],
    function (_widgetDirectiveAbstract, _object
    ) {

  /**
   * Enables the settings of the content type to be stored as a widget directive.
   *
   * @api
   */
  var WidgetDirective = /*#__PURE__*/function (_widgetDirectiveAbstr) {
    "use strict";

    _inheritsLoose(WidgetDirective, _widgetDirectiveAbstr);

    function WidgetDirective() {
      return _widgetDirectiveAbstr.apply(this, arguments) || this;
    }

    var _proto = WidgetDirective.prototype;

    /**
     * Convert value to internal format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    _proto.fromDom = function fromDom(data, config) {
      var attributes = _widgetDirectiveAbstr.prototype.fromDom.call(this, data, config);

      data.template = attributes.template;
      data.category_id = attributes.category_id;
      data.category_count = attributes.category_count;
      data.subcategory_count = attributes.subcategory_count;
      data.column_count = attributes.column_count;
      data.show_image = attributes.show_image;
      data.image_width = attributes.image_width;
      data.image_height = attributes.image_height;
      data.parent_category_position = attributes.parent_category_position;
      data.sizes = attributes.sizes;
      data.category_to_show = attributes.category_to_show;
      data.category_to_hide = attributes.category_to_hide;
      data.hide_when_filter_is_used = attributes.hide_when_filter_is_used;

      return data;
    }
    /**
     * Convert value to knockout format
     *
     * @param {object} data
     * @param {object} config
     * @returns {object}
     */
    ;

    _proto.toDom = function toDom(data, config) {
      var attributes = {
          type: "Swissup\\Easycatalogimg\\Block\\Widget\\SubcategoriesList",
          template: data.template,
          category_id: data.category_id,
          category_count: data.category_count,
          subcategory_count: data.subcategory_count,
          column_count: data.column_count,
          show_image: data.show_image,
          image_width: data.image_width,
          image_height: data.image_height,
          parent_category_position: data.parent_category_position,
          sizes: data.sizes,
          category_to_show: data.category_to_show,
          category_to_hide: data.category_to_hide,
          hide_when_filter_is_used: data.hide_when_filter_is_used
      };

      if (!attributes.category_id || !attributes.template) {
        return data;
      }

      (0, _object.set)(data, config.html_variable, this.buildDirective(attributes));
      return data;
    };

    return WidgetDirective;
  }(_widgetDirectiveAbstract);

  return WidgetDirective;
});
//# sourceMappingURL=widget-directive.js.map
