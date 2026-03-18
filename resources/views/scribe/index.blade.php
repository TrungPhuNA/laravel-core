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
        var tryItOutBaseUrl = "http://laravel-core.test";
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
                    <ul id="tocify-header-cai-dat" class="tocify-header">
                <li class="tocify-item level-1" data-unique="cai-dat">
                    <a href="#cai-dat">Cài đặt</a>
                </li>
                                    <ul id="tocify-subheader-cai-dat" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="cai-dat-cong-khai">
                                <a href="#cai-dat-cong-khai">Công khai</a>
                            </li>
                                                            <ul id="tocify-subheader-cai-dat-cong-khai" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-public">
                                            <a href="#cai-dat-GETapi-v1-settings-public">Danh sách setting công khai</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="cai-dat-quan-tri">
                                <a href="#cai-dat-quan-tri">Quản trị</a>
                            </li>
                                                            <ul id="tocify-subheader-cai-dat-quan-tri" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings">
                                            <a href="#cai-dat-GETapi-v1-settings">Danh sách tất cả setting</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-PUTapi-v1-settings">
                                            <a href="#cai-dat-PUTapi-v1-settings">Upsert settings (bulk)</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="cai-dat-hang-doi">
                                <a href="#cai-dat-hang-doi">Hàng đợi</a>
                            </li>
                                                            <ul id="tocify-subheader-cai-dat-hang-doi" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-stats">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-stats">Thống kê queue</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-jobs">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-jobs">Danh sách job (jobs)</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-jobs--id-">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-jobs--id-">Chi tiết job</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-failed-jobs">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-failed-jobs">Danh sách failed jobs</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-failed-jobs--id-">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-failed-jobs--id-">Chi tiết failed job</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-POSTapi-v1-settings-queue-failed-jobs--id--retry">
                                            <a href="#cai-dat-POSTapi-v1-settings-queue-failed-jobs--id--retry">Retry failed job</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-DELETEapi-v1-settings-queue-failed-jobs--id-">
                                            <a href="#cai-dat-DELETEapi-v1-settings-queue-failed-jobs--id-">Xoá failed job khỏi failed_jobs</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-batches">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-batches">Danh sách job batches</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings-queue-batches--id-">
                                            <a href="#cai-dat-GETapi-v1-settings-queue-batches--id-">Chi tiết batch</a>
                                        </li>
                                                                    </ul>
                                                                                <li class="tocify-item level-2" data-unique="cai-dat-theo-key">
                                <a href="#cai-dat-theo-key">Theo key</a>
                            </li>
                                                            <ul id="tocify-subheader-cai-dat-theo-key" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="cai-dat-GETapi-v1-settings--key-">
                                            <a href="#cai-dat-GETapi-v1-settings--key-">Lấy setting theo key</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-tai-khoan" class="tocify-header">
                <li class="tocify-item level-1" data-unique="tai-khoan">
                    <a href="#tai-khoan">Tài khoản</a>
                </li>
                                    <ul id="tocify-subheader-tai-khoan" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="tai-khoan-quan-tri">
                                <a href="#tai-khoan-quan-tri">Quản trị</a>
                            </li>
                                                            <ul id="tocify-subheader-tai-khoan-quan-tri" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-GETapi-v1-users">
                                            <a href="#tai-khoan-GETapi-v1-users">Danh sách tài khoản</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-GETapi-v1-users--id-">
                                            <a href="#tai-khoan-GETapi-v1-users--id-">Chi tiết tài khoản</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-POSTapi-v1-users">
                                            <a href="#tai-khoan-POSTapi-v1-users">Tạo tài khoản</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-PUTapi-v1-users--id-">
                                            <a href="#tai-khoan-PUTapi-v1-users--id-">Cập nhật tài khoản</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-PATCHapi-v1-users--id--user-type">
                                            <a href="#tai-khoan-PATCHapi-v1-users--id--user-type">Đổi user_type</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-PATCHapi-v1-users--id--password">
                                            <a href="#tai-khoan-PATCHapi-v1-users--id--password">Reset mật khẩu</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-DELETEapi-v1-users--id-">
                                            <a href="#tai-khoan-DELETEapi-v1-users--id-">Xoá tài khoản (soft delete)</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="tai-khoan-POSTapi-v1-users--id--restore">
                                            <a href="#tai-khoan-POSTapi-v1-users--id--restore">Khôi phục tài khoản đã xoá</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
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
        <li>Cập nhật lần cuối: 18/03/2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="gioi-thieu">Giới thiệu</h1>
<p>Tài liệu mô tả các endpoint API của hệ thống.</p>
<aside>
    <strong>Địa chỉ gốc</strong>: <code>http://laravel-core.test</code>
</aside>
<p>Tài liệu này mô tả toàn bộ endpoint API hiện có.</p>
<p>Xác thực sử dụng Bearer token (Laravel Sanctum).</p>

        <h1 id="xac-thuc-request">Xác thực request</h1>
<p>Để xác thực, gửi header <strong><code>Authorization</code></strong> với giá trị <strong><code>"Bearer {TOKEN}"</code></strong>.</p>
<p>Tất cả endpoint cần xác thực sẽ có nhãn <code>requires authentication</code> trong tài liệu bên dưới.</p>
<p>Lấy token qua <code>POST /api/v1/auth/login</code>, sau đó gửi header <code>Authorization: Bearer &lt;token&gt;</code>.</p>

        <h1 id="cai-dat">Cài đặt</h1>

    

                        <h2 id="cai-dat-cong-khai">Công khai</h2>
                                                    <h2 id="cai-dat-GETapi-v1-settings-public">Danh sách setting công khai</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-public">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/public" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/public"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-public">
    </span>
