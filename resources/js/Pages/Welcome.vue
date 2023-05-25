<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.user" :href="route('dashboard')" class="text-sm text-gray-700 underline">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 underline">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
                    Register
                </Link>
            </template>
        </div>
        <div class="welcome-page">
        <div class="hero">
        <h1 class="title">Welcome to ManageUS</h1>
        <p class="subtitle">Stay organized and productive</p>
        <button class="btn btn-primary">Get Started</button>
        </div>
        <div class="features">
        <div class="feature">
            <i class="fas fa-tasks"></i>
            <h2 class="feature-title">Task Management</h2>
            <p class="feature-description">
            Stay on top of your tasks and projects. Create, assign, and track tasks to ensure smooth progress and completion.
            </p>
        </div>
        <div class="feature">
            <i class="fas fa-users"></i>
            <h2 class="feature-title">Collaboration</h2>
            <p class="feature-description">
            Collaborate with your team members by sharing tasks, comments, and files. Keep everyone informed and aligned.
            </p>
        </div>
        <div class="feature">
            <i class="fas fa-chart-bar"></i>
            <h2 class="feature-title">Insights & Analytics</h2>
            <p class="feature-description">
            Gain valuable insights and track your team's progress. Analyze data and make data-driven decisions for better productivity.
            </p>
        </div>
        </div>
    </div>

        
    </div>
</template>

<style scoped>
    .welcome-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.hero {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 36px;
  margin-bottom: 20px;
}

.subtitle {
  font-size: 24px;
  margin-bottom: 20px;
  color: #888;
}

.btn {
  padding: 10px 20px;
  font-size: 18px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.features {
  display: flex;
  justify-content: space-between;
}

.feature {
  flex-basis: 30%;
  text-align: center;
}

.feature-title {
  font-size: 24px;
  margin-bottom: 10px;
}

.feature-description {
  color: #888;
}
    .bg-gray-100 {
        background-color: #f7fafc;
        background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
    }

    .border-gray-200 {
        border-color: #edf2f7;
        border-color: rgba(237, 242, 247, var(--tw-border-opacity));
    }

    .text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }

    .text-gray-500 {
        color: #a0aec0;
        color: rgba(160, 174, 192, var(--tw-text-opacity));
    }

    .text-gray-600 {
        color: #718096;
        color: rgba(113, 128, 150, var(--tw-text-opacity));
    }

    .text-gray-700 {
        color: #4a5568;
        color: rgba(74, 85, 104, var(--tw-text-opacity));
    }

    .text-gray-900 {
        color: #1a202c;
        color: rgba(26, 32, 44, var(--tw-text-opacity));
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            background-color: #2d3748;
            background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
        }

        .dark\:bg-gray-900 {
            background-color: #1a202c;
            background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
        }

        .dark\:border-gray-700 {
            border-color: #4a5568;
            border-color: rgba(74, 85, 104, var(--tw-border-opacity));
        }

        .dark\:text-white {
            color: #fff;
            color: rgba(255, 255, 255, var(--tw-text-opacity));
        }

        .dark\:text-gray-400 {
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--tw-text-opacity));
        }
    }
</style>
