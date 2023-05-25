<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import Logo from "../../Components/Logo.vue";
import {LISTS_ACTIVITY_AREA} from "./lists"

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
    role: "founder",
    activityAreaGroup:"",
    nameGroup:""
});
// const carouselImages =[
// 'layout-1.png',
// 'layout-2.png',
// 'layout-3.png'
// ]
const submit = () => {
    form.post(route("register"), {
        onFinish: () => {
            form.reset("password", "password_confirmation");
            // reload the page so we get new scripts, etc. in the <head>
            // location.reload()
        },
    });
};
</script>

<template>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <!-- Logo-->
                        <Logo>
                            <Link :href="route('index_page')">
                                <span
                                    ><img
                                        src="assets/images/logo_us.png"
                                        alt=""
                                        class="w-12 h-10 object-contain"
                                /></span>
                            </Link>
                        </Logo>

                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <h4
                                    class="text-dark-50 text-center mt-0 fw-bold"
                                >
                                    Welcome to ManageUs
                                </h4>
                                <p class="text-muted mb-4">
                                    Don't have an account? Create here your
                                    account
                                </p>
                            </div>

                            <form @submit.prevent="submit">
                                Enterprise / Group Information
                                <div class="mb-3">
                                    <label for="fullname" class="form-label"
                                        >Group name</label
                                    >
                                    <input
                                        v-model="form.nameGroup"
                                        class="form-control"
                                        type="text"
                                        id="fullname"
                                        placeholder="Enter your name"
                                        required
                                    />
                                </div>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label"
                                        >Area activity</label
                                    >
                                    <select
                                        class="form-select"
                                        aria-label="Default select example"
                                        v-model="form.activityAreaGroup"
                                    >
                                        <option :value="null">Please select</option>
                                        <option v-for="(area,i) in LISTS_ACTIVITY_AREA" :key="'area.value'+'-'+i" :value="area.value">{{ area.title }}</option>
                                    </select>
                                </div>


                                User Information
                                <hr/>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label"
                                        >Full Name</label
                                    >
                                    <input
                                        v-model="form.name"
                                        class="form-control"
                                        type="text"
                                        id="fullname"
                                        placeholder="Enter your name"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="emailaddress" class="form-label"
                                        >Email address</label
                                    >
                                    <input
                                        v-model="form.email"
                                        class="form-control"
                                        type="email"
                                        id="emailaddress"
                                        required
                                        placeholder="Enter your email"
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label"
                                        >Password</label
                                    >
                                    <div class="input-group input-group-merge">
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            id="password"
                                            class="form-control"
                                            placeholder="Enter your password"
                                        />
                                        <div
                                            class="input-group-text"
                                            data-password="false"
                                        >
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label
                                        for="password_confirm"
                                        class="form-label"
                                        >Confirm Password</label
                                    >
                                    <div class="input-group input-group-merge">
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            id="password_confirm"
                                            class="form-control"
                                            placeholder="Confirm your password"
                                        />
                                        <div
                                            class="input-group-text"
                                            data-password="false"
                                        >
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input
                                            v-model="form.terms"
                                            type="checkbox"
                                            class="form-check-input"
                                            id="checkbox-signup"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="checkbox-signup"
                                        >
                                            I agree to the
                                            <Link
                                                target="_blank"
                                                :href="route('terms.show')"
                                                class="text-muted"
                                            >
                                                Terms of Service
                                            </Link>
                                            and
                                            <Link
                                                target="_blank"
                                                :href="route('policy.show')"
                                                class="text-muted"
                                            >
                                                Privacy Policy
                                            </Link>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3 text-center">
                                    <button
                                        :disabled="form.processing"
                                        class="btn btn-primary"
                                        type="submit"
                                    >
                                        Sign Up
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">
                                Already have account?
                                <Link
                                    :href="route('login')"
                                    class="text-muted ms-1"
                                    ><b>Log In</b></Link
                                >
                            </p>
                        </div>
                        <!-- end col-->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
</template>
<style>
.carousel-container {
  margin-top: 4rem;
}

.carousel {
  width: 100%;
}

.carousel img {
  height: auto;
  max-width: 100%;
}
</style>