<span id="execution-results-GETapi-v1-settings-public" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-public"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-public"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-public" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-public">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-public" data-method="GET"
      data-path="api/v1/settings/public"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-public', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-public"
                    onclick="tryItOut('GETapi-v1-settings-public');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-public"
                    onclick="cancelTryOut('GETapi-v1-settings-public');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-public"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/public</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-public"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-public"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                                <h2 id="cai-dat-quan-tri">Quản trị</h2>
                                                    <h2 id="cai-dat-GETapi-v1-settings">Danh sách tất cả setting</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-v1-settings">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings"
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

<span id="example-responses-GETapi-v1-settings">
    </span>
<span id="execution-results-GETapi-v1-settings" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings" data-method="GET"
      data-path="api/v1/settings"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings"
                    onclick="tryItOut('GETapi-v1-settings');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings"
                    onclick="cancelTryOut('GETapi-v1-settings');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-settings"
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
                              name="Content-Type"                data-endpoint="GETapi-v1-settings"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="cai-dat-PUTapi-v1-settings">Upsert settings (bulk)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-v1-settings">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://laravel-core.test/api/v1/settings" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"items\": [
        {
            \"key\": \"site_name\",
            \"value\": \"Core API\",
            \"group\": \"general\",
            \"is_public\": true,
            \"description\": \"Tên website\"
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings"
);

const headers = {
    "Authorization": "Bearer {TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "items": [
        {
            "key": "site_name",
            "value": "Core API",
            "group": "general",
            "is_public": true,
            "description": "Tên website"
        }
    ]
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-settings">
</span>
<span id="execution-results-PUTapi-v1-settings" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-PUTapi-v1-settings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-settings"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-settings" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-settings">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-PUTapi-v1-settings" data-method="PUT"
      data-path="api/v1/settings"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-settings', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-settings"
                    onclick="tryItOut('PUTapi-v1-settings');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-settings"
                    onclick="cancelTryOut('PUTapi-v1-settings');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-settings"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/settings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-settings"
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
                              name="Content-Type"                data-endpoint="PUTapi-v1-settings"
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
                              name="Accept"                data-endpoint="PUTapi-v1-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>items</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Danh sách settings cần upsert. Trường value phải có tối thiểu 1 phần tử.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="items.0.key"                data-endpoint="PUTapi-v1-settings"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 150 ký tự. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>value</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="items.0.value"                data-endpoint="PUTapi-v1-settings"
               value=""
               data-component="body">
    <br>

                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>group</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="items.0.group"                data-endpoint="PUTapi-v1-settings"
               value="amniihfqcoynlazghdtqt"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 100 ký tự. Example: <code>amniihfqcoynlazghdtqt</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>is_public</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-settings" style="display: none">
            <input type="radio" name="items.0.is_public"
                   value="true"
                   data-endpoint="PUTapi-v1-settings"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-settings" style="display: none">
            <input type="radio" name="items.0.is_public"
                   value="false"
                   data-endpoint="PUTapi-v1-settings"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="items.0.description"                data-endpoint="PUTapi-v1-settings"
               value="Necessitatibus architecto aut consequatur debitis et id."
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>Necessitatibus architecto aut consequatur debitis et id.</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                                <h2 id="cai-dat-hang-doi">Hàng đợi</h2>
                                                    <h2 id="cai-dat-GETapi-v1-settings-queue-stats">Thống kê queue</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-stats">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/stats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/stats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-stats">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-stats" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-stats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-stats"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-stats" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-stats">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-stats" data-method="GET"
      data-path="api/v1/settings/queue/stats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-stats', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-stats"
                    onclick="tryItOut('GETapi-v1-settings-queue-stats');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-stats"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-stats');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-stats"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/stats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-stats"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-stats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-jobs">Danh sách job (jobs)</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-jobs">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/jobs?sort=-id&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/jobs"
);

const params = {
    "sort": "-id",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-jobs">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-jobs" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-jobs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-jobs"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-jobs" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-jobs">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-jobs" data-method="GET"
      data-path="api/v1/settings/queue/jobs"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-jobs', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-jobs"
                    onclick="tryItOut('GETapi-v1-settings-queue-jobs');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-jobs"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-jobs');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-jobs"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/jobs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-jobs"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Query</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filters</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filters"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value="-id"
               data-component="query">
    <br>
<p>Sắp xếp. Mặc định -id. Example: <code>-id</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value="1"
               data-component="query">
    <br>
<p>Trang hiện tại. Trường value phải tối thiểu 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-settings-queue-jobs"
               value="20"
               data-component="query">
    <br>
<p>Số item mỗi trang. Trường value phải tối thiểu 1. Example: <code>20</code></p>
            </div>
                </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-jobs--id-">Chi tiết job</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-jobs--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/jobs/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/jobs/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-jobs--id-">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-jobs--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-jobs--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-jobs--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-jobs--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-jobs--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-jobs--id-" data-method="GET"
      data-path="api/v1/settings/queue/jobs/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-jobs--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-jobs--id-"
                    onclick="tryItOut('GETapi-v1-settings-queue-jobs--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-jobs--id-"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-jobs--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-jobs--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/jobs/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-jobs--id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-jobs--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-v1-settings-queue-jobs--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the job. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-failed-jobs">Danh sách failed jobs</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-failed-jobs">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/failed-jobs?sort=-id&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs"
);

