/**
 * Form Editors
 */

'use strict';

(function () {
  // Snow Theme
  // --------------------------------------------------------------------
  const snowEditor = new Quill('#message-editor', {
    bounds: '#snow-editor',
    modules: {
      formula: true,
      toolbar: '#snow-toolbar'
    },
    theme: 'snow'
  });
})();
