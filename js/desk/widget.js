!function () {
  'use strict';

  document.observe('dom:loaded', function() {
    var origin  = $('desk-widget-list') ? $('desk-widget-list').dataset.origin : ''
      , postMsg = function(method, args) {
        var msg = { method: method, arguments: args };
        if (window.parent != window.self) {
          window.parent.postMessage(JSON.stringify(msg), origin);
        } else {
          console.info('No iframe - origin: ' + origin + '\n' + JSON.stringify(msg, null, 4));
        }
      };

    document.on('click', '.desk-widget h3', function(evt, el) {
      el.up('.desk-widget').toggleClassName('collapsed');
      postMsg('resize', {
        height: document.body.parentNode.getHeight()
      });
    })

    document.on('click', 'a', function(evt, el) {
      Event.stop(evt);
      var method = el.dataset.method || 'modal'
        , args   = el.dataset.arg || { title: el.title, href: el.href }
        ;

      postMsg(method, args);
    })

    postMsg('resize', {
      height: document.body.parentNode.getHeight()
    });
  })

}()
