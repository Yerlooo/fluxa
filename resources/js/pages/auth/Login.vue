
<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff, LoaderCircle, Mail, LockKeyhole } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase title="Welcome back" description="Masuk ke akunmu dan lanjutkan aktivitasmu.">
        <Head title="Login — Fluxa" />

        <div v-if="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-5">
            <div class="grid gap-2">
                <Label for="email" class="text-sm font-semibold">Email address</Label>
                <div class="relative">
                    <Mail class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                </div>
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between gap-2">
                    <Label for="password" class="text-sm font-semibold">Password</Label>
                    <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-xs font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400">
                        Lupa password?
                    </TextLink>
                </div>
                <div class="relative">
                    <LockKeyhole class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                    <Input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                        class="h-12 rounded-xl border-slate-200 bg-slate-50 pl-11 pr-12 focus-visible:ring-violet-500 dark:border-white/10 dark:bg-white/[0.04]"
                    />
                    <button
                        type="button"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-slate-400 transition hover:text-violet-600"
                        @click="showPassword = !showPassword"
                    >
                        <EyeOff v-if="showPassword" :size="18" />
                        <Eye v-else :size="18" />
                    </button>
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center gap-3">
                <Checkbox id="remember" v-model:checked="form.remember" />
                <Label for="remember" class="cursor-pointer text-sm font-normal text-slate-600 dark:text-slate-400">
                    Ingat saya
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1 h-12 w-full rounded-xl bg-violet-600 font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700"
                :disabled="form.processing"
            >
                <LoaderCircle v-if="form.processing" class="mr-2 size-4 animate-spin" />
                {{ form.processing ? 'Sedang masuk...' : 'Masuk ke Fluxa' }}
            </Button>

            <p class="text-center text-sm text-slate-500 dark:text-slate-400">
                Belum punya akun?
                <TextLink :href="route('register')" class="font-semibold text-violet-600 hover:text-violet-700 dark:text-violet-400">
                    Buat akun
                </TextLink>
            </p>
        </form>
    </AuthBase>
</template>
