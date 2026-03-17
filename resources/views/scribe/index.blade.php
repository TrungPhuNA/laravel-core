<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Tài liệu API</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.8.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.8.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Tìm kiếm">
    </div>

    <div id="toc">
                    <ul id="tocify-header-gioi-thieu" class="tocify-header">
                <li class="tocify-item level-1" data-unique="gioi-thieu">
                    <a href="#gioi-thieu">Giới thiệu</a>
                </li>
                            </ul>
                    <ul id="tocify-header-xac-thuc-request" class="tocify-header">
                <li class="tocify-item level-1" data-unique="xac-thuc-request">
                    <a href="#xac-thuc-request">Xác thực request</a>
                </li>
                            </ul>
                    <ul id="tocify-header-xac-thuc" class="tocify-header">
                <li class="tocify-item level-1" data-unique="xac-thuc">
                    <a href="#xac-thuc">Xác thực</a>
                </li>
                                    <ul id="tocify-subheader-xac-thuc" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="xac-thuc-tai-khoan">
                                <a href="#xac-thuc-tai-khoan">Tài khoản</a>
                            </li>
                                                            <ul id="tocify-subheader-xac-thuc-tai-khoan" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="xac-thuc-POSTapi-v1-auth-register">
                                            <a href="#xac-thuc-POSTapi-v1-auth-register">Đăng ký</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="xac-thuc-POSTapi-v1-auth-login">
                                            <a href="#xac-thuc-POSTapi-v1-auth-login">Đăng nhập</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="xac-thuc-ho-so">
                                <a href="#xac-thuc-ho-so">Hồ sơ</a>
                            </li>
                                                            <ul id="tocify-subheader-xac-thuc-ho-so" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="xac-thuc-GETapi-v1-auth-me">
                                            <a href="#xac-thuc-GETapi-v1-auth-me">Thông tin tài khoản</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="xac-thuc-PUTapi-v1-auth-profile">
                                            <a href="#xac-thuc-PUTapi-v1-auth-profile">Cập nhật profile</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="xac-thuc-phien">
                                <a href="#xac-thuc-phien">Phiên</a>
                            </li>
                                                            <ul id="tocify-subheader-xac-thuc-phien" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="xac-thuc-POSTapi-v1-auth-logout">
                                            <a href="#xac-thuc-POSTapi-v1-auth-logout">Đăng xuất</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">Xem Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">Xem OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Cập nhật lần cuối: 17/03/2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="gioi-thieu">Giới thiệu</h1>
<p>Tài liệu mô tả các endpoint API của hệ thống.</p>
<aside>
    <strong>Địa chỉ gốc</strong>: <code>http://localhost</code>
</aside>
<p>Tài liệu này mô tả toàn bộ endpoint API hiện có.</p>
<p>Xác thực sử dụng Bearer token (Laravel Sanctum).</p>

        <h1 id="xac-thuc-request">Xác thực request</h1>
<p>Để xác thực, gửi header <strong><code>Authorization</code></strong> với giá trị <strong><code>"Bearer {TOKEN}"</code></strong>.</p>
<p>Tất cả endpoint cần xác thực sẽ có nhãn <code>requires authentication</code> trong tài liệu bên dưới.</p>
<p>Lấy token qua <code>POST /api/v1/auth/login</code>, sau đó gửi header <code>Authorization: Bearer &lt;token&gt;</code>.</p>

        <h1 id="xac-thuc">Xác thực</h1>

    <p>API đăng ký/đăng nhập/cập nhật profile sử dụng Bearer token (Sanctum).</p>

                        <h2 id="xac-thuc-tai-khoan">Tài khoản</h2>
                                        <p>
                    <p>Các thao tác đăng ký/đăng nhập.</p>
                </p>
                                        <h2 id="xac-thuc-POSTapi-v1-auth-register">Đăng ký</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-register">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Demo User\",
    \"email\": \"demo@example.com\",
    \"password\": \"password123\",
    \"device_name\": \"postman\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Demo User",
    "email": "demo@example.com",
    "password": "password123",
    "device_name": "postman"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-register">
</span>
<span id="execution-results-POSTapi-v1-auth-register" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-auth-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-register"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-register" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-register">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-register" data-method="POST"
      data-path="api/v1/auth/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-register', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-register"
                    onclick="tryItOut('POSTapi-v1-auth-register');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-register"
                    onclick="cancelTryOut('POSTapi-v1-auth-register');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-register"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-auth-register"
               value="Demo User"
               data-component="body">
    <br>
<p>User display name. Must not be greater than 150 characters. Example: <code>Demo User</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-auth-register"
               value="demo@example.com"
               data-component="body">
    <br>