const params = {
    "sort": "-id",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-failed-jobs">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-failed-jobs" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-failed-jobs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-failed-jobs"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-failed-jobs" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-failed-jobs">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-failed-jobs" data-method="GET"
      data-path="api/v1/settings/queue/failed-jobs"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-failed-jobs', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-failed-jobs"
                    onclick="tryItOut('GETapi-v1-settings-queue-failed-jobs');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-failed-jobs"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-failed-jobs');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-failed-jobs"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/failed-jobs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Query</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filters</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filters"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value="-id"
               data-component="query">
    <br>
<p>Sắp xếp. Mặc định -id. Example: <code>-id</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value="1"
               data-component="query">
    <br>
<p>Trang hiện tại. Trường value phải tối thiểu 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-settings-queue-failed-jobs"
               value="20"
               data-component="query">
    <br>
<p>Số item mỗi trang. Trường value phải tối thiểu 1. Example: <code>20</code></p>
            </div>
                </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-failed-jobs--id-">Chi tiết failed job</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-failed-jobs--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-failed-jobs--id-">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-failed-jobs--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-failed-jobs--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-failed-jobs--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-failed-jobs--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-failed-jobs--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-failed-jobs--id-" data-method="GET"
      data-path="api/v1/settings/queue/failed-jobs/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-failed-jobs--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-failed-jobs--id-"
                    onclick="tryItOut('GETapi-v1-settings-queue-failed-jobs--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-failed-jobs--id-"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-failed-jobs--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-failed-jobs--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/failed-jobs/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-failed-jobs--id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-failed-jobs--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-v1-settings-queue-failed-jobs--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the failed job. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="cai-dat-POSTapi-v1-settings-queue-failed-jobs--id--retry">Retry failed job</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-settings-queue-failed-jobs--id--retry">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562/retry" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562/retry"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-settings-queue-failed-jobs--id--retry">
</span>
<span id="execution-results-POSTapi-v1-settings-queue-failed-jobs--id--retry" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-settings-queue-failed-jobs--id--retry"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-settings-queue-failed-jobs--id--retry"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-settings-queue-failed-jobs--id--retry" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-settings-queue-failed-jobs--id--retry">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-settings-queue-failed-jobs--id--retry" data-method="POST"
      data-path="api/v1/settings/queue/failed-jobs/{id}/retry"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-settings-queue-failed-jobs--id--retry', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-settings-queue-failed-jobs--id--retry"
                    onclick="tryItOut('POSTapi-v1-settings-queue-failed-jobs--id--retry');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-settings-queue-failed-jobs--id--retry"
                    onclick="cancelTryOut('POSTapi-v1-settings-queue-failed-jobs--id--retry');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-settings-queue-failed-jobs--id--retry"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/settings/queue/failed-jobs/{id}/retry</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-settings-queue-failed-jobs--id--retry"
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
                              name="Accept"                data-endpoint="POSTapi-v1-settings-queue-failed-jobs--id--retry"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-v1-settings-queue-failed-jobs--id--retry"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the failed job. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="cai-dat-DELETEapi-v1-settings-queue-failed-jobs--id-">Xoá failed job khỏi failed_jobs</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-settings-queue-failed-jobs--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/failed-jobs/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-settings-queue-failed-jobs--id-">
</span>
<span id="execution-results-DELETEapi-v1-settings-queue-failed-jobs--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-DELETEapi-v1-settings-queue-failed-jobs--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-settings-queue-failed-jobs--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-settings-queue-failed-jobs--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-settings-queue-failed-jobs--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-DELETEapi-v1-settings-queue-failed-jobs--id-" data-method="DELETE"
      data-path="api/v1/settings/queue/failed-jobs/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-settings-queue-failed-jobs--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-settings-queue-failed-jobs--id-"
                    onclick="tryItOut('DELETEapi-v1-settings-queue-failed-jobs--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-settings-queue-failed-jobs--id-"
                    onclick="cancelTryOut('DELETEapi-v1-settings-queue-failed-jobs--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-settings-queue-failed-jobs--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/settings/queue/failed-jobs/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-settings-queue-failed-jobs--id-"
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
                              name="Accept"                data-endpoint="DELETEapi-v1-settings-queue-failed-jobs--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-v1-settings-queue-failed-jobs--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the failed job. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-batches">Danh sách job batches</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-batches">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/batches?sort=-created_at&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/batches"
);

const params = {
    "sort": "-created_at",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-batches">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-batches" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-batches"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-batches"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-batches" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-batches">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-batches" data-method="GET"
      data-path="api/v1/settings/queue/batches"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-batches', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-batches"
                    onclick="tryItOut('GETapi-v1-settings-queue-batches');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-batches"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-batches');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-batches"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/batches</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-batches"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-batches"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Query</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filters</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filters"                data-endpoint="GETapi-v1-settings-queue-batches"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter"                data-endpoint="GETapi-v1-settings-queue-batches"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-settings-queue-batches"
               value="-created_at"
               data-component="query">
    <br>
<p>Sắp xếp. Mặc định -created_at. Example: <code>-created_at</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-settings-queue-batches"
               value="1"
               data-component="query">
    <br>
<p>Trang hiện tại. Trường value phải tối thiểu 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-settings-queue-batches"
               value="20"
               data-component="query">
    <br>
<p>Số item mỗi trang. Trường value phải tối thiểu 1. Example: <code>20</code></p>
            </div>
                </form>

                    <h2 id="cai-dat-GETapi-v1-settings-queue-batches--id-">Chi tiết batch</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-settings-queue-batches--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/queue/batches/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/queue/batches/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings-queue-batches--id-">
    </span>
<span id="execution-results-GETapi-v1-settings-queue-batches--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings-queue-batches--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings-queue-batches--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings-queue-batches--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings-queue-batches--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings-queue-batches--id-" data-method="GET"
      data-path="api/v1/settings/queue/batches/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings-queue-batches--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings-queue-batches--id-"
                    onclick="tryItOut('GETapi-v1-settings-queue-batches--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings-queue-batches--id-"
                    onclick="cancelTryOut('GETapi-v1-settings-queue-batches--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings-queue-batches--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/queue/batches/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings-queue-batches--id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings-queue-batches--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-v1-settings-queue-batches--id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the batch. Example: <code>consequatur</code></p>
            </div>
                    </form>

                                <h2 id="cai-dat-theo-key">Theo key</h2>
                                                    <h2 id="cai-dat-GETapi-v1-settings--key-">Lấy setting theo key</h2>

<p>
</p>

<p>Nếu setting là public: không cần đăng nhập.
Nếu setting không public: yêu cầu user_type là ADMIN hoặc SYSTEM.</p>

<span id="example-requests-GETapi-v1-settings--key-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/settings/!public$)!queue$^//^" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/settings/!public$)!queue$^//^"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-settings--key-">
    </span>
