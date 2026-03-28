<script setup lang="ts">
import { ArrowRight } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <section class="bg-card ">
        <div class="mx-auto max-w-350">
            <div
                class="relative overflow-hidden rounded-[32px] bg-card px-4 pt-20 pb-14 sm:px-6 sm:pt-8 md:pt-20 sm:pb-8 md:pb-20 lg:pt-30 lg:pb-50   xl:min-h-[760px] xl:px-4 xl:pt-20 xl:pb-0">
                <div class="grid-overlay-hero">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>



                <div
                    class="relative z-10 mx-auto mt-10 max-w-[760px] xl:mt-0 xl:flex xl:min-h-[640px] xl:flex-col xl:items-center xl:justify-center">
                    <div class="xl:absolute xl:-left-40 xl:top-55 xl:flex hidden hero-fade-in-delayed">
                        <div class="flex items-center gap-3">
                            <div class="relative overflow-hidden">
                                <div
                                    class="animate-draw-line h-px w-[100px] bg-[linear-gradient(to_right,rgba(0,0,0,0.3)_60%,transparent_0%)] bg-[length:20px_1px] bg-repeat-x">
                                </div>
                            </div>

                            <div class="animate-arrow-in h-[10px] w-[10px] border-r border-t border-black/50 opacity-0">
                            </div>
                        </div>
                    </div>

                    <div class="w-full text-center xl:text-right">
                        <h1
                            class="hero-fade-up-1 font-onest text-[32px] font-medium leading-[1.08] tracking-[-0.03em] text-[#20243B] sm:text-[38px] md:text-[44px] xl:text-[50px] xl:leading-[1.15]">
                            Подберите стиль
                            <span class="mx-1.5 inline-flex items-center sm:mx-2">
                                <span
                                    class="hero-photo-chip inline-block translate-y-[4px] rotate-[6deg] [filter:drop-shadow(0px_12px_24px_rgba(0,0,0,0.12))]">
                                    <span class="block rounded-[12px] bg-white p-1.5">
                                        <img src="/images/hero-1.png" alt=""
                                            class="h-[32px] w-[58px] rounded-[8px] object-cover sm:h-[36px] sm:w-[66px] xl:h-[42px] xl:w-[74px]" />
                                    </span>
                                </span>
                            </span>
                            <span class="font-playfair text-[#4252FF] italic font-semibold">фотосессии</span>
                            <br />
                            в Белгороде за
                            <br class="sm:hidden" />
                            несколько минут
                        </h1>

                        <p
                            class="hero-fade-up-2 mx-auto mt-4 max-w-[320px] text-center text-[16px] leading-6 text-[#14172D] sm:max-w-[420px] sm:text-[17px] sm:leading-7 xl:ml-auto xl:mr-0 xl:mt-2 xl:max-w-[550px] xl:text-left">
                            Изучите референсы, выберите подходящую локацию и подготовьте подробный бриф, который можно
                            отправить фотографу.
                        </p>

                        <div class="hero-fade-up-3 mt-6 flex justify-center sm:mt-8 xl:mt-8">
                            <Link href="/catalog"
                                class="group button-primary hero-cta inline-flex w-full max-w-[320px] items-center justify-center gap-2 py-4 sm:w-auto sm:max-w-none">
                                <span>Посмотреть идеи</span>
                                <ArrowRight
                                    class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" />
                            </Link>
                        </div>
                    </div>
                </div>

                <div
                    class="hero-visual-left relative z-10 mt-10 lg:absolute lg:left-[40px] lg:top-[300px] lg:mt-0 xl:left-40 xl:top-100">
                    <div
                        class="relative mx-auto h-[300px] w-full max-w-[330px] sm:h-[330px] sm:max-w-[360px] lg:h-[330px] lg:w-[360px] xl:h-[330px] xl:w-[360px] xl:max-w-none">
                        <div
                            class="hero-card hero-card-float-1 hero-card-enter-1 absolute left-0 top-0 w-[72%] rotate-[6deg] [filter:drop-shadow(0px_18px_40px_rgba(0,0,0,0.14))] xl:w-[340px]">
                            <div class="rounded-[14px] bg-white p-2.5 pb-5 sm:rounded-[16px] sm:p-3 sm:pb-6">
                                <img src="/images/hero-2.png" alt="Hero left image"
                                    class="h-[210px] w-full rounded-[10px] object-cover object-top sm:h-[230px] xl:h-[180px]" />
                            </div>
                        </div>

                        <div
    class="hero-card hero-card-float-2 hero-card-enter-2 absolute right-0 top-[54px] w-[42%] rotate-[10deg] [filter:drop-shadow(0px_18px_40px_rgba(0,0,0,0.14))] xl:left-[225px] xl:top-[70px] xl:w-[170px]">
                            <div class="rounded-[14px] bg-white p-2.5 pb-5 sm:rounded-[16px] sm:p-3 sm:pb-6">
                                <img src="/images/hero-3.png" alt="Hero secondary image"
                                    class="h-[150px] w-full rounded-[10px] object-cover object-top sm:h-[165px] xl:h-[150px]" />
                            </div>
                        </div>
                    </div>
                </div>

                <div
    class="hero-visual-right relative z-10 mt-6 xl:absolute lg:right-20 lg:top-80 lg:absolute xl:right-40 xl:top-[480px] xl:mt-0">
    <div
        class="hero-card hero-card-float-3 hero-card-enter-3 mx-auto w-full max-w-[340px] [filter:drop-shadow(0px_18px_40px_rgba(0px_18px_40px_rgba(0,0,0,0.14))] xl:w-[360px] xl:max-w-none">
                        <div class="rounded-[14px] bg-white p-2.5 pb-5 sm:rounded-[16px] sm:p-3 sm:pb-6">
                            <img src="/images/hero-4.png" alt="Hero right image"
                                class="h-[180px] w-full rounded-[10px] object-cover sm:h-[210px] lg:h-[140px] xl:h-[140px]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>