"use strict";

class claraConfigurator {
  constructor(config) {
    this.api                = null;
    this.claraSceneId       = null;
    this.playerDivId        = null;
    this.configuratorDivId  = null;
    this.configuratorForm   = null;

    if (config) {
      // init variables
      this.playerDivId          = config.playerDivId;
      this.claraSceneId         = config.claraSceneId;
      this.configuratorDivId    = config.configuratorDivId;
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

          //api.on('configurationChange', onConfigurationChange({ api }));
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
}

(function() {
  var opts = {
    playerDivId       : 'clara-player',
    claraSceneId      : php_vars.clarauuid,
    configuratorDivId : document.getElementById('panel-embed')
  };
  var cc = new claraConfigurator(opts);

}());
