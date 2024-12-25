<x-filament-panels::page>
    <h3>Statistic for :: {{$short->short}}</h3>
    <x-filament::button>Reset Statistic</x-filament::button>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        {{-- Total Blocked Card --}}
        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Total Blocked</h2>
                <p class="text-3xl font-semibold text-danger-500">
                    {{ $totalBlocked }}
                </p>
            </div>
        </x-filament::card>

        {{-- Total Allowed Card --}}
        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Total Allowed</h2>
                <p class="text-3xl font-semibold text-success-500">
                    {{ $totalAllowed }}
                </p>
            </div>
        </x-filament::card>
    </div>

    {{-- Logs Section --}}
    <x-filament::card>
        <div class="p-2">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Blocked Logs</h3>
            <textarea 
                readonly 
                class="w-full h-64 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white" style="height: 300px"
            >{{ $logs_blocked }}</textarea>
        </div>
    </x-filament::card>
    <x-filament::card>
        <div class="p-2">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Allowed Logs</h3>
            <textarea 
                readonly 
                style="height: 300px"
                class="w-full h-64 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >{{ $logs_allowed }}</textarea>
        </div>
    </x-filament::card>
</x-filament-panels::page>