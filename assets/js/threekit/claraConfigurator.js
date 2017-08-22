(function() {
  "use strict";

  var clara = claraplayer('clara-player');

  clara.on('loaded', function() {
    console.log('Clara player is loaded and ready');
  });

  // Fetch and initialize the sceneId
  clara.sceneIO.fetchAndUse(php_vars.clarauuid);

}());
