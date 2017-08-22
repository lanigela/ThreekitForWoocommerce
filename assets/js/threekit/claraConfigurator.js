(function() {
  "use strict";

  var clara = claraplayer('clara-player');
  var panelControl = document.getElementById('panelControl');
  var customDiv = document.getElementById('panel-embed');

  clara.on('loaded', function() {
    panelControl.onchange = changeHandler;

    clara.configuration.initConfigurator({
      el    : customDiv
    });
  });

  function changeHandler(ev) {
    clara.configuration.initConfigurator({
      el:  customDiv
    });
    customDiv.style.display = 'block';
  }

  // Fetch and initialize the sceneId
  clara.sceneIO.fetchAndUse(php_vars.clarauuid);

}());
