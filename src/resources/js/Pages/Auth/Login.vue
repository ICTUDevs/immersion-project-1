<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, onMounted } from "vue";

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    Oauth_status: Boolean,
});

const isLogin = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const form1 = useForm({
    credential: "",
});

const handleCredentialResponse = (response) => {
    console.log("Encoded JWT ID token: " + response.credential);
    // Send the token to your backend for verification and login

    form1.credential = response.credential;
    form1.post(route('auth.google.callback'), {
        onFinish: () => form1.reset("credential"),
    });
};

onMounted(() => {
    const script = document.createElement('script');
    script.src = "https://accounts.google.com/gsi/client";
    script.async = true;
    script.onload = () => {
        google.accounts.id.initialize({
            client_id: "908852665610-0euftd8i58r5j79l2t2d1lehaenrc9ki.apps.googleusercontent.com",
            callback: handleCredentialResponse,
        });
        google.accounts.id.renderButton(
            document.getElementById("buttonDiv"),
            { theme: "outline", size: "large", 'longtitle': true, 'z-index': 0, display: 'flex', width: '400' } // customization attributes
        );
        google.accounts.id.prompt(); // also display the One Tap dialog
    };
    document.head.appendChild(script);
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div
            v-if="status"
            class="mb-4 font-medium text-sm text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>

        <div v-if="Oauth_status">
           <div class="flex flex-row justify-between">
                <div>
                    <h5 class="font-extrabold text-2xl dark:text-white">
                        Welcome back
                    </h5>
                    <p class="text-slate-500 text-sm">
                        Sign in to your account using:
                    </p>
                </div>
                <div>
                   
                </div>
           </div>

            <hr class="my-6 h-px bg-gray-200 border-0 dark:bg-gray-700" />

            <div id="buttonDiv"></div>

            <hr
                class="w-full h-px my-6 bg-gray-200 border-0 dark:bg-gray-700"
            />
            <ThemeToggle class="text-black dark:text-white float-end" />
        </div>

        <form @submit.prevent="submit" v-if="isLogin">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>