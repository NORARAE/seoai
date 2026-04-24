<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SanitizeAiInput;
use Illuminate\Http\Request;
use Tests\TestCase;

class SanitizeAiInputTest extends TestCase
{
    private function sanitize(string $message, string $path = '/ai/chat'): string
    {
        $request = Request::create($path, 'POST', ['message' => $message]);
        $middleware = new SanitizeAiInput();
        $middleware->handle($request, function (Request $req) use (&$result) {
            $result = $req->input('message');
        });
        return $result ?? $message;
    }

    public function test_normal_message_passes_unchanged(): void
    {
        $msg = 'How do I improve my market coverage?';
        $this->assertSame($msg, $this->sanitize($msg));
    }

    public function test_email_is_redacted(): void
    {
        $this->assertSame(
            'Contact me at [email]',
            $this->sanitize('Contact me at test@email.com')
        );
    }

    public function test_phone_number_is_redacted(): void
    {
        $this->assertSame(
            'Call me at [number]',
            $this->sanitize('Call me at 2065551234')
        );
    }

    public function test_long_input_is_truncated_to_2000_chars(): void
    {
        $out = $this->sanitize(str_repeat('a', 2500));
        $this->assertSame(2000, strlen($out));
    }

    public function test_non_ai_route_is_not_touched(): void
    {
        $msg = 'test@email.com 2065551234';
        $result = $msg;
        $request = Request::create('/some/other/route', 'POST', ['message' => $msg]);
        $middleware = new SanitizeAiInput();
        $middleware->handle($request, function (Request $req) use (&$result) {
            $result = $req->input('message');
        });
        $this->assertSame($msg, $result);
    }
}
