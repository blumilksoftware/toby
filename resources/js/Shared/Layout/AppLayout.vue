<template>
    <div class="min-h-full">
        <MainMenu />
        <main class="lg:ml-64 flex flex-col flex-1 py-8">
            <div>
                <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import MainMenu from '@/Shared/MainMenu';

export default {
    name: 'AppLayout',
    components: {MainMenu},
};
</script>
