if(!self.define){let e,s={};const i=(i,r)=>(i=new URL(i+".js",r).href,s[i]||new Promise((s=>{if("document"in self){const e=document.createElement("script");e.src=i,e.onload=s,document.head.appendChild(e)}else e=i,importScripts(i),s()})).then((()=>{let e=s[i];if(!e)throw new Error(`Module ${i} didn’t register its module`);return e})));self.define=(r,t)=>{const n=e||("document"in self?document.currentScript.src:"")||location.href;if(s[n])return;let o={};const d=e=>i(e,n),c={module:{uri:n},exports:o,require:d};s[n]=Promise.all(r.map((e=>c[e]||d(e)))).then((e=>(t(...e),o)))}}define(["./workbox-5b385ed2"],(function(e){"use strict";e.setCacheNameDetails({prefix:"vue-app"}),self.addEventListener("message",(e=>{e.data&&"SKIP_WAITING"===e.data.type&&self.skipWaiting()})),e.precacheAndRoute([{url:"/js/vue-app/dist/css/app.css",revision:"164d80eaca504aaae89145ef4d7b25d4"},{url:"/js/vue-app/dist/index.html",revision:"9e8fafc5e3994f9f617945b2bf95aa97"},{url:"/js/vue-app/dist/js/app.js",revision:"c35ee6996573ddce5c3bdd1a7153e717"},{url:"/js/vue-app/dist/js/chunk-vendors.js",revision:"36d142773aebb12d2e01ccaf141029e9"},{url:"/js/vue-app/dist/manifest.json",revision:"a7bb829f05709f04c4cc8f0bb0289d89"}],{})}));
//# sourceMappingURL=service-worker.js.map