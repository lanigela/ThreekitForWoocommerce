"use strict";

class claraConfigurator {
  constructor(config) {
    this.api                    = null;
    this.claraSceneId           = null;
    this.playerDivId            = null;
    this.configuratorDivId      = null;
    this.configuratorInputDivId = null;
    this.configuratorForm       = null;
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
    var config = this.api.configuration.getConfiguration();
    console.log(config);

    var configuratorInputDiv = document.getElementById(this.configuratorInputDivId);
    if (!configuratorInputDiv) {
      return;
    }

  }
}

(function() {
  var opts = {
    playerDivId           : 'clara-player',
    configuratorDivId     : 'panel-embed',
    configuratorInputDivId: 'threekit-add-to-cart-inputs',
    claraSceneId          : php_vars.clarauuid,
    available_attributes  : php_vars.available_attributes,
    attributes            : php_vars.attributes
  };
  var cc = new claraConfigurator(opts);

}());
