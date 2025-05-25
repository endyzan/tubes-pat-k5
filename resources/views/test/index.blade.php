@extends('main-layout')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Top 10 Cryptocurrencies</h1>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">#</th>
                        <th scope="col" class="px-6 py-3">Coin</th>
                        <th scope="col" class="px-6 py-3">Symbol</th>
                        <th scope="col" class="px-6 py-3">Price</th>
                        <th scope="col" class="px-6 py-3">Market Cap</th>
                        <th scope="col" class="px-6 py-3">24h Change</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($coins as $index => $coin)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">{{ (int) $index + 1 }}</td>
                            <td class="px-6 py-4 flex items-center gap-2">
                                @if (isset($coin['image']))
                                    <img src="{{ $coin['image'] }}" alt="{{ $coin['name'] ?? 'Coin' }}"
                                        class="w-6 h-6 rounded-full">
                                @endif
                                {{ $coin['name'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 uppercase">{{ $coin['symbol'] }}</td>
                            <td class="px-6 py-4">${{ number_format($coin['current_price'], 2) }}</td>
                            <td class="px-6 py-4">${{ number_format($coin['market_cap']) }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="{{ $coin['price_change_percentage_24h'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($coin['price_change_percentage_24h'], 2) }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
