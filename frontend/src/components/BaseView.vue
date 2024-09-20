<template>
    <div id="base-view">
        <div id="home-view" class="bg-slate-200 h-screen w-screen text-gray-700 md:grid md:grid-cols-3">
            <div id="left" class="md:col-span-1 flex justify-center md:justify-end bg-slate-300 md:pt-1">
                <a href="/" class="flex py-3 md:pt-8 md:h-10 items-center mr-4">
                    <img class="w-10 h-10 mr-2" src="../assets/img/qplogo.png">
                    <p>QuestProxy</p>
                </a>
            </div>
            <div id="right" class="px-10 md:px-16 py-6 md:col-span-2">
                <slot name="right-section"></slot>
            </div>
        </div>
    </div>
</template>