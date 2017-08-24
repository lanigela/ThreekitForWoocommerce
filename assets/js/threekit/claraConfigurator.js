"use strict";

class claraConfigurator {
  constructor(config) {
    this.api                    = null;
    this.claraSceneId           = null;
    this.playerDivId            = null;
    this.configuratorDivId      = null;
    this.configuratorInputDivId = null;
    this.configuratorForm       = null;
    this.addtocartClassName     = null;
    this.addtocartButton        = null;
    /*
    *
    */
    this.attributes           = null;
    /*
    *
    */
    this.available_attributes = null;

    if (config) {
      // init variables
      this.playerDivId            = config.playerDivId;
      this.configuratorDivId      = config.configuratorDivId;
      this.claraSceneId           = config.claraSceneId;
      this.attributes             = config.attributes;
      this.available_attributes   = config.available_attributes;
      this.configuratorInputDivId = config.configuratorInputDivId;
      this.addtocartClassName     = config.addtocartClassName;

      this.addtocartButton = document.getElementsByClassName(this.addtocartClassName)[0];
    }
    console.log(this.attributes);
    console.log(this.available_attributes);
    this._initClara();
  }

  _initClara() {
    var self = this;
    // Fetch and initialize the sceneId
    this.api = claraplayer(this.playerDivId);
    this.api.sceneIO
      .fetchAndUse(this.claraSceneId, null, { waitForPublish: true })
      .then(() => {

        // use the first form in the scene
        self._getConfiguratorForm();

        var configuratorEl = document.getElementById(self.configuratorDivId);

        if (self.configuratorForm && configuratorEl) {
          self.api.configuration.initConfigurator({
            id    : self.claraSceneId,
            form  : self.configuratorForm,
            el    : configuratorEl
          });

          self.api.on('configurationChange', (ev) => {
            self._onConfigurationChange();
          });
        }
      });
  }

  _getConfiguratorForm() {
    if (!this.api) {
      return;
    }
    var forms = this.api.configuration.getForms();
    if (forms.length > 0) {
      this.configuratorForm = forms[0].name;
    }
  }

  _onConfigurationChange() {
    var self = this;
    var config = this.api.configuration.getConfiguration();
    console.log(config);

    var configuratorInputDiv = document.getElementById(this.configuratorInputDivId);
    if (!configuratorInputDiv) {
      return;
    }

    // check if config attribute name and value exist for the current product
    var additionalAttrs = [];
    var attribute_keys = Object.keys(this.attributes);
    var config_keys = Object.keys(config);

    if (config_keys.length < attribute_keys.length) {
      console.warn("Threekit attribute number is smaller than in WooCommerce, product will not be able to added to cart");
    }
    for (var key in config) {
      // looking for "key" in attribute_keys
      var found = false;
      var legal = true;
      for (var ele in attribute_keys) {
        // remove "pa_"
        var trimEle = ele;
        if (trimEle.startsWith('pa_')) {
          trimEle = trimEle.substr(3);
        }
        if (self.ignoreCaseStrcmp(trimEle, key)) {
          found = true;
          if (!self.attributes[ele].includes(config[key])) {
            // config attribute value is illegal
            legal = false;
          }
          break;
        }
      }
      if (!legal) {
        self._disableAddtocartButton();
        return;
      }
      if (!found) {
        // additional attributes will be posted to server as text
        additionalAttrs.push(key);
      }
    }
    /*
    *  Check if the product is available
    *  Attributes in WooCommerce can be overlapping,
    *  using the first matching attribute in this.available_attributes
    */

    // calculate price

  }

  _disableAddtocartButton() {
    if (!this.addtocartButton) {
      return;
    }
    this.addtocartButton.classList.add('disabled');
  }

  _enableAddtocartButton() {
    if (!this.addtocartButton) {
      return;
    }
    this.addtocartButton.classList.remove('disabled');
  }

  /*
  * Utility functions
  */
  ignoreCaseStrcmp(str1, str2) {
    return str1.toLowerCase() === str2.toLowerCase();
  }
}

(function() {
  var opts = {
    addtocartClassName    : 'single_add_to_cart_button',
    playerDivId           : 'clara-player',
    configuratorDivId     : 'panel-embed',
    configuratorInputDivId: 'threekit-add-to-cart-inputs',
    claraSceneId          : php_vars.clarauuid,
    available_attributes  : php_vars.available_attributes,
    attributes            : php_vars.attributes
  };
  var cc = new claraConfigurator(opts);

}());
