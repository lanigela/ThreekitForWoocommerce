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
    this.addtocartEnabled       = false;
    this.variation_idClassName  = null;
    this.variation_idInput      = null;
    this.variationInputId       = null;
    this.variationInputDiv      = null;
    this.priceId                = null;
    this.priceDiv               = null;
    /*
    *
    */
    this.attributes           = null;
    /*
    *
    */
    this.available_attributes = null;

    this.usingAddons          = false;

    if (config) {
      var self = this;
      // init variables
      this.playerDivId            = config.playerDivId;
      this.configuratorDivId      = config.configuratorDivId;
      this.claraSceneId           = config.claraSceneId;
      this.attributes             = config.attributes;
      this.available_attributes   = config.available_attributes;
      this.configuratorInputDivId = config.configuratorInputDivId;
      this.addtocartClassName     = config.addtocartClassName;
      this.variation_idClassName  = config.variation_idClassName;
      this.variationInputId       = config.variationInputId;
      this.priceId                = config.priceId;
      this.usingAddons            = config.usingAddons;

      this.variation_idInput = document.getElementsByClassName(this.variation_idClassName)[0];
      this.variationInputDiv = document.getElementById(this.variationInputId);
      this.addtocartButton = document.getElementsByClassName(this.addtocartClassName)[0];
      this.priceDiv = document.getElementById(this.priceId);

      if (this.addtocartButton) {
        this.addtocartButton.onclick = (ev) => {
          if (!self.addtocartEnabled) {
            event.preventDefault();
          }
        };
      }

    }

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

          if (self.usingAddons) {
            self.api.on('configurationChange', (ev) => {
              self._onConfigurationChangeAddon();
            });
          }
          else {
            self.api.on('configurationChange', (ev) => {
              self._onConfigurationChange();
            });
          }

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

  _onConfigurationChangeAddon() {
    var self = this;
    var config = this.api.configuration.getConfiguration();
    console.log(config);
    console.log(this.attributes);

    var configuratorInputDiv = document.getElementById(this.configuratorInputDivId);
    if (!configuratorInputDiv) {
      return;
    }

    var selectedVaration = {};

    for (var key in config) {
      // looking for key in attributes
      var nameFound = false;
      for (var ele in this.attributes) {
        nameFound = true;
        if (key === this.attributes[ele].name) {
          switch (this.attributes[ele].type) {
            case 'select':
              // looking for config[key] in attributes[ele].options
              var optionFound = false;
              for (var opt in this.attributes[ele].options) {
                if (config[key] === this.attributes[ele].options[opt].label) {
                  optionFound = true;
                  // add input to form
                  var tailNumber = opt + 1;
                  selectedVaration['addon-' + this.attributes[ele]['field-name']] = config[key].toLowerCase() + tailNumber;
                  // calculate price
                }
              }
              if ( !optionFound ) {
                console.warn("Option " + config[key] + " not found in " + key);
              }
            break;
            case 'checkbox':
              if (config[key]) {
                selectedVaration['addon-' + this.attributes[ele]['field-name'] + '[]'] = this.attributes[ele].options[0].label;
              }
            break;
            case 'custom':
              selectedVaration['addon-' + this.attributes[ele]['field-name'] + '[0]' = config[key];
            break;
          }
        }
      }
      if (!nameFound) {
        console.warn("Configuration " + key + " not found, unable to post to server");
      }
    }

    if (this.variationInputDiv.hasChildNodes()) {
      // remove all child
      while (this.variationInputDiv.firstChild) {
        this.variationInputDiv.removeChild(this.variationInputDiv.firstChild);
      }
    }

    for (var key in selectedVaration) {
      var keyInput = document.createElement('input');
      keyInput.setAttribute('name', key);
      keyInput.setAttribute('value', selectedVaration[key]);
      keyInput.setAttribute('type','hidden');
      this.variationInputDiv.appendChild(keyInput);
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
    var attrValueTobeSubmited = [];
    if (Object.keys(this.attributes).length < Object.keys(config).length) {
      console.warn("Threekit attribute number is smaller than in WooCommerce, product will not be able to added to cart");
    }
    for (var key in config) {
      // looking for "key" in attributes
      var found = false;
      var legal = true;
      for (var ele in this.attributes) {
        // remove "pa_"
        var trimEle = ele;
        if (trimEle.startsWith('pa_')) {
          trimEle = trimEle.substr(3);
        }
        if (this.ignoreCaseStrcmp(trimEle, key)) {
          found = true;
          if (!this.ignoreCaseIncludes(this.attributes[ele], config[key])) {
            // config attribute value is illegal
            legal = false;
          }
          break;
        }
      }
      if (!legal) {
        this._disableAddtocartButton();
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
    var foundMatch = false;
    var selectedVaration = {};
    for (var i = 0; i < this.available_attributes.length; i++) {
      var attrs = this.available_attributes[i].attributes;
      if (Object.keys(attrs).length != Object.keys(config).length) {
        continue;
      }
      var match = true;
      selectedVaration = {};
      for (var key in config) {
        var found = false;
        for(var ele in attrs) {
          // remove "attribute_pa_"
          var trimEle = ele;
          if (trimEle.startsWith('attribute_pa_')) {
            trimEle = trimEle.substr(13);
          }
          if (this.ignoreCaseStrcmp(trimEle, key)) {
            if (attrs[ele] === "" || this.ignoreCaseStrcmp(attrs[ele], config[key])) {
              found = true;
              selectedVaration[ele] = config[key].toLowerCase();
              break;
            }
          }
        }
        if (!found) {
          // matching fail
          match = false;
          break;
        }
      }
      if (match && this.available_attributes[i].is_in_stock && this.available_attributes[i].is_purchasable) {
        // find a match!
        foundMatch = true;

        this.priceDiv.innerHTML = '<span class="woocommerce-Price-currencySymbol">$</span>' + this.available_attributes[i].display_price;
        console.log(this.available_attributes[i].variation_id);
        this._enableAddtocartButton();
        this.variation_idInput.setAttribute('value', this.available_attributes[i].variation_id);

        if (this.variationInputDiv.hasChildNodes()) {
          // remove all child
          while (this.variationInputDiv.firstChild) {
            this.variationInputDiv.removeChild(this.variationInputDiv.firstChild);
          }
        }
        for (var key in selectedVaration) {
          var keyInput = document.createElement('input');
          keyInput.setAttribute('name', key);
          keyInput.setAttribute('value', selectedVaration[key]);
          keyInput.setAttribute('type','hidden');
          this.variationInputDiv.appendChild(keyInput);
        }
        break;
      }
    }
    if (!foundMatch) {
      this._disableAddtocartButton();
    }

    // calculate price

  }

  _disableAddtocartButton() {
    if (!this.addtocartButton) {
      return;
    }
    this.addtocartEnabled = false;
    this.addtocartButton.classList.add('disabled');
    if (this.priceDiv) {
      this.priceDiv.innerHTML = '<span class="woocommerce-Price-currencySymbol">$</span>';
    }
  }

  _enableAddtocartButton() {
    if (!this.addtocartButton) {
      return;
    }
    this.addtocartEnabled = true;
    this.addtocartButton.classList.remove('disabled');
  }

  /*
  * Utility functions
  */
  ignoreCaseStrcmp(str1, str2) {
    return str1.toLowerCase() === str2.toLowerCase();
  }
  ignoreCaseIncludes(arr, ele) {
    var self = this;
    for (var iter in arr) {
      if (self.ignoreCaseStrcmp(arr[iter], ele)) {
        return true;
      }
    }
    return false;
  }
}

(function() {
  var opts = {
    addtocartClassName    : 'single_add_to_cart_button',
    variation_idClassName : 'variation_id',
    variationInputId      : 'threekit-add-to-cart-inputs',
    playerDivId           : 'clara-player',
    configuratorDivId     : 'panel-embed',
    configuratorInputDivId: 'threekit-add-to-cart-inputs',
    priceId               : 'threekit_price',
    claraSceneId          : php_vars.clarauuid,
    available_attributes  : php_vars.available_attributes,
    attributes            : php_vars.attributes,
    usingAddons           : php_vars.usingAddons
  };
  var cc = new claraConfigurator(opts);

}());
