(()=>{var a="🌚",l="🌝",m="utterances",c="light",r="dark",n=document.getElementById("theme-switcher");n.innerHTML=localStorage.theme===c?a:l;n.addEventListener("click",function(){let e=localStorage.theme,t=e===c?r:c;u(e,t),m==="utterances"&&h(`github-${t}`)});function u(e,t){let{classList:s}=document.documentElement,i=t===c?a:l;s.remove(e),s.add(t),localStorage.theme=t,n.innerHTML=i}var d=".utterances-frame",o;function h(e){o||(o=document.querySelector(d)),o.contentWindow.postMessage({type:"set-theme",theme:e},"https://utteranc.es")}document.querySelectorAll(".md ul").forEach(e=>{/<li><input .+>.+<\/li>/.test(e.innerHTML)&&e.classList.add("ul-checkbox")});document.querySelector("body").style.setProperty("--global-font-family","LXGW WenKai");var f="true";f==="false"&&(document.documentElement.style="user-select:none");})();
