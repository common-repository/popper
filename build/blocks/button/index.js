(()=>{var e={967:(e,t)=>{var r;!function(){"use strict";var o={}.hasOwnProperty;function l(){for(var e="",t=0;t<arguments.length;t++){var r=arguments[t];r&&(e=a(e,n(r)))}return e}function n(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return l.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var r in e)o.call(e,r)&&e[r]&&(t=a(t,r));return t}function a(e,t){return t?e?e+" "+t:e+t:e}e.exports?(l.default=l,e.exports=l):void 0===(r=function(){return l}.apply(t,[]))||(e.exports=r)}()}},t={};function r(o){var l=t[o];if(void 0!==l)return l.exports;var n=t[o]={exports:{}};return e[o](n,n.exports,r),n.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.wp.blocks,t=window.React,o=window.wp.i18n,l=window.wp.blockEditor,n=window.wp.element;var a,s=r(967),p=r.n(s);function i(){return i=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},i.apply(this,arguments)}var c=function(e){return t.createElement("svg",i({viewBox:"0 0 16 16"},e),a||(a=t.createElement("path",{d:"M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414"})))};const u=window.wp.components,d=window.wp.primitives,m=window.ReactJSXRuntime,b=(0,m.jsx)(d.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,m.jsx)(d.Path,{d:"M5 5.5h8V4H5v1.5ZM5 20h8v-1.5H5V20ZM19 9H5v6h14V9Z"})}),g=(0,m.jsx)(d.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,m.jsx)(d.Path,{d:"M19 5.5H5V4h14v1.5ZM19 20H5v-1.5h14V20ZM7 9h10v6H7V9Z"})}),_=(0,m.jsx)(d.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,m.jsx)(d.Path,{d:"M19 5.5h-8V4h8v1.5ZM19 20h-8v-1.5h8V20ZM5 9h14v6H5V9Z"})}),v=[{icon:b,title:(0,o.__)("Align left"),align:"left"},{icon:g,title:(0,o.__)("Align center"),align:"center"},{icon:_,title:(0,o.__)("Align right"),align:"right"}],w=e=>{const{attributes:{position:r,isIcon:n,align:a},setAttributes:s}=e,p=[{label:(0,o.__)("Inside","popper"),value:"inside"},{label:(0,o.__)("Outside","popper"),value:"outside"},{label:(0,o.__)("Corner","popper"),value:"corner"}];return(0,t.createElement)(t.Fragment,null,(0,t.createElement)(l.BlockControls,null,"outside"===r&&(0,t.createElement)(u.ToolbarGroup,null,(0,t.createElement)(l.AlignmentControl,{alignmentControls:v,value:a||"right",onChange:e=>{s({align:e})}}))),(0,t.createElement)(u.PanelBody,{title:(0,o.__)("Settings","popper")},(0,t.createElement)(u.RadioControl,{label:(0,o.__)("Button position","popper"),onChange:e=>s({position:e}),selected:r,options:p}),(0,t.createElement)(u.RadioControl,{label:(0,o.__)("Button type","popper"),onChange:()=>{s({isIcon:!n})},selected:n,options:[{label:(0,o.__)("Icon","popper"),value:!0},{label:(0,o.__)("Text","popper"),value:!1}]})))},x=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"popper/button","category":"popper","ancestor":["popper/popup"],"title":"Close Button","icon":"dismiss","textdomain":"popper","attributes":{"text":{"type":"string","source":"text","selector":"button.wp-block-popper__close","default":"Close"},"align":{"type":"string","default":"right"},"isIcon":{"type":"boolean","selector":"svg","default":true},"position":{"type":"string"},"style":{"type":"object"}},"supports":{"html":true,"ariaLabel":true,"lock":false,"multiple":false,"reusable":false,"className":false,"color":{"background":true,"text":true,"gradients":true},"__experimentalBorder":{"color":true,"radius":true,"style":true,"width":true},"typography":{"fontSize":true,"lineHeight":true},"spacing":{"margin":true,"padding":true,"__experimentalDefaultControls":{"padding":true}}},"example":{"attributes":{"isIcon":true}},"styles":[{"name":"regular","label":"Default","isDefault":true},{"name":"rounded","label":"Rounded black"},{"name":"rounded-alt","label":"Rounded black alt"},{"name":"squared","label":"Squared black"},{"name":"squared-alt","label":"Squared black alt"}],"editorScript":"file:./index.js","style":"file:./style-index.css"}'),h=[{attributes:{lock:{type:"object",default:{move:!0,remove:!1}},text:{type:"string",source:"text",selector:"button.wp-block-popper__close",default:"Close"},isIcon:{type:"boolean",selector:"svg",default:!0},position:{type:"string"},style:{type:"object"}},supports:{html:!0,ariaLabel:!0,lock:!1,multiple:!1,reusable:!1,className:!1,color:{background:!0,text:!0,gradients:!0},__experimentalBorder:!0,typography:{fontSize:!0,lineHeight:!0},spacing:{margin:!0,padding:!0,__experimentalDefaultControls:{padding:!0}}},migrate:({openBehaviour:e,...t})=>(console.log(e),{...t,openBehaviour:[e]}),save({attributes:e,className:r}){const{text:n,position:a,isIcon:s,style:i}=e,c=i?.border?.radius,u=(0,l.__experimentalGetBorderClassesAndStyles)(e);"number"==typeof c&&(u.style.borderRadius=`${c}px`);const d=(0,l.__experimentalGetColorClassesAndStyles)(e),m=p()(u.className,d.className,"wp-block-popper__close",{"wp-block-popper__close-outside":"outside"===a,"wp-block-popper__close-corner":"corner"===a}),b=l.useBlockProps.save({className:m,style:d.style});return s?(0,t.createElement)("button",{...b,"aria-label":(0,o.__)("Close modal","popper")},(0,t.createElement)("svg",{viewBox:"0 0 16 16"},(0,t.createElement)("path",{d:"M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 0 1-1.414-1.414L6.586 8 .293 1.707a1 1 0 0 1 0-1.414z"}))):(0,t.createElement)(l.RichText.Content,{...b,tagName:"button",value:n})}}];(0,e.registerBlockType)(x,{deprecated:h,edit:function(e){const{attributes:r,setAttributes:a}=e,{style:s,text:i,position:u,isIcon:d,align:m}=r,b=s?.border?.radius,g=(0,l.__experimentalUseBorderProps)(r);"number"==typeof b&&(g.style.borderRadius=`${b}px`);const _=(0,l.__experimentalUseColorProps)(r),v=p()(g.className,_.className,"wp-block-popper__close",{"wp-block-popper__close--outside":"outside"===u,"wp-block-popper__close--corner":"corner"===u,aligncenter:"center"===m,alignleft:"left"===m}),x=(0,l.useBlockProps)({className:v}),h=()=>d?(0,t.createElement)("button",{...x},(0,t.createElement)(c,null)):(0,t.createElement)("span",{...x},(0,t.createElement)(l.RichText,{tagName:"span",value:i,onChange:e=>a({text:e}),placeholder:(0,o.__)("Enter close text…","popper"),allowedFormats:["core/bold","core/italic"],multiline:!1}));return(0,t.createElement)(n.Fragment,null,(0,t.createElement)(l.InspectorControls,null,(0,t.createElement)(w,{...e})),"outside"===u?(0,t.createElement)("div",{className:"wp-block-popper__header"},h()):h())},save:function({attributes:e}){const{text:t,position:r,isIcon:n,style:a,align:s}=e,i=a?.border?.radius,u=(0,l.__experimentalGetBorderClassesAndStyles)(e);"number"==typeof i&&(u.style.borderRadius=`${i}px`);const d=(0,l.__experimentalGetColorClassesAndStyles)(e),b=p()(u.className,d.className,"wp-block-popper__close",{"wp-block-popper__close--outside":"outside"===r,"wp-block-popper__close--corner":"corner"===r,aligncenter:"center"===s,alignleft:"left"===s}),g=l.useBlockProps.save({className:b,style:d.style}),_=()=>n?(0,m.jsx)("button",{...g,"aria-label":(0,o.__)("Close modal","popper"),children:(0,m.jsx)(c,{})}):(0,m.jsx)(l.RichText.Content,{...g,tagName:"button",value:t||(0,o.__)("Close","popper")});return"outside"===r?(0,m.jsx)("div",{className:"wp-block-popper__header",children:_()}):_()}})})()})();