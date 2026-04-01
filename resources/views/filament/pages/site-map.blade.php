<x-filament-panels::page>
    <div class="space-y-6">

        <x-filament::section>
            <x-slot name="heading">Public Marketing Pages</x-slot>
            <x-slot name="description">Customer-facing pages that form the main site funnel.</x-slot>

            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Page</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Route</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Open</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">

                        @php
                        $pages = [
                            ['name' => 'Landing',             'route' => '/',                         'status' => 'live'],
                            ['name' => 'How It Works',        'route' => '/how-it-works',             'status' => 'live'],
                            ['name' => 'Solutions Hub',       'route' => '/solutions',                'status' => 'live'],
                            ['name' => 'Solutions — Agencies','route' => '/solutions/agencies',       'status' => 'live'],
                            ['name' => 'Solutions — Business Owners', 'route' => '/solutions/business-owners', 'status' => 'live'],
                            ['name' => 'Book a Session',      'route' => '/book',                     'status' => 'live'],
                            ['name' => 'Book — Upgrade (prep)','route' => '/book/upgrade',            'status' => 'live'],
                            ['name' => 'Booking Confirmed',   'route' => '/book/confirmed',           'status' => 'live'],
                            ['name' => 'Access (redirect)',   'route' => '/access',                   'status' => 'redirect → /onboarding/start'],
                            ['name' => 'Onboarding Start',    'route' => '/onboarding/start',         'status' => 'live'],
                            ['name' => 'Privacy Policy',      'route' => '/privacy',                  'status' => 'live'],
                            ['name' => 'Terms of Service',    'route' => '/terms',                    'status' => 'live'],
                        ];
                        @endphp

                        @foreach($pages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $page['name'] }}</td>
                            <td class="px-4 py-3 font-mono text-gray-600">{{ $page['route'] }}</td>
                            <td class="px-4 py-3">
                                @if(str_starts_with($page['status'], 'redirect'))
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">{{ $page['status'] }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">{{ $page['status'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ url($page['route']) }}" target="_blank" class="text-primary-600 hover:text-primary-800 text-xs font-medium">↗ View</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Admin Panel Routes</x-slot>
            <x-slot name="description">Internal Filament panel entry points.</x-slot>

            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Page</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Route</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Open</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">

                        @php
                        $adminPages = [
                            ['name' => 'Admin Login',     'route' => '/admin/login',    'status' => 'live'],
                            ['name' => 'Admin Register',  'route' => '/admin/register', 'status' => 'live'],
                            ['name' => 'Dashboard',       'route' => '/admin',          'status' => 'live'],
                        ];
                        @endphp

                        @foreach($adminPages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $page['name'] }}</td>
                            <td class="px-4 py-3 font-mono text-gray-600">{{ $page['route'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20">{{ $page['status'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ url($page['route']) }}" target="_blank" class="text-primary-600 hover:text-primary-800 text-xs font-medium">↗ View</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
