<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:gap-8">
        <!-- Wordpress Plugin Card -->
        <x-filament::card >
            <div class="hoverable-card flex flex-col items-center p-2 text-center" @click="alert('Coming very soon!')">
                <img src="/img/wordpress.png" style="max-width:100px" />
                <h2 class="text-xl font-bold">Wordpress Plugin</h2>
            </div>
        </x-filament::card>

        <!-- Source Code Card -->
        <x-filament::card >
            <div class="hoverable-card flex flex-col items-center p-2 text-center" @click="alert('Coming very soon!')">
                <img src="/img/php.png" style="max-width:100px"/>
                <h2 class="text-xl font-bold">Source Code (.PHP)</h2>
            </div>
        </x-filament::card>

        <!-- Custom Domain Card -->
        <x-filament::card>
            <div class="hoverable-card flex flex-col items-center p-2 text-center" @click="alert('Coming very soon!')">
                <img src="/img/domain-name.png" style="max-width:100px"/>
                <h2 class="text-xl font-bold">Custom Domain</h2>
            </div>
        </x-filament::card>
    </div>

<style scoped>
    .hoverable-card {
        transition: transform 0.3s;
        cursor: pointer;
    }

    .hoverable-card:hover {
        transform: scale(1.05);
    }
</style>

</x-filament-panels::page>