<p>Unique email address. Must be a valid email address. Must not be greater than 255 characters. Example: <code>demo@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-auth-register"
               value="password123"
               data-component="body">
    <br>
<p>Password (min 8 chars). Must be at least 8 characters. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-register"
               value="postman"
               data-component="body">
    <br>
<p>Optional device name for the token. Must not be greater than 100 characters. Example: <code>postman</code></p>
        </div>
        </form>

                    <h2 id="xac-thuc-POSTapi-v1-auth-login">Đăng nhập</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-login">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"demo@example.com\",
    \"password\": \"password123\",
    \"device_name\": \"postman\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "demo@example.com",
    "password": "password123",
    "device_name": "postman"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-login">
</span>
<span id="execution-results-POSTapi-v1-auth-login" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-login"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-login" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-login">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-login" data-method="POST"
      data-path="api/v1/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-login', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-login"
                    onclick="tryItOut('POSTapi-v1-auth-login');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-login"
                    onclick="cancelTryOut('POSTapi-v1-auth-login');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-login"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-auth-login"
               value="demo@example.com"
               data-component="body">
    <br>
<p>Registered email address. Must be a valid email address. Must not be greater than 255 characters. Example: <code>demo@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-auth-login"
               value="password123"
               data-component="body">
    <br>
<p>Account password. Example: <code>password123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-login"
               value="postman"
               data-component="body">
    <br>
<p>Optional device name for the token. Must not be greater than 100 characters. Example: <code>postman</code></p>
        </div>
        </form>

                                <h2 id="xac-thuc-ho-so">Hồ sơ</h2>
                                        <p>
                    <p>Các thao tác xem/cập nhật hồ sơ.</p>
                </p>
                                        <h2 id="xac-thuc-GETapi-v1-auth-me">Thông tin tài khoản</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Lấy thông tin user đang đăng nhập.</p>

<span id="example-requests-GETapi-v1-auth-me">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/auth/me" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/me"
);

const headers = {
    "Authorization": "Bearer {TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-auth-me">
    </span>
<span id="execution-results-GETapi-v1-auth-me" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-auth-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-auth-me"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-auth-me" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-auth-me">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-auth-me" data-method="GET"
      data-path="api/v1/auth/me"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-auth-me', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-auth-me"
                    onclick="tryItOut('GETapi-v1-auth-me');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-auth-me"
                    onclick="cancelTryOut('GETapi-v1-auth-me');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-auth-me"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/auth/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-auth-me"
               value="Bearer {TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="xac-thuc-PUTapi-v1-auth-profile">Cập nhật profile</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cập nhật thông tin profile của user hiện tại.</p>

<span id="example-requests-PUTapi-v1-auth-profile">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/v1/auth/profile" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Demo User\",
    \"phone\": \"0900000000\",
    \"avatar_url\": \"https:\\/\\/example.com\\/avatar.png\",
    \"date_of_birth\": \"1990-01-01\",
    \"gender\": \"male\",
    \"address_line1\": \"123 Street\",
    \"address_line2\": \"Apt 4\",
    \"ward\": \"Ward 1\",
    \"district\": \"District 1\",
    \"province\": \"HCM\",
    \"country\": \"VN\",
    \"postal_code\": \"700000\",
    \"company\": \"Core Co\",
    \"job_title\": \"Engineer\",
    \"timezone\": \"Asia\\/Ho_Chi_Minh\",
    \"locale\": \"vi\",
    \"bio\": \"Hello\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/profile"
);

