
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, LoaderCircle, Mail, LockKeyhole, UserRound } from 'lucide-vue-next';
import { ref } from 'vue';

const showPassword = ref(false);
const showConfirmation = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create your account" description="Buat ruang pribadimu dan mulai jalani hari dengan lebih terorganisir.">
        <Head title="Register — Fluxa" />

        <form @submit.prevent="submit" class="flex flex-col gap-4">
            <div class="grid gap-2">
                <Label for="name" class="text-sm font-semibold">Nama lengkap</Label>
                <div class="relative">
                    <UserRound class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Nama kamu"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                </div>
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email" class="text-sm font-semibold">Email address</Label>
                <div class="relative">
                    <Mail class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password" class="text-sm font-semibold">Password</Label>
                <div class="relative">
                    <LockKeyhole class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Buat password"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 pr-12 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                    <button
                        type="button"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-slate-400 hover:text-violet-600"
                        @click="showPassword = !showPassword"
                    >
                        <EyeOff v-if="showPassword" :size="18" />
                        <Eye v-else :size="18" />
                    </button>
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation" class="text-sm font-semibold">Konfirmasi password</Label>
                <div class="relative">
                    <LockKeyhole class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirmation ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 pr-12 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                    <button
                        type="button"
                        :aria-label="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-slate-400 hover:text-violet-600"
                        @click="showConfirmation = !showConfirmation"
                    >
                        <EyeOff v-if="showConfirmation" :size="18" />
                        <Eye v-else :size="18" />
                    </button>
                </div>
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 h-12 w-full rounded-xl bg-violet-600 font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="mr-2 size-4 animate-spin" />
                {{ form.processing ? 'Membuat akun...' : 'Buat akun Fluxa' }}
            </Button>

            <p class="pt-1 text-center text-sm text-slate-500 dark:text-slate-400">
                Sudah punya akun?
                <TextLink :href="route('login')" class="font-semibold text-violet-600 hover:text-violet-700 dark:text-violet-400">
                    Masuk
                </TextLink>
            </p>
        </form>
    </AuthBase>
</template>
