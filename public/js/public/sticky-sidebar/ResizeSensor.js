!function(){var e=function(t,i){function s(){var e,t;this.q=[],this.add=function(e){this.q.push(e)},this.call=function(){for(e=0,t=this.q.length;e<t;e++)this.q[e].call()}}function o(e,t){if(e.resizedAttached){if(e.resizedAttached)return void e.resizedAttached.add(t)}else e.resizedAttached=new s,e.resizedAttached.add(t);e.resizeSensor=document.createElement("div"),e.resizeSensor.className="resize-sensor";var i="position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; z-index: -1; visibility: hidden;",o="position: absolute; left: 0; top: 0; transition: 0s;";e.resizeSensor.style.cssText=i,e.resizeSensor.innerHTML='<div class="resize-sensor-expand" style="'+i+'"><div style="'+o+'"></div></div><div class="resize-sensor-shrink" style="'+i+'"><div style="'+o+' width: 200%; height: 200%"></div></div>',e.appendChild(e.resizeSensor),{fixed:1,absolute:1}[function(e,t){return e.currentStyle?e.currentStyle[t]:window.getComputedStyle?window.getComputedStyle(e,null).getPropertyValue(t):e.style[t]}(e,"position")]||(e.style.position="relative");var n,r,d=e.resizeSensor.childNodes[0],l=d.childNodes[0],c=e.resizeSensor.childNodes[1],h=(c.childNodes[0],function(){l.style.width=d.offsetWidth+10+"px",l.style.height=d.offsetHeight+10+"px",d.scrollLeft=d.scrollWidth,d.scrollTop=d.scrollHeight,c.scrollLeft=c.scrollWidth,c.scrollTop=c.scrollHeight,n=e.offsetWidth,r=e.offsetHeight});h();var a=function(e,t,i){e.attachEvent?e.attachEvent("on"+t,i):e.addEventListener(t,i)},f=function(){e.offsetWidth==n&&e.offsetHeight==r||e.resizedAttached&&e.resizedAttached.call(),h()};a(d,"scroll",f),a(c,"scroll",f)}var n=Object.prototype.toString.call(t),r="[object Array]"===n||"[object NodeList]"===n||"[object HTMLCollection]"===n||"undefined"!=typeof jQuery&&t instanceof jQuery||"undefined"!=typeof Elements&&t instanceof Elements;if(r)for(var d=0,l=t.length;d<l;d++)o(t[d],i);else o(t,i);this.detach=function(){if(r)for(var i=0,s=t.length;i<s;i++)e.detach(t[i]);else e.detach(t)}};e.detach=function(e){e.resizeSensor&&(e.removeChild(e.resizeSensor),delete e.resizeSensor,delete e.resizedAttached)},"undefined"!=typeof module&&void 0!==module.exports?module.exports=e:window.ResizeSensor=e}();
