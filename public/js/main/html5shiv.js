!function(e,t){function n(){var e=f.elements;return"string"==typeof e?e.split(" "):e}function a(e){var t=u[e[h]];return t||(t={},d++,e[h]=d,u[d]=t),t}function r(e,n,r){return n||(n=t),i?n.createElement(e):(r||(r=a(n)),(n=r.cache[e]?r.cache[e].cloneNode():s.test(e)?(r.cache[e]=r.createElem(e)).cloneNode():r.createElem(e)).canHaveChildren&&!m.test(e)?r.frag.appendChild(n):n)}function c(e){e||(e=t);var c=a(e);if(f.shivCSS&&!o&&!c.hasCSS){var l,m=e;l=m.createElement("p"),m=m.getElementsByTagName("head")[0]||m.documentElement,l.innerHTML="x<style>article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}</style>",l=m.insertBefore(l.lastChild,m.firstChild),c.hasCSS=!!l}return i||function(e,t){t.cache||(t.cache={},t.createElem=e.createElement,t.createFrag=e.createDocumentFragment,t.frag=t.createFrag()),e.createElement=function(n){return f.shivMethods?r(n,e,t):t.createElem(n)},e.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+n().join().replace(/[\w\-]+/g,function(e){return t.createElem(e),t.frag.createElement(e),'c("'+e+'")'})+");return n}")(f,t.frag)}(e,c),e}var o,i,l=e.html5||{},m=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,s=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,h="_html5shiv",d=0,u={};!function(){try{var e,n=t.createElement("a");if(n.innerHTML="<xyz></xyz>",o="hidden"in n,!(e=1==n.childNodes.length)){t.createElement("a");var a=t.createDocumentFragment();e=void 0===a.cloneNode||void 0===a.createDocumentFragment||void 0===a.createElement}i=e}catch(e){i=o=!0}}();var f={elements:l.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",version:"3.7.0",shivCSS:!1!==l.shivCSS,supportsUnknownElements:i,shivMethods:!1!==l.shivMethods,type:"default",shivDocument:c,createElement:r,createDocumentFragment:function(e,r){if(e||(e=t),i)return e.createDocumentFragment();for(var c=(r=r||a(e)).frag.cloneNode(),o=0,l=n(),m=l.length;o<m;o++)c.createElement(l[o]);return c}};e.html5=f,c(t)}(this,document);