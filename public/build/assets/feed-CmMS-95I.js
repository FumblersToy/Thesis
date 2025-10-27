document.addEventListener("DOMContentLoaded",function(){const T=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),j=document.getElementById("createPostForm"),k=document.getElementById("postsGrid"),w=document.getElementById("loadMore"),S=document.getElementById("image"),h=document.getElementById("fileName"),L=document.getElementById("fileText"),E=document.getElementById("mobileMenuButton"),x=document.getElementById("mobileMenu"),y=document.getElementById("profileButton"),l=document.getElementById("profileDropdown"),$=document.getElementById("applyFilters"),m=document.getElementById("getCurrentLocation"),v=document.getElementById("locationStatus"),b=document.getElementById("sortBy"),I=document.getElementById("distanceFilter");let A=1,M=!1,p=null;if(E&&x&&E.addEventListener("click",function(){x.classList.toggle("-translate-x-full")}),y&&l?(console.log("Profile elements found:",y,l),y.addEventListener("click",function(e){e.preventDefault(),e.stopPropagation(),console.log("Profile button clicked"),console.log("Dropdown classes before:",l.classList.toString()),l.classList.contains("hidden")||window.getComputedStyle(l).display==="none"?(l.classList.remove("hidden"),l.style.display="block",console.log("Showing dropdown")):(l.classList.add("hidden"),l.style.display="none",console.log("Hiding dropdown")),console.log("Dropdown classes after:",l.classList.toString()),console.log("Dropdown visibility:",window.getComputedStyle(l).display)}),l.addEventListener("click",function(e){e.stopPropagation()})):console.log("Profile elements not found:",{profileButton:!!y,profileDropdown:!!l}),m&&m.addEventListener("click",function(){P()}),b&&b.addEventListener("change",function(){this.value==="distance"?(I.classList.remove("hidden"),p||P()):I.classList.add("hidden")}),document.addEventListener("click",function(e){l&&y&&!y.contains(e.target)&&!l.contains(e.target)&&(l.classList.add("hidden"),l.style.display="none"),x&&E&&!x.contains(e.target)&&!E.contains(e.target)&&x.classList.add("-translate-x-full")}),S){let t=function(s){s.preventDefault(),s.stopPropagation()},o=function(s){e.classList.add("border-purple-400","bg-purple-50")},n=function(s){e.classList.remove("border-purple-400","bg-purple-50")},a=function(s){const r=s.dataTransfer.files;if(r.length>0){S.files=r;const c=r[0];h.textContent=c.name,h.classList.remove("hidden"),L.textContent="File selected"}};var Y=t,Q=o,Z=n,ee=a;S.addEventListener("change",function(s){const i=s.target.files[0];i?(h.textContent=i.name,h.classList.remove("hidden"),L.textContent="File selected"):(h.classList.add("hidden"),L.textContent="Choose an image or drag it here")});const e=document.querySelector(".custom-file-input");["dragenter","dragover","dragleave","drop"].forEach(s=>{e.addEventListener(s,t,!1)}),["dragenter","dragover"].forEach(s=>{e.addEventListener(s,o,!1)}),["dragleave","drop"].forEach(s=>{e.addEventListener(s,n,!1)}),e.addEventListener("drop",a,!1)}j&&j.addEventListener("submit",async function(e){e.preventDefault();const t=this.querySelector('button[type="submit"]'),o=t.textContent;try{t.disabled=!0,t.textContent="Posting...";const n=new FormData(this),s=await(await fetch("/posts",{method:"POST",headers:{"X-CSRF-TOKEN":T,Accept:"application/json"},body:n})).json();s.success?(d("Post created successfully!","success"),this.reset(),h.classList.add("hidden"),L.textContent="Choose an image or drag it here",G(s.post)):d(s.message||"Error creating post","error")}catch(n){console.error("Error:",n),d("Network error. Please try again.","error")}finally{t.disabled=!1,t.textContent=o}}),$&&$.addEventListener("click",function(){const e=this,t=e.innerHTML;e.innerHTML="🔄 Applying...",A=1,B(1,!1),setTimeout(()=>{e.innerHTML="✅ Applied!",setTimeout(()=>{e.innerHTML=t},1e3)},1e3)}),w&&w.addEventListener("click",function(){const e=this,t=e.innerHTML;e.innerHTML="🔄 Loading...",B(A+1,!0),setTimeout(()=>{e.innerHTML=t},1500)});async function B(e=1,t=!1){if(!M){M=!0;try{const o=z(),n=new URLSearchParams({page:e,per_page:12,...o}),s=await(await fetch(`/api/posts?${n}`,{headers:{Accept:"application/json","X-CSRF-TOKEN":T}})).json();s.success&&(t?N(s.posts):U(s.posts),s.pagination.has_more?(w.style.display="block",A=s.pagination.current_page):w.style.display="none")}catch(o){console.error("Error loading posts:",o),d("Error loading posts","error")}finally{M=!1}}}function P(){if(!navigator.geolocation){C("Geolocation is not supported by this browser.","error");return}C("Getting your location...","loading"),m.disabled=!0,m.textContent="🔄 Getting Location...",navigator.geolocation.getCurrentPosition(function(e){p={latitude:e.coords.latitude,longitude:e.coords.longitude},C(`Location found: ${p.latitude.toFixed(4)}, ${p.longitude.toFixed(4)}`,"success"),m.textContent="✅ Location Set",m.disabled=!1,b.value==="distance"&&$.click()},function(e){let t="Unable to get your location.";switch(e.code){case e.PERMISSION_DENIED:t="Location access denied by user.";break;case e.POSITION_UNAVAILABLE:t="Location information is unavailable.";break;case e.TIMEOUT:t="Location request timed out.";break}C(t,"error"),m.textContent="📍 Use My Location",m.disabled=!1})}function C(e,t){switch(v.textContent=e,v.classList.remove("hidden","text-green-400","text-red-400","text-yellow-400"),t){case"success":v.classList.add("text-green-400");break;case"error":v.classList.add("text-red-400");break;case"loading":v.classList.add("text-yellow-400");break}v.classList.remove("hidden")}function z(){const e={},t=Array.from(document.querySelectorAll('#instruments input[type="checkbox"]:checked')).map(n=>n.value),o=Array.from(document.querySelectorAll('#venues input[type="checkbox"]:checked')).map(n=>n.value);if(t.length>0&&(e.instruments=t.join(",")),o.length>0&&(e.venues=o.join(",")),b&&(e.sort_by=b.value),p&&b.value==="distance"){e.user_latitude=p.latitude,e.user_longitude=p.longitude;const n=document.getElementById("maxDistance").value;n&&(e.max_distance=n)}return e}function U(e){k.innerHTML="",N(e)}function N(e){e.forEach(t=>{const o=_(t);k.appendChild(o)})}function G(e){const t=_(e);t.style.opacity="0",k.insertBefore(t,k.firstChild),setTimeout(()=>{t.style.transition="opacity 0.5s ease-in-out",t.style.opacity="1"},100)}function _(e){const t=e.image_path||"/images/sample-post-1.jpg",o=e.user_name||"User",n=e.user_genre||"",a=e.user_type||"member",s=e.user_avatar||null,i=e.created_at||new Date().toISOString(),r=document.createElement("div");r.className="bg-white/80 backdrop-blur-xl rounded-3xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 animate-scale-in border border-gray-200";const c=a==="musician"?"🎵":a==="business"?"🏢":"👤",f=s?`<img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200" src="${s}" alt="avatar">`:`<div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold">${o.charAt(0).toUpperCase()}</div>`;return r.innerHTML=`
            <div class="relative">
                <img class="post-image w-full h-80 object-cover cursor-pointer hover:opacity-90 transition-opacity" 
                     src="${t}" 
                     alt="Post image" 
                     loading="lazy"
                     data-post-id="${e.id}"
                     data-image-url="${t}"
                     data-user-name="${o}"
                     data-user-genre="${n}"
                     data-user-type="${a}"
                     data-user-avatar="${s||""}"
                     data-description="${e.description||""}"
                     data-created-at="${i}">
                <div class="absolute top-4 right-4 bg-black/50 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm">
                    ${c} ${a}
                </div>
                ${e.is_owner?`
                    <button class="delete-post-btn absolute top-4 left-4 bg-red-500/80 hover:bg-red-600 text-white p-2 rounded-full transition-colors duration-200" 
                            data-post-id="${e.id}" 
                            title="Delete post">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `:""}
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    ${f}
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">${o}</h3>
                        <p class="text-gray-600">${n}</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4 leading-relaxed">${e.description||""}</p>
                <div class="flex justify-between items-center text-gray-500 text-sm">
                    <span>${i}</span>
                    <div class="flex gap-4">
                        <button class="hover:text-red-500 transition-colors flex items-center gap-1">
                            ❤️ <span>0</span>
                        </button>
                        <button class="hover:text-blue-500 transition-colors flex items-center gap-1">
                            💬 <span>0</span>
                        </button>
                    </div>
                </div>
            </div>
        `,r}function d(e,t="info"){const o=document.createElement("div");o.className=`fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-lg backdrop-blur-xl transform translate-x-full transition-transform duration-300 ${t==="success"?"bg-green-500/90 text-white":t==="error"?"bg-red-500/90 text-white":"bg-blue-500/90 text-white"}`,o.textContent=e,document.body.appendChild(o),setTimeout(()=>{o.classList.remove("translate-x-full")},100),setTimeout(()=>{o.classList.add("translate-x-full"),setTimeout(()=>{document.body.removeChild(o)},300)},3e3)}async function D(){const e=["Guitar","Drums","Piano","Bass","Vocals","Violin","Saxophone"],t=["Studio","Club","Theater","Cafe","Restaurant","Bar","Event Venue","Music Hall"];H("instruments",e),H("venues",t)}function H(e,t){const o=document.getElementById(e);o&&(o.innerHTML="",t.forEach(n=>{const a=document.createElement("div");a.innerHTML=`
                <label class="flex items-center gap-3 text-white/80 cursor-pointer hover:text-white transition-colors">
                    <input type="checkbox" value="${n}" class="rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-400">
                    <span>${n}</span>
                </label>
            `,o.appendChild(a)}))}document.addEventListener("mousemove",function(e){const t=document.querySelector(".floating-elements");if(t){const o=e.clientX/window.innerWidth,n=e.clientY/window.innerHeight;t.style.transform=`translate(${o*20}px, ${n*20}px)`}}),tailwind.config={theme:{extend:{colors:{primary:"#6366f1","primary-dark":"#4f46e5",glass:"rgba(255, 255, 255, 0.1)","glass-dark":"rgba(0, 0, 0, 0.1)","bg-main":"#f2f4f7"},backdropBlur:{xs:"2px"},animation:{float:"float 3s ease-in-out infinite","pulse-slow":"pulse 3s ease-in-out infinite","slide-up":"slideUp 0.3s ease-out","fade-in":"fadeIn 0.5s ease-out","scale-in":"scaleIn 0.3s ease-out"}}}},document.addEventListener("click",function(e){if(e.target.closest(".delete-post-btn")){e.preventDefault();const t=e.target.closest(".delete-post-btn"),o=t.getAttribute("data-post-id");R(o,t)}}),document.addEventListener("click",function(e){if(e.target.closest(".post-image")){e.preventDefault(),console.log("Post image clicked!");const t=e.target.closest(".post-image"),o=X(t);console.log("Post data:",o),K(o)}});function R(e,t){const o=document.createElement("div");o.className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4",o.style.opacity="0",o.style.transition="opacity 0.3s ease-out";const n=document.createElement("div");n.className="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-300",n.innerHTML=`
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Post</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="flex gap-3 justify-center">
                    <button class="cancel-delete px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button class="confirm-delete px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        `,o.appendChild(n),document.body.appendChild(o),setTimeout(()=>{o.style.opacity="1",n.style.transform="scale(1)"},10);const a=n.querySelector(".cancel-delete"),s=n.querySelector(".confirm-delete"),i=()=>{o.style.opacity="0",n.style.transform="scale(0.95)",setTimeout(()=>{document.body.removeChild(o)},300)};a.addEventListener("click",i),s.addEventListener("click",()=>{i(),V(e,t)}),o.addEventListener("click",c=>{c.target===o&&i()});const r=c=>{c.key==="Escape"&&(i(),document.removeEventListener("keydown",r))};document.addEventListener("keydown",r)}async function V(e,t){const o=t.innerHTML;try{t.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',t.disabled=!0;const a=await(await fetch(`/posts/${e}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":T,Accept:"application/json"}})).json();if(a.success){d("Post deleted successfully!","success");const s=t.closest(".bg-white\\/80");s&&(s.style.transition="opacity 0.3s ease-out, transform 0.3s ease-out",s.style.opacity="0",s.style.transform="scale(0.95)",setTimeout(()=>{s.remove()},300))}else d(a.message||"Error deleting post","error"),t.innerHTML=o,t.disabled=!1}catch(n){console.error("Error:",n),d("Network error. Please try again.","error"),t.innerHTML=o,t.disabled=!1}}function X(e){return e?{id:e.getAttribute("data-post-id"),imageUrl:e.getAttribute("data-image-url"),userName:e.getAttribute("data-user-name"),userGenre:e.getAttribute("data-user-genre"),userType:e.getAttribute("data-user-type"),userAvatar:e.getAttribute("data-user-avatar"),description:e.getAttribute("data-description"),createdAt:e.getAttribute("data-created-at"),like_count:parseInt(e.getAttribute("data-like-count"))||0,comment_count:parseInt(e.getAttribute("data-comment-count"))||0,is_liked:e.getAttribute("data-is-liked")==="true"}:null}function K(e){if(console.log("showImageModal called with:",e),!e){console.log("No post data, returning");return}const t=document.createElement("div");t.className="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center",t.style.opacity="0",t.style.transition="opacity 0.3s ease-out";const o=document.createElement("div");o.className="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300";const n=e.userType==="musician"?"🎵":e.userType==="business"?"🏢":"👤",a=e.userAvatar?`<img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="${e.userAvatar}" alt="avatar">`:`<div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xl">${e.userName.charAt(0).toUpperCase()}</div>`;o.innerHTML=`
            <div class="flex h-full max-h-[90vh]">
                <!-- Image Section -->
                <div class="flex-1 bg-black flex items-center justify-center">
                    <img src="${e.imageUrl}" 
                         alt="Post image" 
                         class="max-w-full max-h-full object-contain">
                </div>
                
                <!-- Details Section -->
                <div class="w-96 bg-white flex flex-col">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center gap-4 mb-4">
                            ${a}
                            <div>
                                <h3 class="font-bold text-gray-800 text-xl">${e.userName}</h3>
                                <p class="text-gray-600">${e.userGenre}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>${n} ${e.userType}</span>
                            <span>•</span>
                            <span>${new Date(e.createdAt).toLocaleDateString()}</span>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="flex-1 p-6 overflow-y-auto">
                        ${e.description?`
                            <div class="mb-6">
                                <p class="text-gray-700 leading-relaxed">${e.description}</p>
                            </div>
                        `:""}
                        
                        <!-- Comments Section -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-800">Comments</h4>
                            <div class="space-y-3">
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p>No comments yet</p>
                                    <p class="text-sm">Be the first to comment!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="p-6 border-t border-gray-200">
                        <div class="flex items-center gap-6 mb-4">
                            <button class="like-btn flex items-center gap-2 transition-colors" 
                                    data-post-id="${e.id}"
                                    data-liked="${e.is_liked||!1}">
                                <svg class="w-6 h-6 ${e.is_liked?"fill-red-500 text-red-500":"fill-none text-gray-600 hover:text-red-500"}" 
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="font-medium like-count">${e.like_count||0}</span>
                            </button>
                            <button class="comment-btn flex items-center gap-2 text-gray-600 hover:text-blue-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span class="font-medium comment-count">${e.comment_count||0}</span>
                            </button>
                            <button class="share-btn flex items-center gap-2 text-gray-600 hover:text-green-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                <span class="font-medium">Share</span>
                            </button>
                        </div>
                        
                        <!-- Comment Input -->
                        <div class="flex gap-3">
                            <input type="text" 
                                   placeholder="Add a comment..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button class="comment-submit-btn px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                                Post
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Close Button -->
            <button class="close-modal absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `,t.appendChild(o),document.body.appendChild(t),document.body.style.overflow="hidden",console.log("Modal created and added to DOM"),setTimeout(()=>{t.style.opacity="1",o.style.transform="scale(1)"},10);const s=o.querySelector(".close-modal"),i=()=>{t.style.opacity="0",o.style.transform="scale(0.95)",document.body.style.overflow="",setTimeout(()=>{document.body.contains(t)&&document.body.removeChild(t)},300)};s.addEventListener("click",i),t.addEventListener("click",u=>{u.target===t&&i()});const r=u=>{u.key==="Escape"&&(i(),document.removeEventListener("keydown",r))};document.addEventListener("keydown",r);const c=o.querySelector(".like-btn");c?(console.log("Setting up like button for post:",e.id),c.addEventListener("click",u=>{u.preventDefault(),console.log("Like button clicked for post:",e.id),W(c,e.id)})):console.log("Like button not found in modal");const f=o.querySelector('input[type="text"]'),O=o.querySelector(".comment-submit-btn");f&&O?(console.log("Setting up comment functionality for post:",e.id),O.addEventListener("click",u=>{u.preventDefault();const g=f.value.trim();console.log("Comment submit button clicked, content:",g),g&&F(e.id,g,f,o)}),f.addEventListener("keypress",u=>{if(u.key==="Enter"){const g=f.value.trim();console.log("Enter pressed in comment input, content:",g),g&&F(e.id,g,f,o)}})):console.log("Comment input or submit button not found in modal"),console.log("Loading comments for post:",e.id),J(e.id,o)}async function W(e,t){if(console.log("toggleLike called with postId:",t),t.startsWith("sample-")){console.log("This is a sample post, like functionality not available"),d("Like functionality is only available for real posts. Create a post to test this feature!","info");return}const o=document.querySelector('meta[name="csrf-token"]').getAttribute("content");e.getAttribute("data-liked");try{const a=await(await fetch(`/posts/${t}/like`,{method:"POST",headers:{"X-CSRF-TOKEN":o,Accept:"application/json"}})).json();if(a.success){const s=e.querySelector("svg"),i=e.querySelector(".like-count");a.liked?(s.setAttribute("class","w-6 h-6 fill-red-500 text-red-500"),e.setAttribute("data-liked","true")):(s.setAttribute("class","w-6 h-6 fill-none text-gray-600 hover:text-red-500"),e.setAttribute("data-liked","false")),i.textContent=a.like_count;const r=document.querySelector(`[data-post-id="${t}"]`);r&&(r.setAttribute("data-like-count",a.like_count),r.setAttribute("data-is-liked",a.liked))}}catch(n){console.error("Error toggling like:",n)}}async function F(e,t,o,n){if(console.log("addComment called with postId:",e,"content:",t),e.startsWith("sample-")){console.log("This is a sample post, comment functionality not available"),d("Comment functionality is only available for real posts. Create a post to test this feature!","info");return}const a=document.querySelector('meta[name="csrf-token"]').getAttribute("content");try{const i=await(await fetch(`/posts/${e}/comments`,{method:"POST",headers:{"X-CSRF-TOKEN":a,Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify({content:t})})).json();if(i.success){console.log("Comment added successfully:",i.comment),o.value="",q(i.comment,n);const r=n.querySelector(".comment-count");r&&(r.textContent=parseInt(r.textContent)+1)}else console.log("Failed to add comment:",i)}catch(s){console.error("Error adding comment:",s)}}async function J(e,t){if(console.log("loadComments called with postId:",e,"modal:",t),e.startsWith("sample-")){console.log("This is a sample post, comments not available");return}const o=document.querySelector('meta[name="csrf-token"]').getAttribute("content");try{console.log("Fetching comments from:",`/posts/${e}/comments`);const n=await fetch(`/posts/${e}/comments`,{method:"GET",headers:{"X-CSRF-TOKEN":o,Accept:"application/json"}});console.log("Comments response status:",n.status);const a=await n.json();if(console.log("Comments response:",a),a.success&&a.comments.length>0){const s=t.querySelector(".space-y-3");console.log("Comments container for loading:",s),s?(s.innerHTML="",a.comments.forEach(i=>{q(i,t)})):console.log("Comments container not found for loading!")}else a.success&&a.comments.length===0?console.log("No comments found for this post"):console.log("Error loading comments:",a)}catch(n){console.error("Error loading comments:",n)}}function q(e,t){console.log("Adding comment to modal:",e);const o=t.querySelector(".space-y-3");if(console.log("Comments container found:",o),!o){console.log("Comments container not found!");return}const n=document.createElement("div");n.className="flex gap-3 p-3 bg-gray-50 rounded-lg";const a=e.user_name||"Unknown User",s=a.charAt(0).toUpperCase();let i="";e.user_avatar?i=`<img src="/storage/${e.user_avatar}" alt="${a}" class="w-8 h-8 rounded-full object-cover">`:i=`<div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">${s}</div>`,n.innerHTML=`
            <div class="w-8 h-8 flex-shrink-0">
                ${i}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-sm text-gray-800">${a}</span>
                    <span class="text-xs text-gray-500">${new Date(e.created_at).toLocaleDateString()}</span>
                </div>
                <p class="text-sm text-gray-700">${e.content}</p>
            </div>
        `,o.appendChild(n),console.log("Comment added to container")}D(),B(1,!1)});
