(function() {
  "use strict";

  var api = claraplayer('clara-player');
  var claraSceneId = php_vars.clarauuid;
  var configuratorEl = document.getElementById('panel-embed');
  var configuratorForm = "All";

  // Fetch and initialize the sceneId
  api.sceneIO
    .fetchAndUse(claraSceneId, null, { waitForPublish: true })
    .then(() => {

      if (configuratorForm && configuratorEl) {
        api.configuration.initConfigurator({
          id: claraSceneId,
          form: configuratorForm,
          el: configuratorEl
        });

        //api.on('configurationChange', onConfigurationChange({ api }));
      }
    });

}());