const headers = {
    "Authorization": "Bearer {TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Demo User",
    "phone": "0900000000",
    "avatar_url": "https:\/\/example.com\/avatar.png",
    "date_of_birth": "1990-01-01",
    "gender": "male",
    "address_line1": "123 Street",
    "address_line2": "Apt 4",
    "ward": "Ward 1",
    "district": "District 1",
    "province": "HCM",
    "country": "VN",
    "postal_code": "700000",
    "company": "Core Co",
    "job_title": "Engineer",
    "timezone": "Asia\/Ho_Chi_Minh",
    "locale": "vi",
    "bio": "Hello"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-auth-profile">
</span>
<span id="execution-results-PUTapi-v1-auth-profile" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-PUTapi-v1-auth-profile"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-auth-profile"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-auth-profile" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-auth-profile">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-PUTapi-v1-auth-profile" data-method="PUT"
      data-path="api/v1/auth/profile"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-auth-profile', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-auth-profile"
                    onclick="tryItOut('PUTapi-v1-auth-profile');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-auth-profile"
                    onclick="cancelTryOut('PUTapi-v1-auth-profile');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-auth-profile"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/auth/profile</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-auth-profile"
               value="Bearer {TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-auth-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-auth-profile"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-auth-profile"
               value="Demo User"
               data-component="body">
    <br>
<p>User display name. Must not be greater than 150 characters. Example: <code>Demo User</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="PUTapi-v1-auth-profile"
               value="0900000000"
               data-component="body">
    <br>
<p>Phone number. Must not be greater than 30 characters. Example: <code>0900000000</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>avatar_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="avatar_url"                data-endpoint="PUTapi-v1-auth-profile"
               value="https://example.com/avatar.png"
               data-component="body">
    <br>
<p>Avatar image URL. Must not be greater than 255 characters. Example: <code>https://example.com/avatar.png</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date_of_birth</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_of_birth"                data-endpoint="PUTapi-v1-auth-profile"
               value="1990-01-01"
               data-component="body">
    <br>
<p>Date of birth (YYYY-MM-DD). Must be a valid date. Example: <code>1990-01-01</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>gender</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="gender"                data-endpoint="PUTapi-v1-auth-profile"
               value="male"
               data-component="body">
    <br>
<p>Gender (free text). Must not be greater than 20 characters. Example: <code>male</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line1"                data-endpoint="PUTapi-v1-auth-profile"
               value="123 Street"
               data-component="body">
    <br>
<p>Address line 1. Must not be greater than 255 characters. Example: <code>123 Street</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line2"                data-endpoint="PUTapi-v1-auth-profile"
               value="Apt 4"
               data-component="body">
    <br>
<p>Address line 2. Must not be greater than 255 characters. Example: <code>Apt 4</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ward</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ward"                data-endpoint="PUTapi-v1-auth-profile"
               value="Ward 1"
               data-component="body">
    <br>
<p>Ward. Must not be greater than 255 characters. Example: <code>Ward 1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>district</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="district"                data-endpoint="PUTapi-v1-auth-profile"
               value="District 1"
               data-component="body">
    <br>
<p>District. Must not be greater than 255 characters. Example: <code>District 1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>province</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="province"                data-endpoint="PUTapi-v1-auth-profile"
               value="HCM"
               data-component="body">
    <br>
<p>Province/City. Must not be greater than 255 characters. Example: <code>HCM</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="country"                data-endpoint="PUTapi-v1-auth-profile"
               value="VN"
               data-component="body">
    <br>
<p>ISO 3166-1 alpha-2 country code. Must be 2 characters. Example: <code>VN</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>postal_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="postal_code"                data-endpoint="PUTapi-v1-auth-profile"
               value="700000"
               data-component="body">
    <br>
<p>Postal code. Must not be greater than 20 characters. Example: <code>700000</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>company</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="company"                data-endpoint="PUTapi-v1-auth-profile"
               value="Core Co"
               data-component="body">
    <br>
<p>Company name. Must not be greater than 255 characters. Example: <code>Core Co</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>job_title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="job_title"                data-endpoint="PUTapi-v1-auth-profile"
               value="Engineer"
               data-component="body">
    <br>
<p>Job title. Must not be greater than 255 characters. Example: <code>Engineer</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="timezone"                data-endpoint="PUTapi-v1-auth-profile"
               value="Asia/Ho_Chi_Minh"
               data-component="body">
    <br>
<p>Timezone identifier. Must not be greater than 50 characters. Example: <code>Asia/Ho_Chi_Minh</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="PUTapi-v1-auth-profile"
               value="vi"
               data-component="body">
    <br>
<p>Locale code. Must not be greater than 10 characters. Example: <code>vi</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>bio</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="bio"                data-endpoint="PUTapi-v1-auth-profile"
               value="Hello"
               data-component="body">
    <br>
<p>Short bio. Must not be greater than 2000 characters. Example: <code>Hello</code></p>
        </div>
        </form>

                                <h2 id="xac-thuc-phien">Phiên</h2>
                                        <p>
                    <p>Quản lý phiên và token.</p>
                </p>
                                        <h2 id="xac-thuc-POSTapi-v1-auth-logout">Đăng xuất</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Thu hồi token hiện tại.</p>

<span id="example-requests-POSTapi-v1-auth-logout">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/logout" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/logout"
);

const headers = {
    "Authorization": "Bearer {TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-logout">
</span>
<span id="execution-results-POSTapi-v1-auth-logout" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-logout"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-logout" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-logout">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-logout" data-method="POST"
      data-path="api/v1/auth/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-logout', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-logout"
                    onclick="tryItOut('POSTapi-v1-auth-logout');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-logout"
                    onclick="cancelTryOut('POSTapi-v1-auth-logout');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-logout"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-auth-logout"
               value="Bearer {TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
