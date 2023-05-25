<script setup>
import { Link } from '@inertiajs/inertia-vue3';
defineProps({
  team:Object
});

</script>


<template>
  <ul class="side-nav">
    <!-- <li class="side-nav-title side-nav-item">Navigation</li> -->
    <li class="side-nav-item">
      <Link :href="route('dashboard')" class="side-nav-link">
        <i class="uil-home-alt"></i>
        <span> Dashboard </span>
    </Link>
    </li>
    <!-- <li class="side-nav-item">
      <a
        data-bs-toggle="collapse"
        href="#sidebarProjects"
        aria-expanded="false"
        aria-controls="sidebarProjects"
        class="side-nav-link"
      >
        <i class="uil-briefcase"></i>
        <span> Projects </span>
        <span class="menu-arrow"></span>
      </a>
      <div class="collapse" id="sidebarProjects">
        <ul class="side-nav-second-level">
          <li>
            <a href="#">List</a>
          </li>
          <li>
            <a href="#">Details</a>
          </li>
          <li>
            <a href="#">Create Project</a>
          </li>
        </ul>
      </div>
    </li> -->

    <li class="side-nav-item">
      <Link :href="route('teams')" class="side-nav-link">
      <i class="uil-users-alt"></i>
        <span> Teams </span>
    </Link>
    </li>
    <li class="side-nav-item">
      <Link :href="route('tasks')" class="side-nav-link">
        <i class="uil-briefcase"></i>
        <span> Tasks  </span>
    </Link>
    </li>
   

     <li class="side-nav-item">
      <Link :href="route('events')" class="side-nav-link">
        <i class="uil-calender"></i>
        <span> Calendar  </span>
    </Link>
    </li>

    <!-- <li class="side-nav-item">
      <a href="apps-chat.html" class="side-nav-link">
        <i class="uil-comments-alt"></i>
        <span> Chat </span>
      </a>
    </li>
    <li class="side-nav-item">
      <a href="apps-chat.html" class="side-nav-link">
        <i class="uil-setting"></i>
        <span> Setting </span>
      </a>
    </li>  -->

   
    <!-- <li class="side-nav-item">
    <a data-bs-toggle="collapse" href="#sidebarTasks" aria-expanded="false" aria-controls="sidebarTasks" class="side-nav-link">
        <i class="uil-clipboard-alt"></i>
        <span> Tasks </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarTasks">
        <ul class="side-nav-second-level">
            <li>
                <a href="apps-tasks.html">List</a>
            </li>
            <li>
                <a href="apps-tasks-details.html">Details</a>
            </li>
            <li>
                <a href="apps-kanban.html">Kanban Board</a>
            </li>
        </ul>
    </div>
</li> -->

    <!-- <li class="side-nav-item">
      <a href="apps-file-manager.html" class="side-nav-link">
        <i class="uil-folder-plus"></i>
        <span> File Manager </span>
      </a>
    </li> -->
  </ul>
</template>
