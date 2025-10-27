document.addEventListener("DOMContentLoaded",function(){const m=document.getElementById("searchInput"),b=document.getElementById("mobileSearchInput"),g=document.getElementById("searchResults"),p=document.getElementById("searchResultsContent"),x=document.getElementById("searchLoading"),h=document.getElementById("noResults");let k,w="";function L(e){if(e.length<2){C();return}e!==w&&(w=e,B(),clearTimeout(k),k=setTimeout(()=>{fetch(`/api/search?query=${encodeURIComponent(e)}`,{method:"GET",headers:{"Content-Type":"application/json","X-Requested-With":"XMLHttpRequest","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")||""}}).then(s=>s.json()).then(s=>{E(),_(s)}).catch(s=>{console.error("Search error:",s),E(),$()})},300))}function _(e){if(!e||e.musicians?.length===0&&e.venues?.length===0&&e.posts?.length===0){$();return}let s="";e.musicians&&e.musicians.length>0&&(s+='<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Musicians</div>',e.musicians.forEach(t=>{s+=`
                    <a href="/profile/${t.user_id}" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${t.profile_image?`<img class="w-10 h-10 rounded-full object-cover" src="${t.profile_image}" alt="${t.stage_name}">`:`<div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">${(t.stage_name||"M").charAt(0).toUpperCase()}</div>`}
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${t.stage_name||"Musician"}</div>
                            <div class="text-sm text-gray-500">${t.genre||"Musician"}</div>
                        </div>
                        <div class="text-xs text-gray-400">👤</div>
                    </a>
                `})),e.venues&&e.venues.length>0&&(s+='<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Venues</div>',e.venues.forEach(t=>{s+=`
                    <a href="/profile/${t.user_id}" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${t.profile_image?`<img class="w-10 h-10 rounded-full object-cover" src="${t.profile_image}" alt="${t.business_name}">`:`<div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">${(t.business_name||"V").charAt(0).toUpperCase()}</div>`}
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${t.business_name||"Venue"}</div>
                            <div class="text-sm text-gray-500">${t.location||"Venue"}</div>
                        </div>
                        <div class="text-xs text-gray-400">🏢</div>
                    </a>
                `})),e.posts&&e.posts.length>0&&(s+='<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50">Posts</div>',e.posts.forEach(t=>{s+=`
                    <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-100 border-b border-gray-100">
                        <div class="flex-shrink-0">
                            ${t.image_path?`<img class="w-10 h-10 rounded object-cover" src="${t.image_path}" alt="Post">`:'<div class="w-10 h-10 bg-purple-500 rounded flex items-center justify-center text-white font-semibold">📝</div>'}
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${(t.description||"").substring(0,50)}${(t.description||"").length>50?"...":""}</div>
                            <div class="text-sm text-gray-500">by ${t.author_name||"Unknown"}</div>
                        </div>
                        <div class="text-xs text-gray-400">📄</div>
                    </a>
                `})),p.innerHTML=s,v()}function v(){g.classList.remove("hidden"),h.classList.add("hidden")}function C(){g.classList.add("hidden")}function B(){x.classList.remove("hidden"),p.innerHTML="",h.classList.add("hidden"),v()}function E(){x.classList.add("hidden")}function $(){h.classList.remove("hidden"),p.innerHTML="",v()}m&&(m.addEventListener("input",function(e){const s=e.target.value.trim();L(s)}),m.addEventListener("focus",function(e){const s=e.target.value.trim();s.length>=2&&L(s)}),document.addEventListener("click",function(e){!m.contains(e.target)&&!g.contains(e.target)&&C()}),m.addEventListener("keydown",function(e){e.key==="Enter"&&document.getElementById("searchForm").submit()})),b&&b.addEventListener("keydown",function(e){e.key==="Enter"&&document.getElementById("mobileSearchForm").submit()});const T=document.getElementById("mobileMenuButton"),y=document.getElementById("mobileMenu"),M=document.getElementById("menuOpenIcon"),A=document.getElementById("menuCloseIcon");T&&T.addEventListener("click",function(){y.classList.contains("hidden")?(y.classList.remove("hidden"),M.classList.add("hidden"),A.classList.remove("hidden")):(y.classList.add("hidden"),M.classList.remove("hidden"),A.classList.add("hidden"))}),document.addEventListener("click",function(e){if(e.target.closest(".delete-post-btn")){e.preventDefault();const s=e.target.closest(".delete-post-btn"),t=s.getAttribute("data-post-id");N(t,s)}}),document.addEventListener("click",function(e){if(e.target.closest(".post-image")){e.preventDefault(),console.log("Post image clicked!");const s=e.target.closest(".post-image"),t=H(s);console.log("Post data:",t),R(t)}});function N(e,s){const t=document.createElement("div");t.className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4",t.style.opacity="0",t.style.transition="opacity 0.3s ease-out";const r=document.createElement("div");r.className="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-300",r.innerHTML=`
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
        `,t.appendChild(r),document.body.appendChild(t),setTimeout(()=>{t.style.opacity="1",r.style.transform="scale(1)"},10);const n=r.querySelector(".cancel-delete"),i=r.querySelector(".confirm-delete"),o=()=>{t.style.opacity="0",r.style.transform="scale(0.95)",setTimeout(()=>{document.body.removeChild(t)},300)};n.addEventListener("click",o),i.addEventListener("click",()=>{o(),q(e,s)}),t.addEventListener("click",l=>{l.target===t&&o()});const a=l=>{l.key==="Escape"&&(o(),document.removeEventListener("keydown",a))};document.addEventListener("keydown",a)}async function q(e,s){const t=s.innerHTML,r=document.querySelector('meta[name="csrf-token"]').getAttribute("content");try{s.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',s.disabled=!0;const i=await(await fetch(`/posts/${e}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":r,Accept:"application/json"}})).json();if(i.success){f("Post deleted successfully!","success");const o=s.closest("article");o&&(o.style.transition="opacity 0.3s ease-out, transform 0.3s ease-out",o.style.opacity="0",o.style.transform="scale(0.95)",setTimeout(()=>{o.remove()},300))}else f(i.message||"Error deleting post","error"),s.innerHTML=t,s.disabled=!1}catch(n){console.error("Error:",n),f("Network error. Please try again.","error"),s.innerHTML=t,s.disabled=!1}}function f(e,s="info"){const t=document.createElement("div");t.className=`fixed top-4 right-4 z-50 p-4 rounded-2xl shadow-lg backdrop-blur-xl transform translate-x-full transition-transform duration-300 ${s==="success"?"bg-green-500/90 text-white":s==="error"?"bg-red-500/90 text-white":"bg-blue-500/90 text-white"}`,t.innerHTML=`
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    ${s==="success"?"✅":s==="error"?"❌":"ℹ️"}
                </div>
                <div class="text-sm font-medium">${e}</div>
            </div>
        `,document.body.appendChild(t),setTimeout(()=>{t.style.transform="translateX(0)"},100),setTimeout(()=>{t.style.transform="translateX(full)",setTimeout(()=>{document.body.contains(t)&&document.body.removeChild(t)},300)},4e3)}function H(e){return e?{id:e.getAttribute("data-post-id"),imageUrl:e.getAttribute("data-image-url"),userName:e.getAttribute("data-user-name"),userGenre:e.getAttribute("data-user-genre"),userType:e.getAttribute("data-user-type"),userAvatar:e.getAttribute("data-user-avatar"),description:e.getAttribute("data-description"),createdAt:e.getAttribute("data-created-at"),like_count:parseInt(e.getAttribute("data-like-count"))||0,comment_count:parseInt(e.getAttribute("data-comment-count"))||0,is_liked:e.getAttribute("data-is-liked")==="true"}:null}function R(e){if(console.log("showImageModal called with:",e),!e)return;const s=document.createElement("div");s.className="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center",s.style.opacity="0",s.style.transition="opacity 0.3s ease-out";const t=document.createElement("div");t.className="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden transform scale-95 transition-transform duration-300";const r=e.userType==="musician"?"🎵":e.userType==="business"?"🏢":"👤",n=e.userAvatar?`<img class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" src="${e.userAvatar}" alt="avatar">`:`<div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-xl">${e.userName.charAt(0).toUpperCase()}</div>`;t.innerHTML=`
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
                            ${n}
                            <div>
                                <h3 class="font-bold text-gray-800 text-xl">${e.userName}</h3>
                                <p class="text-gray-600">${e.userGenre}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>${r} ${e.userType}</span>
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
        `,s.appendChild(t),document.body.appendChild(s),document.body.style.overflow="hidden",console.log("Modal created and added to DOM"),setTimeout(()=>{s.style.opacity="1",t.style.transform="scale(1)"},10);const i=t.querySelector(".close-modal"),o=()=>{s.style.opacity="0",t.style.transform="scale(0.95)",document.body.style.overflow="",setTimeout(()=>{document.body.contains(s)&&document.body.removeChild(s)},300)};i.addEventListener("click",o),s.addEventListener("click",c=>{c.target===s&&o()});const a=c=>{c.key==="Escape"&&(o(),document.removeEventListener("keydown",a))};document.addEventListener("keydown",a);const l=t.querySelector(".like-btn");l?(console.log("Setting up like button for post:",e.id),l.addEventListener("click",c=>{c.preventDefault(),console.log("Like button clicked for post:",e.id),P(l,e.id)})):console.log("Like button not found in modal");const d=t.querySelector('input[type="text"]'),I=t.querySelector(".comment-submit-btn");d&&I&&(I.addEventListener("click",c=>{c.preventDefault();const u=d.value.trim();console.log("Comment submit button clicked, content:",u),u&&S(e.id,u,d,t)}),d.addEventListener("keypress",c=>{if(c.key==="Enter"){const u=d.value.trim();u&&S(e.id,u,d,t)}})),console.log("Loading comments for post:",e.id),z(e.id,t)}async function P(e,s){if(console.log("toggleLike called with postId:",s),s.startsWith("sample-")){console.log("This is a sample post, like functionality not available"),f("Like functionality is only available for real posts. Create a post to test this feature!","info");return}const t=document.querySelector('meta[name="csrf-token"]').getAttribute("content");e.getAttribute("data-liked");try{const n=await(await fetch(`/posts/${s}/like`,{method:"POST",headers:{"X-CSRF-TOKEN":t,Accept:"application/json"}})).json();if(n.success){const i=e.querySelector("svg"),o=e.querySelector(".like-count");n.liked?(i.setAttribute("class","w-6 h-6 fill-red-500 text-red-500"),e.setAttribute("data-liked","true")):(i.setAttribute("class","w-6 h-6 fill-none text-gray-600 hover:text-red-500"),e.setAttribute("data-liked","false")),o.textContent=n.like_count;const a=document.querySelector(`[data-post-id="${s}"]`);a&&(a.setAttribute("data-like-count",n.like_count),a.setAttribute("data-is-liked",n.liked))}}catch(r){console.error("Error toggling like:",r)}}async function S(e,s,t,r){if(console.log("addComment called with postId:",e,"content:",s),e.startsWith("sample-")){console.log("This is a sample post, comment functionality not available"),f("Comment functionality is only available for real posts. Create a post to test this feature!","info");return}const n=document.querySelector('meta[name="csrf-token"]').getAttribute("content");try{const o=await(await fetch(`/posts/${e}/comments`,{method:"POST",headers:{"X-CSRF-TOKEN":n,Accept:"application/json","Content-Type":"application/json"},body:JSON.stringify({content:s})})).json();if(o.success){t.value="",j(o.comment,r);const a=r.querySelector(".comment-count");a&&(a.textContent=parseInt(a.textContent)+1)}}catch(i){console.error("Error adding comment:",i)}}async function z(e,s){if(e.startsWith("sample-")){console.log("This is a sample post, comments not available");return}const t=document.querySelector('meta[name="csrf-token"]').getAttribute("content");try{const n=await(await fetch(`/posts/${e}/comments`,{method:"GET",headers:{"X-CSRF-TOKEN":t,Accept:"application/json"}})).json();if(console.log("Comments response:",n),n.success&&n.comments.length>0){const i=s.querySelector(".space-y-3");console.log("Comments container for loading:",i),i?(i.innerHTML="",n.comments.forEach(o=>{j(o,s)})):console.log("Comments container not found for loading!")}else n.success&&n.comments.length===0?console.log("No comments found for this post"):console.log("Error loading comments:",n)}catch(r){console.error("Error loading comments:",r)}}function j(e,s){console.log("Adding comment to modal:",e);const t=s.querySelector(".space-y-3");if(console.log("Comments container found:",t),!t){console.log("Comments container not found!");return}const r=document.createElement("div");r.className="flex gap-3 p-3 bg-gray-50 rounded-lg";const n=e.user_name||"Unknown User",i=n.charAt(0).toUpperCase();let o="";e.user_avatar?o=`<img src="/storage/${e.user_avatar}" alt="${n}" class="w-8 h-8 rounded-full object-cover">`:o=`<div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center text-white font-bold text-sm">${i}</div>`,r.innerHTML=`
            <div class="w-8 h-8 flex-shrink-0">
                ${o}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-sm text-gray-800">${n}</span>
                    <span class="text-xs text-gray-500">${new Date(e.created_at).toLocaleDateString()}</span>
                </div>
                <p class="text-sm text-gray-700">${e.content}</p>
            </div>
        `,t.appendChild(r),console.log("Comment added to container")}});
