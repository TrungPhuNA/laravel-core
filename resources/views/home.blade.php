<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel Core') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh bg-slate-50 text-slate-900">
        <div class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -top-40 left-1/2 h-[560px] w-[900px] -translate-x-1/2 rounded-full bg-gradient-to-r from-sky-200/60 via-cyan-100/60 to-indigo-200/60 blur-3xl"></div>
                <div class="absolute -bottom-56 left-[-10%] h-[520px] w-[520px] rounded-full bg-gradient-to-tr from-emerald-200/40 via-sky-200/40 to-transparent blur-3xl"></div>
                <div class="absolute -bottom-56 right-[-10%] h-[520px] w-[520px] rounded-full bg-gradient-to-tl from-indigo-200/50 via-sky-200/40 to-transparent blur-3xl"></div>
            </div>

            <header class="relative">
                <div class="mx-auto max-w-7xl px-6 py-5">
                    <div class="flex items-center justify-between gap-4">
                        <a href="/" class="inline-flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-white shadow">
                                <span class="text-sm font-bold">LC</span>
                            </span>
                            <div class="leading-tight">
                                <div class="text-sm font-semibold tracking-tight">{{ config('app.name', 'Laravel Core') }}</div>
                                <div class="text-xs text-slate-600">Core khung Laravel + modules + API</div>
                            </div>
                        </a>

                        <nav class="hidden items-center gap-3 text-sm md:flex">
                            <a class="rounded-lg px-3 py-2 text-slate-700 hover:bg-white/60 hover:text-slate-900" href="/docs">Tài liệu API</a>
                            <a class="rounded-lg px-3 py-2 text-slate-700 hover:bg-white/60 hover:text-slate-900" href="/webhook/channels">Webhook</a>
                            <a class="rounded-lg px-3 py-2 text-slate-700 hover:bg-white/60 hover:text-slate-900" href="/admin/settings">Setting</a>
                            <a class="rounded-lg px-3 py-2 text-slate-700 hover:bg-white/60 hover:text-slate-900" href="/auth/login">Đăng nhập</a>
                        </nav>
                    </div>
                </div>
            </header>

            <main class="relative">
                <section class="mx-auto max-w-7xl px-6 pb-10 pt-6">
                    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:items-center">
                        <div class="lg:col-span-7">
                            <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/70 px-3 py-1 text-xs font-semibold text-slate-700 shadow-sm">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Sẵn sàng dùng lại cho nhiều dự án
                            </div>

                            <h1 class="mt-4 text-balance text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">
                                Laravel Core Scaffold
                                <span class="block text-slate-600">API chuẩn hoá, module hoá, có FE quản trị theo module</span>
                            </h1>

                            <p class="mt-4 max-w-2xl text-pretty text-sm leading-6 text-slate-700 sm:text-base">
                                Đây là project core của bạn: dựng khung Laravel (PHP 8.2, Laravel 12) theo kiểu modules (nwidart),
                                chuẩn response/error, hỗ trợ đa ngôn ngữ, có docs API và một vài module mẫu để kiểm thử luồng.
                            </p>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <a
                                    href="/docs"
                                    class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800"
                                >
                                    Xem tài liệu API
                                </a>
                                <a
                                    href="/webhook/channels"
                                    class="inline-flex items-center justify-center rounded-lg bg-white/80 px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 hover:bg-white"
                                >
                                    Vào module Webhook
                                </a>
                                <a
                                    href="/auth/login"
                                    class="inline-flex items-center justify-center rounded-lg bg-white/80 px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 hover:bg-white"
                                >
                                    Đăng nhập / Đăng ký
                                </a>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-600">API Base</div>
                                    <div class="mt-1 font-mono text-xs text-slate-900">/api/v1</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-600">Auth</div>
                                    <div class="mt-1 font-mono text-xs text-slate-900">Sanctum token</div>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white/70 p-4 shadow-sm">
                                    <div class="text-xs font-semibold text-slate-600">Modules</div>
                                    <div class="mt-1 font-mono text-xs text-slate-900">Modules/*</div>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-5">
                            <div class="rounded-3xl border border-slate-200 bg-white/70 p-5 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold tracking-tight">Điểm nổi bật</div>
                                        <div class="text-xs text-slate-600">Những thứ “core” dùng lại cho mọi dự án</div>
                                    </div>
                                    <div class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">Core</div>
                                </div>

                                <div class="mt-4 grid grid-cols-1 gap-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="text-sm font-semibold">Chuẩn response + error</div>
                                        <div class="mt-1 text-xs leading-5 text-slate-600">
                                            Format thống nhất, có <span class="font-mono">trace_id</span>, dễ debug và dễ dùng cho FE.
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="text-sm font-semibold">Module hoá</div>
                                        <div class="mt-1 text-xs leading-5 text-slate-600">
                                            Mỗi tính năng là một module: route, controller, request, service, repository, docs.
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="text-sm font-semibold">Tài liệu API</div>
                                        <div class="mt-1 text-xs leading-5 text-slate-600">
                                            Scribe generate docs tại <span class="font-mono">/docs</span>.
                                        </div>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <div class="text-sm font-semibold">FE theo module</div>
                                        <div class="mt-1 text-xs leading-5 text-slate-600">
                                            React + Vite, mỗi module có FE riêng, dùng chung shared UI.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-6 pb-12">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold tracking-tight">Module mẫu</h2>
                            <p class="mt-1 text-sm text-slate-600">Một vài module demo để bạn kiểm tra luồng và mở rộng dần.</p>
                        </div>
                        <a class="hidden text-sm font-semibold text-slate-900 hover:underline md:inline" href="/docs">Mở tài liệu API</a>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div class="group rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="text-sm font-semibold">Auth</div>
                            <div class="mt-1 text-xs leading-5 text-slate-600">Đăng ký, đăng nhập, profile, locale, token.</div>
                            <div class="mt-4 flex items-center gap-2">
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/auth/login">FE</a>
                                <span class="text-slate-300">/</span>
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/docs">API</a>
                            </div>
                        </div>

                        <div class="group rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="text-sm font-semibold">User</div>
                            <div class="mt-1 text-xs leading-5 text-slate-600">CRUD tài khoản, filter/sort/pagination.</div>
                            <div class="mt-4 flex items-center gap-2">
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/docs">API</a>
                            </div>
                        </div>

                        <div class="group rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="text-sm font-semibold">Setting</div>
                            <div class="mt-1 text-xs leading-5 text-slate-600">Cấu hình theo key/group, public/private + queue tools.</div>
                            <div class="mt-4 flex items-center gap-2">
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/admin/settings">FE</a>
                                <span class="text-slate-300">/</span>
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/docs">API</a>
                            </div>
                        </div>

                        <div class="group rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md">
                            <div class="text-sm font-semibold">Webhook</div>
                            <div class="mt-1 text-xs leading-5 text-slate-600">Tạo kênh, receiver, auth token/HMAC, logs + prune.</div>
                            <div class="mt-4 flex items-center gap-2">
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/webhook/channels">FE</a>
                                <span class="text-slate-300">/</span>
                                <a class="text-xs font-semibold text-slate-900 hover:underline" href="/docs">API</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-6 pb-14">
                    <div class="rounded-3xl border border-slate-200 bg-white/70 p-6 shadow-sm">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-center">
                            <div class="lg:col-span-7">
                                <div class="text-sm font-semibold tracking-tight">Bạn sẽ dùng core này như thế nào?</div>
                                <div class="mt-1 text-sm text-slate-600">
                                    Khi có dự án mới, bạn chỉ cần clone core, bật module cần dùng, viết thêm module mới theo template
                                    và giữ nguyên chuẩn response/docs.
                                </div>
                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <a
                                        href="/docs"
                                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800"
                                    >
                                        Tài liệu API
                                    </a>
                                    <a
                                        href="/webhook/channels"
                                        class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50"
                                    >
                                        Xem module Webhook
                                    </a>
                                </div>
                            </div>
                            <div class="lg:col-span-5">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="text-xs font-semibold text-slate-600">Gợi ý đường dẫn</div>
                                    <div class="mt-2 space-y-2 font-mono text-xs text-slate-900">
                                        <div><span class="text-slate-500">Docs:</span> /docs</div>
                                        <div><span class="text-slate-500">Auth FE:</span> /auth/login</div>
                                        <div><span class="text-slate-500">Webhook FE:</span> /webhook/channels</div>
                                        <div><span class="text-slate-500">Setting FE:</span> /admin/settings</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="relative border-t border-slate-200 bg-white/60">
                <div class="mx-auto max-w-7xl px-6 py-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="text-sm font-semibold">{{ config('app.name', 'Laravel Core') }}</div>
                        <div class="text-xs text-slate-600">
                            PHP 8.2 · Laravel 12 · Modules (nwidart) · Sanctum · Scribe · React (theo module)
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