<span id="execution-results-GETapi-v1-settings--key-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-settings--key-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-settings--key-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-settings--key-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-settings--key-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-settings--key-" data-method="GET"
      data-path="api/v1/settings/{key}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-settings--key-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-settings--key-"
                    onclick="tryItOut('GETapi-v1-settings--key-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-settings--key-"
                    onclick="cancelTryOut('GETapi-v1-settings--key-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-settings--key-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/settings/{key}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-settings--key-"
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
                              name="Accept"                data-endpoint="GETapi-v1-settings--key-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="key"                data-endpoint="GETapi-v1-settings--key-"
               value="!public$)!queue$^//^"
               data-component="url">
    <br>
<p>Example: <code>!public$)!queue$^//^</code></p>
            </div>
                    </form>

                <h1 id="tai-khoan">Tài khoản</h1>

    

                        <h2 id="tai-khoan-quan-tri">Quản trị</h2>
                                                    <h2 id="tai-khoan-GETapi-v1-users">Danh sách tài khoản</h2>

<p>
</p>

<p>Hỗ trợ query:</p>
<ul>
<li>filter[name], filter[email], filter[user_type], filter[phone]</li>
<li>sort=id,name,email,user_type,created_at,updated_at (có thể thêm dấu "-" để desc)</li>
<li>page, per_page</li>
</ul>

<span id="example-requests-GETapi-v1-users">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/users?include=consequatur&amp;sort=-id&amp;page=1&amp;per_page=20" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users"
);

