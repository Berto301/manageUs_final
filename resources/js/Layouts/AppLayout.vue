<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
// import { Head, Link } from '@inertiajs/inertia-vue3';
// import JetApplicationMark from '@/Components/ApplicationMark.vue';
// import JetBanner from '@/Components/Banner.vue';
// import JetDropdown from '@/Components/Dropdown.vue';
// import JetDropdownLink from '@/Components/DropdownLink.vue';
// import JetNavLink from '@/Components/NavLink.vue';
// import JetResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Navbar from "./Navbar.vue";
import Sidebar from "./Sidebar.vue";
import Footer from "./Footer.vue";
import RightSidebar from "./RightSidebar.vue";

defineProps({
  title: String,
  user:Object,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
  Inertia.put(
    route("current-team.update"),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};

const logout = () => {
  Inertia.post(route("logout"));
};
</script>

<template>
  <!-- Topbar Start -->
  <Navbar :user="user"/>
  <!-- end Topbar -->

  <!-- Start Content-->
  <div class="container-fluid">
    <!-- Begin page -->
    <div class="wrapper">
      <!-- ========== Left Sidebar Start ========== -->
      <div class="leftside-menu leftside-menu-detached" style="z-index: 5!important;">
        <div class="leftbar-user">
          <a href="javascript: void(0);">
            <img
              :src="`storage/${user.profile_photo_path}`"
              alt="user-image"
              height="42"
              class="rounded-circle h-40 w-40 object-cover ml-8"
            />
            <span class="leftbar-user-name">{{ user?.name }}</span>
          </a>
        </div>

        <!--- Sidemenu -->
        <Sidebar />
      </div>
      <!-- Left Sidebar End -->

      <div class="content-page">
        <div class="content">
          <slot />
        </div>
        <!-- End Content -->

        <!-- Footer Start -->
        <Footer />

        <!-- end Footer -->
      </div>
      <!-- content-page -->
    </div>
    <!-- end wrapper-->
  </div>
  <!-- END Container -->

  <!-- Right Sidebar -->
  <RightSidebar />

  <div class="rightbar-overlay"></div>
  <!-- /End-bar -->
</template>
