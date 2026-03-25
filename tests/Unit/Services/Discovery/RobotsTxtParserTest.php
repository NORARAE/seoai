<?php

namespace Tests\Unit\Services\Discovery;

use App\Services\Discovery\RobotsTxtParser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RobotsTxtParserTest extends TestCase
{
    #[Test]
    public function it_parses_rules_for_supported_user_agents_and_sitemaps(): void
    {
        $parser = new RobotsTxtParser();

        $parsed = $parser->parse(<<<TXT
User-agent: *
Disallow: /admin
Allow: /admin/public
Crawl-delay: 5
Sitemap: https://example.com/sitemap.xml

User-agent: Googlebot
Disallow: /private
TXT);

        $this->assertSame(['/admin/public'], $parsed['allow_rules']);
        $this->assertSame(['/admin'], $parsed['disallow_rules']);
        $this->assertSame(['https://example.com/sitemap.xml'], $parsed['sitemap_urls']);
        $this->assertSame(5, $parsed['crawl_delay']);
    }

    #[Test]
    public function allow_rule_wins_when_more_specific_than_disallow(): void
    {
        $parser = new RobotsTxtParser();

        $isAllowed = $parser->isAllowed('/admin/public/page', ['/admin/public'], ['/admin']);

        $this->assertTrue($isAllowed);
    }
}