const params = {
    "include": "consequatur",
    "sort": "-id",
    "page": "1",
    "per_page": "20",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-users">
    </span>
<span id="execution-results-GETapi-v1-users" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-users"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-users"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-users" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-users">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-users" data-method="GET"
      data-path="api/v1/users"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-users', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-users"
                    onclick="tryItOut('GETapi-v1-users');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-users"
                    onclick="cancelTryOut('GETapi-v1-users');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-users"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/users</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-users"
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
                              name="Accept"                data-endpoint="GETapi-v1-users"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Query</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filters</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filters"                data-endpoint="GETapi-v1-users"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter"                data-endpoint="GETapi-v1-users"
               value=""
               data-component="query">
    <br>

            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>include</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="include"                data-endpoint="GETapi-v1-users"
               value="consequatur"
               data-component="query">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-users"
               value="-id"
               data-component="query">
    <br>
<p>Sắp xếp. Ví dụ: "-id" (mặc định nếu bỏ trống), "-created_at,name". Example: <code>-id</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-users"
               value="1"
               data-component="query">
    <br>
<p>Trang hiện tại. Trường value phải tối thiểu 1. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-users"
               value="20"
               data-component="query">
    <br>
<p>Số item mỗi trang (giới hạn theo CORE_API_MAX_PER_PAGE). Trường value phải tối thiểu 1. Example: <code>20</code></p>
            </div>
                </form>

                    <h2 id="tai-khoan-GETapi-v1-users--id-">Chi tiết tài khoản</h2>

<p>
</p>



<span id="example-requests-GETapi-v1-users--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://laravel-core.test/api/v1/users/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-users--id-">
    </span>
<span id="execution-results-GETapi-v1-users--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-GETapi-v1-users--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-users--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-users--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-users--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-GETapi-v1-users--id-" data-method="GET"
      data-path="api/v1/users/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-users--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-users--id-"
                    onclick="tryItOut('GETapi-v1-users--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-users--id-"
                    onclick="cancelTryOut('GETapi-v1-users--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-users--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/users/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-users--id-"
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
                              name="Accept"                data-endpoint="GETapi-v1-users--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-v1-users--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="tai-khoan-POSTapi-v1-users">Tạo tài khoản</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-users">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://laravel-core.test/api/v1/users" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Demo User\",
    \"email\": \"demo2@example.com\",
    \"password\": \"123456789\",
    \"user_type\": \"USER\",
    \"phone\": \"0986420994\",
    \"avatar_url\": \"http:\\/\\/kunze.biz\\/iste-laborum-eius-est-dolor.html\",
    \"date_of_birth\": \"2026-03-18T07:06:46\",
    \"gender\": \"dtdsufvyvddqamnii\",
    \"address_line1\": \"hfqcoynlazghdtqtqxbaj\",
    \"address_line2\": \"wbpilpmufinllwloauydl\",
    \"ward\": \"smsjuryvojcybzvrbyick\",
    \"district\": \"znkygloigmkwxphlvazjr\",
    \"province\": \"HCM\",
    \"country\": \"cn\",
    \"postal_code\": \"fbaqywuxhgjjmzuxj\",
    \"company\": \"ubqouzswiwxtrkimfcatb\",
    \"job_title\": \"xspzmrazsroyjpxmqesed\",
    \"timezone\": \"Pacific\\/Guam\",
    \"locale\": \"en_MP\",
    \"bio\": \"consequatur\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Demo User",
    "email": "demo2@example.com",
    "password": "123456789",
    "user_type": "USER",
    "phone": "0986420994",
    "avatar_url": "http:\/\/kunze.biz\/iste-laborum-eius-est-dolor.html",
    "date_of_birth": "2026-03-18T07:06:46",
    "gender": "dtdsufvyvddqamnii",
    "address_line1": "hfqcoynlazghdtqtqxbaj",
    "address_line2": "wbpilpmufinllwloauydl",
    "ward": "smsjuryvojcybzvrbyick",
    "district": "znkygloigmkwxphlvazjr",
    "province": "HCM",
    "country": "cn",
    "postal_code": "fbaqywuxhgjjmzuxj",
    "company": "ubqouzswiwxtrkimfcatb",
    "job_title": "xspzmrazsroyjpxmqesed",
    "timezone": "Pacific\/Guam",
    "locale": "en_MP",
    "bio": "consequatur"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-users">
</span>
<span id="execution-results-POSTapi-v1-users" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-users"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-users"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-users" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-users">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-users" data-method="POST"
      data-path="api/v1/users"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-users', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-users"
                    onclick="tryItOut('POSTapi-v1-users');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-users"
                    onclick="cancelTryOut('POSTapi-v1-users');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-users"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/users</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-users"
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
                              name="Accept"                data-endpoint="POSTapi-v1-users"
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
                              name="name"                data-endpoint="POSTapi-v1-users"
               value="Demo User"
               data-component="body">
    <br>
<p>Tên hiển thị của tài khoản. Trường value không được lớn hơn 255 ký tự. Example: <code>Demo User</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-v1-users"
               value="demo2@example.com"
               data-component="body">
    <br>
<p>Email (duy nhất). Trường value phải là địa chỉ email hợp lệ. Trường value không được lớn hơn 255 ký tự. Example: <code>demo2@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-v1-users"
               value="123456789"
               data-component="body">
    <br>
<p>Mật khẩu (tối thiểu 8 ký tự). Trường value phải tối thiểu 8 ký tự. Trường value không được lớn hơn 255 ký tự. Example: <code>123456789</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>user_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="user_type"                data-endpoint="POSTapi-v1-users"
               value="USER"
               data-component="body">
    <br>
<p>Loại tài khoản (ADMIN|USER|SYSTEM). Example: <code>USER</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>ADMIN</code></li> <li><code>USER</code></li> <li><code>SYSTEM</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="POSTapi-v1-users"
               value="0986420994"
               data-component="body">
    <br>
<p>Số điện thoại. Trường value không được lớn hơn 30 ký tự. Example: <code>0986420994</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>avatar_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="avatar_url"                data-endpoint="POSTapi-v1-users"
               value="http://kunze.biz/iste-laborum-eius-est-dolor.html"
               data-component="body">
    <br>
<p>Must be a valid URL. Example: <code>http://kunze.biz/iste-laborum-eius-est-dolor.html</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date_of_birth</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_of_birth"                data-endpoint="POSTapi-v1-users"
               value="2026-03-18T07:06:46"
               data-component="body">
    <br>
<p>Trường value không phải là ngày hợp lệ. Example: <code>2026-03-18T07:06:46</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>gender</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="gender"                data-endpoint="POSTapi-v1-users"
               value="dtdsufvyvddqamnii"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 20 ký tự. Example: <code>dtdsufvyvddqamnii</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line1"                data-endpoint="POSTapi-v1-users"
               value="hfqcoynlazghdtqtqxbaj"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>hfqcoynlazghdtqtqxbaj</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line2"                data-endpoint="POSTapi-v1-users"
               value="wbpilpmufinllwloauydl"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>wbpilpmufinllwloauydl</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ward</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ward"                data-endpoint="POSTapi-v1-users"
               value="smsjuryvojcybzvrbyick"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>smsjuryvojcybzvrbyick</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>district</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="district"                data-endpoint="POSTapi-v1-users"
               value="znkygloigmkwxphlvazjr"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>znkygloigmkwxphlvazjr</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>province</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="province"                data-endpoint="POSTapi-v1-users"
               value="HCM"
               data-component="body">
    <br>
<p>Tỉnh/Thành. Trường value không được lớn hơn 255 ký tự. Example: <code>HCM</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="country"                data-endpoint="POSTapi-v1-users"
               value="cn"
               data-component="body">
    <br>
<p>Trường value phải có 2 ký tự. Example: <code>cn</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>postal_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="postal_code"                data-endpoint="POSTapi-v1-users"
               value="fbaqywuxhgjjmzuxj"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 20 ký tự. Example: <code>fbaqywuxhgjjmzuxj</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>company</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="company"                data-endpoint="POSTapi-v1-users"
               value="ubqouzswiwxtrkimfcatb"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>ubqouzswiwxtrkimfcatb</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>job_title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="job_title"                data-endpoint="POSTapi-v1-users"
               value="xspzmrazsroyjpxmqesed"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>xspzmrazsroyjpxmqesed</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="timezone"                data-endpoint="POSTapi-v1-users"
               value="Pacific/Guam"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 50 ký tự. Example: <code>Pacific/Guam</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="POSTapi-v1-users"
               value="en_MP"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 10 ký tự. Example: <code>en_MP</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>bio</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="bio"                data-endpoint="POSTapi-v1-users"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
        </form>

                    <h2 id="tai-khoan-PUTapi-v1-users--id-">Cập nhật tài khoản</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-users--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://laravel-core.test/api/v1/users/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Nguyễn Văn A\",
    \"email\": \"demo@example.com\",
    \"user_type\": \"USER\",
    \"phone\": \"0900000000\",
    \"avatar_url\": \"http:\\/\\/kunze.biz\\/iste-laborum-eius-est-dolor.html\",
    \"date_of_birth\": \"2026-03-18T07:06:46\",
    \"gender\": \"dtdsufvyvddqamnii\",
    \"address_line1\": \"hfqcoynlazghdtqtqxbaj\",
    \"address_line2\": \"wbpilpmufinllwloauydl\",
    \"ward\": \"smsjuryvojcybzvrbyick\",
    \"district\": \"znkygloigmkwxphlvazjr\",
    \"province\": \"cnfbaqywuxhgjjmzuxjub\",
    \"country\": \"qo\",
    \"postal_code\": \"uzswiwxtrkimfcatb\",
    \"company\": \"xspzmrazsroyjpxmqesed\",
    \"job_title\": \"yghenqcopwvownkbamlnf\",
    \"timezone\": \"Asia\\/Colombo\",
    \"locale\": \"en_PH\",
    \"bio\": \"Mô tả ngắn về user\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Nguyễn Văn A",
    "email": "demo@example.com",
    "user_type": "USER",
    "phone": "0900000000",
    "avatar_url": "http:\/\/kunze.biz\/iste-laborum-eius-est-dolor.html",
    "date_of_birth": "2026-03-18T07:06:46",
    "gender": "dtdsufvyvddqamnii",
    "address_line1": "hfqcoynlazghdtqtqxbaj",
    "address_line2": "wbpilpmufinllwloauydl",
    "ward": "smsjuryvojcybzvrbyick",
    "district": "znkygloigmkwxphlvazjr",
    "province": "cnfbaqywuxhgjjmzuxjub",
    "country": "qo",
    "postal_code": "uzswiwxtrkimfcatb",
    "company": "xspzmrazsroyjpxmqesed",
    "job_title": "yghenqcopwvownkbamlnf",
    "timezone": "Asia\/Colombo",
    "locale": "en_PH",
    "bio": "Mô tả ngắn về user"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-users--id-">
</span>
<span id="execution-results-PUTapi-v1-users--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-PUTapi-v1-users--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-users--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-users--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-users--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-PUTapi-v1-users--id-" data-method="PUT"
      data-path="api/v1/users/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-users--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-users--id-"
                    onclick="tryItOut('PUTapi-v1-users--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-users--id-"
                    onclick="cancelTryOut('PUTapi-v1-users--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-users--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/users/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-users--id-"
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
                              name="Accept"                data-endpoint="PUTapi-v1-users--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-v1-users--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-v1-users--id-"
               value="Nguyễn Văn A"
               data-component="body">
    <br>
<p>Tên hiển thị. Trường value không được lớn hơn 255 ký tự. Example: <code>Nguyễn Văn A</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-v1-users--id-"
               value="demo@example.com"
               data-component="body">
    <br>
<p>Email (duy nhất). Trường value phải là địa chỉ email hợp lệ. Trường value không được lớn hơn 255 ký tự. Example: <code>demo@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>user_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="user_type"                data-endpoint="PUTapi-v1-users--id-"
               value="USER"
               data-component="body">
    <br>
<p>Loại tài khoản (ADMIN|USER|SYSTEM). Example: <code>USER</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>ADMIN</code></li> <li><code>USER</code></li> <li><code>SYSTEM</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="PUTapi-v1-users--id-"
               value="0900000000"
               data-component="body">
    <br>
<p>Số điện thoại. Trường value không được lớn hơn 30 ký tự. Example: <code>0900000000</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>avatar_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="avatar_url"                data-endpoint="PUTapi-v1-users--id-"
               value="http://kunze.biz/iste-laborum-eius-est-dolor.html"
               data-component="body">
    <br>
<p>Must be a valid URL. Example: <code>http://kunze.biz/iste-laborum-eius-est-dolor.html</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>date_of_birth</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_of_birth"                data-endpoint="PUTapi-v1-users--id-"
               value="2026-03-18T07:06:46"
               data-component="body">
    <br>
<p>Trường value không phải là ngày hợp lệ. Example: <code>2026-03-18T07:06:46</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>gender</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="gender"                data-endpoint="PUTapi-v1-users--id-"
               value="dtdsufvyvddqamnii"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 20 ký tự. Example: <code>dtdsufvyvddqamnii</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line1"                data-endpoint="PUTapi-v1-users--id-"
               value="hfqcoynlazghdtqtqxbaj"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>hfqcoynlazghdtqtqxbaj</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address_line2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address_line2"                data-endpoint="PUTapi-v1-users--id-"
               value="wbpilpmufinllwloauydl"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>wbpilpmufinllwloauydl</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ward</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ward"                data-endpoint="PUTapi-v1-users--id-"
               value="smsjuryvojcybzvrbyick"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>smsjuryvojcybzvrbyick</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>district</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="district"                data-endpoint="PUTapi-v1-users--id-"
               value="znkygloigmkwxphlvazjr"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>znkygloigmkwxphlvazjr</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>province</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="province"                data-endpoint="PUTapi-v1-users--id-"
               value="cnfbaqywuxhgjjmzuxjub"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>cnfbaqywuxhgjjmzuxjub</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="country"                data-endpoint="PUTapi-v1-users--id-"
               value="qo"
               data-component="body">
    <br>
<p>Trường value phải có 2 ký tự. Example: <code>qo</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>postal_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="postal_code"                data-endpoint="PUTapi-v1-users--id-"
               value="uzswiwxtrkimfcatb"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 20 ký tự. Example: <code>uzswiwxtrkimfcatb</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>company</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="company"                data-endpoint="PUTapi-v1-users--id-"
               value="xspzmrazsroyjpxmqesed"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>xspzmrazsroyjpxmqesed</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>job_title</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="job_title"                data-endpoint="PUTapi-v1-users--id-"
               value="yghenqcopwvownkbamlnf"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 255 ký tự. Example: <code>yghenqcopwvownkbamlnf</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>timezone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="timezone"                data-endpoint="PUTapi-v1-users--id-"
               value="Asia/Colombo"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 50 ký tự. Example: <code>Asia/Colombo</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="PUTapi-v1-users--id-"
               value="en_PH"
               data-component="body">
    <br>
<p>Trường value không được lớn hơn 10 ký tự. Example: <code>en_PH</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>bio</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="bio"                data-endpoint="PUTapi-v1-users--id-"
               value="Mô tả ngắn về user"
               data-component="body">
    <br>
<p>Giới thiệu ngắn. Example: <code>Mô tả ngắn về user</code></p>
        </div>
        </form>

                    <h2 id="tai-khoan-PATCHapi-v1-users--id--user-type">Đổi user_type</h2>

<p>
</p>



<span id="example-requests-PATCHapi-v1-users--id--user-type">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://laravel-core.test/api/v1/users/1562/user-type" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"user_type\": \"ADMIN\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562/user-type"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "user_type": "ADMIN"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-v1-users--id--user-type">
</span>
<span id="execution-results-PATCHapi-v1-users--id--user-type" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-PATCHapi-v1-users--id--user-type"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-v1-users--id--user-type"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-v1-users--id--user-type" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-v1-users--id--user-type">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-PATCHapi-v1-users--id--user-type" data-method="PATCH"
      data-path="api/v1/users/{id}/user-type"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-v1-users--id--user-type', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-v1-users--id--user-type"
                    onclick="tryItOut('PATCHapi-v1-users--id--user-type');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-v1-users--id--user-type"
                    onclick="cancelTryOut('PATCHapi-v1-users--id--user-type');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-v1-users--id--user-type"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/users/{id}/user-type</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-v1-users--id--user-type"
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
                              name="Accept"                data-endpoint="PATCHapi-v1-users--id--user-type"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PATCHapi-v1-users--id--user-type"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>user_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="user_type"                data-endpoint="PATCHapi-v1-users--id--user-type"
               value="ADMIN"
               data-component="body">
    <br>
<p>Loại tài khoản mới (ADMIN|USER|SYSTEM). Example: <code>ADMIN</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>ADMIN</code></li> <li><code>USER</code></li> <li><code>SYSTEM</code></li></ul>
        </div>
        </form>

                    <h2 id="tai-khoan-PATCHapi-v1-users--id--password">Reset mật khẩu</h2>

<p>
</p>



<span id="example-requests-PATCHapi-v1-users--id--password">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://laravel-core.test/api/v1/users/1562/password" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"password\": \"123456789\",
    \"password_confirmation\": \"123456789\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562/password"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "password": "123456789",
    "password_confirmation": "123456789"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-v1-users--id--password">
</span>
<span id="execution-results-PATCHapi-v1-users--id--password" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-PATCHapi-v1-users--id--password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-v1-users--id--password"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-v1-users--id--password" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-v1-users--id--password">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-PATCHapi-v1-users--id--password" data-method="PATCH"
      data-path="api/v1/users/{id}/password"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-v1-users--id--password', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-v1-users--id--password"
                    onclick="tryItOut('PATCHapi-v1-users--id--password');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-v1-users--id--password"
                    onclick="cancelTryOut('PATCHapi-v1-users--id--password');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-v1-users--id--password"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/users/{id}/password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-v1-users--id--password"
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
                              name="Accept"                data-endpoint="PATCHapi-v1-users--id--password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PATCHapi-v1-users--id--password"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Tham số Body</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="PATCHapi-v1-users--id--password"
               value="123456789"
               data-component="body">
    <br>
<p>Mật khẩu mới (tối thiểu 8 ký tự). Trường value phải tối thiểu 8 ký tự. Trường value không được lớn hơn 255 ký tự. Example: <code>123456789</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="PATCHapi-v1-users--id--password"
               value="123456789"
               data-component="body">
    <br>
<p>Nhập lại mật khẩu mới (phải giống password). The value and <code>password</code> must match. Example: <code>123456789</code></p>
        </div>
        </form>

                    <h2 id="tai-khoan-DELETEapi-v1-users--id-">Xoá tài khoản (soft delete)</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-users--id-">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://laravel-core.test/api/v1/users/1562" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-users--id-">
</span>
<span id="execution-results-DELETEapi-v1-users--id-" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-DELETEapi-v1-users--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-users--id-"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-users--id-" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-users--id-">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-DELETEapi-v1-users--id-" data-method="DELETE"
      data-path="api/v1/users/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-users--id-', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-users--id-"
                    onclick="tryItOut('DELETEapi-v1-users--id-');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-users--id-"
                    onclick="cancelTryOut('DELETEapi-v1-users--id-');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-users--id-"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/users/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-users--id-"
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
                              name="Accept"                data-endpoint="DELETEapi-v1-users--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-v1-users--id-"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                    </form>

                    <h2 id="tai-khoan-POSTapi-v1-users--id--restore">Khôi phục tài khoản đã xoá</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-users--id--restore">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://laravel-core.test/api/v1/users/1562/restore" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/users/1562/restore"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-users--id--restore">
</span>
<span id="execution-results-POSTapi-v1-users--id--restore" hidden>
    <blockquote>Đã nhận response<span
                id="execution-response-status-POSTapi-v1-users--id--restore"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-users--id--restore"
      data-empty-response-text="<Response rỗng>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-users--id--restore" hidden>
    <blockquote>Request bị lỗi:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-users--id--restore">

Gợi ý: Kiểm tra kết nối mạng.
Nếu bạn là người vận hành API, hãy đảm bảo API đang chạy và đã bật CORS.
Có thể mở DevTools Console để debug.</code></pre>
</span>
<form id="form-POSTapi-v1-users--id--restore" data-method="POST"
      data-path="api/v1/users/{id}/restore"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-users--id--restore', this);">
    <h3>
        Yêu cầu&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-users--id--restore"
                    onclick="tryItOut('POSTapi-v1-users--id--restore');">Thử ngay
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-users--id--restore"
                    onclick="cancelTryOut('POSTapi-v1-users--id--restore');" hidden>Hủy
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-users--id--restore"
                    data-initial-text="Gửi request"
                    data-loading-text="Đang gửi..."
                    hidden>Gửi request
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/users/{id}/restore</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Header</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-users--id--restore"
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
                              name="Accept"                data-endpoint="POSTapi-v1-users--id--restore"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Tham số URL</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="POSTapi-v1-users--id--restore"
               value="1562"
               data-component="url">
    <br>
<p>The ID of the user. Example: <code>1562</code></p>
            </div>
                    </form>

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
    "http://laravel-core.test/api/v1/auth/register" \
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
    "http://laravel-core.test/api/v1/auth/register"
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
<p>User display name. Trường value không được lớn hơn 150 ký tự. Example: <code>Demo User</code></p>
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
<p>Unique email address. Trường value phải là địa chỉ email hợp lệ. Trường value không được lớn hơn 255 ký tự. Example: <code>demo@example.com</code></p>
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
<p>Password (min 8 chars). Trường value phải tối thiểu 8 ký tự. Example: <code>password123</code></p>
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
<p>Optional device name for the token. Trường value không được lớn hơn 100 ký tự. Example: <code>postman</code></p>
        </div>
        </form>

                    <h2 id="xac-thuc-POSTapi-v1-auth-login">Đăng nhập</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-login">
<blockquote>Ví dụ request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://laravel-core.test/api/v1/auth/login" \
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
    "http://laravel-core.test/api/v1/auth/login"
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
<p>Registered email address. Trường value phải là địa chỉ email hợp lệ. Trường value không được lớn hơn 255 ký tự. Example: <code>demo@example.com</code></p>
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
<p>Optional device name for the token. Trường value không được lớn hơn 100 ký tự. Example: <code>postman</code></p>
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
    --get "http://laravel-core.test/api/v1/auth/me" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/auth/me"
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
    "http://laravel-core.test/api/v1/auth/profile" \
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
    "http://laravel-core.test/api/v1/auth/profile"
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
<p>User display name. Trường value không được lớn hơn 150 ký tự. Example: <code>Demo User</code></p>
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
<p>Phone number. Trường value không được lớn hơn 30 ký tự. Example: <code>0900000000</code></p>
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
<p>Avatar image URL. Trường value không được lớn hơn 255 ký tự. Example: <code>https://example.com/avatar.png</code></p>
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
<p>Date of birth (YYYY-MM-DD). Trường value không phải là ngày hợp lệ. Example: <code>1990-01-01</code></p>
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
<p>Gender (free text). Trường value không được lớn hơn 20 ký tự. Example: <code>male</code></p>
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
<p>Address line 1. Trường value không được lớn hơn 255 ký tự. Example: <code>123 Street</code></p>
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
<p>Address line 2. Trường value không được lớn hơn 255 ký tự. Example: <code>Apt 4</code></p>
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
<p>Ward. Trường value không được lớn hơn 255 ký tự. Example: <code>Ward 1</code></p>
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
<p>District. Trường value không được lớn hơn 255 ký tự. Example: <code>District 1</code></p>
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
<p>Province/City. Trường value không được lớn hơn 255 ký tự. Example: <code>HCM</code></p>
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
<p>ISO 3166-1 alpha-2 country code. Trường value phải có 2 ký tự. Example: <code>VN</code></p>
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
<p>Postal code. Trường value không được lớn hơn 20 ký tự. Example: <code>700000</code></p>
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
<p>Company name. Trường value không được lớn hơn 255 ký tự. Example: <code>Core Co</code></p>
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
<p>Job title. Trường value không được lớn hơn 255 ký tự. Example: <code>Engineer</code></p>
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
<p>Timezone identifier. Trường value không được lớn hơn 50 ký tự. Example: <code>Asia/Ho_Chi_Minh</code></p>
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
<p>Locale code. Trường value không được lớn hơn 10 ký tự. Example: <code>vi</code></p>
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
<p>Short bio. Trường value không được lớn hơn 2000 ký tự. Example: <code>Hello</code></p>
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
    "http://laravel-core.test/api/v1/auth/logout" \
    --header "Authorization: Bearer {TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://laravel-core.test/api/v1/auth/logout"
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
