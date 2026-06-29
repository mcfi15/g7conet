@php use Modules\Blog\App\Models\Blog; @endphp
@extends('frontend.templates.main_demo_layout')

@section('title')
    <title>{{ $blog->seo_title }}</title>
    <meta name="title" content="{{ $blog->seo_title }}">
    <meta name="description" content="{{ $blog->seo_description }}">

    @php
        $tags = '';
        if($blog->tags){
            foreach (json_decode($blog->tags) as $key => $blog_tag) {
                $tags .= $blog_tag->value.', ';
            }
        }
    @endphp

    <meta name="keyword" content="{{ $tags }}">
@endsection



@section('content')

<style>
    /* Restore TinyMCE / rich-text content styling */
    .entry-content ul,
    .entry-content ol {
        padding-left: 2rem;
        margin-bottom: 1rem;
        list-style: revert; /* ← key fix for bullets disappearing */
    }

    .entry-content ul {
        list-style-type: disc;
    }

    .entry-content ol {
        list-style-type: decimal;
    }

    .entry-content li {
        margin-bottom: 0.4rem;
        display: list-item; /* override any flex/grid resets */
    }

    .entry-content h1, .entry-content h2,
    .entry-content h3, .entry-content h4,
    .entry-content h5, .entry-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 700;
        line-height: 1.3;
    }

    .entry-content blockquote {
        border-left: 4px solid #ccc;
        padding-left: 1rem;
        margin: 1.5rem 0;
        color: #666;
        font-style: italic;
    }

    .entry-content table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    .entry-content table th,
    .entry-content table td {
        border: 1px solid #ddd;
        padding: 0.6rem 0.8rem;
        text-align: left;
    }

    .entry-content table thead {
        background-color: #f4f4f4;
        font-weight: 700;
    }

    .entry-content pre,
    .entry-content code {
        background: #f4f4f4;
        border-radius: 4px;
        padding: 0.2em 0.4em;
        font-family: monospace;
        font-size: 0.9em;
    }

    .entry-content pre code {
        padding: 0;
        background: transparent;
    }

    .entry-content strong { font-weight: 700; }
    .entry-content em     { font-style: italic; }

    .entry-content img {
        max-width: 100%;
        height: auto;
    }

    .entry-content a {
        color: inherit;
        text-decoration: underline;
    }
</style>

<!-- Main Start -->
<div class="optech-breadcrumb" style="background-image: url({{ asset($general_setting->breadcrumb_image) }})">
    <div class="container">
        <h1 class="post__title">{{ $blog->translate?->title }}</h1>
        <nav class="breadcrumbs">
            <ul>
                <li><a href="{{ route('home') }}">{{ __('translate.Home') }}</a></li>
                <li><a href="{{ route('blogs') }}">{{ __('translate.Blog') }}</a></li>
                <li aria-current="page">{{ $blog->translate?->title }}</li>
            </ul>
        </nav>
    </div>
</div>
<!-- End breadcrumb -->

<div class="section optech-section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="optech-blog-thumb single-blog" data-aos="fade-up" data-aos-duration="800">
                    <img src="{{ asset($blog->image) }}" alt="Blog Image">
                </div>
                <div class="optech-single-post-content-wrap">
                    <div class="optech-single-post-meta">
                        <ul>
                            <li><a href=""><i class="ri-calendar-fill"></i>{{ __($blog->created_at->format('d M Y')) }}</a></li>
                            <li><a href=""><i class="ri-bookmark-fill"></i>{{ $blog->category->translate?->name }}</a></li>
                            <li><a href=""><i class="ri-chat-2-fill"></i> {{ $blog->total_comment }} {{ __('translate.Comments') }}</a></li>
                        </ul>
                    </div>
                    <div class="entry-content">
                        {!! clean($blog->translate?->description) !!}

                        <div class="optech-single-post-tag-wrap">
                            <div class="optech-blog-tags">
                                <ul>
                                    @if ($blog->tags)
                                        @foreach (json_decode($blog->tags) as $blog_tag)
                                        <li><a href="javascript:;">{{ $blog_tag->value }}</a></li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="optech-post-navigation">
                            @if($previous)
                                <a href="{{ route('blog', $previous->slug) }}" class="nav-previous">
                                    <i class="ri-arrow-left-line"></i> {{ __('translate.Previous Post') }}
                                </a>
                            @else
                                <span class="nav-previous disabled">
                                    <i class="ri-arrow-left-line"></i>
                                    {{ __('translate.No Previous Post') }}
                                </span>
                            @endif
                            @if($next)
                                <a href="{{ route('blog', $next->slug) }}" class="nav-next">
                                    {{ __('translate.Next Post') }} <i class="ri-arrow-right-line"></i>
                                </a>
                                @else
                                 <span class="nav-next disabled">
                                    {{ __('translate.No Next Post') }}
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                            @endif
                        </div>

                        <div class="optech-post-comment">
                            <h3>{{ __('translate.Comments:') }}</h3>
                            <ul>
                                @foreach ($blog_comments as $blog_comment)
                                <li>
                                    <div class="optech-post-comment-wrap">
                                        <div class="optech-post-comment-thumb">
                                            <img src="{{ asset($general_setting->default_avatar) }}" alt="">
                                        </div>
                                        <div class="optech-post-comment-data">
                                            <p>
                                                {{ html_decode($blog_comment->comment) }}
                                            </p>
                                            <strong>{{ html_decode($blog_comment->name) }}</strong> <span>{{ $blog_comment->created_at->format('d M Y') }}</span>

                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="optech-comment-box">
                            <h3>{{ __('translate.Leave a comments:') }}</h3>
                            <form action="{{ route('store-blog-comment', $blog->id) }}" method="POST">
                                @csrf
                                <div class="optech-comment-box-form">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="optech-comment-form">
                                                <input
                                                    type="text"
                                                    id="name"
                                                    name="name"
                                                    value="{{ old('name') }}"
                                                    placeholder="{{ __('translate.Name') }}"
                                                >
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="optech-comment-form">
                                                <input
                                                    type="email"
                                                    id="email"
                                                    name="email"
                                                    value="{{ old('email') }}"
                                                    placeholder="{{ __('translate.Email') }}"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="optech-comment-form">
                                       <textarea
                                           id="desc"
                                           name="comment"
                                           placeholder="{{ __('translate.Comment') }}"
                                       >{{ old('comment') }}
                                       </textarea>
                                    </div>
                                    <div class="optech-check">
                                        <input type="checkbox" id="css">
                                        <label for="css">
                                            {{ __('translate.Save my name, email, and website in this browser for the next time I comment') }}.
                                        </label>
                                    </div>

                                    @if($general_setting->recaptcha_status == 1)
                                        <div class="contact-form-input col-lg-12 mt-4">
                                            <div class="g-recaptcha" data-sitekey="{{ $general_setting->recaptcha_site_key }}"></div>
                                        </div>
                                    @endif

                                    <button id="optech-default-btn" type="submit" data-text="{{ __('translate.Send Message') }}">
                                        <span class="btn-wraper">
                                            {{ __('translate.Send Message') }}
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @include('blog_sidebar')
        </div>
    </div>
</div>
    <!-- End Main Blog Details -->
@endsection

@push('js_section')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

@endpush
