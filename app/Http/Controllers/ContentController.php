<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\SiteSetting;
use App\Support\SeoMetadata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function __construct(private SeoMetadata $seoMetadata) {}

    public function index(): View
    {
        $contents = Content::query()->published()->latest('published_at')->get();
        $settings = $this->settings();

        return view('contents.index', [
            'contents' => $contents,
            'seo' => $this->seoMetadata->page('Nuttime İçerikler', 'Tarifler, haberler ve Nuttime dünyasından SEO odaklı içerikler.', route('contents'), $settings),
        ]);
    }

    public function show(string $slug): View|RedirectResponse
    {
        $content = Content::query()->published()->where('slug', $slug)->firstOrFail();
        $settings = $this->settings();
        $canonical = filter_var($content->seo_canonical, FILTER_VALIDATE_URL) ? $content->seo_canonical : route('content', $content);
        $breadcrumbs = $this->seoMetadata->breadcrumbs([
            ['name' => 'Ana Sayfa', 'url' => route('home')],
            ['name' => 'İçerikler', 'url' => route('contents')],
            ['name' => $content->title, 'url' => $canonical],
        ]);
        $image = $content->cover_image ? asset('storage/'.$content->cover_image) : null;

        return view('contents.show', [
            'content' => $content,
            'breadcrumbs' => $breadcrumbs['itemListElement'],
            'seo' => $this->seoMetadata->page($content->seo_title ?: $content->title, $content->seo_description ?: ($content->excerpt ?: $content->body), $canonical, $settings, [$breadcrumbs], 'article', $image),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        return Schema::hasTable('site_settings') ? SiteSetting::current()->toArray() : [];
    }
}
